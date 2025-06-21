<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

use DB;
use Modules\SystemCore\Entities;

class InvoiceItem extends Model 
{
    public $timestamps = false;

    protected $table   = 'lgl_invoices_items';

    protected $fillable = [
        'invoice_id',
        'shippment_id',
        'description',
        'sub_cost',
        'discount',
        'cost',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function shippment()
    {
        return $this->belongsTo(Shippment::class, 'shippment_id', 'id');
    }
}
