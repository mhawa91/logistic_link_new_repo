<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;

class AbilityTranslation extends Model
{
    protected $table = 'abilities_translations';

    protected $fillable = [
        'default',
        'ability_id',
        'locale',
        'title',
        'description'
    ];

    public $timestamps = false;
}
