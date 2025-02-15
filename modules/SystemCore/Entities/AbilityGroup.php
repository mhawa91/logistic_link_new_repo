<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class AbilityGroup extends Model implements TranslatableContract
{
    use Translatable;

    public $timestamps = false;

    protected $table = 'abilities_groups';

    protected $fillable = [
        'code',
        'icon',
    ];

    public $translationModel = 'Modules\SystemCore\Entities\AbilityGroupTranslation';

    public $translatedAttributes = [
        'default',
        'title',
        'description',
    ];

    public function Abilities()
    {
        return $this->hasMany(Ability::class, 'group_id', 'id');
    }
}
