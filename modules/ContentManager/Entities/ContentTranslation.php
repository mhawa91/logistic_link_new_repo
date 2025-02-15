<?php

namespace Modules\ContentManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class ContentTranslation extends Model 
{
    protected $table    = 'contents_translations';

    public $timestamps  = false;

    protected $fillable = [
        'content_id',
        'locale',
        'default',
        'sub_title',
        'title',
        'short_content',
        'long_content',
        'image',
        'seo_image',
        'icon',
        'url',
        'meta_alt_img',
        'meta_title',
        'meta_description',
        'slug',
    ];
}
