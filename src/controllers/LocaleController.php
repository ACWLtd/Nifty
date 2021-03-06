<?php namespace Kjamesy\Cms\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Routing\Controller as BaseController;
use Kjamesy\Cms\Models\Locale;
use Kjamesy\Cms\Models\User;

class LocaleController extends BaseController
{
    public function __construct() {
        $this->middleware('manage_content');
        $this->user = Auth::user();
        $this->isAdmin = User::isAdmin( $this->user );
        $this->logged_in_for = $this->user->last_login->diffForHumans();
        $this->rules = Locale::$rules;
        $this->activeParent = 'locales';
    }

    public function index() {
        return View::make("cms::locales.angular", [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'alllocales'
        ]);
    }

    public function destroy() {
        $id = Input::get('id');
        Locale::whereId($id)->delete();

        Cache::flush();
        
        return Response::json(['success' => 'Locale successfully deleted']);
    }

}