<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;

class ShippmentImportRow extends Model
{
    protected $table = 'lgl_shippment_import_rows';

    protected $fillable = [
        'import_id', 'row_number', 'import_key', 'shippment_id', 'shippment_ids',
        'operation', 'status', 'payload', 'errors',
    ];

    protected $casts = [
        'shippment_ids' => 'array',
        'payload' => 'array',
        'errors' => 'array',
    ];

    public function import()
    {
        return $this->belongsTo(ShippmentImport::class, 'import_id');
    }

    public function shippment()
    {
        return $this->belongsTo(Shippment::class, 'shippment_id');
    }

}
