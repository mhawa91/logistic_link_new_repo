@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.contracts.list'),
        'items' => [
            [
                'title' => __('contenttypes::strings.contracts.menu'),
                'link'  => route('TransportersController@index')
            ],
            [
                'title' => __('contenttypes::strings.contracts.list'),
                'link'  => route('TransportersController@index')
            ]
        ],
    ]
])
    @if ($Service->can('create'))
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="javascript:;" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
                    <em class="icon ni ni-menu-alt-r"></em>
                </a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="{{ route('TransportersController@create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em><span>{{ __('contenttypes::strings.transporters.create') }}</span>
                            </a>
                        </li>
                        {{-- <li class="nk-block-tools-opt">
                            <a href="#" class="btn btn-white btn-outline-light">
                                <em class="icon ni ni-download-cloud"></em><span>Export</span>
                            </a>
                        </li> --}}
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
            @if(auth()->check() && (auth()->user()->type == 'DEFAULT' || ! in_array(auth()->user()->type, ['TRANSPORTER', 'DEFAULT'])))
                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'transportar_name',
                        'label'       => __('contenttypes::inputs.transportar_name.label'),
                        'placeholder' => __('contenttypes::inputs.transportar_name.placeholder'),
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'transportar_email',
                        'label'       => __('contenttypes::inputs.transportar_email.label'),
                        'placeholder' => __('contenttypes::inputs.transportar_email.placeholder'),
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'transportar_phone_number',
                        'label'       => __('contenttypes::inputs.transportar_phone_number.label'),
                        'placeholder' => __('contenttypes::inputs.transportar_phone_number.placeholder'),
                    ],
                ])
            @endif
            
            @if(auth()->check() && (auth()->user()->type == 'TRANSPORTER' || ! in_array(auth()->user()->type, ['TRANSPORTER', 'DEFAULT'])))
                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'client_name',
                        'label'       => __('contenttypes::inputs.client_name.label'),
                        'placeholder' => __('contenttypes::inputs.client_name.placeholder'),
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'client_email',
                        'label'       => __('contenttypes::inputs.client_email.label'),
                        'placeholder' => __('contenttypes::inputs.client_email.placeholder'),
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'client_phone_number',
                        'label'       => __('contenttypes::inputs.client_phone_number.label'),
                        'placeholder' => __('contenttypes::inputs.client_phone_number.placeholder'),
                    ],
                ])
            @endif

            <hr class="preview-hr">

            @component('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'daterange',
                    'name'        => 'contract_daterange',
                    'label'       => __('contenttypes::inputs.start_end_date.label'),
                    'placeholder' => __('contenttypes::inputs.start_end_date.placeholder'),
                    'description' => __('contenttypes::inputs.start_end_date.description'),
                    'attributes'  => []
                ],
            ])
            @endcomponent
            
            <hr class="preview-hr">

            @include('ui::components.radios', [
                'options' => [
                    'view'              => 'INLINE',
                    'type'              => 'radio',
                    'name'              => 'contract_status',
                    'label'             => __('contenttypes::inputs.contract_status.description'),
                    'placeholder'       => __('contenttypes::inputs.contract_status.placeholder'),
                    // 'description'       => __('contenttypes::inputs.contract_status.description'),
                    'attributes'        => [],
                    'value'             => null,
                    'search'            => true,
                    'multiple'          => false,
                    'options'           => [
                        [
                            'id'       => 'cs_1',
                            'value'    => '',
                            'label'    => __('ui::inputs.options.ALL'),
                            'selected' => true,
                        ],
                        [
                            'id'       => 'cs_2',
                            'value'    => 'ACTIVE',
                            'label'    => __('contenttypes::inputs.contract_status.options.ACTIVE'),
                            'selected' => false,
                        ],
                        [
                            'id'       => 'cs_3',
                            'value'    => 'EXPIRED',
                            'label'    => __('contenttypes::inputs.contract_status.options.EXPIRED'),
                            'selected' => false,
                        ],
                    ],
                    'option_id'         => function($key, $option)                   { return $option['id']; },
                    'option_text'       => function($key, $option)                   { return $option['label']; },
                    'option_sub_text'   => function($key, $option)                   { return null; },
                    'option_value'      => function($key, $option)                   { return $option['value']; },
                    'option_attributes' => function($key, $option)                   { return []; },
                    'selected'          => function($options, $key, $option, $value) { return $option['selected']; },
                    'disabled'          => function($options, $key, $option, $value) { return false; },
                ],
            ])

            @include('ui::components.radios', [
                'options' => [
                    'view'              => 'INLINE',
                    'type'              => 'radio',
                    'name'              => 'sys_status',
                    'label'             => __('admin::inputs.status.label'),
                    'placeholder'       => __('admin::inputs.status.placeholder'),
                    'description'       => __('admin::inputs.status.description'),
                    'attributes'        => [],
                    'value'             => null,
                    'search'            => true,
                    'multiple'          => false,
                    'options'           => [
                        [
                            'id'       => 's_1',
                            'value'    => '',
                            'label'    => __('ui::inputs.options.ALL'),
                            'selected' => true,
                        ],
                        [
                            'id'       => 's_2',
                            'value'    => 'ACTIVE',
                            'label'    => __('ui::inputs.status.options.ACTIVE'),
                            'selected' => false,
                        ],
                        [
                            'id'       => 's_3',
                            'value'    => 'DISABLED',
                            'label'    => __('ui::inputs.status.options.DISABLED'),
                            'selected' => false,
                        ],
                    ],
                    'option_id'         => function($key, $option)                   { return $option['id']; },
                    'option_text'       => function($key, $option)                   { return $option['label']; },
                    'option_sub_text'   => function($key, $option)                   { return null; },
                    'option_value'      => function($key, $option)                   { return $option['value']; },
                    'option_attributes' => function($key, $option)                   { return []; },
                    'selected'          => function($options, $key, $option, $value) { return $option['selected']; },
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
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.start_date.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.end_date.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.contract_status.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('admin::inputs.status.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('ui::inputs.created_at.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '<span class="sub-text">' . __('ui::inputs.updated_at.label') . '</span>',
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
                        url  : "{!! route('TransportersController@postIndex') !!}",
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
                            data       : "company_user_id",
                            name       : 'company_user_id',
                            orderable  : true,
                            searchable : false,
                            visible    : {{ auth()->check() && (auth()->user()->type == 'TRANSPORTER' || ! in_array(auth()->user()->type, ['TRANSPORTER', 'DEFAULT'])) ? 'true' : 'false' }},
                            render     : function(data,type,Row,meta) {
                                if (! Row.client) {
                                    return `<span class="text-danger">N/A</span>`;
                                }
                                let _img = '<span>AB</span>';
                                if (Row.client_avatar) {
                                    _img = `<img src="${Row.client_avatar}" alt="${Row.client.name}">`;
                                }
                                return `
                                    <div class="user-card">
                                        <div class="user-avatar sq sm bg-dark">
                                            ${_img}
                                        </div>
                                        <div class="user-name">
                                            <span>${Row.client.company?.company_name || Row.client.name}</span>
                                            <br>
                                            <span dir="ltr">${Row.client.company?.company_email || Row.client.email || '---------'}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : "transporter_user_id",
                            name       : 'transporter_user_id',
                            orderable  : true,
                            searchable : false,
                            visible    : {{ auth()->check() && (auth()->user()->type == 'DEFAULT' || ! in_array(auth()->user()->type, ['TRANSPORTER', 'DEFAULT'])) ? 'true' : 'false' }},
                            render     : function(data,type,Row,meta) {
                                if (! Row.transporter) {
                                    return `<span class="text-danger">N/A</span>`;
                                }
                                let _img = '<span>AB</span>';
                                if (Row.transporter_avatar) {
                                    _img = `<img src="${Row.transporter_avatar}" alt="${Row.transporter.name}">`;
                                }
                                return `
                                    <div class="user-card">
                                        <div class="user-avatar sq sm bg-dark">
                                            ${_img}
                                        </div>
                                        <div class="user-name">
                                            <span>${Row.transporter.company?.company_name || Row.transporter.name}</span>
                                            <br>
                                            <span dir="ltr">${Row.transporter.company?.company_email || Row.transporter.email || '---------'}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "order_start_date",
                            name       : 'order_start_date',
                            render     : function(data,type,Row,meta) {
                                return `<span dir="ltr" class="text-warning">${Row.new_order_start_date || Row.order_start_date}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "order_end_date",
                            name       : 'order_end_date',
                            render     : function(data,type,Row,meta) {
                                return `<span dir="ltr" class="text-danger">${Row.new_order_end_date || Row.order_end_date}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "new_status_by_date",
                            name       : 'new_status_by_date',
                            render     : function(data,type,Row,meta) {
                                return `<span class="badge badge-dot badge-${Row.new_status_by_date_color}">${Row.new_status_by_date ?? 'N/A'}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "new_status",
                            name       : 'new_status',
                            render     : function(data,type,Row,meta) {
                                return `<span class="badge badge-dot badge-${Row.new_status_color}">${Row.new_status ?? 'N/A'}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "created_at",
                            name       : 'created_at',
                            render     : function(data,type,Row,meta) {
                                return `<span dir="ltr">${Row.new_created_at || Row.created_at}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "updated_at",
                            name       : 'updated_at',
                            visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<span dir="ltr">${Row.new_updated_at || Row.updated_at}</span>`;
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
                                                            ${(! Row.actions['update']      ? `` : `<li><a href="${Row.actions['update']['action']}"><em class="icon ni ni-update"></em><span>${Row.actions['update']['label']}</span></a></li>`)}
                                                            <li class="divider"></li>
                                                            ${(! Row.actions['delete']      ? `` : `<li><a href="javascript:;" onclick="BS.ajaxDelete('{{ route('TransportersController@postDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete"></em><span>${Row.actions['delete']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['restore']     ? `` : `<li><a href="javascript:;" onclick="BS.ajaxRestore('{{ route('TransportersController@postRestore') }}', ${Row.id}, datatable)"><em class="icon ni ni-redo"></em><span>${Row.actions['restore']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['permaDelete'] ? `` : `<li><a href="javascript:;" onclick="BS.ajaxPermaDelete('{{ route('TransportersController@postPermaDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete text-danger"></em><span>${Row.actions['permaDelete']['label']}</span></a></li>`)}
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
            var form = $('<form action="{{ route('TransportersController@postExport') }}" method="POST" style="display: none;"></form>');

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