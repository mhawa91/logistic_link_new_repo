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
            if(auth()->check() && auth()->user()->type == 'DEFAULT') {
                $this->registerDataTableAction('rfq_read', function($Row) {
                    return $Row->is_rfq == 'YES' ? [
                        'label'  => __('ui::strings.read'),
                        'action' => route('ShippmentsController@read', ['model' => $Row->id, 'rfq_user_id' => $Row->rfq_transporter_user_id]),
                    ] : null;
                });
                $this->registerDataTableAction('rfq_list', function($Row) {
                    return $Row->is_rfq == 'YES' ? [
                        'label'  => __('contenttypes::strings.shippments_rfqs.menu'),
                        'action' => route('RfqController@index', ['shippment_id' => $Row->id]),
                    ] : null;
                });
            }
        }
        if ($this->can('update') && auth()->check() && auth()->user()->type == 'DEFAULT') {
            $this->registerDataTableAction('duplicate', function($Row) {
                return [
                    'label'  => __('ui::strings.duplicate'),
                    'action' => route('ShippmentsController@update', ['model' => $Row->id, 'duplicate' => 'Y']),
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
            'total_weight'          => 'required|numeric|min:0.1|max:48',
            'commodity_id'          => 'required|array',
            'commodity_id.*'        => 'required|numeric',
            'declared_order_value'  => 'required|numeric',
            'load_size_id'          => 'required|numeric',
            'load_type_id'          => 'required|numeric',
            'number_of_pallets'     => 'required|numeric|min:1',
            // 'number_of_trucks'      => 'required|numeric|min:1',
            'truck_type_id'         => 'required|numeric',
            'temperature'           => 'required|numeric',
            'equipment_length'      => 'required|numeric|min:0',
            'equipment_width'       => 'required|numeric|min:0',
            'delivery_required_at'  => 'required|date_format:Y-m-d',
            'public_notes'          => 'nullable',
            'private_notes'         => 'nullable',
            'ATTACHMENT_1'          => 'nullable|file',
            'ATTACHMENT_2'          => 'nullable|file',
            'ATTACHMENT_3'          => 'nullable|file',
            'ATTACHMENT_4'          => 'nullable|file',

            'from_city_id'          => 'required|numeric|exists:cities,id',
            'to_city_id'            => 'required|numeric|exists:cities,id',
            'loading_type'          => 'required|in:' . collect(config('contenttypes.loadingTypes'))->pluck('id')->implode(','),

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

            // required_if
            'offloading_address'                                   => 'required|array',
            'offloading_address.*.primary_contact_company_name'    => 'required_if:offloading_address.*.removed,N',
            'offloading_address.*.primary_contact_name'            => 'required_if:offloading_address.*.removed,N',
            'offloading_address.*.primary_contact_email'           => 'required_if:offloading_address.*.removed,N',
            'offloading_address.*.primary_contact_phone_number'    => 'required_if:offloading_address.*.removed,N',
            'offloading_address.*.lat_lng'                         => 'required_if:offloading_address.*.removed,N',
            'offloading_address.*.lat'                             => 'required_if:offloading_address.*.removed,N',
            'offloading_address.*.lng'                             => 'required_if:offloading_address.*.removed,N',
            'offloading_address.*.full_address'                    => 'required_if:offloading_address.*.removed,N',
            'offloading_address.*.public_notes'                    => 'nullable',
            'offloading_address.*.private_notes'                   => 'nullable',
            'offloading_address.*.round_trip'                      => 'required_if:offloading_address.*.removed,N|in:YES,NO',
        ];
        $attributes = [
            'total_weight'                                       => __('contenttypes::inputs.weight.label'),
            'commodity_id'                                       => __('contenttypes::inputs.commodity_id.label'),
            'commodity_id.*'                                     => __('contenttypes::inputs.commodity_id.label'),
            'declared_order_value'                               => __('contenttypes::inputs.declared_order_value.label'),
            'load_size_id'                                       => __('contenttypes::inputs.load_size_id.label'),
            'load_type_id'                                       => __('contenttypes::inputs.load_type_id.label'),
            'number_of_pallets'                                  => __('contenttypes::inputs.number_of_pallets.label'),
            'number_of_trucks'                                   => __('contenttypes::inputs.number_of_trucks.label'),
            'truck_type_id'                                      => __('contenttypes::inputs.truck_type_id.label'),
            'temperature'                                        => __('contenttypes::inputs.temperature.label'),
            'equipment_length'                                   => __('contenttypes::inputs.equipment_length.label'),
            'equipment_width'                                    => __('contenttypes::inputs.equipment_width.label'),
            'delivery_required_at'                               => __('contenttypes::inputs.delivery_required_at.label'),
            'public_notes'                                       => __('contenttypes::inputs.public_notes.label'),
            'private_notes'                                      => __('contenttypes::inputs.private_notes.label'),
            'ATTACHMENT_1'                                       => __('contenttypes::inputs.attachment_1.label'),
            'ATTACHMENT_2'                                       => __('contenttypes::inputs.attachment_2.label'),
            'ATTACHMENT_3'                                       => __('contenttypes::inputs.attachment_3.label'),
            'ATTACHMENT_4'                                       => __('contenttypes::inputs.attachment_4.label'),
            'from_city_id'                                       => __('contenttypes::inputs.from_city_id.label'),
            'to_city_id'                                         => __('contenttypes::inputs.to_city_id.label'),
            'loading_type'                                       => __('contenttypes::inputs.loading_type.label'),
            'loading_address'                                    => __('contenttypes::strings.shippments.loading_address'),
            'loading_address.primary_contact_company_name'       => __('contenttypes::inputs.address_name.label'),
            'loading_address.primary_contact_name'               => __('contenttypes::inputs.address_primary_contact.label'),
            'loading_address.primary_contact_email'              => __('contenttypes::inputs.address_email.label'),
            'loading_address.primary_contact_phone_number'       => __('contenttypes::inputs.address_phone_number.label'),
            'loading_address.lat_lng'                            => __('contenttypes::inputs.lat.label') . ' / ' . __('contenttypes::inputs.lng.label'),
            'loading_address.lat'                                => __('contenttypes::inputs.lat.label'),
            'loading_address.lng'                                => __('contenttypes::inputs.lng.label'),
            'loading_address.full_address'                       => __('contenttypes::inputs.full_address.label'),
            'loading_address.public_notes'                       => __('contenttypes::inputs.public_notes.label'),
            'loading_address.private_notes'                      => __('contenttypes::inputs.private_notes.label'),
            'offloading_address'                                 => __('contenttypes::strings.shippments.offloading_address'),
            'offloading_address.*.primary_contact_company_name'  => __('contenttypes::inputs.address_name.label'),
            'offloading_address.*.primary_contact_name'          => __('contenttypes::inputs.address_primary_contact.label'),
            'offloading_address.*.primary_contact_email'         => __('contenttypes::inputs.address_email.label'),
            'offloading_address.*.primary_contact_phone_number'  => __('contenttypes::inputs.address_phone_number.label'),
            'offloading_address.*.lat_lng'                       => __('contenttypes::inputs.lat.label') . ' / ' . __('contenttypes::inputs.lng.label'),
            'offloading_address.*.lat'                           => __('contenttypes::inputs.lat.label'),
            'offloading_address.*.lng'                           => __('contenttypes::inputs.lng.label'),
            'offloading_address.*.full_address'                  => __('contenttypes::inputs.full_address.label'),
            'offloading_address.*.public_notes'                  => __('contenttypes::inputs.public_notes.label'),
            'offloading_address.*.private_notes'                 => __('contenttypes::inputs.private_notes.label'),
            'offloading_address.*.round_trip'                    => __('contenttypes::inputs.round_trip.label'),
        ];
        $messages = [];
        foreach ([
            'offloading_address.*.primary_contact_company_name',
            'offloading_address.*.primary_contact_name',
            'offloading_address.*.primary_contact_email',
            'offloading_address.*.primary_contact_phone_number',
            'offloading_address.*.lat_lng',
            'offloading_address.*.lat',
            'offloading_address.*.lng',
            'offloading_address.*.full_address',
            'offloading_address.*.round_trip',
        ] as $field) {
            $messages[$field . '.required_if'] = __('validation.required', [
                'attribute' => $attributes[$field],
            ]);
        }
        if ($type == 'create') {
            $rules['number_of_trucks'] = 'required|numeric|min:1';
            return Validator::make($data, $rules, $messages, $attributes);
        }
        if ($type == 'update') {
            return Validator::make($data, $rules, $messages, $attributes);
        }
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        if (! empty($keyword)) {
            $query->withSearch($keyword);
        }
    }

    protected function advancedFilter($query, array $filter = [])
    {
        $query->advancedFilter($filter);
    }

    protected function query()
    {
        return $this->model()->select([
            'lgl_shippments.*',
        ])
        ->withSelectMeta()
        ->WithClientSelect()
        ->WithTransportarSelect()
        ->WithCountryAndCitySelect()
        ->WithLocationsCountSelect()
        ->WithWaybillSelect()
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->where('lgl_shippments.company_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where(function($query) {
                $query->whereHas('matchedTransportars', function($rel) {
                    $rel->where('transporter_user_id', auth()->id());
                    $rel->whereIn('status', ['PENDING_RESPONSE', 'ACCEPTED']);
                });
                $query->orWhereHas('rfqs', function($rel) {
                    $rel->where('transporter_user_id', auth()->id());
                    $rel->whereIn('status', ['PENDING_RESPONSE', 'PENDING_ACCEPTANCE', 'ACCEPTED']);
                });
            });
        })
        ->when(auth()->check() && auth()->user()->type != 'DEFAULT' && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query;
        })
        ->when(request()->force_client == 'YES', function($query) {
            $query->where('lgl_shippments.company_user_id', empty(request()->client_id) ? 0 : request()->client_id);
        })
        ->when(request()->force_invoice == 'YES', function($query) {
            $query->where('lgl_shippments.sys_status', 'DELIVERED_PENDING_PAYMENT');
        });
    }

    public function dataTable($Filter = null)
    {
        $query = $this->query();
        $helper = app(\Modules\SystemCore\System\Classes\Helpers::class);
        $table  = parent::dataTable($Filter);

        $table->addColumn('formated_total_price', function($row) use($helper) {
            return $helper->moneyFormat($row->total_price, 2)->abs;
        });

        return $table;
    }

    public function rfqDataTable($Filter = null)
    {
        $this->model()->select([
            'lgl_shippments.*',
            DB::raw('
                lgl_shippments_rfqs.id AS rfq_id,
                lgl_shippments_rfqs.transporter_user_id AS rfq_transporter_user_id
            ')
        ])
        ->WithRfqSelectMeta()
        ->WithClientSelect()
        ->WithRfqTransportarSelect()
        ->WithCountryAndCitySelect()
        ->WithLocationsCountSelect()
        ->where('lgl_shippments.is_rfq', 'YES')
        ->where('lgl_shippments.sys_status', '!=', 'DRAFT')
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->where('lgl_shippments.company_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where(function($query) {
                $query->whereHas('matchedTransportars', function($rel) {
                    $rel->where('transporter_user_id', auth()->id());
                    $rel->whereIn('status', ['PENDING_RESPONSE', 'ACCEPTED']);
                });
                $query->orWhereHas('rfqs', function($rel) {
                    $rel->where('transporter_user_id', auth()->id());
                    $rel->whereIn('status', ['PENDING_RESPONSE', 'PENDING_ACCEPTANCE', 'ACCEPTED']);
                });
            });
        })
        ->when(auth()->check() && auth()->user()->type != 'DEFAULT' && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query;
        })
        ->when(request()->force_client == 'YES', function($query) {
            $query->where('lgl_shippments.company_user_id', empty(request()->client_id) ? 0 : request()->client_id);
        });

        $helper = app(\Modules\SystemCore\System\Classes\Helpers::class);
        $table  = parent::dataTable($Filter);

        $table->addColumn('formated_total_price', function($row) use($helper) {
            return $helper->moneyFormat($row->rfq_total_price, 2)->abs;
        });

        return $table;
    }

    public function companiesDataTable($Filter = null)
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
            $query->where('lgl_shippments.company_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where(function($query) {
                $query->whereHas('matchedTransportars', function($rel) {
                    $rel->where('transporter_user_id', auth()->id());
                    $rel->whereIn('status', ['PENDING_RESPONSE', 'ACCEPTED']);
                });
                $query->orWhereHas('rfqs', function($rel) {
                    $rel->where('transporter_user_id', auth()->id());
                    $rel->whereIn('status', ['PENDING_RESPONSE', 'PENDING_ACCEPTANCE', 'ACCEPTED']);
                });
            });
        })
        ->when(auth()->check() && auth()->user()->type != 'DEFAULT' && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query;
        });

        $helper = app(\Modules\SystemCore\System\Classes\Helpers::class);
        $table  = parent::dataTable($Filter);

        $table->addColumn('formated_total_price', function($row) use($helper) {
            return $helper->moneyFormat($row->total_price, 2)->abs;
        });

        return $table;
    }

    public function export($filter = null)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }

        $helper = app(\Modules\SystemCore\System\Classes\Helpers::class);        

        $query = $this->query();
        $this->filter($query, $filter);
        $exporter = app(ExportService::class)->of($query)->name('Shippments.csv');

        $exporter->addColumn('id', function($row) {
            return $row->id;
        });

        $exporter->addColumn('CLient Name', function($row) {
            return $row->client_company_name;
        });
        $exporter->addColumn('Client email', function($row) {
            return $row->client_company_email;
        });
        $exporter->addColumn('Transportar name', function($row) {
            return $row->transportar_company_name;
        });
        $exporter->addColumn('transportar company email', function($row) {
            return $row->transportar_company_email;
        });
        $exporter->addColumn('from_country_name', function($row) {
            return $row->from_country_name;
        });
        $exporter->addColumn('from city name', function($row) {
            return $row->from_city_name;
        });
        $exporter->addColumn('to country name', function($row) {
            return $row->to_country_name;
        });
        $exporter->addColumn('to city name', function($row) {
            return $row->to_city_name;
        });
        $exporter->addColumn('type', function($row) {
            if ($row->type == 'CITY_TO_CITY') {
                return __('contenttypes::inputs.loading_type.options_short.CITY_TO_CITY');
            }
            if ($row->type == 'CITY_TO_CITY_MULTIPLE_LOCATIONS') {
                return __('contenttypes::inputs.loading_type.options_short.CITY_TO_CITY') . ' (' . __('contenttypes::inputs.loading_type.options_short.MULTIPLE_LOCATIONS') . ')';
            }
            return $row->type;
        });
        $exporter->addColumn('is round trip', function($row) {
            return $row->round_trip == 'YES' ? __('contenttypes::inputs.loading_type.options_short.ROUND_TRIP.YES') : __('contenttypes::inputs.loading_type.options_short.ROUND_TRIP.NO');
        });
        $exporter->addColumn('Offloading locations count', function($row) {
            return $row->offloading_locations_count;
        });
        $exporter->addColumn('total price', function($row) use($helper) {
            return $helper->moneyFormat($row->total_price, 2)->abs;
        });
        $exporter->addColumn('status', function($row) {
            return $row->new_status;
        });
        $exporter->addColumn('created at', function($row) {
            return $row->new_created_at;
        });
        $exporter->addColumn('last update', function($row) {
            return $row->new_updated_at;
        });
        $exporter->addColumn('delivery required at', function($row) {
            return $row->new_delivery_required_at;
        });
        $exporter->addColumn('public notes', function($row) {
            return $row->public_notes;
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
            'is_rfq'               => $data['is_rfq'] == 'Y' ? 'YES' : 'NO',
            'total_weight'         => $data['total_weight'],
            'decalred_value'       => $data['declared_order_value'],
            'load_size_id'         => $data['load_size_id'],
            'load_type_id'         => $data['load_type_id'],
            'number_of_pallets'    => $data['number_of_pallets'],
            'number_of_trucks'     => 1,
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
            'sys_status'           => $data['is_draft'] == 'Y' ? 'DRAFT' : ($data['is_rfq'] == 'Y' ? 'RFQ_PENDING_TRANSPORTER_SELECTION' : 'MATCHING'),
        ];

        $model = parent::create( $ModelData )->data();

        $this->syncFile($model, 'ATTACHMENT_1', $data['ATTACHMENT_1'] ?? null);
        $this->syncFile($model, 'ATTACHMENT_2', $data['ATTACHMENT_2'] ?? null);
        $this->syncFile($model, 'ATTACHMENT_3', $data['ATTACHMENT_3'] ?? null);
        $this->syncFile($model, 'ATTACHMENT_4', $data['ATTACHMENT_4'] ?? null);

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
            'is_rfq'               => $data['is_rfq'] == 'Y' ? 'YES' : 'NO',
            'total_weight'         => $data['total_weight'],
            'decalred_value'       => $data['declared_order_value'],
            'load_size_id'         => $data['load_size_id'],
            'load_type_id'         => $data['load_type_id'],
            'number_of_pallets'    => $data['number_of_pallets'],
            'number_of_trucks'     => 1,
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
            'sys_status'           => $data['is_draft'] == 'Y' ? 'DRAFT' : ($data['is_rfq'] == 'Y' ? 'RFQ_PENDING_TRANSPORTER_SELECTION' : 'MATCHING'),
        ];

        $model = parent::update( $ModelData )->data();

        $this->syncFile($model, 'ATTACHMENT_1', $data['ATTACHMENT_1'] ?? null);
        $this->syncFile($model, 'ATTACHMENT_2', $data['ATTACHMENT_2'] ?? null);
        $this->syncFile($model, 'ATTACHMENT_3', $data['ATTACHMENT_3'] ?? null);
        $this->syncFile($model, 'ATTACHMENT_4', $data['ATTACHMENT_4'] ?? null);

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
