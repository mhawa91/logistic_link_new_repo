@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.shippments.list'),
        'items' => [
            [
                'title' => __('contenttypes::strings.shippments.menu'),
                'link'  => route('ShippmentsController@index')
            ],
            [
                'title' => __('contenttypes::strings.shippments.list'),
                'link'  => route('ShippmentsController@index')
            ]
        ],
    ]
])
    @if ($Service->can('create'))
        <div class="nk-block-head-content" id="select-app">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="javascript:;" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
                    <em class="icon ni ni-menu-alt-r"></em>
                </a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="{{ route('ShippmentsController@create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em><span>{{ __('contenttypes::strings.shippments.create') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('ShippmentsController@downloadImportTemplate') }}" class="btn btn-outline-primary">
                                <em class="icon ni ni-download-cloud"></em><span>{{ __('contenttypes::strings.shipment_import.download_template') }}</span>
                            </a>
                        </li>
                        @if(auth()->user()->type === 'DEFAULT')
                            @if($Service->can('update'))
                                <li>
                                    <a href="{{ route('ShippmentsController@downloadImportExport') }}" class="btn btn-outline-primary">
                                        <em class="icon ni ni-file-download"></em><span>{{ __('contenttypes::strings.shipment_import.export_shipments') }}</span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('ShippmentsController@imports') }}" class="btn btn-outline-primary">
                                    <em class="icon ni ni-upload-cloud"></em><span>{{ __('contenttypes::strings.shipment_import.import_shipments') }}</span>
                                </a>
                            </li>
                        @endif
                        {{-- <li class="nk-block-tools-opt">
                            <a href="#" class="btn btn-white btn-outline-light">
                                <em class="icon ni ni-download-cloud"></em><span>Export</span>
                            </a>
                        </li> --}}
                        <li v-if="selected.length > 0">
                            <form action="{{ route('ShippmentsController@postBulkSend') }}" method="POST" onsubmit="BS.AJAX_FORM(event);">
                                @csrf
                                <input v-for="(item, item_index) in selected" type="hidden" name="shippments[]" :value="item">
                                <button type="submit" class="btn btn-danger">
                                    <em class="icon ni ni-edit"></em><span>{{ __('contenttypes::strings.send_shippments_to_suppliers') }}</span>
                                </button>
                            </form>
                        </li>     
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endcomponent

@section('content')

    @component('ui::components.form', [
        'options' => [
            'id'                => 'datatable-advanced-search-form',
            'action'            => 'javascript:;',
            'method'            => 'POST',
            'class'             => '',
            'attributes'        => [
                'onsubmit'                     => 'datatable.ajax.reload()',
                'data-keep-loading-on-success' => 'false',
            ],
        ],
    ])

        @component('ui::components.modal', [
            'options' => [
                'id'        => 'searchModal',
                'title'     => __('ui::strings.advanced_filter'),
                'size'      => 'md',
                'fit'       => false,
            ]
        ])

            @if(auth()->user()->type == 'TRANSPORTER' || auth()->user()->type == 'SYSTEM_ADMIN' || auth()->user()->type == 'ROOT')
                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'client_name',
                        'label'       => __('contenttypes::inputs.client_name.label'),
                        'description' => __('contenttypes::inputs.client_name.description'),
                    ],
                ])
            @endif

            @if(auth()->user()->type == 'DEFAULT' || auth()->user()->type == 'SYSTEM_ADMIN' || auth()->user()->type == 'ROOT')
                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'transportar_name',
                        'label'       => __('contenttypes::inputs.transportar_name.label'),
                        'description' => __('contenttypes::inputs.transportar_name.description'),
                    ],
                ])
            @endif

            <hr class="preview-hr">

            @component('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'daterange',
                    'name'        => 'created_at',
                    'label'       => __('contenttypes::inputs.created_at.label'),
                    'placeholder' => __('contenttypes::inputs.created_at.placeholder'),
                    'description' => __('contenttypes::inputs.created_at.description'),
                    'attributes'  => []
                ],
            ])
            @endcomponent

            @component('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'daterange',
                    'name'        => 'status_updated_at',
                    'label'       => __('contenttypes::inputs.status_updated_at.label'),
                    'placeholder' => __('contenttypes::inputs.status_updated_at.placeholder'),
                    'description' => __('contenttypes::inputs.status_updated_at.description'),
                    'attributes'  => []
                ],
            ])
            @endcomponent

            <hr class="preview-hr">

            @include('ui::components.select', [
                'options' => [
                    'view'              => 'INLINE',
                    'name'              => 'sys_statuses',
                    'label'             => __('contenttypes::inputs.shippment_status.label'),
                    'placeholder'       => __('contenttypes::inputs.shippment_status.placeholder'),
                    'description'       => __('contenttypes::inputs.shippment_status.description'),
                    'attributes'        => [],
                    'value'             => $dashboardShippmentStatuses ?? [],
                    'search'            => true,
                    'multiple'          => true,
                    'nullable'          => true,
                    'nullable_value'    => '0',
                    'options'           => __('contenttypes::inputs.shippment_status.options'),
                    'option_text'       => function($key, $option)                   { return $option; },
                    'option_value'      => function($key, $option)                   { return $key; },
                    'option_attributes' => function($key, $option)                   { return []; },
                    'selected'          => function($options, $key, $option, $value) { return in_array($key, (array) $value, true); },
                    'disabled'          => function($options, $key, $option, $value) { return false; },
                ],
            ])

            <x-slot name="footer">
                <button type="submit" class="btn btn-sm btn-success">
                    <span>{{ __('ui::strings.search') }}</span>
                </button>

                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger" onclick="BS.resetForm(this); datatable.ajax.reload();">
                    <span>{{ __('ui::strings.cancel') }}</span>
                </button>
            </x-slot>
        @endcomponent
    @endcomponent

    @component('ui::components.form', [
        'options' => [
            'id'                => 'supplier-waybill-and-driver-app',
            'action'            => route('ShippmentsController@postDriverAndWaybill'),
            'method'            => 'POST',
            'class'             => '',
            'attributes'        => [
                'onsubmit'                     => 'postDriverAndWaybill(event)',
                'data-keep-loading-on-success' => 'false',
            ],
        ],
    ])

        @component('ui::components.modal', [
            'options' => [
                'id'        => 'supplier-waybill-and-driver-modal',
                'title'     => __('contenttypes::inputs.waybill.label'),
                'size'      => 'lg',
                'fit'       => false,
            ]
        ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'mode',
                    'attributes'  => [
                        'v-model' => 'mode',
                    ]
                ],
            ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'model',
                    'value'       => null,
                    'attributes'  => [
                        'v-model' => 'model',
                    ]
                ],
            ])
            
            <div class="text-center m-5" v-if="is_loading">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div>
            </div>

            <div v-show="!is_loading">
                <div class="row gy-3">
                    <div class="col-lg-12">
                        @include('ui::components.radios', [
                            'options' => [
                                'view'              => 'DEFAULT',
                                'type'              => 'radio',
                                'name'              => 'driver_mode',
                                'label'             => __('contenttypes::inputs.driver_mode.description'),
                                'placeholder'       => __('contenttypes::inputs.driver_mode.placeholder'),
                                'attributes'        => [],
                                'value'             => null,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => [
                                    [
                                        'id'       => 'driver_mode_cs_1',
                                        'value'    => 'existing',
                                        'label'    => __('contenttypes::inputs.driver_mode.options.existing'),
                                        'selected' => true,
                                    ],
                                    [
                                        'id'       => 'driver_mode_cs_2',
                                        'value'    => 'new',
                                        'label'    => __('contenttypes::inputs.driver_mode.options.new'),
                                        'selected' => false,
                                    ],
                                ],
                                'option_id'         => function($key, $option)                   { return $option['id']; },
                                'option_text'       => function($key, $option)                   { return $option['label']; },
                                'option_sub_text'   => function($key, $option)                   { return null; },
                                'option_value'      => function($key, $option)                   { return $option['value']; },
                                'option_attributes' => function($key, $option)                   { 
                                    return [
                                        'v-model' => 'mode',
                                    ]; 
                                },
                                'selected'          => function($options, $key, $option, $value) { return $option['selected']; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])
                    </div>

                    <div class="col-lg-6" v-if="mode == 'new'">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_name',
                                'label'       => __('contenttypes::inputs.driver_name.label'),
                                'placeholder' => __('contenttypes::inputs.driver_name.placeholder'),
                                'attributes'  => []
                            ],
                        ])
                    </div>
                    <div class="col-lg-6" v-if="mode == 'new'">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_id_number',
                                'label'       => __('contenttypes::inputs.driver_id_number.label'),
                                'placeholder' => __('contenttypes::inputs.driver_id_number.placeholder'),
                                'attributes'  => []
                            ],
                        ])
                    </div>
                    <div class="col-md-6" v-show="mode == 'new'">
                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'country_select',
                                'jsSelect2'         => 'countryJsSelect2',
                                'view'              => 'DEFAULT',
                                'name'              => 'driver_nationality_country_id',
                                'label'             => __('contenttypes::inputs.driver_nationality_country_id.label'),
                                'placeholder'       => __('contenttypes::inputs.driver_nationality_country_id.placeholder'),
                                'attributes'        => [],
                                'value'             => null,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => [],
                                'option_text'       => function($key, $option)                   { return $option['text']; },
                                'option_value'      => function($key, $option)                   { return $option['id']; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])
                    </div>

                    <div class="col-md-6" v-show="mode == 'existing'">
                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'driver_id_select',
                                'jsSelect2'         => 'driver_id_select2',
                                'view'              => 'DEFAULT',
                                'name'              => 'driver_id',
                                'label'             => __('contenttypes::inputs.driver_id.label'),
                                'placeholder'       => __('contenttypes::inputs.driver_id.placeholder'),
                                'attributes'        => [],
                                'value'             => null,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => [],
                                'option_text'       => function($key, $option)                   { return $option['text']; },
                                'option_value'      => function($key, $option)                   { return $option['id']; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])
                        <span class="form-note mt-2">
                            {{ __('contenttypes::strings.shippments.driver_availability_hint') }}
                        </span>
                    </div>

                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'car_plate',
                                'label'       => __('contenttypes::inputs.car_plate.label'),
                                'placeholder' => __('contenttypes::inputs.car_plate.placeholder'),
                                'attributes'  => [
                                    'v-model' => 'car_plate',
                                ]
                            ],
                        ])
                    </div>

                    <div class="col-lg-12">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'textarea',
                                'name'        => 'car_information',
                                'label'       => __('contenttypes::inputs.car_information.label'),
                                'placeholder' => __('contenttypes::inputs.car_information.placeholder'),
                                'rows'        => 3,
                                'attributes'  => [
                                    'v-model' => 'car_information',
                                ],
                            ],
                        ])
                    </div>

                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'WAYBILL',
                                'file_preview' => true,
                                'label'       => __('contenttypes::inputs.waybill.label'),
                                'description' => __('contenttypes::inputs.attachment_1.description'),
                                'placeholder' => null,
                                'value'       => null,
                            ],
                        ])
                    </div>
                    <div class="col-lg-6" v-if="waybill">
                        <div class="form-group">
                            <label>{{ __('contenttypes::inputs.waybill.label') }}</label>
                            <div class="form-control-wrap">
                                <a :href="waybill.path" class="btn btn-sm btn-success btn-block" target="_blank">
                                    <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                    <span>{{ __('contenttypes::inputs.waybill.view_file') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div v-if="!is_loading && mode != 'existing'">
                    <button type="submit" class="btn btn-sm btn-success">
                        <span>{{ __('contenttypes::inputs.waybill.upload') }}</span>
                    </button>
                </div>
                <div v-if="!is_loading && mode == 'existing'">
                    <button type="submit" class="btn btn-sm btn-success">
                        <span>{{ __('contenttypes::inputs.waybill.upload') }}</span>
                    </button>
                </div>

                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">
                    <span>{{ __('ui::strings.cancel') }}</span>
                </button>
            </x-slot>
        @endcomponent
    @endcomponent

    @component('ui::components.form', [
        'options' => [
            'id'                => 'supplier-waybill-and-driver-view-app',
            'action'            => route('ShippmentsController@postDriverAndWaybill'),
            'method'            => 'POST',
            'class'             => '',
            'attributes'        => [
                'onsubmit'                     => 'BS.AJAX_FORM(event)',
                'data-keep-loading-on-success' => 'false',
            ],
        ],
    ])

        @component('ui::components.modal', [
            'options' => [
                'id'        => 'supplier-waybill-and-driver-view-modal',
                'title'     => __('contenttypes::inputs.waybill.label'),
                'size'      => 'lg',
                'fit'       => false,
            ]
        ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'mode',
                    'attributes'  => [
                        'v-model' => 'mode',
                    ]
                ],
            ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'model',
                    'value'       => null,
                    'attributes'  => [
                        'v-model' => 'model',
                    ]
                ],
            ])
            
            <div class="text-center m-5" v-if="is_loading">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div>
            </div>

            <div v-show="!is_loading">
                <div class="row gy-3">
                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_name',
                                'label'       => __('contenttypes::inputs.driver_name.label'),
                                'placeholder' => __('contenttypes::inputs.driver_name.placeholder'),
                                'attributes'  => [
                                    ':value'   => 'driver ? driver.name : null',
                                    'disabled' => 'true',
                                ]
                            ],
                        ])
                    </div>
                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_id_number',
                                'label'       => __('contenttypes::inputs.driver_id_number.label'),
                                'placeholder' => __('contenttypes::inputs.driver_id_number.placeholder'),
                                'attributes'  => [
                                    ':value'   => 'driver ? driver.id_number : null',
                                    'disabled' => 'true',
                                ]
                            ],
                        ])
                    </div>
                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_nationality_country_id',
                                'label'       => __('contenttypes::inputs.driver_nationality_country_id.label'),
                                'placeholder' => __('contenttypes::inputs.driver_nationality_country_id.placeholder'),
                                'attributes'  => [
                                    ':value'   => 'driver && driver.nationality ? (driver.nationality.name_en + \' / \' + driver.nationality.name_ar) : null',
                                    'disabled' => 'true',
                                ]
                            ],
                        ])
                    </div>
                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'car_plate',
                                'label'       => __('contenttypes::inputs.car_plate.label'),
                                'placeholder' => __('contenttypes::inputs.car_plate.placeholder'),
                                'attributes'  => [
                                    ':value'   => 'car_plate',
                                    'disabled' => 'true',
                                ]
                            ],
                        ])
                    </div>

                    <div class="col-lg-12">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'textarea',
                                'name'        => 'car_information_view',
                                'label'       => __('contenttypes::inputs.car_information.label'),
                                'placeholder' => __('contenttypes::inputs.car_information.placeholder'),
                                'rows'        => 3,
                                'attributes'  => [
                                    ':value'   => 'driver ? driver.car_information : null',
                                    'disabled' => 'true',
                                ],
                            ],
                        ])
                    </div>

                    <div class="col-lg-6" v-if="waybill">
                        <div class="form-group">
                            <label>{{ __('contenttypes::inputs.waybill.label') }}</label>
                            <div class="form-control-wrap">
                                <a :href="waybill.path" class="btn btn-sm btn-success btn-block" target="_blank">
                                    <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                    <span>{{ __('contenttypes::inputs.waybill.view_file') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                @if (auth()->check() && auth()->user()->type == 'TRANSPORTER')
                    <div v-if="!is_loading">
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#supplier-waybill-and-driver-modal" onclick="supplierWaybillAndDriverViewApp.openEditModal()" data-dismiss="modal">
                            <span>{{ __('contenttypes::inputs.waybill.edit') }}</span>
                        </button>
                    </div>
                @endif
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">
                    <span>{{ __('ui::strings.cancel') }}</span>
                </button>
            </x-slot>
        @endcomponent
    @endcomponent

    @component('ui::components.datatable', [
        'options' => [
            'id'           => null,
            'callable'     => 'datatableTools',
            'jsDatatable'  => 'datatable',
            'searchModal'  => 'searchModal',
            'attributes'   => [],
            'class'        => [],
            'size'         => 'compact',
            'tools'        => [
                'search'    => true,
                'available' => true,
                'trashed'   => $Service->can('restore') || $Service->can('permaDelete'),
                'refresh'   => true,
                'refresh'   => true,
                'export'    => 'exportData()',
            ],
            'columns'      => [
                [
                    'label' => '<span class="sub-text">' . __('ui::inputs.id.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.client.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.transportar.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.from.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.to.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.loading_type.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.shippment_total_price.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.waybill.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('admin::inputs.status.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.status_updated_at.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '<span class="sub-text">' . __('ui::inputs.created_at.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.is_rfq.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '',
                    'class' => ['nk-tb-col-tools', 'text-right', 'fit'],
                ]
            ],
        ]
    ])
        <x-slot name="script">
            <script>
                var datatableTools = BS.loadCallable('datatableTools');
                var datatable      = BS.DATATABLE('.bs-datatable', {
                    ajax : {
                        url  : "{!! route('ShippmentsController@postIndex') !!}",
                        type : "POST",
                        data : function(data){
                            $('#searchModal').find('.close').first().click();
                            data.records_type = datatableTools.getRecordsType();
                            data._token       = `{!! csrf_token() !!}`;
                            data.advanced_search = $('#datatable-advanced-search-form').first().serializeObject();
                            return data;
                        }
                    },
                    responsive: {
                        details  : true,
                        language : dataTableTranslations
                    },
                    searching    : true,
                    bLengthChange: false,
                    processing   : true,
                    serverSide   : true,
                    pageLength   : 50,
                    order        :  [[10, 'desc'], [0, 'desc']],
                    drawCallback: function () {
                        $('.bs-datatable tbody tr').addClass('nk-tb-item');
                        $('.bs-datatable-card .pagination').addClass('pagination-sm');
                    },
                    columns: [
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'id',
                            name       : 'lgl_shippments.id',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                if (Row.sys_status != 'DRAFT') {
                                    return `<span class="text-danger">${Row.id}</span>`;
                                }
                                let isChecked = isSelected(Row.id);
                                return `
                                    <div class="custom-control custom-switch">
                                        <input name="selected[]" type="checkbox" class="custom-control-input shippment-select" id="customSwitch${Row.id}" value="${Row.id}" ${isChecked ? 'checked' : ''}>
                                        <label class="custom-control-label" for="customSwitch${Row.id}"></label>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'client_company_name',
                            name       : 'client_company_name',
                            orderable  : true,
                            searchable : true,
                            visible    : {{ auth()->check() && (auth()->user()->type == 'TRANSPORTER' || ! in_array(auth()->user()->type, ['TRANSPORTER', 'DEFAULT'])) ? 'true' : 'false' }},
                            render     : function(data,type,Row,meta) {
                                return `
                                    <div class="user-card">
                                        <div class="user-avatar sq sm bg-light">
                                            <img src="${Row.client_company_logo}" alt="${Row.client_company_name}">
                                        </div>
                                        <div class="user-name">
                                            <span>${Row.client_company_name}</span>
                                            <br>
                                            <span class="text-muted" dir="ltr">${Row.client_company_email || Row.client_user_email || '---------'}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'transportar_company_name',
                            name       : 'transportar_company_name',
                            orderable  : true,
                            searchable : true,
                            visible    : {{ auth()->check() && (auth()->user()->type == 'DEFAULT' || ! in_array(auth()->user()->type, ['TRANSPORTER', 'DEFAULT'])) ? 'true' : 'false' }},
                            render     : function(data,type,Row,meta) {
                                if (! Row.transporter_user_id) {
                                    return `
                                        <div class="user-card">
                                            <div class="user-avatar sq sm bg-light">
                                                T
                                            </div>
                                            <div class="user-name">
                                                <span>{{ __('contenttypes::strings.shippments.unspecified_yet') }}</span>
                                                <br>
                                                <span class="text-muted" dir="ltr">${'---------'}</span>
                                            </div>
                                        </div>
                                    `;
                                }
                                return `
                                    <div class="user-card">
                                        <div class="user-avatar sq sm bg-light">
                                            <img src="${Row.transportar_company_logo}" alt="${Row.transportar_company_name}">
                                        </div>
                                        <div class="user-name">
                                            <span>${Row.transportar_company_name}</span>
                                            <br>
                                            <span class="text-muted" dir="ltr">${Row.transportar_company_email || Row.transportar_user_email || '---------'}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'from_city_name',
                            name       : 'from_city_name',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<div>
                                    <span>${Row.from_city_name}</span> <br>
                                    <strong class="text-primary">${Row.from_country_name}</strong>
                                </div>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'to_city_name',
                            name       : 'to_city_name',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<div>
                                    <strong>${Row.to_city_name}</strong> <br>
                                    <strong class="text-primary">${Row.to_country_name}</strong>
                                </div>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'type',
                            name       : 'type',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                var label     = '';
                                var roundTrip = '';
                                if (Row.type == 'CITY_TO_CITY') {
                                    label = '<strong>{{ __('contenttypes::inputs.loading_type.options_short.CITY_TO_CITY') }}</strong>'; 
                                }
                                if (Row.type == 'CITY_TO_CITY_MULTIPLE_LOCATIONS') {
                                    label = `
                                        <strong>{{ __('contenttypes::inputs.loading_type.options_short.CITY_TO_CITY') }}</strong> 
                                        <small>({{ __('contenttypes::inputs.loading_type.options_short.MULTIPLE_LOCATIONS') }})</small>
                                    `; 
                                }
                                if (Row.round_trip == 'YES') {
                                    roundTrip = '<small class="text-primary">({{ __('contenttypes::inputs.loading_type.options_short.ROUND_TRIP.YES') }})</small>'; 
                                }
                                return `<div>
                                    ${label} <br>
                                    <strong style="color: #FB4F28 !important;">${Row.offloading_locations_count} {{ __('contenttypes::inputs.loading_type.options_short.locations') }}</strong>
                                    ${roundTrip}
                                </div>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'total_price',
                            name       : 'total_price',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<div>
                                    <strong>${Row.formated_total_price}</strong> SAR
                                </div>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'waybill_path',
                            name       : 'waybill_path',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                if (Row.waybill_path) {
                                    return `<span>
                                        <a class="link link-primary link-sm" style="margin-left: 10px; margin-right: 10px;" tabindex="-1" href="javascript:;" data-toggle="modal" data-target="#supplier-waybill-and-driver-view-modal" onclick="fetchWaybillAndDriverView(${Row.id})">
                                            <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                            {{ __('contenttypes::inputs.files.options.view') }}
                                        </a>
                                    </span>`;
                                }
                                @if(auth()->check() && auth()->user()->type == 'TRANSPORTER')
                                    return `<span>
                                        <a class="link link-primary link-sm text-orange underline" style="margin-left: 10px; margin-right: 10px;" tabindex="-1" href="javascript:;" data-toggle="modal" data-target="#supplier-waybill-and-driver-modal" onclick="fetchWaybillAndDriver(${Row.id})">
                                            <i class="fas fa-upload" style="margin-left: 5px; margin-right: 5px;"></i>
                                            {{ __('contenttypes::inputs.waybill.upload') }}
                                        </a>
                                    </span>`;
                                @else
                                    return `<span>
                                        <a class="link link-muted link-sm text-muted" style="margin-left: 10px; margin-right: 10px;" tabindex="-1" href="javascript:;">
                                            <i class="fas fa-info-circle" style="margin-left: 5px; margin-right: 5px;"></i>
                                            {{ __('contenttypes::inputs.waybill.pending_upload') }}
                                        </a>
                                    </span>`;
                                @endif
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "sys_status",
                            name       : 'sys_status',
                            render     : function(data,type,Row,meta) {
                                return `<span class="badge badge-dot badge-${Row.new_status_color}">${Row.new_status ?? 'N/A'}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "updated_at",
                            name       : 'updated_at',
                            render     : function(data,type,Row,meta) {
                                return `<small dir="ltr">${Row.new_updated_at || Row.updated_at}</small>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "created_at",
                            name       : 'lgl_shippments.created_at',
                            render     : function(data,type,Row,meta) {
                                return `<small dir="ltr">${Row.new_created_at || Row.created_at}</small>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "new_is_rfq",
                            name       : 'new_is_rfq',
                            render     : function(data,type,Row,meta) {
                                return `<span class="badge badge-md badge-${Row.new_is_rfq_color}">${Row.new_is_rfq ?? 'N/A'}</span>`;
                            }
                        },
                        {
                            className      : BS.CONFIG('actionsTableCellClass'),
                            data           : "actions",
                            defaultContent : '',
                            orderable      : false,
                            searchable     : false,
                            render     : function(data,type,Row,meta) {
                                var actions = ``;
                                if (Row.actions) {
                                    actions += `
                                        <ul class="nk-tb-actions gx-2">
                                            <li>
                                                <div class="dropdown">
                                                    <a href="javascript:;" class="btn btn-sm btn-icon btn-trigger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <ul class="link-list-plain no-bdr sm">
                                                            ${(! Row.actions['read']        ? `` : `<li><a href="${Row.actions['read']['action']}"><em class="icon ni ni-eye"></em><span>${Row.actions['read']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['rfq_list']    ? `` : `<li><a href="${Row.actions['rfq_list']['action']}"><em class="icon ni ni-list"></em><span>${Row.actions['rfq_list']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['update']      ? `` : `<li><a href="${Row.actions['update']['action']}"><em class="icon ni ni-update"></em><span>${Row.actions['update']['label']}</span></a></li>`)}
                                                            <li class="divider"></li>
                                                            ${(! Row.actions['duplicate']      ? `` : `<li><a href="${Row.actions['duplicate']['action']}"><em class="icon ni ni-copy"></em><span>${Row.actions['duplicate']['label']}</span></a></li>`)}
                                                            <li class="divider"></li>
                                                            ${(! Row.actions['delete']      ? `` : `<li><a href="javascript:;" onclick="BS.ajaxDelete('{{ route('ShippmentsController@postDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete"></em><span>${Row.actions['delete']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['restore']     ? `` : `<li><a href="javascript:;" onclick="BS.ajaxRestore('{{ route('ShippmentsController@postRestore') }}', ${Row.id}, datatable)"><em class="icon ni ni-redo"></em><span>${Row.actions['restore']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['permaDelete'] ? `` : `<li><a href="javascript:;" onclick="BS.ajaxPermaDelete('{{ route('ShippmentsController@postPermaDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete text-danger"></em><span>${Row.actions['permaDelete']['label']}</span></a></li>`)}
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    `;
                                }
                                return actions;
                            }
                        },
                    ],
                });

                const supplierWaybillAndDriverApp = new Vue({
                    el: '#supplier-waybill-and-driver-app',
                    data: {
                        form       : 'edit',
                        mode       : 'existing',
                        model      : null,
                        is_loading : false,
                        driver     : null,
                        waybill    : null,
                        car_plate  : null,
                        car_information : null,
                    },
                    methods: {
                        changeMode: function(mode) {
                            this.mode = mode;
                        },
                        resetForm: function() {
                            this.mode = 'existing';
                            this.model = null;
                            this.car_information = null;
                        },
                        fetchWaybillAndDriver: function(model) {
                            const vm = this;
                            vm.is_loading = true;
                            vm.model      = model;
                            $.get('{{ route('ShippmentsController@driverAndWaybill') }}', {
                                model : vm.model,
                            }, function(data) {
                                if (data['redirect_url'] || data['redirect']) {
                                    window.location = data['redirect_url'] || data['redirect'];
                                    return;
                                }
                                if (data['toastr']) {
                                    NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                                }
                                vm.driver          = data['driver'] || null;
                                vm.waybill         = data['waybill'] || null;
                                vm.car_plate       = data['car_plate'] || null;
                                vm.car_information = data['driver'] ? (data['driver']['car_information'] || null) : null;
                                vm.is_loading = false;
                            })
                        },
                        reload: function() {
                            if (this.model) {
                                this.fetchWaybillAndDriver(this.model);
                            }
                        },
                    },
                    watch: {
                        mode: function(mode) {
                            this.car_information = mode === 'existing' && this.driver
                                ? (this.driver.car_information || null)
                                : null;
                        },
                    },
                });

                const fetchWaybillAndDriver = function(model) {
                    supplierWaybillAndDriverApp.fetchWaybillAndDriver(model);
                }

                const supplierWaybillAndDriverViewApp = new Vue({
                    el: '#supplier-waybill-and-driver-view-app',
                    data: {
                        form       : 'edit',
                        mode       : 'existing',
                        model      : null,
                        is_loading : false,
                        driver     : null,
                        waybill    : null,
                        car_plate  : null,
                    },
                    methods: {
                        openEditModal: function() {
                            fetchWaybillAndDriver(this.model);
                        },
                        changeMode: function(mode) {
                            this.mode = mode;
                        },
                        resetForm: function() {
                            this.mode = 'existing';
                            this.model = null;
                        },
                        fetchWaybillAndDriver: function(model) {
                            const vm = this;
                            vm.is_loading = true;
                            vm.model      = model;
                            $.get('{{ route('ShippmentsController@driverAndWaybill') }}', {
                                model : vm.model,
                            }, function(data) {
                                if (data['redirect_url'] || data['redirect']) {
                                    window.location = data['redirect_url'] || data['redirect'];
                                    return;
                                }
                                if (data['toastr']) {
                                    NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                                }
                                if(data['driver']) {
                                    vm.driver  = data['driver'];
                                }
                                if(data['waybill']) {
                                    vm.waybill = data['waybill'];
                                }
                                if(data['car_plate']) {
                                    vm.car_plate = data['car_plate'];
                                }
                                vm.is_loading = false;
                            })
                        },
                        reload: function() {
                            if (this.model) {
                                this.fetchWaybillAndDriver(this.model);
                            }
                        },
                    }
                });

                const fetchWaybillAndDriverView = function(model) {
                    supplierWaybillAndDriverViewApp.fetchWaybillAndDriver(model);
                }

                var countryJsSelect2 = $('#country_select').select2({
                    ajax: {
                        delay: 250,
                        url: function (params) {
                            return '{{ route('CountriesController@select') }}';
                        },
                        data: function (params) {
                            var query = {
                                nullable : 'true',
                                search   : params.term,
                                page     : params.page || 1
                            }
                            return query;
                        },
                        processResults: function (data) {
                            return data;
                        }
                    }
                })
                .on("select2:select", function() {
                    let _data = $(this).select2('data')[0];
                });

                var driver_id_select2 = $('#driver_id_select').select2({
                    ajax: {
                        delay: 250,
                        url: function (params) {
                            return '{{ route('ShippmentsController@driversSelect') }}';
                        },
                        data: function (params) {
                            var query = {
                                search   : params.term,
                                page     : params.page || 1,
                                model    : supplierWaybillAndDriverApp.model
                            }
                            return query;
                        },
                        processResults: function (data) {
                            return data;
                        }
                    }
                })
                .on("select2:select", function() {
                    let _data = $(this).select2('data')[0];
                    supplierWaybillAndDriverApp.driver = _data && _data.data ? _data.data : null;
                    supplierWaybillAndDriverApp.car_plate = _data && _data.data
                        ? (_data.data.car_plate || null)
                        : null;
                    supplierWaybillAndDriverApp.car_information = _data && _data.data
                        ? (_data.data.car_information || null)
                        : null;
                })
                .on("select2:clear", function() {
                    supplierWaybillAndDriverApp.driver = null;
                    supplierWaybillAndDriverApp.car_plate = null;
                    supplierWaybillAndDriverApp.car_information = null;
                });

                const postDriverAndWaybill = function(event) {
                    BS.AJAX_FORM(event, {
                        200: (data) => {
                            datatable.ajax.reload();
                            supplierWaybillAndDriverApp.reload();
                            supplierWaybillAndDriverViewApp.reload();
                        }
                    });
                }
            </script>
        </x-slot>
    @endcomponent
@endsection

@push('script')
    <script>
        const selectApp = new Vue({
            el: '#select-app',
            data: {
                selected: [],
            },
            methods: {
                isSelected: function (id) {
                    return this.selected.includes(id);
                },
                toggleSelect: function (id) {
                    if (this.isSelected(id)) {
                        this.selected = this.selected.filter(item => item !== id);
                    } 
                    else {
                        this.selected.push(id);
                    }
                    console.log(this.selected);
                },
            }
        });

        $(document).on('click', '.shippment-select', function() {
            const id = $(this).val();
            selectApp.toggleSelect(id);
        });

        const isSelected = function (id) {
            return selectApp.isSelected(id);
        }

        function exportData() {
            // create form using jquery and submit
            var form = $('<form action="{{ route('ShippmentsController@postExport') }}" method="POST" style="display: none;"></form>');

            let data = {};
            data.advanced_search = $('#datatable-advanced-search-form').first().serializeObject();

            form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            form.append('<input type="hidden" name="records_type" value="'+ datatableTools.getRecordsType() +'">');

            // append inputs to form from data object
            for (let key in data.advanced_search) {
                form.append(`<input type="hidden" name="advanced_search[${key}]" value="${data[key] || ''}">`);
            }
            form.appendTo('body').submit().remove();
        }
    </script>
@endpush
