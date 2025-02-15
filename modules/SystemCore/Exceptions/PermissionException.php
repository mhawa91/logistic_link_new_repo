<?php

namespace Modules\SystemCore\Exceptions;

use Exception;
use Modules\SystemCore\Http\Responses\ActionResponse;

class PermissionException extends Exception
{
    protected $neededPermissions = [];

    public function setNeededPermissions(array $permissions)
    {
        $this->neededPermissions = $permissions;

        return $this;
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $Response = app(ActionResponse::class)->module('SystemCore')->code('403')->with([
            'permissions_needed' => $this->neededPermissions
        ]);
        $Response->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'error',
                'title'       => $Response->getTitle(),
                'description' => $Response->getMessage(),
            ],
        ])
        ->fail();

        return $Response;
    }
}
