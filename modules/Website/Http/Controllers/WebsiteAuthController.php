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
use Bouncer;

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
        $userType = in_array($Request->type, ['DEFAULT', 'TRANSPORTER']) ? $Request->type : 'DEFAULT';
        session(['sign_type' => $userType]);
        return Socialite::driver('facebook')->redirect();
    }

    public function loginByGoogle(Request $Request)
    {
        $userType = in_array($Request->type, ['DEFAULT', 'TRANSPORTER']) ? $Request->type : 'DEFAULT';
        session(['sign_type' => $userType]);
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
                    // Validate the selected type matches allowed types (same as regular registration)
                    $userType = in_array($TYPE, ['DEFAULT', 'TRANSPORTER']) ? $TYPE : 'DEFAULT';
                    
                    $this->data['user']  = app(config('systemcore.models.User'))->create([
                        'name'                    => $user->getName(),
                        'email'                   => $user->getEmail() ?? null,
                        'password'                => Hash::make((string)\Illuminate\Support\Str::uuid().'@'.$user->getId()),
                        'type'                    => $userType,
                        'locale'                  => app()->getLocale(),
                        'sys_status'              => 'ACTIVE',
                        'facebook_auth_id'        => $user->getId(),
                        'is_registered'           => 'N',
                        'email_verification_code' => null,
                    ]);
                    
                    // Assign role based on user type (same as regular registration)
                    Bouncer::assign($userType)->to($this->data['user']);
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
                else {
                    // Validate the selected type matches allowed types (same as regular registration)
                    $userType = in_array($TYPE, ['DEFAULT', 'TRANSPORTER']) ? $TYPE : 'DEFAULT';
                    
                    $this->data['user']  = app(config('systemcore.models.User'))->create([
                        'name'                    => $user->getName(),
                        'email'                   => $user->getEmail() ?? null,
                        'password'                => Hash::make((string)\Illuminate\Support\Str::uuid().'@'.$user->getId()),
                        'type'                    => $userType,
                        'locale'                  => app()->getLocale(),
                        'sys_status'              => 'ACTIVE',
                        'google_auth_id'          => $user->getId(),
                        'is_registered'           => 'N',
                        'email_verification_code' => null,
                    ]);
                    
                    // Assign role based on user type (same as regular registration)
                    Bouncer::assign($userType)->to($this->data['user']);
                }
            }
            
            Auth::loginUsingId($this->data['user']->id, false);
        } 
        catch (Exception $e) {
            throw $e;
        }
        
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route(config('systemcore.auth.REDIRECT_TO_ROUTE_NAME'))
        ]);
    }
}