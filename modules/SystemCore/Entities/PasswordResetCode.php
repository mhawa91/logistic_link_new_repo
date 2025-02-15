<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PasswordResetCode extends Model
{
    protected $table    = 'password_reset_codes';

    public $timestamps  = false;

    protected $fillable = [
        'user_id',
        'code',
        'valid_until',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}