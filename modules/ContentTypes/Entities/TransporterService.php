<?php

namespace Modules\ContentTypes\Entities;

use Validator;
use DB;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;
use Modules\SystemCore\System\Services\ExportService;

use App\Models\User;
use Bouncer;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Crypt;
use Modules\ContentTypes\Jobs\SendTransportarInvitationEmail;
use Modules\ContentTypes\Jobs\SendContractNotificationEmail;

class TransporterService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = TransporterContract::class;
        $this->guard();
        if ($this->can('update')) {
            $this->registerDataTableAction('update', function($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => route('TransportersController@update', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('delete')) {
            $this->registerDataTableAction('delete', function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('TransportersController@postDelete', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('restore')) {
            $this->registerDataTableAction('restore', function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('TransportersController@postRestore', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('permaDelete')) {
            $this->registerDataTableAction('permaDelete', function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('TransportersController@postPermaDelete', ['model' => $Row->id]),
                ];
            });
        }
        $this->registerAjaxSelectCallback(function($Row) {
            return [
                'id'   => ! $Row ? '0'            : $Row->id,
                'text' => ! $Row ? '-- Select --' : $Row->code . ' - [' . $Row->symbol . ']',
                'data' => ! $Row ? []             : $Row->toArray(),
            ];
        });
    }

    public function validator($type = 'create', array $data = [])
    {
        $rules = [
            // USER
            'name'                                  => 'required|string|max:255',
            'email'                                 => 'required|string|email|max:255',
            'phone_number'                          => 'required|string|max:255',
            // CONTRACT
            'payment_term_id'                       => 'required|numeric|exists:contents,id',
            'insurance_value_limit'                 => 'required|numeric',
            'trucks_type_id'                        => 'required|array',
            'trucks_type_id.*'                      => 'required|numeric|exists:contents,id',
            'commodity_id'                          => 'required|array',
            'commodity_id.*'                        => 'required|numeric|exists:contents,id',
            'TRANSPORTER_TAX_CERTIFICATE'           => 'nullable|file',
            'TRANSPORTER_REGISTAION_LICENCE'        => 'nullable|file',
            'TRANSPORTER_INSURANCE_CERTIFICATE'     => 'nullable|file',
            'TRANSPORTER_TRANSPORTATION_LICENCE'    => 'nullable|file',
            'TRANSPORTER_CONTRACT'                  => 'nullable|file',
            'sys_status'                            => 'required|in:ACTIVE,DISABLED',
            'items'                                 => 'required|array',
            'items.*.truck_type_id'                 => 'required|numeric|exists:contents,id',
            'items.*.loading_type_id'               => 'required|in:' . collect(config('contenttypes.loadingTypes'))->pluck('id')->implode(','),
            'items.*.from_city_id'                  => 'required|numeric|exists:cities,id',
            'items.*.to_city_id'                    => 'required|numeric|exists:cities,id',
            'items.*.price'                         => 'required|numeric',
            'items.*.pre_additional_location_price' => 'required_if:items.*.loading_type_id,CITY_TO_CITY_MULTIPLE_LOCATIONS',
            'TERMS_AND_CONDITIONS'                  => 'nullable|string',
            'INSURANCE_DETAILS'                     => 'nullable|string',
            'PENALITIES'                            => 'nullable|string',
        ];
        if ($type == 'create') {
            $rules = $rules + [
                'contract_daterange_from'               => 'required|date_format:m/d/Y',
                'contract_daterange_to'                 => 'required|date_format:m/d/Y',    
            ];
            return Validator::make($data, $rules);
        }
        if ($type == 'createByUserId') {
            unset($rules['name']);
            unset($rules['email']);
            unset($rules['phone_number']);
            $rules = $rules + [
                'user_id'                 => 'required|numeric|exists:users,id,type,TRANSPORTER',
                'contract_daterange_from' => 'required|date_format:m/d/Y',
                'contract_daterange_to'   => 'required|date_format:m/d/Y',
            ];
            $rules = $rules + [
                
            ];
            return Validator::make($data, $rules);
        }
        if ($type == 'update') {
            return Validator::make($data, $rules);
        }
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        $query->where(function($Model) use($keyword) {
            $Model->whereHas('translations', function($Rel) use($keyword) {
                $Rel->where('title', 'like', "%{$keyword}%");
            });
        });
    }

    protected function advancedFilter($query, array $filter = [])
    {
        if (! empty($filter['title'])) {
            $query->whereHas('translations', function($Rel) use($filter) {
                $Rel->where('title', 'like', "%{$filter['title']}%");
            });
        }
    }

    public function dataTable($Filter = null)
    {
        $this->model()
        ->select('*')
        ->withSelectMeta()
        ->withCurrentInstance()
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->withTransporter()->where('company_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->withClient()->where('transporter_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type != 'DEFAULT' && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query->withClient()->withTransporter();
        });

        $table = parent::dataTable($Filter);

        $table->addColumn('client_avatar', function($row) {
            return $row->client && $row->client->company ? app('GraphManager')->src('400x400', 'company', $row->client->company?->company_logo) : app('GraphManager')->defaultSrc('400x400', 'company');
        });

        $table->addColumn('transporter_avatar', function($row) {
            return $row->transporter && $row->transporter->company ? app('GraphManager')->src('400x400', 'company', $row->transporter->company?->company_logo) : app('GraphManager')->defaultSrc('400x400', 'company');
        });

        return $table;
    }

    public function export($filter = null)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }

        $query    = $this->model();
        $this->filter($query, $filter);

        $exporter = app(ExportService::class)->of($query)->name('Data.csv');

        $exporter->addColumn('id', function($row) {
            return $row->id;
        });

        return $exporter;
    }

    public function create(array $data = [])
    {
        $user      = $this->createTransporterUser(
            $data['name'], 
            $data['email'], 
            $data['phone_number']
        );

        $ModelData = [
            'company_user_id'     => auth()->id(),
            'transporter_user_id' => $user->id,
            'is_rfq'              => $data['is_rfq'] ?? 'NO',
            'order_start_date'    => Carbon::parse($data['contract_daterange_from'])->toDateTimeString(),
            'order_end_date'      => Carbon::parse($data['contract_daterange_to'])->toDateTimeString(),
            'created_by_user_id'  => auth()->id(),
            'updated_by_user_id'  => null,
            'sys_status'          => $data['sys_status']  ?? 'ACTIVE',
        ];

        $contract = parent::create( $ModelData )->data();

        $this->syncFile($contract, 'TRANSPORTER_TAX_CERTIFICATE'        , $data['TRANSPORTER_TAX_CERTIFICATE']        ?? null);
        $this->syncFile($contract, 'TRANSPORTER_REGISTAION_LICENCE'     , $data['TRANSPORTER_REGISTAION_LICENCE']     ?? null);
        $this->syncFile($contract, 'TRANSPORTER_INSURANCE_CERTIFICATE'  , $data['TRANSPORTER_INSURANCE_CERTIFICATE']  ?? null);
        $this->syncFile($contract, 'TRANSPORTER_TRANSPORTATION_LICENCE' , $data['TRANSPORTER_TRANSPORTATION_LICENCE'] ?? null);
        $this->syncFile($contract, 'TRANSPORTER_CONTRACT'               , $data['TRANSPORTER_CONTRACT']               ?? null);

        $instanceData = $data + [
            'payment_term_id'       => $data['payment_term_id']       ?? null,
            'insurance_value_limit' => $data['insurance_value_limit'] ?? null,
        ];
        $instance = $this->createInstance($contract, $instanceData);

        $currentCompany = auth()->user()->company ? auth()->user()->company->company_name : null;
        $userHash       = Crypt::encryptString(json_encode([
            'id'    => $user->id,
            'email' => $user->email,
        ]));
        SendContractNotificationEmail::dispatch($user, $currentCompany, null, $userHash);

        return $contract;
    }

    public function createByUserId(array $data = [])
    {
        $user      = User::where('id', $data['user_id'])->where('type', 'TRANSPORTER')->firstOrFail();
        $ModelData = [
            'company_user_id'     => auth()->id(),
            'transporter_user_id' => $user->id,
            'is_rfq'              => $data['is_rfq'] ?? 'NO',
            'order_start_date'    => Carbon::parse($data['contract_daterange_from'])->toDateTimeString(),
            'order_end_date'      => Carbon::parse($data['contract_daterange_to'])->toDateTimeString(),
            'created_by_user_id'  => auth()->id(),
            'updated_by_user_id'  => null,
            'sys_status'          => $data['sys_status']  ?? 'ACTIVE',
        ];

        $contract = parent::create( $ModelData )->data();

        $this->syncFile($contract, 'TRANSPORTER_TAX_CERTIFICATE'        , $data['TRANSPORTER_TAX_CERTIFICATE']        ?? null);
        $this->syncFile($contract, 'TRANSPORTER_REGISTAION_LICENCE'     , $data['TRANSPORTER_REGISTAION_LICENCE']     ?? null);
        $this->syncFile($contract, 'TRANSPORTER_INSURANCE_CERTIFICATE'  , $data['TRANSPORTER_INSURANCE_CERTIFICATE']  ?? null);
        $this->syncFile($contract, 'TRANSPORTER_TRANSPORTATION_LICENCE' , $data['TRANSPORTER_TRANSPORTATION_LICENCE'] ?? null);
        $this->syncFile($contract, 'TRANSPORTER_CONTRACT'               , $data['TRANSPORTER_CONTRACT']               ?? null);

        $instanceData = $data + [
            'payment_term_id'       => $data['payment_term_id']       ?? null,
            'insurance_value_limit' => $data['insurance_value_limit'] ?? null,
        ];
        $instance = $this->createInstance($contract, $instanceData);

        $currentCompany = auth()->user()->company ? auth()->user()->company->company_name : null;
        $userHash       = Crypt::encryptString(json_encode([
            'id'    => $user->id,
            'email' => $user->email,
        ]));
        SendContractNotificationEmail::dispatch($user, $currentCompany, null, $userHash);

        return $contract;
    }

    public function update(array $data = [])
    {
        $ModelData = [
            'company_user_id'     => $this->data->company_user_id,
            'transporter_user_id' => $this->data->transporter_user_id,
            'is_rfq'              => $this->data->is_rfq,
            'order_start_date'    => $this->data->order_start_date,
            'order_end_date'      => $this->data->order_end_date,
            'created_by_user_id'  => $this->data->created_by_user_id,
            'updated_by_user_id'  => auth()->id(),
            'sys_status'          => $data['sys_status']  ?? 'ACTIVE',
        ];

        $contract = parent::update( $ModelData )->data();

        $this->syncFile($contract, 'TRANSPORTER_TAX_CERTIFICATE'        , $data['TRANSPORTER_TAX_CERTIFICATE']        ?? null);
        $this->syncFile($contract, 'TRANSPORTER_REGISTAION_LICENCE'     , $data['TRANSPORTER_REGISTAION_LICENCE']     ?? null);
        $this->syncFile($contract, 'TRANSPORTER_INSURANCE_CERTIFICATE'  , $data['TRANSPORTER_INSURANCE_CERTIFICATE']  ?? null);
        $this->syncFile($contract, 'TRANSPORTER_TRANSPORTATION_LICENCE' , $data['TRANSPORTER_TRANSPORTATION_LICENCE'] ?? null);
        $this->syncFile($contract, 'TRANSPORTER_CONTRACT'               , $data['TRANSPORTER_CONTRACT']               ?? null);

        $instanceData = $data + [
            'payment_term_id'       => $data['payment_term_id']       ?? null,
            'insurance_value_limit' => $data['insurance_value_limit'] ?? null,
        ];
        $instance = $this->createInstance($contract, $instanceData);

        $user = User::where('id', $contract->transporter_user_id)->first();
        $currentCompany = auth()->user()->company ? auth()->user()->company->company_name : null;
        $userHash       = Crypt::encryptString(json_encode([
            'id'    => $user->id,
            'email' => $user->email,
        ]));
        SendContractNotificationEmail::dispatch($user, $currentCompany, null, $userHash);

        return $contract;
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        // $this->model()->whereNull('id');
        // $this->model()->with([
        //    'translations' => fn($rel) => $rel,
        // ]);
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function previousCompaniesSelect($search = null, int $page = 1, int $limit = 20)
    {
        if (! $this->can('create')) {
            $this->throwPermissionException('create');
        }

        $page  = max($page, 1);
        $limit = min(max($limit, 1), 50);

        $query = TransporterContract::query()
        ->join('users AS previous_transporters', 'previous_transporters.id', '=', 'lgl_transporters_contracts.transporter_user_id')
        ->whereNull('previous_transporters.deleted_at')
        ->whereNotNull('previous_transporters.name')
        ->whereNotNull('previous_transporters.email')
        ->whereNotNull('previous_transporters.phone_number')
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->where('lgl_transporters_contracts.company_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where('lgl_transporters_contracts.transporter_user_id', auth()->id());
        })
        ->when(! empty($search), function($query) use($search) {
            $query->where(function($query) use($search) {
                $query->where('previous_transporters.name', 'like', "%{$search}%")
                ->orWhere('previous_transporters.email', 'like', "%{$search}%")
                ->orWhere('previous_transporters.phone_number', 'like', "%{$search}%");
            });
        })
        ->select([
            DB::raw('MIN(previous_transporters.id) AS id'),
            'previous_transporters.name',
            'previous_transporters.email',
            'previous_transporters.phone_number',
        ])
        ->groupBy([
            'previous_transporters.name',
            'previous_transporters.email',
            'previous_transporters.phone_number',
        ])
        ->orderBy('previous_transporters.name');

        $companies = $query->paginate($limit, ['*'], 'page', $page);

        return [
            'results' => collect($companies->items())->map(function($company) {
                $phoneNumber = (string) $company->phone_number;

                return [
                    'id'            => $company->id,
                    'text'          => $company->name,
                    'name'          => $company->name,
                    'email'         => $company->email,
                    'phone_number'  => preg_replace('/^\+966/', '', $phoneNumber),
                    'phone_display' => $phoneNumber,
                ];
            })->values(),
            'pagination' => [
                'more' => $companies->hasMorePages(),
            ],
        ];
    }

    public function install() : bool
    {
        $Group = AbilityGroup::firstOrNew([
            'code' => $this->getPermissionGroupName()
        ]);
        $Group->icon               = null;
        $Group->{'default:ar'}      = 'Y';
        $Group->{'title:ar'}       = 'TransporterContract';
        $Group->{'description:ar'} = null;
        $Group->save();

        $read = Ability::firstOrNew([
            'name' => $this->getPermissionName('read')
        ]);
        $read->group_id           = $Group->id;
        $read->{'default:ar'}      = 'Y';
        $read->{'title:ar'}       = 'read';
        $read->{'description:ar'} = null;
        $read->save();

        $create = Ability::firstOrNew([
            'name' => $this->getPermissionName('create')
        ]);
        $create->group_id           = $Group->id;
        $create->{'default:ar'}      = 'Y';
        $create->{'title:ar'}       = 'create';
        $create->{'description:ar'} = null;
        $create->save();

        $update = Ability::firstOrNew([
            'name' => $this->getPermissionName('update')
        ]);
        $update->group_id           = $Group->id;
        $update->{'default:ar'}      = 'Y';
        $update->{'title:ar'}       = 'update';
        $update->{'description:ar'} = null;
        $update->save();

        $delete = Ability::firstOrNew([
            'name' => $this->getPermissionName('delete')
        ]);
        $delete->group_id           = $Group->id;
        $delete->{'default:ar'}      = 'Y';
        $delete->{'title:ar'}       = 'delete';
        $delete->{'description:ar'} = null;
        $delete->save();

        $restore = Ability::firstOrNew([
            'name' => $this->getPermissionName('restore')
        ]);
        $restore->group_id           = $Group->id;
        $restore->{'default:ar'}      = 'Y';
        $restore->{'title:ar'}       = 'restore';
        $restore->{'description:ar'} = null;
        $restore->save();

        $permaDelete = Ability::firstOrNew([
            'name' => $this->getPermissionName('permaDelete')
        ]);
        $permaDelete->group_id           = $Group->id;
        $permaDelete->{'default:ar'}      = 'Y';
        $permaDelete->{'title:ar'}       = 'permaDelete';
        $permaDelete->{'description:ar'} = null;
        $permaDelete->save();

        $statusUpdate = Ability::firstOrNew([
            'name' => $this->getPermissionName('statusUpdate')
        ]);
        $statusUpdate->group_id           = $Group->id;
        $statusUpdate->{'default:ar'}      = 'Y';
        $statusUpdate->{'title:ar'}       = 'statusUpdate';
        $statusUpdate->{'description:ar'} = null;
        $statusUpdate->save();

        $ADMIN_Role = Role::where('name', 'SYSTEM_ADMIN')->first();
        if ($ADMIN_Role) {
            $ADMIN_Role->abilities()->syncWithoutDetaching([
                $read->id,
                $create->id,
                $update->id,
                $delete->id,
                // $permaDelete->id,
                $statusUpdate->id,
            ]);
        }

        $CLIENT_Role = Role::where('name', 'DEFAULT')->first();
        if ($CLIENT_Role) {
            $CLIENT_Role->abilities()->syncWithoutDetaching([
                $read->id,
                $create->id,
                $update->id,
                // $delete->id,
                // $permaDelete->id,
                $statusUpdate->id,
            ]);
        }

        $TRANSPORTER_Role = Role::where('name', 'TRANSPORTER')->first();
        if ($TRANSPORTER_Role) {
            $TRANSPORTER_Role->abilities()->syncWithoutDetaching([
                $read->id,
                // $create->id,
                $update->id,
                // $delete->id,
                // $permaDelete->id,
                $statusUpdate->id,
            ]);
        }

        return true;
    }

    public function createTransporterUser($name, $email, $phone_number)
    {
        $user     = User::where('email', $email)->first();
        if (! $user) {
            $user = User::create([
                'name'         => $name,
                'email'        => $email,
                'phone_number' => '+966' . $phone_number,
                'password'     => bcrypt(Str::random(10)),
                'type'         => 'TRANSPORTER',
                'sys_status'   => 'ACTIVE',
            ]);
            Bouncer::assign('TRANSPORTER')->to($user);
            $userHash = Crypt::encryptString(json_encode([
                'id'    => $user->id,
                'email' => $user->email,
            ]));
            SendTransportarInvitationEmail::dispatch($user, $userHash);
        }
        return $user;
    }

    public function createInstance(TransporterContract $contract, array $data = [])
    {
        TransporterContractInstance::where('contract_id', $contract->id)->update([
            'is_current' => 'NO',
        ]);

        $instance                        = new TransporterContractInstance;
        $instance->contract_id           = $contract->id;
        $instance->company_user_id       = $contract->company_user_id;
        $instance->transporter_user_id   = $contract->transporter_user_id;
        $instance->payment_term_id       = $data['payment_term_id'];
        $instance->insurance_value_limit = $data['insurance_value_limit'];
        $instance->order_start_date      = $contract->order_start_date;
        $instance->order_end_date        = $contract->order_end_date;
        $instance->is_current            = 'YES';
        $instance->created_by_user_id    = auth()->id();
        $instance->updated_by_user_id    = null;
        $instance->sys_status            = 'ACTIVE';
        $instance->save();

        $TERMS_AND_CONDITIONS = new TransporterContractInstanceMetadata;
        $TERMS_AND_CONDITIONS->contract_id           = $contract->id;
        $TERMS_AND_CONDITIONS->contract_instance_id  = $instance->id;
        $TERMS_AND_CONDITIONS->company_user_id       = $contract->company_user_id;
        $TERMS_AND_CONDITIONS->transporter_user_id   = $contract->transporter_user_id;
        $TERMS_AND_CONDITIONS->type                  = 'TERMS_AND_CONDITIONS';
        $TERMS_AND_CONDITIONS->description           = $data['TERMS_AND_CONDITIONS'] ?? null;
        $TERMS_AND_CONDITIONS->save();

        $INSURANCE_DETAILS = new TransporterContractInstanceMetadata;
        $INSURANCE_DETAILS->contract_id           = $contract->id;
        $INSURANCE_DETAILS->contract_instance_id  = $instance->id;
        $INSURANCE_DETAILS->company_user_id       = $contract->company_user_id;
        $INSURANCE_DETAILS->transporter_user_id   = $contract->transporter_user_id;
        $INSURANCE_DETAILS->type                  = 'INSURANCE_DETAILS';
        $INSURANCE_DETAILS->description           = $data['INSURANCE_DETAILS'] ?? null;
        $INSURANCE_DETAILS->save();

        $PENALITIES = new TransporterContractInstanceMetadata;
        $PENALITIES->contract_id           = $contract->id;
        $PENALITIES->contract_instance_id  = $instance->id;
        $PENALITIES->company_user_id       = $contract->company_user_id;
        $PENALITIES->transporter_user_id   = $contract->transporter_user_id;
        $PENALITIES->type                  = 'PENALITIES';
        $PENALITIES->description           = $data['PENALITIES'] ?? null;
        $PENALITIES->save();

        $commodities = collect($data['commodity_id'] ?? [])->map(function($item) use($instance) {
            return [
                'contract_id'          => $instance->contract_id,
                'contract_instance_id' => $instance->id,
                'company_user_id'      => $instance->company_user_id,
                'transporter_user_id'  => $instance->transporter_user_id,
                'commodity_id'         => $item,
            ];
        });

        if ($commodities->isNotEmpty()) {
            TransporterContractInstanceCommodity::insert($commodities->toArray());
        }

        $items = collect($data['items'] ?? [])->map(function($item) use($instance) {
            return [
                'contract_id'                   => $instance->contract_id,
                'contract_instance_id'          => $instance->id,
                'company_user_id'               => $instance->company_user_id,
                'transporter_user_id'           => $instance->transporter_user_id,
                'truck_type_id'                 => $item['truck_type_id'],
                'type'                          => $item['loading_type_id'],
                'from_country_id'               => 191,
                'from_city_id'                  => $item['from_city_id'],
                'to_country_id'                 => 191,
                'to_city_id'                    => $item['to_city_id'],
                'price'                         => $item['price'],
                'pre_additional_location_price' => $item['pre_additional_location_price'] ?? 0,
            ];
        });

        if ($items->isNotEmpty()) {
            TransporterContractInstancePrice::insert($items->toArray());
        }

        return $instance;
    }

    public function syncFile(TransporterContract $contract, string $type, $uplaodedFile)
    {
        if ($uplaodedFile && !is_string($uplaodedFile)) {
            $file = UploadedFile::firstOrNew([
                'file_tag'     => $type,
                'related_type' => 'contract',
                'related_id'   => $contract->id,
            ]);
            $file->related_id         = $contract->id;
            $file->related_type       = 'contract';
            $file->file_tag           = $type;
            $file->file_name          = $uplaodedFile->getClientOriginalName();
            $file->file_extension     = $uplaodedFile->getClientOriginalExtension();
            $file->file_size          = $uplaodedFile->getSize();
            $file->created_by_user_id = auth()->id();
            $file->updated_by_user_id = auth()->id();
            $file->sys_status         = 'ACTIVE';
            $file->file_path          = app('GraphManager')->process('files', $uplaodedFile);
            $file->save();
        }
    }

    public function findOrFail($id)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }
        $this->data = $this->model()
        ->select('*')
        ->withSelectMeta()
        ->withCurrentInstance()
        ->withClient()
        ->withTransporter()
        ->withInstanceDetails()
        ->withFiles()
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->where('company_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where('transporter_user_id', auth()->id());
        })
        ->findOrFail($id);
        return $this;
    }
}
