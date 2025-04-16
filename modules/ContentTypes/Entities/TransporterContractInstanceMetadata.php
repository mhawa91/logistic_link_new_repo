<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class TransporterContractInstanceMetadata extends Model 
{
    public $timestamps = false;
    
    protected $table = 'lgl_transporters_contracts_metadata';

    protected $fillable = [
        'contract_id',
        'contract_instance_id',
        'company_user_id',
        'transporter_user_id',
        'type', // ['TERMS_AND_CONDITIONS', 'INSURANCE_DETAILS', 'PENALITIES']
        'description'
    ];
}
