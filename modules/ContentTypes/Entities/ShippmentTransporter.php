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

class ShippmentTransporter extends Model 
{
    public $timestamps = false;
    
    protected $table = 'lgl_shippments_matched_transporters';

    protected $fillable = [
        'shippment_id',
        'company_user_id',
        'transporter_user_id',
        'contract_id',
        'contract_instance_id',
        'approved_contract_instance_id',
        'price',
        'pre_additional_location_price',
        'total_price',
        'status',
        'requested_at',
        'responded_at',
    ];

    public function shippment()
    {
        return $this->belongsTo(Shippment::class, 'shippment_id', 'id');
    }

    public function companyUser()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function transporterUser()
    {
        return $this->belongsTo(User::class, 'transporter_user_id', 'id');
    }

    public function contract()
    {
        return $this->belongsTo(TransporterContract::class, 'contract_id', 'id');
    }

    public function contractInstance()
    {
        return $this->belongsTo(TransporterContractInstance::class, 'contract_instance_id', 'id');
    }

    public function approvedContractInstance()
    {
        return $this->belongsTo(TransporterContractInstance::class, 'approved_contract_instance_id', 'id');
    }
}
