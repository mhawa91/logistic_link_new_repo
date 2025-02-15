<?php

namespace Modules\SystemCore\System;

use Illuminate\Contracts\Debug\ExceptionHandler;

class Core
{
    /**
     * Register application exceptions.
     *
     * @return void
     */
    public function registerExceptionHandler($Handler)
    {
        resolve(ExceptionHandler::class)->renderable($Handler);
    }

    /**
     * system Classes\Helpers class.
     *
     * @return void
     */
    public function helpers($Handler)
    {
        return app(Classes\Helpers::class);
    }

     /**
     * system quick permissions check.
     *
     * @return void
     */
    public function can($Permissions)
    {
        if (! auth()->check()) {
            return false;
        }
        if (auth()->user()->isAn('ROOT')) {
            return true;
        }
        if (! is_array($Permissions)) {
            if (auth()->user()->can($Permissions)) {
                return true;
            }
        }
        else {
            foreach ($Permissions as $key => $Permission) {
                if (auth()->user()->can($Permissions)) {
                    return true;
                }
            }
        }
        return false;
    }
}