<?php

namespace Modules\ContactUs\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContactUs\Entities\ContactUs;

class ContactUsDashboardController extends SystemCoreHttpController
{
    public function __construct()
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
    }

    public function getCount(Request $Request)
    {
        return [
            'pending_count'  => ContactUs::where('sys_status', 'PENDING')->count(),
            'reviewed_count' => ContactUs::where('sys_status', 'REVIEWED')->count(),
        ];
    }
}