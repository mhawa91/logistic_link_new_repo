<?php

namespace Modules\ContentTypes\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentTypes\Entities\InvoiceService;
use Modules\ContentTypes\Entities\ClientSelectService;
use Modules\ContentTypes\Services\DashboardStatisticsService;

class InvoicesController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contenttypes::invoices';

    public function __construct(InvoiceService $InvoiceService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($InvoiceService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($InvoiceService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($InvoiceService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($InvoiceService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($InvoiceService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($InvoiceService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($InvoiceService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['Service'] = $InvoiceService;
        $this->data['dashboardInvoiceStatuses'] = DashboardStatisticsService::invoiceStatusesForFilter(
            $Request->query('dashboard_filter')
        );
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['model'] = $InvoiceService->withTrashed()->findOrFail($Request->model)->data();
        if (auth()->check() && auth()->user()->isAn('TRANSPORTER') && $this->data['model']->transportar_user_id != auth()->id()) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'redirect' => route('InvoicesController@index'),
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.not_allowed.title'),
                    'description' => __('contenttypes::strings.messages.not_allowed.description'),
                ],
            ]);
        }
        if (auth()->check() && auth()->user()->isAn('DEFAULT') && $this->data['model']->company_user_id != auth()->id()) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'redirect' => route('InvoicesController@index'),
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.not_allowed.title'),
                    'description' => __('contenttypes::strings.messages.not_allowed.description'),
                ],
            ]);
        }

        $this->data['model']->load([
            'ATTACHMENT',
            'INVOICE_FILE',
            'items'           => fn($query) => $query->with([
                'shippment' => fn($query)   => $query->select([
                    'lgl_shippments.*',
                ])
                ->withSelectMeta()
                ->WithClientSelect()
                ->WithTransportarSelect()
                ->WithCountryAndCitySelect()
                ->WithLocationsCountSelect()
                ->WithWaybillSelect(),
            ]),
            'transportarUser' => fn($query) => $query->withTrashed(),
            'clientUser'      => fn($query) => $query->withTrashed(),
        ]);

        $this->data['items'] = $this->data['model']->items->map(function($item) {
            return [
                'shippment'   => $item->shippment->toArray(),
                'sub_cost'    => $item->sub_cost,
                'discount'    => $item->discount,
                'cost'        => $item->cost,
            ];
        });

        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['model'] = $InvoiceService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        return $InvoiceService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['Validator'] = $InvoiceService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $InvoiceService) {
            $InvoiceService->create($Request->all());
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('InvoicesController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['model']     = $InvoiceService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $InvoiceService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $InvoiceService) {
            $InvoiceService->update($Request->all());
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('InvoicesController@index'),
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, ClientSelectService $ClientSelectService)
    {
        return $ClientSelectService->ajaxSelect($Request->search ?? null, [
            'nullable'                => $Request->nullable                ?? true,
            'has_uninvoiced_invoices' => $Request->has_uninvoiced_invoices ?? null,
            'transportar_id'          => $Request->transportar_id         ?? null,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['model'] = $InvoiceService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $InvoiceService) {
            $InvoiceService->statusUpdate($Request->status);
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['model'] = $InvoiceService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $InvoiceService) {
            $InvoiceService->delete();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['model'] = $InvoiceService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $InvoiceService) {
            $InvoiceService->restore();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        $this->data['model'] = $InvoiceService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $InvoiceService) {
            $InvoiceService->permaDelete();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.permaDeleted.title'),
                'description' => __('ui::strings.messages.permaDeleted.description'),
            ],
        ]);
    }

    public function postExport(Request $Request, ActionResponse $Response, InvoiceService $InvoiceService)
    {
        return $InvoiceService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->export($Request->search['value'] ?? $Request->advanced_search ?? null)->toCsv();
    }

    public function groupedIndex(Request $Request, ActionResponse $Response, ClientSelectService $ClientSelectService, InvoiceService $InvoiceService)
    {
        $this->data['Service'] = $InvoiceService;
        return view($this->viewsFolderPath . '.grouped_index', $this->data);
    }

    public function postGroupedIndexIndex(Request $Request, ActionResponse $Response, ClientSelectService $ClientSelectService)
    {
        return $ClientSelectService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }
}
