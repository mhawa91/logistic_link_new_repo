<?php

namespace Modules\ContentTypes\Entities;

use Validator;
use DB;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;
use Modules\SystemCore\System\Services\ExportService;
use Carbon\Carbon;

class ShippmentService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = Shippment::class;
        $this->guard();
        if ($this->can('read')) {
            $this->registerDataTableAction('read', function($Row) {
                return [
                    'label'  => __('ui::strings.read'),
                    'action' => route('ShippmentsController@read', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('update')) {
            $this->registerDataTableAction('update', function($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => route('ShippmentsController@update', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('delete')) {
            $this->registerDataTableAction('delete', function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('ShippmentsController@postDelete', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('restore')) {
            $this->registerDataTableAction('restore', function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('ShippmentsController@postRestore', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('permaDelete')) {
            $this->registerDataTableAction('permaDelete', function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('ShippmentsController@postPermaDelete', ['model' => $Row->id]),
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
            'total_weight'          => 'required|numeric',
            'commodity_id'          => 'required|array',
            'commodity_id.*'        => 'required|numeric',
            'declared_order_value'  => 'required|numeric',
            'load_size_id'          => 'required|numeric',
            'load_type_id'          => 'required|numeric',
            'number_of_pallets'     => 'required|numeric|min:1',
            'number_of_trucks'      => 'required|numeric|min:1',
            'truck_type_id'         => 'required|numeric',
            'temperature'           => 'required|numeric',
            'equipment_length'      => 'required|numeric|min:0.1',
            'equipment_width'       => 'required|numeric|min:0.1',
            'delivery_required_at'  => 'required|date_format:Y-m-d',
            'public_notes'          => 'nullable',
            'private_notes'         => 'nullable',
            'ADDITIONAL_DATA'       => 'nullable|file',

            'from_city_id'          => 'required|numeric|exists:cities,id',
            'to_city_id'            => 'required|numeric|exists:cities,id',
            'loading_type'          => 'required|required|in:' . collect(config('contenttypes.loadingTypes'))->pluck('id')->implode(','),

            'loading_address'                                      => 'required|array',
            'loading_address.primary_contact_company_name'         => 'required',
            'loading_address.primary_contact_name'                 => 'required',
            'loading_address.primary_contact_email'                => 'required|email',
            'loading_address.primary_contact_phone_number'         => 'required',
            'loading_address.lat_lng'                              => 'required',
            'loading_address.lat'                                  => 'required',
            'loading_address.lng'                                  => 'required',
            'loading_address.full_address'                         => 'required',
            'loading_address.public_notes'                         => 'nullable',
            'loading_address.private_notes'                        => 'nullable',

            'offloading_address'                                   => 'required|array',
            'offloading_address.*.primary_contact_company_name'    => 'required',
            'offloading_address.*.primary_contact_name'            => 'required',
            'offloading_address.*.primary_contact_email'           => 'required|email',
            'offloading_address.*.primary_contact_phone_number'    => 'required',
            'offloading_address.*.lat_lng'                         => 'required',
            'offloading_address.*.lat'                             => 'required',
            'offloading_address.*.lng'                             => 'required',
            'offloading_address.*.full_address'                    => 'required',
            'offloading_address.*.public_notes'                    => 'nullable',
            'offloading_address.*.private_notes'                   => 'nullable',
            'offloading_address.*.round_trip'                      => 'required|in:YES,NO',
        ];
        if ($type == 'create') {
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
        $this->model()->select([
            'lgl_shippments.*',
        ])
        ->withSelectMeta()
        ->WithClientSelect()
        ->WithTransportarSelect()
        ->WithCountryAndCitySelect()
        ->WithLocationsCountSelect()
        ->WithWaybillSelect()
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->where('company_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->whereHas('matchedTransportars', function($rel) {
                $rel->where('transporter_user_id', auth()->id());
                $rel->whereIn('status', ['PENDING_RESPONSE', 'ACCEPTED']);
            });
        })
        ->when(auth()->check() && auth()->user()->type != 'DEFAULT' && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query;
        });

        return parent::dataTable($Filter);
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
        $ModelData = [
            'company_user_id'      => auth()->id(),
            'from_country_id'      => 191,
            'from_city_id'         => $data['from_city_id'],
            'to_country_id'        => 191,
            'to_city_id'           => $data['to_city_id'],
            'round_trip'           => 'NO',
            'is_rfq'               => 'NO',
            'total_weight'         => $data['total_weight'],
            'decalred_value'       => $data['declared_order_value'],
            'load_size_id'         => $data['load_size_id'],
            'load_type_id'         => $data['load_type_id'],
            'number_of_pallets'    => $data['number_of_pallets'],
            'number_of_trucks'     => $data['number_of_trucks'],
            'truck_type_id'        => $data['truck_type_id'],
            'temperature'          => $data['temperature'],
            'equipment_length'     => $data['equipment_length'],
            'equipment_width'      => $data['equipment_width'],
            'delivery_required_at' => Carbon::parse($data['delivery_required_at'])->format('Y-m-d') . ' 00:00:00',
            'public_notes'         => $data['public_notes']  ?? null,
            'private_notes'        => $data['private_notes'] ?? null,
            'type'                 => $data['loading_type'],
            'created_by_user_id'   => auth()->id(),
            'updated_by_user_id'   => null,
            'sys_status'           => $data['is_draft'] == 'Y' ? 'DRAFT' : 'MATCHING',
        ];

        $model = parent::create( $ModelData )->data();

        $this->syncFile($model, 'ADDITIONAL_DATA', $data['ADDITIONAL_DATA'] ?? null);

        $commodities = collect($data['commodity_id'] ?? [])->map(function($id) use($model) {
            return [
                'shippment_id' => $model->id,
                'commodity_id' => $id,
            ];
        });
        if ($commodities->isNotEmpty()) {
            $model->commodities()->insert($commodities->toArray());
        }

        $loadingAddress = $this->syncAddress($model, 'SHIPPMENT_LOADING_POINT', [
            'id'                              => empty($data['loading_address']['id']) ? null : $data['loading_address']['id'],
            'related_id'                      => $model->id,
            'related_type'                    => 'SHIPPMENT',
            'address_tag'                     => 'SHIPPMENT_LOADING_POINT',
            'country_id'                      => 191,
            'city_id'                         => $data['from_city_id'],
            'full_address'                    => $data['loading_address']['full_address']  ?? null,
            'public_notes'                    => $data['loading_address']['public_notes']  ?? null,
            'private_notes'                   => $data['loading_address']['private_notes'] ?? null,
            'primary_contact_company_name'    => $data['loading_address']['primary_contact_company_name'],
            'primary_contact_name'            => $data['loading_address']['primary_contact_name'],
            'primary_contact_email'           => $data['loading_address']['primary_contact_email'],
            'primary_contact_phone_number_ext'=> '+966',
            'primary_contact_phone_number'    => $data['loading_address']['primary_contact_phone_number'],
            'lat_lng'                         => $data['loading_address']['lat_lng'],
            'lat'                             => $data['loading_address']['lat'],
            'lng'                             => $data['loading_address']['lng'],
        ]);

        $offloadingAddresses = collect($data['offloading_address'] ?? [])->where('removed', 'N')->map(function($item) use($model, $data) {
            return $this->syncAddress($model, 'SHIPPMENT_OFFLOADING_POINT', [
                'id'                              => empty($item['id']) ? null : $item['id'],
                'related_id'                      => $model->id,
                'related_type'                    => 'SHIPPMENT',
                'address_tag'                     => 'SHIPPMENT_OFFLOADING_POINT',
                'country_id'                      => 191,
                'city_id'                         => $data['to_city_id'],
                'full_address'                    => $item['full_address']  ?? null,
                'public_notes'                    => $item['public_notes']  ?? null,
                'private_notes'                   => $item['private_notes'] ?? null,
                'primary_contact_company_name'    => $item['primary_contact_company_name'],
                'primary_contact_name'            => $item['primary_contact_name'],
                'primary_contact_email'           => $item['primary_contact_email'],
                'primary_contact_phone_number_ext'=> '+966',
                'primary_contact_phone_number'    => $item['primary_contact_phone_number'],
                'lat_lng'                         => $item['lat_lng'],
                'lat'                             => $item['lat'],
                'lng'                             => $item['lng'],
                'round_trip'                      => $item['round_trip'],
            ]);
        });

        $model->offloadingLocations()->whereNotIn('id', $offloadingAddresses->pluck('id')->toArray())->forceDelete();

        $hasRoundTrip = $offloadingAddresses->where('round_trip', 'YES')->count();
        if ($hasRoundTrip > 0) {
            $model->round_trip = 'YES';
            $model->save();
        }

        return $model;
    }

    public function update(array $data = [])
    {
        $ModelData = [
            'company_user_id'      => $this->data->company_user_id,
            'from_country_id'      => 191,
            'from_city_id'         => $data['from_city_id'],
            'to_country_id'        => 191,
            'to_city_id'           => $data['to_city_id'],
            'round_trip'           => 'NO',
            'is_rfq'               => $this->data->is_rfq,
            'total_weight'         => $data['total_weight'],
            'decalred_value'       => $data['declared_order_value'],
            'load_size_id'         => $data['load_size_id'],
            'load_type_id'         => $data['load_type_id'],
            'number_of_pallets'    => $data['number_of_pallets'],
            'number_of_trucks'     => $data['number_of_trucks'],
            'truck_type_id'        => $data['truck_type_id'],
            'temperature'          => $data['temperature'],
            'equipment_length'     => $data['equipment_length'],
            'equipment_width'      => $data['equipment_width'],
            'delivery_required_at' => Carbon::parse($data['delivery_required_at'])->format('Y-m-d') . ' 00:00:00',
            'public_notes'         => $data['public_notes']  ?? null,
            'private_notes'        => $data['private_notes'] ?? null,
            'type'                 => $data['loading_type'],
            'created_by_user_id'   => $this->data->created_by_user_id,
            'updated_by_user_id'   => auth()->id(),
            'sys_status'           => $data['is_draft'] == 'Y' ? 'DRAFT' : 'MATCHING',
        ];

        $model = parent::update( $ModelData )->data();

        $this->syncFile($model, 'ADDITIONAL_DATA', $data['ADDITIONAL_DATA'] ?? null);

        $commodities = collect($data['commodity_id'] ?? [])->map(function($id) use($model) {
            return [
                'shippment_id' => $model->id,
                'commodity_id' => $id,
            ];
        });
        $model->commodities()->delete();
        if ($commodities->isNotEmpty()) {
            $model->commodities()->insert($commodities->toArray());
        }

        $loadingAddress = $this->syncAddress($model, 'SHIPPMENT_LOADING_POINT', [
            'id'                              => empty($data['loading_address']['id']) ? null : $data['loading_address']['id'],
            'related_id'                      => $model->id,
            'related_type'                    => 'SHIPPMENT',
            'address_tag'                     => 'SHIPPMENT_LOADING_POINT',
            'country_id'                      => 191,
            'city_id'                         => $data['from_city_id'],
            'full_address'                    => $data['loading_address']['full_address']  ?? null,
            'public_notes'                    => $data['loading_address']['public_notes']  ?? null,
            'private_notes'                   => $data['loading_address']['private_notes'] ?? null,
            'primary_contact_company_name'    => $data['loading_address']['primary_contact_company_name'],
            'primary_contact_name'            => $data['loading_address']['primary_contact_name'],
            'primary_contact_email'           => $data['loading_address']['primary_contact_email'],
            'primary_contact_phone_number_ext'=> '+966',
            'primary_contact_phone_number'    => $data['loading_address']['primary_contact_phone_number'],
            'lat_lng'                         => $data['loading_address']['lat_lng'],
            'lat'                             => $data['loading_address']['lat'],
            'lng'                             => $data['loading_address']['lng'],
        ]);

        $offloadingAddresses = collect($data['offloading_address'] ?? [])->where('removed', 'N')->map(function($item) use($model, $data) {
            return $this->syncAddress($model, 'SHIPPMENT_OFFLOADING_POINT', [
                'id'                              => empty($item['id']) ? null : $item['id'],
                'related_id'                      => $model->id,
                'related_type'                    => 'SHIPPMENT',
                'address_tag'                     => 'SHIPPMENT_OFFLOADING_POINT',
                'country_id'                      => 191,
                'city_id'                         => $data['to_city_id'],
                'full_address'                    => $item['full_address']  ?? null,
                'public_notes'                    => $item['public_notes']  ?? null,
                'private_notes'                   => $item['private_notes'] ?? null,
                'primary_contact_company_name'    => $item['primary_contact_company_name'],
                'primary_contact_name'            => $item['primary_contact_name'],
                'primary_contact_email'           => $item['primary_contact_email'],
                'primary_contact_phone_number_ext'=> '+966',
                'primary_contact_phone_number'    => $item['primary_contact_phone_number'],
                'lat_lng'                         => $item['lat_lng'],
                'lat'                             => $item['lat'],
                'lng'                             => $item['lng'],
                'round_trip'                      => $item['round_trip'],
            ]);
        });

        $model->offloadingLocations()->whereNotIn('id', $offloadingAddresses->pluck('id')->toArray())->forceDelete();

        $hasRoundTrip = $offloadingAddresses->where('round_trip', 'YES')->count();
        if ($hasRoundTrip > 0) {
            $model->round_trip = 'YES';
            $model->save();
        }

        return $model;
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        // $this->model()->whereNull('id');
        // $this->model()->with([
        //    'translations' => fn($rel) => $rel,
        // ]);
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function install() : bool
    {
        $Group = AbilityGroup::firstOrNew([
            'code' => $this->getPermissionGroupName()
        ]);
        $Group->icon               = null;
        $Group->{'default:ar'}      = 'Y';
        $Group->{'title:ar'}       = 'Shippment';
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
                $delete->id,
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

    public function syncFile(Shippment $shippment, string $type, $uplaodedFile)
    {
        if ($uplaodedFile && !is_string($uplaodedFile)) {
            $file = UploadedFile::firstOrNew([
                'file_tag'     => $type,
                'related_type' => 'shippment',
                'related_id'   => $shippment->id,
            ]);
            $file->related_id         = $shippment->id;
            $file->related_type       = 'shippment';
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

    public function syncAddress(Shippment $shippment, string $type, array $data)
    {
        if ($data && is_array($data)) {
            $address = ! empty($data['id']) ? Address::find($data['id']) : new Address();
            if ($address->exists) {
                $address->updated_by_user_id = auth()->id();
            } else {
                $address->created_by_user_id = auth()->id();
            }
            $address->related_id                      = $shippment->id;
            $address->related_type                    = 'SHIPPMENT';
            $address->address_tag                     = $type;
            $address->country_id                      = 191;
            $address->city_id                         = $data['city_id'];
            $address->street                          = null;
            $address->postal_code                     = null;
            $address->line_1                          = null;
            $address->line_2                          = null;
            $address->full_address                    = $data['full_address']  ?? null;
            $address->public_notes                    = $data['public_notes']  ?? null;
            $address->private_notes                   = $data['private_notes'] ?? null;
            $address->primary_contact_company_name    = $data['primary_contact_company_name'];
            $address->primary_contact_source_name     = null;
            $address->primary_contact_name            = $data['primary_contact_name'];
            $address->primary_contact_email           = $data['primary_contact_email'];
            $address->primary_contact_phone_number_ext= $data['primary_contact_phone_number_ext'];
            $address->primary_contact_phone_number    = $data['primary_contact_phone_number'];
            $address->lat                             = $data['lat'];
            $address->lng                             = $data['lng'];
            $address->round_trip                      = $data['round_trip'] ?? null;
            $address->sys_status                      = 'ACTIVE';
            $address->save();
            return $address;
        }
        return null;
    }
}
