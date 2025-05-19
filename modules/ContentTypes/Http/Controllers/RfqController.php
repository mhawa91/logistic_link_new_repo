<?php

namespace Modules\ContentTypes\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentTypes\Entities\ShippmentService;
use Modules\ContentTypes\Entities\Company;
use Modules\ContentTypes\Entities\Shippment;
use Modules\ContentTypes\Entities\UploadedFile;
use Modules\ContentManager\Entities\Content;
use Validator;

use Modules\ContentTypes\Jobs;

class RfqController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contenttypes::rfqs';

    public function __construct(ShippmentService $ShippmentService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
    }

    public function index(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['Service'] = $ShippmentService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        return $ShippmentService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->rfqDataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }
}