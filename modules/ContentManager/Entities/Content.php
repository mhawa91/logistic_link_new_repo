<?php

namespace Modules\ContentManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class Content extends Model implements TranslatableContract
{
    use SoftDeletes, 
        Translatable, 
        TranslatableHelper;

    protected $table    = 'contents';

    protected $fillable = [
        'base_type',
        'type',
        'parent_id',
        'ordering',
        'published_at',
        'views_count',
        'created_by_user_id',
        'updated_by_user_id',
        'sys_status',
    ];

    public $translationModel = "Modules\ContentManager\Entities\ContentTranslation";
    
    public $translatedAttributes = [
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
    ];

    public function Parent()
    {
        return $this->belongsTo(Content::class, 'parent_id', 'id');
    }

    public function Categories()
    {
        return $this->belongsToMany(Content::class, 'contents_categories', 'content_id', 'category_id');
    }

    public function SubContents()
    {
        return $this->belongsToMany(Content::class, 'contents_categories', 'category_id', 'content_id');
    }

    public function Tags()
    {
        return $this->morphToMany(Tag::class, 'content_taggables');
        // return $this->belongsToMany(Content::class, 'content_taggables', 'content_id', 'category_id');
    }
}
