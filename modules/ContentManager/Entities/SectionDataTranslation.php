<?php

namespace Modules\ContentManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class SectionDataTranslation extends Model 
{
    protected $table    = 'sections_data_translations';

    public $timestamps  = false;

    protected $fillable = [
        'section_data_id',
        'default',
        'locale',
        'x_value',
    ];
}
