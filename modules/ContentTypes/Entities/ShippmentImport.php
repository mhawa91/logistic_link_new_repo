<?php

namespace Modules\ContentTypes\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ShippmentImport extends Model
{
    protected $table = 'lgl_shippment_imports';

    protected $fillable = [
        'user_id', 'original_filename', 'disk', 'file_path', 'locale', 'status',
        'total_rows', 'processed_rows', 'created_count', 'updated_count',
        'failed_count', 'error_message', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rows()
    {
        return $this->hasMany(ShippmentImportRow::class, 'import_id')->orderBy('row_number');
    }

}
