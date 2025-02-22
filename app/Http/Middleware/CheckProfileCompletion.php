<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\ContentTypes\Entities\Company;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedRoutes = $request->is(config('systemcore.adminPanelRoutePath') . '/users/profile') 
        || $request->is(config('systemcore.adminPanelRoutePath') . '/countries/select') 
        || $request->is(config('systemcore.adminPanelRoutePath') . '/cities/select')
        || $request->is(config('systemcore.adminPanelRoutePath') . '/geo/*')
        || $request->is(config('systemcore.adminPanelRoutePath') . '/users/company')
        || $request->is(config('systemcore.adminPanelRoutePath') . '/logout');

        if ($allowedRoutes) {
            return $next($request);
        }
        if (! auth()->check()) {
            return $next($request);
        }
        if (auth()->user()->type == 'DEFAULT' || auth()->user()->type == 'TRANSPORTER') {
            $profile = Company::where('user_id', auth()->id())->first();
            if (! $profile) {
                return redirect()->route('UsersController@profile');
            }
        }

        return $next($request);
    }
}
