<?php

namespace Modules\SystemCore\Entities;

use Mail;
use Carbon\Carbon;
use App\Models\User;

class PasswordResetCodeService
{
    protected static $WAITING_TO_REQUEST_IN_MINUTES = 2;

    protected static $VALIDITY_IN_MINUTES           = 5;

    public static function canRequestByEmail(User $user): array
    {
        if (empty($user->email)) {
            return [
                'status'  => 'FAIL',
                'message' => 'USER_HAS_NO_EMAIL',
            ];
        }
        $TIME_CHECK = PasswordResetCode::where('user_id', $user->id)
        ->where('valid_until', '>=', Carbon::now()->toDateTimeString())
        ->orderBy('valid_until', 'DESC')
        ->first();
        if (! empty($TIME_CHECK)) {
            return [
                'status'  => 'FAIL',
                'message' => 'USER_HAS_TO_WAIT',
                'waiting' => $TIME_CHECK->valid_until,
                'now'     => Carbon::now()->toDateTimeString()
            ];
        }
        return [
            'status'  => 'OK',
            'message' => 'REQUEST_CAN_BE_SENT',
        ];
    }

    public static function userFromCode(string $code) :? User
    {
        $CODE = PasswordResetCode::with([
            'User' => function($Rel) {
                $Rel->where('sys_status', 'ACTIVE');
            }
        ])
        ->whereHas('User', function($Rel) {
            $Rel->where('sys_status', 'ACTIVE');
        })
        ->where('code', $code)
        ->where('valid_until', '>=', Carbon::now()->toDateTimeString())
        ->first();
        if ($CODE) {
            return $CODE->User;
        }
        return null;
    }

    public static function invalidateCode(string $code)
    {
        PasswordResetCode::with([
            'User' => function($Rel) {
                $Rel->where('sys_status', 'ACTIVE');
            }
        ])
        ->whereHas('User', function($Rel) {
            $Rel->where('sys_status', 'ACTIVE');
        })
        ->where('code', $code)
        ->delete();
    }

    public static function checkCode(User $user, string $code)
    {
        $CHECK = PasswordResetCode::where('user_id', $user->id)
        ->where('code', $code)
        ->where('valid_until', '>=', Carbon::now()->toDateTimeString())
        ->first();
        if (empty($CHECK)) {
            return [
                'status'  => 'FAIL',
                'message' => 'INVALID_CODE',
            ];
        }
        return [
            'status'  => 'OK',
            'message' => 'CODE_IS_VALID',
        ];
    }

    protected static function generateResetCode(User $user): PasswordResetCode
    {
        $Code             = new PasswordResetCode;
        $Code->user_id    = $user->id;
        $Code->code       = 
                ( rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) )
        . '-' . ( rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) )
        . '-' . ( rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) )
        . '-' . ( rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) )
        ;
        $Code->valid_until = Carbon::now()->addMinutes(static::$VALIDITY_IN_MINUTES)->toDateTimeString();
        $Code->save();
        return $Code;
    }

    public static function RequestCodeByEmail(User $user): PasswordResetCode
    {
        return static::generateResetCode($user);
    }

    public static function RequestCodeBySms(User $user)
    {
        $PasswordResetCode = static::generateResetCode($user);
    }

    public static function CleanDatabase()
    {
        PasswordResetCode::where('valid_until', '<', Carbon::now()->toDateTimeString())->delete();
    }
}
