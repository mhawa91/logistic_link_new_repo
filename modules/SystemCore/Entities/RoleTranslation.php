<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;

class RoleTranslation extends Model
{
    protected $table = 'roles_translations';

    protected $fillable = [
        'default',
        'role_id',
        'locale',
        'title',
        'description'
    ];

    public $timestamps = false;
}
