<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictAccessToControlPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post')) {
            return $next($request);
        }
        if ($request->is(config('systemcore.adminPanelRoutePath') . '/register') || $request->is(config('systemcore.adminPanelRoutePath') . '/login') || $request->is(config('systemcore.adminPanelRoutePath') . '/reset') || $request->is(config('systemcore.adminPanelRoutePath') . '/reset/*') || $request->is(config('systemcore.adminPanelRoutePath') . '/logout')) {
            return $next($request);
        }
        if ($request->is(config('systemcore.adminPanelRoutePath')) || $request->is(config('systemcore.adminPanelRoutePath') . '/*')) {
            $Groups = config('admin.controll-panel-allowed-access-groups');            
            if (in_array('*', $Groups)) {
                return $next($request);
            } 
            if (auth()->check() && in_array(auth()->user()->type, $Groups)) {
                return $next($request);
            }
            return abort(401);
        }
        return $next($request);
    }
}
