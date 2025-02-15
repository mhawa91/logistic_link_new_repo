<?php

namespace Modules\ContentManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class Tag extends Model implements TranslatableContract
{
    use Translatable, TranslatableHelper;

    protected $table    = 'content_tags';

    public $timestamps  = false;

    protected $fillable = [];

    public $translationModel = "Modules\ContentManager\Entities\TagTranslation";
    
    public $translatedAttributes = [
        'locale',
        'title',
    ];

    public function Contents()
    {
        return $this->morphedByMany(Content::class, 'content_taggables');
    }

    public function scopeSearch($query, $Keyword)
    {
        $query->where(function($q) use($Keyword) {
            $q->where('id', $Keyword);
            $q->orWhereHas('translations', fn($Rel) => $Rel->where('title', 'like', "%{$Keyword}%"));
        });
        return $query;
    }

    public function scopeSearchStrict($query, $Keyword)
    {
        $query->where(function($q) use($Keyword) {
            $q->where('id', $Keyword);
            $q->orWhereHas('translations', fn($Rel) => $Rel->where('title', $Keyword));
        });
        return $query;
    }

    public static function loadByKeyword($Tags)
    {
        $_Tags = static::where(function($Query) use($Tags) {
            $Query->whereIn('id', $Tags);
            $Query->orWhereHas('translations', fn($Rel) => $Rel->whereIn('title', 'like', $Tags));
        })->get();
    }
}
