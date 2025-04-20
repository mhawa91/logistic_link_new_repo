<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

use App\Models\User;
use Modules\ContentManager\Entities\Content;
use Modules\SystemCore\Entities\Role;

class ShippmentCommodity extends Model 
{
    public $timestamps = false;
    
    protected $table = 'lgl_shippments_commodities';

    protected $fillable = [
        'shippment_id',
        'commodity_id',
    ];

    public function commodity()
    {
        return $this->belongsTo(Content::class, 'commodity_id', 'id');
    }
}
