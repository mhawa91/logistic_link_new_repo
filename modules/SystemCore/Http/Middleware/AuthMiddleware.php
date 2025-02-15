<?php

namespace Modules\SystemCore\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Middleware\Authenticate;
use Modules\SystemCore\Exceptions\PermissionException;
use Modules\SystemCore\Http\Responses\ActionResponse;

class AuthMiddleware extends Authenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route( config('systemcore.auth.LOGIN_ROUTE') );
        }
        throw new PermissionException('you_must_be_logged_id', 403);
    }
}
