<?php

namespace Modules\SystemCore\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\SystemCore\Exceptions\PermissionException;
use Modules\SystemCore\Http\Responses\ActionResponse;

class PermissionsMiddleware
{
    protected $data = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if ( auth()->check() ) {
            $this->data['User'] = $request->user();
            if ( $this->data['User']->isAn('ROOT') ) {
                return $next($request);
            }
            foreach ($permissions as $i => $PermissionCode) {
                if ( $this->data['User']->can( $PermissionCode ) ) {
                    return $next($request);
                }
            }
        }
        if (in_array('GUEST', $permissions)) {
            return $next($request);
        }
        $PermissionException = new PermissionException('permissions_error:' . implode(',', $permissions), 403);
        $PermissionException->setNeededPermissions($permissions);
        throw $PermissionException;
    }
}
