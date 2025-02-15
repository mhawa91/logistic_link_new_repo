<?php

namespace Modules\ContentManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class TagTranslation extends Model 
{
    protected $table    = 'contents_tags_translations';

    public $timestamps  = false;

    protected $fillable = [
        'tag_id',
        'locale',
        'title',
    ];
}
