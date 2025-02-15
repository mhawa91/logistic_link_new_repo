<?php

namespace Modules\SystemCore\Entities;

use Silber\Bouncer\Database\Role as BaseRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class Role extends BaseRole implements TranslatableContract
{
    use Translatable,
        TranslatableHelper,
        SoftDeletes;

    protected $fillable = [
        'name',
        'level',
        'scope',
        'suspendable',
        'updatable',
    ];

    public $translationModel = 'Modules\SystemCore\Entities\RoleTranslation';

    public $translatedAttributes = [
        'default',
        'title',
        'description',
    ];
}
