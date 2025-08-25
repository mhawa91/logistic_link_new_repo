<?php

namespace Modules\Website\Http\Controllers;

use DataTables;
use Auth;
use Hash;
use Exception;
use Validator;
use Carbon\Carbon;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;

use Modules\Admin\Entities\UserService;
use Modules\SystemCore\Entities\Contracts\SystemConfigInterface;
use Modules\ContentManager\Entities\Contracts\SectionInterface;
use Modules\ContentManager\Entities\Contracts\ContentInterface;
use Modules\ContentManager\Entities\Contracts\CategoryInterface;

use Modules\SystemCore\Entities\Country;
use Modules\SystemCore\Entities\City;
use Laravel\Socialite\Facades\Socialite;

use Modules\SystemCore\Entities\PasswordResetCodeService;
use Modules\Admin\Emails\ResetPasswordRequest;
use Modules\Admin\Emails\WelcomeMessage;
use Mail;

class WebsiteAuthController extends SystemCoreHttpController
{
    protected $ContentInterface  = null;
    protected $CategoryInterface = null;

    public function __construct (
        SectionInterface      $SectionInterface,
        SystemConfigInterface $SystemConfigInterface,
        ContentInterface      $ContentInterface,
        CategoryInterface     $CategoryInterface
    )
    {
        $this->data['SystemConfig'] = $SystemConfigInterface;
        $this->data['Sections']     = $SectionInterface;
        $this->ContentInterface     = $ContentInterface;
        $this->CategoryInterface    = $CategoryInterface;

        $this->data['SystemConfig']->load();
        $this->data['Sections']->load();
    }

    public function loginByFacebook(Request $Request)
    {
        session(['sign_type' => $Request->type ?? null, 'new_account' => $Request->new_account ?? 'N']);
        return Socialite::driver('facebook')->redirect();
    }

    public function loginByGoogle(Request $Request)
    {
        session(['sign_type' => $Request->type ?? null, 'new_account' => $Request->new_account ?? 'N']);
        return Socialite::driver('google')->redirect();
    }

    public function postLoginByFacebook(Request $Request, UserService $UserService, ActionResponse $Response)
    {
        try {
            $TYPE               = session()->pull('sign_type', null);
            $IS_NEW             = session()->pull('new_account', null);
            $user               = Socialite::driver('facebook')->stateless()->user();
            $this->data['user'] = app(config('systemcore.models.User'))->where('facebook_auth_id', $user->getId())->first();
            $USER_BY_EMAIL      = app(config('systemcore.models.User'))->where('email', $user->getEmail())->first();
            if (! $this->data['user']) {
                if (! empty($user->getEmail()) && ! empty($USER_BY_EMAIL)) {
                    $USER_BY_EMAIL->update([
                        'facebook_auth_id'        => $user->getId(),
                        'email_verification_code' => null,
                    ]);
                    $this->data['user'] = $USER_BY_EMAIL;
                }
                else if($IS_NEW != 'Y') {
                    return $Response->module('Admin')->code('500')->success()->withOnlyToAjaxWeb([
                        'redirect' => route('WebsiteController@home'),
                        'toastr' => [
                            'type'        => 'error',
                            'title'       => 'عذرا ولكن حصل خطأ',
                            'description' => 'على مايبدو ان هذا الحساب غير مسجل بالنظام. يرجى انشاء حساب جديد قبل تسجيل الدخول',
                        ],
                    ]);
                }
                else {
                    $this->data['user']  = app(config('systemcore.models.User'))->create([
                        'facebook_auth_id'        => $user->getId(),
                        'type'                    => in_array($TYPE, ['DEFAULT', 'COMPANY']) ? $TYPE : 'DEFAULT',
                        'name'                    => $user->getName(),
                        'email'                   => $EMAIL ?? null,
                        'password'                => Hash::make((string)\Illuminate\Support\Str::uuid().'@'.$user->getId()),
                        'sys_status'              => 'ACTIVE',
                        'is_registered'           => 'N',
                        'email_verification_code' => null,
                    ]);
                }
            }
            Auth::loginUsingId($this->data['user']->id, false);
        } 
        catch (Exception $e) {
            throw $e;
        }
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('WebsiteController@singleProfileC', ['username' => $this->data['user']->username ?? $this->data['user']->id]),
        ]);
    }

    public function postLoginByGoogle(Request $Request, UserService $UserService, ActionResponse $Response)
    {
        try {
            $TYPE               = session()->pull('sign_type', null);
            $IS_NEW             = session()->pull('new_account', null);
            $user               = Socialite::driver('google')->stateless()->user();
            $this->data['user'] = app(config('systemcore.models.User'))->where('google_auth_id', $user->getId())->first();
            $USER_BY_EMAIL      = app(config('systemcore.models.User'))->where('email', $user->getEmail())->first();
            if (! $this->data['user']) {
                if (! empty($user->getEmail()) && ! empty($USER_BY_EMAIL)) {
                    $USER_BY_EMAIL->update([
                        'google_auth_id'          => $user->getId(),
                        'email_verification_code' => null,
                    ]);
                    $this->data['user'] = $USER_BY_EMAIL;
                }
                else if($IS_NEW != 'Y') {
                    return $Response->module('Admin')->code('500')->success()->withOnlyToAjaxWeb([
                        'redirect' => route('WebsiteController@home'),
                        'toastr' => [
                            'type'        => 'error',
                            'title'       => 'عذرا ولكن حصل خطأ',
                            'description' => 'على مايبدو ان هذا الحساب غير مسجل بالنظام. يرجى انشاء حساب جديد قبل تسجيل الدخول',
                        ],
                    ]);
                }
                else {
                    $this->data['user']  = app(config('systemcore.models.User'))->create([
                        'google_auth_id'          => $user->getId(),
                        'type'                    => in_array($TYPE, ['DEFAULT', 'COMPANY']) ? $TYPE : 'DEFAULT',
                        'name'                    => $user->getName(),
                        'email'                   => $user->getEmail() ?? null,
                        'password'                => Hash::make((string)\Illuminate\Support\Str::uuid().'@'.$user->getId()),
                        'sys_status'              => 'ACTIVE',
                        'is_registered'           => 'N',
                        'email_verification_code' => null,
                    ]);
                }
            }
            Auth::loginUsingId($this->data['user']->id, false);
        } 
        catch (Exception $e) {
            throw $e;
        }
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('WebsiteController@singleProfileC', ['username' => $this->data['user']->username ?? $this->data['user']->id]),
        ]);
    }
}