<?php namespace Kjamesy\Cms\Models;
use Baum\Node;
use Illuminate\Support\Facades\Cache;

class Page extends Node
{

    protected $table = 'cms_pages';

    public static $rules = [
        'title' => 'required|max:255',
        'summary' => 'required|max:512',
        'content' => 'required',
        'order' => 'integer',
        'create_date' => 'date'
    ];

    protected static $cache_minutes = 1440;
    protected static $orderBy = 'lft';

    public function user(){
        return $this->belongsTo('User');
    }

    public function pagetranslations(){
        return $this->hasMany('Kjamesy\Cms\Models\PageTranslation'); //'page_id'
    }

    public function pagemeta(){
        return $this->hasMany('Kjamesy\Cms\Models\Meta');
    }

    public static function getPageResource() {
        return static::with('user')->with('pagetranslations.locale')->orderBy(static::$orderBy)->get();
    }

    public static function getSinglePageResource($id) {
        return static::with('user')->with('pagetranslations.locale')->with('pagemeta')->find($id);
    }

    public static function changePagesStatus($ids, $action) {
        if ( count($ids) ) {
            if ($action == 'destroy') {
                foreach ($ids as $id) {
                    $page = static::find($id);
                    if ($page) {
                        $page->delete();
                    }
                }
            }
            else {
                foreach ($ids as $id) {
                    $page = static::find($id);
                    if ($page) {
                        if ($action == 'publish')
                            $page->is_online = 1;
                        elseif ($action == 'draft')
                            $page->is_online = 0;
                        elseif ($action == 'trash') {
                            $page->is_deleted = 1;
                            $descendants = $page->getDescendants();

                            foreach ($descendants as $descendant) {
                                $descendant->is_deleted = 1;
                                $descendant->save();
                            }
                        } elseif ($action == 'restore') {
                            $page->is_deleted = 0;
                            $parent = $page->parent()->first();
                            if ($parent) {
                                $parent->is_deleted = 0;
                                $parent->save();
                            }
                        }
                        $page->save();
                    }
                }
            }

            static::rebuild(true);
        }
    }

    public static function getParentOptions($exceptId){
        return $exceptId ? ['0' => '(no parent)'] + static::whereIsDeleted(0)
                ->whereNotIn('id', [$exceptId])
                ->lists('title', 'id')
            : ['0' => '(no parent)'] + static::whereIsDeleted(0)
                ->lists('title', 'id');
    }

    public static function getPagesBelongingToUser($userId) {
        return static::whereHas('user', function($query) use ($userId) { $query->whereId($userId); })->get();
    }


}