<?php

namespace Modules\ContentManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class Section extends Model
{
    protected $table    = 'sections';

    public $timestamps  = false;

    protected $fillable = [
        'tag',
    ];

    public function Data()
    {
        return $this->hasMany(SectionData::class, 'section_id', 'id');
    }
}