<?php

namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Relationship between users and roles
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->belongsToMany('Kjamesy\Cms\Models\User');
    }

    /**
     * Get the role resource
     * @return mixed
     */
    public static function getRoleResource()
    {
        return static::get();
    }
}