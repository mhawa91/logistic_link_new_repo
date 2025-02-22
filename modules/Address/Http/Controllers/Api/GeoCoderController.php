<?php

namespace Modules\Address\Http\Controllers\Api;

use Modules\Address\Http\Controllers\GeoCoderController as BaseGeoCoderController;

class GeoCoderController extends BaseGeoCoderController
{
    public function __construct()
    {
        // $this->middleware(config('systemcore.auth.API_AUTH_MIDDLEWARE'));
    }
}
