<?php

namespace Modules\SystemCore\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProcessingStackMiddleware
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
        $this->before($request);

        $response = $next($request);

        $this->after($request, $response);

        return $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function before($request)
    {
        // Will Do Any Thing Before Request Is Ready By The Controller
    }

    /**
     * Handle an response for incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function after($request, $response)
    {
        // Will Do Any Thing After Request Is Ready By The Controller
    }
}
