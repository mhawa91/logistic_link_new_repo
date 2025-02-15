<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;

class AbilityGroupTranslation extends Model
{
    protected $table = 'abilities_groups_translations';

    public $timestamps = false;

    protected $fillable = [
        'default',
        'title',
        'description',
        'locale',
        'ability_group_id',
    ];
}
