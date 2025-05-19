@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.shippments_rfqs.list'),
        'items' => [
            [
                'title' => __('contenttypes::strings.shippments_rfqs.menu'),
                'link'  => route('RfqController@index')
            ],
            [
                'title' => __('contenttypes::strings.shippments_rfqs.list'),
                'link'  => route('RfqController@index')
            ]
        ],
    ]
])
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
                'size'      => '',
                'fit'       => false,
            ]
        ])
            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'shippment_id',
                    'label'       => __('contenttypes::inputs.transportar_email.label'),
                    'placeholder' => __('contenttypes::inputs.transportar_email.placeholder'),
                    'value'       => request()->shippment_id ?? null,
                ],
            ])
            <hr class="preview-hr">
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
                    'label' => '<span class="sub-text">' . __('admin::inputs.status.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.requested_at.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.responded_at.label') . '</span>',
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
                        url  : "{!! route('RfqController@postIndex') !!}",
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
                    order        :  [],
                    drawCallback: function () {
                        $('.bs-datatable tbody tr').addClass('nk-tb-item');
                        $('.bs-datatable-card .pagination').addClass('pagination-sm');
                    },
                    columns: [
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'id',
                            name       : 'id',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<span class="text-danger">${Row.id}</span>`;
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
                                if (! Row.rfq_transporter_user_id) {
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
                            data       : 'rfq_total_price',
                            name       : 'rfq_total_price',
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
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "sys_status",
                            name       : 'sys_status',
                            render     : function(data,type,Row,meta) {
                                return `<span class="badge badge-dot badge-${Row.new_status_color}">${Row.new_status ?? 'N/A'}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "requested_at",
                            name       : 'requested_at',
                            render     : function(data,type,Row,meta) {
                                return `<small dir="ltr">${Row.new_requested_at || Row.requested_at}</small>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "new_responded_at",
                            name       : 'new_responded_at',
                            render     : function(data,type,Row,meta) {
                                return `<small dir="ltr">${Row.new_responded_at || Row.new_responded_at || '---------'}</small>`;
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
                                                            ${(! Row.actions['rfq_read'] ? `` : `<li><a href="${Row.actions['rfq_read']['action']}"><em class="icon ni ni-eye"></em><span>${Row.actions['rfq_read']['label']}</span></a></li>`)}
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
            </script>
        </x-slot>
    @endcomponent
@endsection

@push('script')
    <script>
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