<?php

namespace Modules\SystemCore\Http\Middleware;

use Closure;
use Modules\SystemCore\Services\SystemService;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Illuminate\Http\Request;

use JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $alloweGuest = false;

    public function handle(Request $request, Closure $next, ...$options)
    {
        $Response = app(ActionResponse::class);
        try {
            // if (! isset($options['disableRegisteredScope'])) {
            //     User::enableRegisteredScope();
            // }
            // if (! isset($options['disableVerifiedScope'])) {
            //     User::enableVerifiedScope();
            // }
            if (in_array('GUEST', $options)) {
                $this->alloweGuest = true;
            }

            $User = JWTAuth::parseToken()->authenticate();
            if( ! $User ) {
                throw new Exception('User Not Found');
            }
            app()->setLocale($User->locale);
        } 
        catch (TokenInvalidException $e) {
            if ($this->alloweGuest) {
                return $next($request);
            }
            return $Response->module('SystemCore')->code('100100')->fail();
        } 
        catch (TokenExpiredException $e) {
            try {
                $NewToken = JWTAuth::refresh(JWTAuth::getToken());
                $User     = JWTAuth::setToken($NewToken)->toUser();
                $request->headers->set('Authorization', 'Bearer '. $NewToken);
                return $Response->module('SystemCore')->code('100200')->fail()->with([
                    'token' => $NewToken
                ]);
            } 
            catch (Exception $e) {
                if ($this->alloweGuest) {
                    return $next($request);
                }
                return $Response->module('SystemCore')->code('100300')->fail();
            }
        } 
        catch (TokenBlacklistedException $e) {
            if ($this->alloweGuest) {
                return $next($request);
            }
            return $Response->module('SystemCore')->code('100400')->fail();
        } 
        catch (JWTException $e) {
            if ($this->alloweGuest) {
                return $next($request);
            }
            return $Response->module('SystemCore')->code('100500')->fail();
        } 
        catch (Exception $e) {
            if ($this->alloweGuest) {
                return $next($request);
            }
            return $Response->module('SystemCore')->code('100600')->fail();
        }
        return $next($request);
    }
}
