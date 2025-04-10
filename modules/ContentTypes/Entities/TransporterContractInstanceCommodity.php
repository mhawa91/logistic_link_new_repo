<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

use DB;
use App\Models\User;
use Modules\ContentManager\Entities\Content;

class TransporterContractInstanceCommodity extends Model 
{
    public $timestamps = false;
    
    protected $table = 'lgl_transporters_contracts_commodities';

    protected $fillable = [
        'contract_id',
        'contract_instance_id',
        'company_user_id',
        'transporter_user_id',
        'commodity_id'
    ];

    public function contract()
    {
        return $this->belongsTo(TransporterContract::class, 'contract_id', 'id');
    }

    public function contractInstance()
    {
        return $this->belongsTo(TransporterContractInstance::class, 'contract_instance_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function transporter()
    {
        return $this->belongsTo(User::class, 'transporter_user_id', 'id');
    }

    public function content()
    {
        return $this->belongsTo(Content::class, 'commodity_id', 'id');
    }
}
