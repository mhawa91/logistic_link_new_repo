<?php

namespace Modules\ContentManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class SectionData extends Model implements TranslatableContract
{
    use Translatable, TranslatableHelper;

    protected $table    = 'sections_data';

    public $timestamps  = false;

    protected $fillable = [
        'section_id',
        'x_key',
    ];

    public $translationModel = "Modules\ContentManager\Entities\SectionDataTranslation";
    
    public $translatedAttributes = [
        'default',
        'x_value'
    ];
}
