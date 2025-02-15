<?php

namespace Modules\SystemCore\Entities;

use Silber\Bouncer\Database\Ability as BaseAbility;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Ability extends BaseAbility implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'name',
        'group_id',
        'entity_id',
        'entity_type',
        'only_owned',
        'scope',
    ];

    public $translationModel = 'Modules\SystemCore\Entities\AbilityTranslation';

    public $translatedAttributes = [
        'default',
        'title',
        'description',
    ];

    public Function Group()
    {
        return $this->belongsTo(AbilityGroup::class, 'group_id', 'id');
    }
}
