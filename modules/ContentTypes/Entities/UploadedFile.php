<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class UploadedFile extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_related_files';

    protected $fillable = [
        'related_id',
        'related_type',
        'file_tag',
        'file_name',
        'file_path',
        'file_size',
        'file_extension',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
    ];
}
