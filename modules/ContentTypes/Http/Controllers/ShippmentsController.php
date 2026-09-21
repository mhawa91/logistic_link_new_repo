<?php

namespace Modules\ContentTypes\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentTypes\Entities\ShippmentService;
use Modules\ContentTypes\Entities\Shippment;
use Modules\ContentTypes\Entities\DriverService;
use Modules\ContentTypes\Entities\Driver;
use Modules\ContentTypes\Entities\UploadedFile;
use Modules\ContentManager\Entities\Content;
use Validator;

use Modules\ContentTypes\Jobs;

class ShippmentsController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contenttypes::shippments';

    public function __construct(ShippmentService $ShippmentService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['transportarApprove']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['transportarApproveWithPriceChange']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['transportarReject']);
    }

    public function index(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['Service'] = $ShippmentService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['loadSizes'] = Content::where('base_type', 'CATEGORY')
        ->with('translations')
        ->where('type', 'load-size')
        ->where('sys_status', 'ACTIVE')
        ->get();

        $this->data['loadTypes'] = Content::where('base_type', 'CATEGORY')
        ->with('translations')
        ->where('type', 'load-type')
        ->where('sys_status', 'ACTIVE')
        ->get();

        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function resolveGoogleMapsUrl(Request $Request, ShippmentService $ShippmentService)
    {
        if (! $ShippmentService->can('create') && ! $ShippmentService->can('update')) {
            $ShippmentService->throwPermissionException('create');
        }

        $url = (string) $Request->query('url');
        if (! $this->isAllowedGoogleMapsUrl($url)) {
            return response()->json(['message' => __('contenttypes::strings.shippments.invalid_google_maps_link')], 422);
        }

        try {
            $resolvedUrl = $url;
            for ($redirect = 0; $redirect < 5 && parse_url($resolvedUrl, PHP_URL_HOST) === 'maps.app.goo.gl'; $redirect++) {
                $response = Http::timeout(6)->withOptions(['allow_redirects' => false])->head($resolvedUrl);
                $location = $response->header('Location');

                if (! $location || ! $this->isAllowedGoogleMapsUrl($location)) {
                    break;
                }
                $resolvedUrl = $location;
            }

            return response()->json(['url' => $resolvedUrl]);
        } catch (\Throwable $exception) {
            return response()->json(['message' => __('contenttypes::strings.shippments.invalid_google_maps_link')], 422);
        }
    }

    private function isAllowedGoogleMapsUrl(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https') {
            return false;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_HOST)), [
            'maps.app.goo.gl',
            'google.com',
            'www.google.com',
            'maps.google.com',
        ], true);
    }

    public function update(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['loadSizes'] = Content::where('base_type', 'CATEGORY')
        ->with('translations')
        ->where('type', 'load-size')
        ->where('sys_status', 'ACTIVE')
        ->get();

        $this->data['loadTypes'] = Content::where('base_type', 'CATEGORY')
        ->with('translations')
        ->where('type', 'load-type')
        ->where('sys_status', 'ACTIVE')
        ->get();

        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();

        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }

        if(auth()->check() && auth()->user()->type == 'TRANSPORTER') {
            return redirect()->route('ShippmentsController@read', ['model' => $this->data['model']->id]);
        }

        // if($this->data['model']->sys_status == 'MATCHING') {
        //     return redirect()->route('ShippmentsController@read', ['model' => $this->data['model']->id]);
        // }

        $this->data['model']->load([
            'loadingLocation' => function($query) {
                $query->withTrashed();
            },
            'offloadingLocations' => function($query) {
                $query->withTrashed();
            },
            'commodities' => function($query) {
                $query->with([
                    'commodity' => function($query) {
                        $query->withTrashed()->with('translations');
                    },
                ]);
            },
            'loadSize' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'loadType' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'truckType' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'companyUser' => function($query) {
                $query->withTrashed();
            },
            'companyUser.company' => function($query) {
                $query->withTrashed();
            },
            'transporterUser' => function($query) {
                $query->withTrashed();
            },
            'transporterUser.company' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_1' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_2' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_3' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_4' => function($query) {
                $query->withTrashed();
            },
            'fromCity' => function($query) {
                $query->withTrashed();
            },
            'toCity' => function($query) {
                $query->withTrashed();
            },
        ]);

        $this->data['addresses'] = $this->data['model']->offloadingLocations->map(function($location, $index) {
            return [
                'el'                           => 'geo_map_' . $index,
                'id'                           => $location->id,
                'primary_contact_company_name' => $location->primary_contact_company_name,
                'primary_contact_name'         => $location->primary_contact_name,
                'primary_contact_email'        => $location->primary_contact_email,
                'primary_contact_phone_number' => $location->primary_contact_phone_number,
                'lat'                          => $location->lat,
                'lng'                          => $location->lng,
                'latlng'                       => $location->lat . ',' . $location->lng,
                'round_trip'                   => $location->round_trip,
                'full_address'                 => $location->full_address,
                'public_notes'                 => $location->public_notes,
                'private_notes'                => $location->private_notes,
                'removed'                      => 'N',
                'map'                          => null,
            ];
        });

        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
            'rfqs' => function($query) {},
        ]);

        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }

        $this->data['matchedTransportar']    = $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first();
        $this->data['matchedRfqTransportar'] = $this->data['model']->rfqs->where('transporter_user_id', $Request->rfq_user_id ?? $this->data['model']->transporter_user_id ?? auth()->id())->first();

        if($this->data['matchedRfqTransportar']) {
            if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['matchedRfqTransportar']->transporter_user_id != auth()->id()) {
                return redirect()->route('ShippmentsController@index');
            }

            if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && ! in_array($this->data['matchedRfqTransportar']->status, ['PENDING_RESPONSE', 'PENDING_ACCEPTANCE', 'ACCEPTED'])) {
                return redirect()->route('ShippmentsController@index');
            }
        }
        else {
            if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['matchedTransportar'] && $this->data['matchedTransportar']->transporter_user_id != auth()->id()) {
                return redirect()->route('ShippmentsController@index');
            }

            if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['matchedTransportar'] && ! in_array($this->data['matchedTransportar']->status, ['PENDING_RESPONSE', 'ACCEPTED'])) {
                return redirect()->route('ShippmentsController@index');
            }
        }
        

        $this->data['model']->load([
            'loadingLocation' => function($query) {
                $query->withTrashed();
                $query->with([
                    'city' => function($query) {
                        $query->withTrashed();
                    },
                ]);
            },
            'offloadingLocations' => function($query) {
                $query->withTrashed();
                $query->with([
                    'city' => function($query) {
                        $query->withTrashed();
                    },
                ]);
            },
            'commodities' => function($query) {
                $query->with([
                    'commodity' => function($query) {
                        $query->withTrashed()->with('translations');
                    },
                ]);
            },
            'loadSize' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'loadType' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'truckType' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'companyUser' => function($query) {
                $query->withTrashed();
            },
            'companyUser.company' => function($query) {
                $query->withTrashed();
            },
            'transporterUser' => function($query) {
                $query->withTrashed();
            },
            'transporterUser.company' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_1' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_2' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_3' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_4' => function($query) {
                $query->withTrashed();
            },
            'WAYBILL' => function($query) {
                $query->withTrashed();
            },
            'driver' => function($query) {
                $query->withTrashed();
                $query->with([
                    'nationality' => function($query) {
                        $query->withTrashed();
                    },
                ]);
            },
            'fromCity' => function($query) {
                $query->withTrashed();
            },
            'toCity' => function($query) {
                $query->withTrashed();
            },
        ]);

        $this->data['helper'] = app(\Modules\SystemCore\System\Classes\Helpers::class);
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        return $ShippmentService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['Validator']  = $ShippmentService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        $this->data['shippments'] = collect([]);
        
        DB::transaction(function() use($Request, $ShippmentService) {
            if($Request->number_of_trucks && $Request->number_of_trucks > 0) {
                for($i = 0; $i < $Request->number_of_trucks; $i++) {
                    $this->data['shippments']->push(
                        $ShippmentService->create($Request->all())
                    );
                }
            }
        });

        $this->data['shippments']->each(function($shippment) {
            if($shippment->sys_status == 'MATCHING') {
                Jobs\ShippmentMatchTransportar::dispatch($shippment);
            }
        });

        $shippment = $this->data['shippments']->first();

        if($Request->is_rfq == 'Y') {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'redirect' => route('PublicTransportarsController@index', ['suitable_for_shippment' => $shippment->id]),
            ]);
        }

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('ShippmentsController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model']     = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }
        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['model']->transporter_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }
        if($Request->duplicate == 'Y') {
            return $this->postCreate($Request, $Response, $ShippmentService);
        }
        $this->data['Validator'] = $ShippmentService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        $shippment = DB::transaction(function() use($Request, $ShippmentService) {
            return $ShippmentService->update($Request->all());
        });

        if($shippment->sys_status == 'MATCHING') {
            Jobs\ShippmentMatchTransportar::dispatch($shippment);
        }

        if($Request->is_rfq == 'Y') {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'redirect' => route('PublicTransportarsController@index', ['suitable_for_shippment' => $shippment->id]),
            ]);
        }

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function postBulkSend(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['models']     = Shippment::whereIn('id', $Request->shippments ?? [])
        ->where('company_user_id', auth()->id())
        ->where('sys_status'     , 'DRAFT')
        ->where('is_rfq'         , 'NO')
        ->get();

        foreach($this->data['models'] as $shippment) {
            $shippment->sys_status = 'MATCHING';
            $shippment->save();
            Jobs\ShippmentMatchTransportar::dispatch($shippment);
        }

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
            'redirect' => route('ShippmentsController@index'),
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        return $ShippmentService->ajaxSelect($Request->search ?? null, [
            'nullable' => $Request->nullable ?? true,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();

        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }
        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['model']->transporter_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->statusUpdate($Request->status);
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->delete();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->restore();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->permaDelete();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.permaDeleted.title'),
                'description' => __('ui::strings.messages.permaDeleted.description'),
            ],
        ]);
    }

    public function postExport(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        return $ShippmentService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->export($Request->search['value'] ?? $Request->advanced_search ?? null)->toCsv();
    }

    public function transportarApproveSwitch(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        if($Request->mode == 'original') {
            return $this->transportarApprove($Request, $Response, $ShippmentService);
        }
        if($Request->mode == 'new') {
            return $this->transportarApproveWithPriceChange($Request, $Response, $ShippmentService);
        }
        if($Request->mode == 'reject') {
            return $this->transportarReject($Request, $Response, $ShippmentService);
        }
        return $Response->module('ContentTypes')->code('200')->fail()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'error',
                'title'       => __('ui::strings.messages.error.title'),
                'description' => __('ui::strings.messages.error.description'),
            ],
        ]);
    }

    public function transportarApprove(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);

        if(
            (auth()->check() && auth()->user()->type != 'TRANSPORTER') || 
            ($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->isEmpty()) || 
            ($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->where('status', 'PENDING_RESPONSE')->isEmpty())
        ) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
                'redirect' => route('ShippmentsController@index'),
            ]);
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first()->update([
                'status' => 'ACCEPTED',
            ]);
            $this->data['model']->sys_status = 'PENDING_PICKUP';
            $this->data['model']->save();
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.transportarApprove.title'),
                'description' => __('contenttypes::strings.messages.transportarApprove.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function transportarApproveWithPriceChange(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);

        if(
            (auth()->check() && auth()->user()->type != 'TRANSPORTER') || 
            ($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->isEmpty()) || 
            ($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->where('status', 'PENDING_RESPONSE')->isEmpty())
        ) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
                'redirect' => route('ShippmentsController@index'),
            ]);
        }

        $this->data['Validator'] = Validator::make($Request->all(), [
            'price'                         => 'required|numeric|min:0',
            'pre_additional_location_price' => 'required_if:type,CITY_TO_CITY_MULTIPLE_LOCATIONS|numeric|min:0',
        ])->validate();

        $this->data['matchedTransportar'] = $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first();

        if($this->data['matchedTransportar']->price == $Request->price && $this->data['matchedTransportar']->pre_additional_location_price == ($Request->pre_additional_location_price ?? 0)) {
            return $this->transportarApprove($Request, $Response, $ShippmentService);
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['model']->sys_status = 'TRANSPORTER_APPROVED_WITH_PRICE_CHANGE';
            $this->data['model']->updated_price                         = $Request->price;
            $this->data['model']->updated_pre_additional_location_price = $Request->pre_additional_location_price ?? 0;
            $this->data['model']->updated_total_price                   = $Request->price + (($this->data['model']->offloadingLocations->count() - 1) * ($Request->pre_additional_location_price ?? 0));
            $this->data['model']->save();

            $this->data['matchedTransportar']->update([
                'status'                                => 'ACCEPTED',
                'updated_price'                         => $this->data['model']->updated_price,
                'updated_pre_additional_location_price' => $this->data['model']->updated_pre_additional_location_price,
                'updated_total_price'                   => $this->data['model']->updated_total_price,
            ]);
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.transportarApproveWithPriceChange.title'),
                'description' => __('contenttypes::strings.messages.transportarApproveWithPriceChange.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function transportarReject(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);

        $this->data['matchedTransportar'] = $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first();

        if(auth()->check() && auth()->user()->type != 'TRANSPORTER') {
            return redirect()->route('ShippmentsController@index');
        }
        if($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->isEmpty()) {
            return redirect()->route('ShippmentsController@index');
        }
        if($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->where('status', 'PENDING_RESPONSE')->isEmpty()) {
            return redirect()->route('ShippmentsController@index');
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first()->update([
                'status' => 'REJECTED',
            ]);
            $this->data['model']->sys_status = 'PENDING_TRANSPORTER_APPROVAL';
            $this->data['model']->save();
        });

        $nextTransportar = $this->data['model']->matchedTransportars()
        ->where('id', '>', $this->data['matchedTransportar']->id)
        ->orderBy('id', 'ASC')
        ->orderBy('price', 'ASC')
        ->first();

        if ($nextTransportar) {
            $nextTransportar->status = 'PENDING_RESPONSE';
            $nextTransportar->save();

            $this->data['model']->transporter_user_id           = $nextTransportar->transporter_user_id;
            $this->data['model']->price                         = $nextTransportar->price;
            $this->data['model']->pre_additional_location_price = $nextTransportar->pre_additional_location_price;
            $this->data['model']->total_price                   = $nextTransportar->total_price;
            $this->data['model']->contract_id                   = $nextTransportar->contract_id;
            $this->data['model']->contract_instance_id          = $nextTransportar->contract_instance_id;
            $this->data['model']->save();

            Jobs\ShippmentSendToTransportar::dispatch($this->data['model'], $nextTransportar);
        }
        else {
            $this->data['model']->transporter_user_id                   = null;
            $this->data['model']->price                                 = 0;
            $this->data['model']->pre_additional_location_price         = 0;
            $this->data['model']->total_price                           = 0;
            $this->data['model']->updated_price                         = 0;
            $this->data['model']->updated_pre_additional_location_price = 0;
            $this->data['model']->updated_total_price                   = 0;
            $this->data['model']->contract_id                           = null;
            $this->data['model']->contract_instance_id                  = null;
            $this->data['model']->contract_id                           = null;
            $this->data['model']->contract_instance_id                  = null;
            $this->data['model']->sys_status                            = 'CANCELLED';
            $this->data['model']->save();
        }

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.transportarReject.title'),
                'description' => __('contenttypes::strings.messages.transportarReject.description'),
            ],
            'redirect' => route('ShippmentsController@index'),
        ]);
    }

    public function locationDelivery(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);

        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['model']->transporter_user_id != auth()->id()) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
            ]);
        }

        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
            ]);
        }

        $this->data['model']->load([
            'driver'  => function($query) {
                $query->withTrashed();
                $query->with([
                    'nationality' => function($query) {
                        $query->withTrashed();
                    },
                ]);
            },
            'WAYBILL' => function($query) {
                $query->withTrashed();
            },
        ]);

        if(! $this->data['model']->driver || ! $this->data['model']->WAYBILL) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_need_driver_and_waybill.title'),
                    'description' => __('contenttypes::strings.messages.shippment_need_driver_and_waybill.description'),
                ],
            ]);
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['address'] = $this->data['model']->allAddressess()->where('id', $Request->address_id)->first();
            $this->data['address']->delivered_at = now()->format('Y-m-d H:i:s');
            $this->data['address']->save();

            if($this->data['address']->address_tag == 'SHIPPMENT_LOADING_POINT') {
                $this->data['model']->sys_status = 'PENDING_DELIVERY';
                $this->data['model']->save();
            }

            $check = $this->data['model']->allAddressess()->whereNull('delivered_at')->count();
            if($check == 0 && $this->data['model']->sys_status == 'PENDING_DELIVERY') {
                $this->data['model']->sys_status = 'DELIVERED_PENDING_PAYMENT';
                $this->data['model']->save();
            }
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.shippment_delivered_to_location.title'),
                'description' => __('contenttypes::strings.messages.shippment_delivered_to_location.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function clientApprovePriceChange(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', $this->data['model']->transporter_user_id);
            },
        ]);

        if(
            (auth()->check() && auth()->user()->type != 'DEFAULT')  || 
            (auth()->id() != $this->data['model']->company_user_id) || 
            ($this->data['model']->sys_status != 'TRANSPORTER_APPROVED_WITH_PRICE_CHANGE')
        ) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
                'redirect' => route('ShippmentsController@index'),
            ]);
        }

        $this->data['matchedTransportar'] = $this->data['model']->matchedTransportars->where('transporter_user_id', $this->data['model']->transporter_user_id)->first();

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['model']->sys_status                            = 'PENDING_PICKUP';

            $this->data['model']->price                                 = $this->data['model']->updated_price;
            $this->data['model']->pre_additional_location_price         = $this->data['model']->updated_pre_additional_location_price ?? 0;
            $this->data['model']->total_price                           = $this->data['model']->updated_total_price;

            $this->data['model']->updated_price                         = null;
            $this->data['model']->updated_pre_additional_location_price = null;
            $this->data['model']->updated_total_price                   = null;
            $this->data['model']->save();

            $this->data['matchedTransportar']->update([
                'status' => 'ACCEPTED',
            ]);
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.clientApprovePriceChange.title'),
                'description' => __('contenttypes::strings.messages.clientApprovePriceChange.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function clientRejectPriceChange(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', $this->data['model']->transporter_user_id);
            },
        ]);

        if(
            (auth()->check() && auth()->user()->type != 'DEFAULT')  || 
            (auth()->id() != $this->data['model']->company_user_id) || 
            ($this->data['model']->sys_status != 'TRANSPORTER_APPROVED_WITH_PRICE_CHANGE')
        ) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
                'redirect' => route('ShippmentsController@index'),
            ]);
        }

        $this->data['matchedTransportar'] = $this->data['model']->matchedTransportars->where('transporter_user_id', $this->data['model']->transporter_user_id)->first();

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['matchedTransportar']->update([
                'status' => 'REJECTED',
            ]);
            $this->data['model']->sys_status = 'REJECTED';
            $this->data['model']->save();
        });

        $nextTransportar = $this->data['model']->matchedTransportars()
        ->where('id', '>', $this->data['matchedTransportar']->id)
        ->orderBy('id', 'ASC')
        ->orderBy('price', 'ASC')
        ->first();

        if ($nextTransportar) {
            $nextTransportar->status = 'PENDING_RESPONSE';
            $nextTransportar->save();

            $this->data['model']->sys_status                    = 'PENDING_TRANSPORTER_APPROVAL';
            $this->data['model']->transporter_user_id           = $nextTransportar->transporter_user_id;
            $this->data['model']->price                         = $nextTransportar->price;
            $this->data['model']->pre_additional_location_price = $nextTransportar->pre_additional_location_price;
            $this->data['model']->total_price                   = $nextTransportar->total_price;
            $this->data['model']->contract_id                   = $nextTransportar->contract_id;
            $this->data['model']->contract_instance_id          = $nextTransportar->contract_instance_id;
            $this->data['model']->save();

            Jobs\ShippmentSendToTransportar::dispatch($this->data['model'], $nextTransportar);
        }
        else {
            $this->data['model']->transporter_user_id                   = null;
            $this->data['model']->price                                 = 0;
            $this->data['model']->pre_additional_location_price         = 0;
            $this->data['model']->total_price                           = 0;
            $this->data['model']->updated_price                         = 0;
            $this->data['model']->updated_pre_additional_location_price = 0;
            $this->data['model']->updated_total_price                   = 0;
            $this->data['model']->contract_id                           = null;
            $this->data['model']->contract_instance_id                  = null;
            $this->data['model']->contract_id                           = null;
            $this->data['model']->contract_instance_id                  = null;
            $this->data['model']->sys_status                            = 'CANCELLED';
            $this->data['model']->save();
        }

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.clientRejectPriceChange.title'),
                'description' => __('contenttypes::strings.messages.clientRejectPriceChange.description'),
            ],
            'redirect' => route('ShippmentsController@index'),
        ]);
    }

    public function driversSelect(Request $Request, ActionResponse $Response, DriverService $DriverService)
    {
        return $DriverService->ajaxSelect($Request->search ?? null, [
            'nullable' => $Request->nullable ?? true,
        ], $Request->page);
    }

    public function driverAndWaybill(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        
        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['model']->transporter_user_id != auth()->id()) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
            ]);
        }

        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
            ]);
        }

        $this->data['model']->load([
            'driver'  => function($query) {
                $query->withTrashed();
                $query->with([
                    'nationality' => function($query) {
                        $query->withTrashed();
                    },
                ]);
            },
            'WAYBILL' => function($query) {
                $query->withTrashed();
            },
        ]);

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'driver'    => $this->data['model']->driver,
            'car_plate' => $this->data['model']->car_plate,
            'waybill'   => $this->data['model']->WAYBILL ? [
                'id'        => $this->data['model']->WAYBILL->id,
                'path'      => app('GraphManager')->src('original', 'files', $this->data['model']->WAYBILL->file_path),
            ] : null,
        ]);
    }

    public function postDriverAndWaybill(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);

        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['model']->transporter_user_id != auth()->id()) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
            ]);
        }

        $rules = [
            'driver_mode'          => 'required|string|in:new,existing',
            'car_plate'            => 'required|string|max:255',
            'WAYBILL_ATTACHMENT_1' => 'nullable|file'
        ];

        if($Request->driver_mode == 'new') {
            $rules = array_merge($rules, [
                'driver_id_number'              => 'required|string|max:255',
                'driver_name'                   => 'required|string|max:255',
                'driver_nationality_country_id' => 'required|integer|exists:countries,id',
                'DRIVER_ATTACHMENT_1'           => 'nullable|file',
            ]);
        }
        else {
            $rules = array_merge($rules, [
                'driver_id'                     => 'required|integer|exists:lgl_drivers,id,transporter_user_id,' . auth()->id(),
            ]);
        }
        $this->data['Validator'] = Validator::make($Request->all(), $rules)->validate();

        DB::transaction(function() use($Request, $ShippmentService) {
            $driver = null;

            if($Request->driver_mode == 'new') {
                $driver = new Driver;
                $driver->transporter_user_id    = auth()->id();
                $driver->name                   = $Request->driver_name;
                $driver->id_number              = $Request->driver_id_number;
                $driver->nationality_country_id = $Request->driver_nationality_country_id;
                $driver->sys_status             = 'ACTIVE';
                $driver->created_by_user_id     = auth()->id();
                $driver->updated_by_user_id     = auth()->id();
                $driver->save();
            }
            else {
                $driver = Driver::where('transporter_user_id', auth()->id())->findOrFail($Request->driver_id);
            }

            $this->data['model']->driver_id = $driver->id;
            $this->data['model']->car_plate = $Request->car_plate;
            $this->data['model']->save();

            if($Request->hasFile('DRIVER_ATTACHMENT_1')) {
                $uplaodedFile = $Request->file('DRIVER_ATTACHMENT_1');
                $file = UploadedFile::firstOrNew([
                    'file_tag'     => 'DRIVER_ATTACHMENT_1',
                    'related_type' => 'driver',
                    'related_id'   => $this->data['model']->id,
                ]);
                $file->related_id         = $this->data['model']->id;
                $file->related_type       = 'shippment';
                $file->file_tag           = 'DRIVER_ATTACHMENT_1';
                $file->file_name          = $uplaodedFile->getClientOriginalName();
                $file->file_extension     = $uplaodedFile->getClientOriginalExtension();
                $file->file_size          = $uplaodedFile->getSize();
                $file->created_by_user_id = auth()->id();
                $file->updated_by_user_id = auth()->id();
                $file->sys_status         = 'ACTIVE';
                $file->file_path          = app('GraphManager')->process('files', $uplaodedFile);
                $file->save();
            }

            if($Request->hasFile('WAYBILL')) {
                $uplaodedFile = $Request->file('WAYBILL');
                $file = UploadedFile::firstOrNew([
                    'file_tag'     => 'WAYBILL',
                    'related_type' => 'shippment',
                    'related_id'   => $this->data['model']->id,
                ]);
                $file->related_id         = $this->data['model']->id;
                $file->related_type       = 'shippment';
                $file->file_tag           = 'WAYBILL';
                $file->file_name          = $uplaodedFile->getClientOriginalName();
                $file->file_extension     = $uplaodedFile->getClientOriginalExtension();
                $file->file_size          = $uplaodedFile->getSize();
                $file->created_by_user_id = auth()->id();
                $file->updated_by_user_id = auth()->id();
                $file->sys_status         = 'ACTIVE';
                $file->file_path          = app('GraphManager')->process('files', $uplaodedFile);
                $file->save();
            }
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.postDriverAndWaybill.title'),
                'description' => __('contenttypes::strings.messages.postDriverAndWaybill.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function pricingItem(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model']              = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['matchedTransportar'] = $this->data['model']->matchedTransportars()
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where('transporter_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query->where('transporter_user_id', $this->data['model']->transporter_user_id);
        })
        ->with([
            'contract' => function($query) {
                $query->withTrashed();
            },
            'contractInstance' => function($query) {
                $query->withTrashed();
            },
            'contractInstancePricing' => function($query) {},
        ])
        ->first();

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb(! $this->data['matchedTransportar'] ? [
            'pricing' => null,
            'toastr'  => [
                'type'        => 'error',
                'title'       => __('contenttypes::strings.messages.pricing_not_available.title'),
                'description' => __('contenttypes::strings.messages.pricing_not_available.description'),
            ],
        ] : [
            'pricing' => $this->data['matchedTransportar'],
        ]);
    }

    public function transportarRfqApprove(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'rfqs' => function($query) {},
        ]);
        
        $this->data['matchedRfqTransportar'] = $this->data['model']->rfqs->where('transporter_user_id', auth()->id())->first();

        if(
            (auth()->check() && auth()->user()->type != 'TRANSPORTER') || 
            (! $this->data['matchedRfqTransportar']) || 
            ($this->data['matchedRfqTransportar']->status != 'PENDING_RESPONSE')
        ) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
                'redirect' => route('ShippmentsController@index'),
            ]);
        }

        $this->data['Validator'] = Validator::make($Request->all(), [
            'price'                         => 'required|numeric|min:0',
            'pre_additional_location_price' => 'required_if:type,CITY_TO_CITY_MULTIPLE_LOCATIONS|numeric|min:0',
        ])->validate();

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['matchedRfqTransportar']->update([
                'status'                                => 'PENDING_ACCEPTANCE',
                'price'                                 => $Request->price,
                'pre_additional_location_price'         => $Request->pre_additional_location_price,
                'total_price'                           => $Request->price + (($this->data['model']->offloadingLocations->count() - 1) * ($Request->pre_additional_location_price ?? 0)),
                'responded_at'                          => now()->format('Y-m-d H:i:s'),
            ]);
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.transportarApproveWithPriceChange.title'),
                'description' => __('contenttypes::strings.messages.transportarApproveWithPriceChange.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function transportarRfqReject(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'rfqs' => function($query) {},
        ]);

        $this->data['matchedRfqTransportar'] = $this->data['model']->rfqs->where('transporter_user_id', auth()->id())->first();

        if(
            (auth()->check() && auth()->user()->type != 'TRANSPORTER') || 
            (! $this->data['matchedRfqTransportar']) || 
            ($this->data['matchedRfqTransportar']->status != 'PENDING_RESPONSE')
        ) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
                'redirect' => route('ShippmentsController@index'),
            ]);
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['matchedRfqTransportar']->update([
                'status'       => 'REJECTED',
                'responded_at' => now()->format('Y-m-d H:i:s'),
            ]);
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.transportarReject.title'),
                'description' => __('contenttypes::strings.messages.transportarReject.description'),
            ],
            'redirect' => route('ShippmentsController@index'),
        ]);
    }

    public function clientApproveRfq(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'rfqs' => function($query) {},
        ]);

        if(
            (auth()->check() && auth()->user()->type != 'DEFAULT')  || 
            (auth()->id() != $this->data['model']->company_user_id) || 
            ($this->data['model']->sys_status != 'RFQ_PENDING_TRANSPORTER_OFFER')
        ) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
                'redirect' => route('ShippmentsController@index'),
            ]);
        }

        $this->data['matchedRfqTransportar'] = $this->data['model']->rfqs->where('id', $Request->rfq_id)->first();

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['model']->sys_status                            = 'PENDING_PICKUP';
            $this->data['model']->price                                 = $this->data['matchedRfqTransportar']->price;
            $this->data['model']->pre_additional_location_price         = $this->data['matchedRfqTransportar']->pre_additional_location_price ?? 0;
            $this->data['model']->total_price                           = $this->data['matchedRfqTransportar']->total_price;
            $this->data['model']->transporter_user_id                   = $this->data['matchedRfqTransportar']->transporter_user_id;
            $this->data['model']->save();

            $this->data['model']->rfqs()->where('id', '!=', $this->data['matchedRfqTransportar']->id)->update([
                'status' => 'REJECTED'
            ]);
            $this->data['matchedRfqTransportar']->update([
                'status' => 'ACCEPTED',
            ]);

            $this->data['model']->matchedTransportars()->delete();
            $this->data['model']->matchedTransportars()->create([
                'transporter_user_id'            => $this->data['matchedRfqTransportar']->transporter_user_id,
                'price'                          => $this->data['model']->price,
                'pre_additional_location_price'  => $this->data['model']->pre_additional_location_price ?? 0,
                'total_price'                    => $this->data['model']->total_price,
                'contract_id'                    => null,
                'contract_instance_id'           => null,
                'status'                         => 'ACCEPTED',
            ]);

            // TO DO: Send notification to Transportar
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.clientApproveRfq.title'),
                'description' => __('contenttypes::strings.messages.clientApproveRfq.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function clientRejectRfq(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'rfqs' => function($query) {},
        ]);

        if(
            (auth()->check() && auth()->user()->type != 'DEFAULT')  || 
            (auth()->id() != $this->data['model']->company_user_id)
        ) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
                'redirect' => route('ShippmentsController@index'),
            ]);
        }

        $this->data['matchedRfqTransportar'] = $this->data['model']->rfqs->where('id', $Request->rfq_id)->first();

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['matchedRfqTransportar']->update([
                'status' => 'REJECTED',
            ]);
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.clientRejectRfq.title'),
                'description' => __('contenttypes::strings.messages.clientRejectRfq.description'),
            ],
            'redirect' => route('RfqController@index', ['shippment_id' => $this->data['model']->id]),
        ]);
    }
}
