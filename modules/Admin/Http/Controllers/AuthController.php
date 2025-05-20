<?php

namespace Modules\Admin\Http\Controllers;

use Auth;
use Hash;
use Exception;
use Validator;
use Modules\SystemCore\Entities\PasswordResetCodeService;
use Modules\Admin\Emails\ResetPasswordRequest;
use Mail;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Crypt;
use Modules\Admin\Jobs\SendRegisterConfirmationEmail;
use Modules\Admin\Jobs\SendWelcomeEmail;
use Bouncer;

class AuthController extends SystemCoreHttpController
{
    public function __construct()
    {
        
    }

    public function login(Request $Request)
    {
        if ($Request->hash) {
            try {
                $userData = Crypt::decryptString($Request->hash);
                $userData = json_decode($userData, true);
                if (! empty($userData['id'])) {
                    Auth::loginUsingId($userData['id'], true);
                    return redirect()->route('AdminDashboardController@index');
                }
            } 
            catch (Exception $e) { }
        }
        return view('ui::auth.login', $this->data);
    }

    public function postLogin(Request $Request, ActionResponse $Response)
    {
        try {
            $this->data['Validator'] = Validator::make($Request->all(), [
                'email_or_phone_or_username' => 'required',
                'password'                   => 'required',
            ]);
            $this->data['Validator']->validate();
            $this->data['user'] = app(config('systemcore.models.User'))->where(function($User) use($Request) {
                $User->where('email', $Request->email_or_phone_or_username);
                $User->orWhere('phone_number', $Request->email_or_phone_or_username);
                $User->orWhere('username', $Request->email_or_phone_or_username);
            });
            $this->data['user'] = $this->data['user']->first();
            $this->data['Validator']->after(function($Validator) use($Request) {
                if (! $this->data['user']) {
                    $Validator->errors()->add('email_or_phone_or_username', __('ui::strings.account_not_found_with_this_data'));
                }
                else if (! Hash::check($Request->password, $this->data['user']->password)) {
                    $Validator->errors()->add('password', __('ui::strings.wrong_password'));
                }
            });
            $this->data['Validator']->validate();
            $Remember = ! empty($Request->remember_me) ? true : false;
            Auth::loginUsingId($this->data['user']->id, $Remember);
        } 
        catch (Exception $e) {
            throw $e;
        }

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route(config('systemcore.auth.REDIRECT_TO_ROUTE_NAME'))
        ]);
    }

    public function reset(Request $Request)
    {
        return view('ui::auth.reset', $this->data);
    }

    public function postReset(Request $Request, ActionResponse $Response)
    {
        try {
            $this->data['Validator'] = Validator::make($Request->all(), [
                'email_or_phone_or_username' => 'required',
            ]);
            $this->data['Validator']->validate();
            $this->data['user'] = app(config('systemcore.models.User'))->where(function($User) use($Request) {
                $User->where('email', $Request->email_or_phone_or_username);
                $User->orWhere('phone_number', $Request->email_or_phone_or_username);
                $User->orWhere('username', $Request->email_or_phone_or_username);
            });
            $this->data['user'] = $this->data['user']->first();
            $this->data['PasswordResetCode'] = null;
            if ($this->data['user']) {
                PasswordResetCodeService::CleanDatabase();
                // $CHECK = PasswordResetCodeService::canRequestByEmail($this->data['user']);
                // if ($CHECK['status'] != 'OK') {
                //     return $Response->module('Admin')->code('500')->success()->withOnlyToAjaxWeb([
                //         'toastr' => [
                //             'type'        => 'error',
                //             'title'       => 'يرجى الانتظار',
                //             'description' => 'يرجى الانتظار لمدة 5 دقائق قبل اعادة ارسال الطلب مرة اخرى',
                //         ],
                //     ]);
                // }
                $this->data['PasswordResetCode'] = PasswordResetCodeService::RequestCodeByEmail($this->data['user']);
                Mail::to($this->data['user'])->send(
                    new ResetPasswordRequest($this->data['user'], $this->data['PasswordResetCode'], route('AuthController@newPassword', [
                        'hash' => $this->data['PasswordResetCode']->code
                    ]))
                );
            }
        } 
        catch (Exception $e) {
            throw $e;
        }
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            // 'redirect' => route('AuthController@newPassword', ['hash' => $this->data['PasswordResetCode'] ? $this->data['PasswordResetCode']->code : '']),
            'toastr' => [
                'type'        => 'success',
                'title'       => 'تم ارسال الطلب',
                'description' => 'في حال كان هذا الحساب مسجلا بالنظام سيتم ارسال التعليمات عبر البريد الالكتروني',
            ],
        ]);
    }

    public function newPassword(Request $Request)
    {
        try {
            $this->data['user'] = PasswordResetCodeService::userFromCode($Request->hash);
            if (! $this->data['user']) {
                return $Response->module('Admin')->code('500')->success()->withOnlyToAjaxWeb([
                    'toastr' => [
                        'type'        => 'error',
                        'title'       => 'الكود غير صالح',
                        'description' => 'عذرا ولكن هذا الطلب لم يعد صالحا',
                    ],
                ]);
            }
            $CHECK = PasswordResetCodeService::checkCode($this->data['user'], $Request->hash);
            if ($CHECK['status'] != 'OK') {
                return $Response->module('Admin')->code('500')->success()->withOnlyToAjaxWeb([
                    'toastr' => [
                        'type'        => 'error',
                        'title'       => 'الكود غير صالح',
                        'description' => 'عذرا ولكن هذا الطلب لم يعد صالحا',
                    ],
                ]);
            }
        } 
        catch (Exception $e) {
            throw $e;
        }
        return view('ui::auth.new_password', $this->data);
    }

    public function postNewPassword(Request $Request, ActionResponse $Response)
    {
        try {
            $this->data['Validator'] = Validator::make($Request->all(), [
                'hash'      => 'required',
                'password'  => 'required|confirmed',
            ]);
            $this->data['Validator']->validate();
            $this->data['user'] = PasswordResetCodeService::userFromCode($Request->hash);
            if (! $this->data['user']) {
                return $Response->module('Admin')->code('500')->success()->withOnlyToAjaxWeb([
                    'toastr' => [
                        'type'        => 'error',
                        'title'       => 'الكود غير صالح',
                        'description' => 'عذرا ولكن هذا الطلب لم يعد صالحا',
                    ],
                ]);
            }
            $CHECK = PasswordResetCodeService::checkCode($this->data['user'], $Request->hash);
            if ($CHECK['status'] != 'OK') {
                return $Response->module('Admin')->code('500')->success()->withOnlyToAjaxWeb([
                    'redirect' => route('AuthController@login'),
                    'toastr' => [
                        'type'        => 'error',
                        'title'       => 'الكود غير صالح',
                        'description' => 'عذرا ولكن هذا الطلب لم يعد صالحا',
                    ],
                ]);
            }
            $this->data['user']->password = Hash::make($Request->password);
            $this->data['user']->save();
            PasswordResetCodeService::invalidateCode($Request->hash);
        } 
        catch (Exception $e) {
            throw $e;
        }
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('AuthController@login'),
            'toastr' => [
                'type'        => 'success',
                'title'       => 'لقد تمت معالجة الطلب بنجاح',
                'description' => 'لقد تمت اعادة تعيين كلمة المرور بنجاح تستطيع الدخول الى حسابك باستخدام كلمة المرور الجديدة',
            ],
        ]);
    }

    public function register(Request $Request)
    {
        return view('ui::auth.register', $this->data);
    }

    public function postRegister(Request $Request, ActionResponse $Response)
    {
        $this->data['Validator'] = Validator::make($Request->all(), [
            'full_name' => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|confirmed',
            'type'      => 'required|in:DEFAULT,TRANSPORTER',
        ]);
        $this->data['Validator']->validate();

        $data = [
            'full_name' => $Request->full_name,
            'email'     => $Request->email,
            'password'  => $Request->password,
            'type'      => $Request->type,
            'code'      => config('app.env', 'production') == 'production' ? rand(1000, 9999) : 1111,
        ];

        $userHash = Crypt::encryptString(json_encode($data));

        SendRegisterConfirmationEmail::dispatch($data, $userHash);

        return $Response->module('Admin')->code('500')->success()->withOnlyToAjaxWeb([
            'redirect' => route('AuthController@confirmRegister', ['hash' => $userHash]),
        ]);
    }

    public function confirmRegister(Request $Request, ActionResponse $Response)
    {
        $this->data['hash'] = $Request->hash;
        return view('ui::auth.registerConfirm', $this->data);
    }

    public function postConfirmRegister(Request $Request, ActionResponse $Response)
    {
        $this->data['Validator'] = Validator::make($Request->all(), [
            'hash'      => 'required',
            'code'      => 'required',
        ]);
        $this->data['Validator']->validate();

        $userData = Crypt::decryptString($Request->hash);
        $userData = json_decode($userData, true);

        if ($Request->code != $userData['code']) {
            $this->data['Validator'] = Validator::make($Request->all(), [
                'hash'      => 'required',
                'code'      => 'required',
            ]);
            $this->data['Validator']->after(function($Validator) use($Request) {
                $Validator->errors()->add('code', __('ui::strings.invalid_code'));
            });
            $this->data['Validator']->validate();
        }

        $this->data['user'] = app(config('systemcore.models.User'))->create([
            'name'       => $userData['full_name'],
            'email'      => $userData['email'],
            'password'   => Hash::make($userData['password']),
            'type'       => $userData['type'],
            'locale'     => app()->getLocale(),
            'sys_status' => 'ACTIVE',
        ]);
        Bouncer::assign($userData['type'])->to($this->data['user']);

        SendWelcomeEmail::dispatch($this->data['user']);

        try {
            Auth::loginUsingId($this->data['user']->id, true);
        } 
        catch (Exception $e) {
            throw $e;
        }

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route(config('systemcore.auth.REDIRECT_TO_ROUTE_NAME'))
        ]);
    }

    public function postLogout(Request $Request)
    {
        try {
            if (!empty(session()->get('OLD_USER_JWT_TOKEN'))) {
                $request->session()->forget(['OLD_USER_JWT_TOKEN']);
            }
            Auth::logout();
        } 
        catch (Exception $e) {
            
        }
        return redirect()->route(config('systemcore.auth.LOGIN_ROUTE'));
    }
}
