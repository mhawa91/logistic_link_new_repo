<?php

namespace Modules\ContentManager\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentManager\Entities\Content;

class ContentDashboardController extends SystemCoreHttpController
{
    public function __construct()
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
    }

    public function getCount(Request $Request)
    {
        $this->data['DATA'] = [];
        $OPTIONS = [
            'types' => collect($Request->types),
        ];
        $OPTIONS['types']->map(function($item) {
            $this->data['DATA'][ $item['as'] ] = Content::whereIn('base_type', $item['base_type'])->whereIn('type', $item['type'])->count();
        });
        return $this->data['DATA'];
    }
}