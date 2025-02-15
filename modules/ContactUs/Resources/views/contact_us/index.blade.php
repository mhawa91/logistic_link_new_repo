@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contactus::strings.contact_us.list'),
        'items' => [
            [
                'title' => __('contactus::strings.contact_us.menu'),
                'link'  => route('ContactUsController@index')
            ],
            [
                'title' => __('contactus::strings.contact_us.list'),
                'link'  => route('ContactUsController@index')
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
                    'type'        => 'text',
                    'name'        => 'name',
                    'label'       => __('contactus::inputs.name.label'),
                    'placeholder' => '',
                    'description' => __('contactus::inputs.name.description'),
                ],
            ])
            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'text',
                    'name'        => 'phone_number',
                    'label'       => __('contactus::inputs.phone_number.label'),
                    'placeholder' => '',
                    'description' => __('contactus::inputs.phone_number.description'),
                ],
            ])
            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'text',
                    'name'        => 'email',
                    'label'       => __('contactus::inputs.email.label'),
                    'placeholder' => '',
                    'description' => __('contactus::inputs.email.description'),
                ],
            ])
            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'text',
                    'name'        => 'subject',
                    'label'       => __('contactus::inputs.subject.label'),
                    'placeholder' => '',
                    'description' => __('contactus::inputs.subject.description'),
                ],
            ])
            <hr class="preview-hr">
            @include('ui::components.radios', [
                'options' => [
                    'view'              => 'INLINE',
                    'type'              => 'radio',
                    'name'              => 'sys_status',
                    'label'             => __('contactus::inputs.status.label'),
                    'placeholder'       => __('contactus::inputs.status.placeholder'),
                    'description'       => __('contactus::inputs.status.description'),
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
                            'value'    => 'PENDING',
                            'label'    => __('contactus::inputs.status.options.PENDING'),
                            'selected' => false,
                        ],
                        [
                            'id'       => 's_3',
                            'value'    => 'REVIEWED',
                            'label'    => __('contactus::inputs.status.options.REVIEWED'),
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

    @component('ui::components.modal', [
        'options' => [
            'id'        => 'vue-details-form',
            'title'     => __('contactus::strings.contact_us.read'),
            'size'      => 'lg',
            'fit'       => true,
        ]
    ])

        <div class="text-center m-5" v-if="is_loading">
            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div>
        </div>

        <ul class="data-list is-compact" v-if="!is_loading && contact_request">
            <li class="data-item">
                <div class="data-col">
                    <div class="data-label" style="width: 25%">{{ __('contactus::inputs.created_at.label') }}</div>
                    <div class="data-value">@{{ contact_request.created_at }}</div>
                </div>
            </li>
            <li class="data-item">
                <div class="data-col">
                    <div class="data-label" style="width: 25%">{{ __('contactus::inputs.status.label') }}</div>
                    <div class="data-value">
                        @include('ui::components.radios', [
                            'options' => [
                                'view'              => 'TABLE',
                                'type'              => 'radio',
                                'name'              => 'sys_status',
                                'label'             => __('admin::inputs.status.label'),
                                'placeholder'       => __('admin::inputs.status.placeholder'),
                                'description'       => __('admin::inputs.status.description'),
                                'value'             => null,
                                'search'            => true,
                                'multiple'          => false,
                                'attributes'        => [],
                                'options'           => [
                                    [
                                        'id'   => 'PENDING',
                                        'text' => __('contactus::inputs.status.options.PENDING'),
                                    ],
                                    [
                                        'id'   => 'REVIEWED',
                                        'text' => __('contactus::inputs.status.options.REVIEWED'),
                                    ]
                                ],
                                'option_text'       => function($key, $option)                   { return $option['text']; },
                                'option_value'      => function($key, $option)                   { return $option['id']; },
                                'option_attributes' => function($key, $option)                   { 
                                    return [
                                        'v-model' => 'contact_request.sys_status',
                                        ':cid'    => 'contact_request.id',
                                    ]; 
                                },
                                'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])
                    </div>
                </div>
            </li>
            <li class="data-item">
                <div class="data-col">
                    <div class="data-label" style="width: 25%">{{ __('contactus::inputs.name.label') }}</div>
                    <div class="data-value">@{{ contact_request.name }}</div>
                </div>
            </li>
            <li class="data-item">
                <div class="data-col">
                    <div class="data-label" style="width: 25%">{{ __('contactus::inputs.email.label') }}</div>
                    <div class="data-value" dir="ltr">@{{ contact_request.email }}</div>
                </div>
            </li>
            <li class="data-item">
                <div class="data-col">
                    <div class="data-label" style="width: 25%">{{ __('contactus::inputs.phone_number.label') }}</div>
                    <div class="data-value" dir="ltr">@{{ contact_request.phone_number }}</div>
                </div>
            </li>
            <li class="data-item">
                <div class="data-col">
                    <div class="data-label" style="width: 25%">{{ __('contactus::inputs.subject.label') }}</div>
                    <div class="data-value">@{{ contact_request.subject }}</div>
                </div>
            </li>
            <li class="data-item">
                <div class="data-col">
                    <div class="data-label" style="width: 25%">{{ __('contactus::inputs.message.label') }}</div>
                    <div class="data-value" v-html="contact_request.message"></div>
                </div>
            </li>
        </ul>
    
        <x-slot name="footer">
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger" onclick="BS.resetForm(this); datatable.ajax.reload();">
                <span>{{ __('contactus::strings.close') }}</span>
            </button>
        </x-slot>
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
            ],
            'columns'      => [
                [
                    'label' => '<span class="sub-text">' . __('ui::inputs.id.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contactus::inputs.name.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contactus::inputs.email.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contactus::inputs.phone_number.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contactus::inputs.subject.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contactus::inputs.status.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contactus::inputs.created_at.label') . '</span>',
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
                        url  : "{!! route('ContactUsController@postIndex') !!}",
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
                    order        :  [
                        [6, 'desc']
                    ],
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
                            data       : 'name',
                            name       : 'name',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `${Row.name || 'N/A'}`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'email',
                            name       : 'email',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<span dir="ltr">${Row.email || 'N/A'}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'phone_number',
                            name       : 'phone_number',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<span dir="ltr">${Row.phone_number || 'N/A'}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'subject',
                            name       : 'subject',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `${Row.subject}`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "sys_status",
                            name       : 'sys_status',
                            render     : function(data,type,Row,meta) {
                                if (Row.sys_status == 'PENDING') {
                                    return `<span class="badge badge-dot badge-warning">{{ __('contactus::inputs.status.options.PENDING') }}</span>`;
                                } 
                                else if (Row.sys_status == 'REVIEWED') {
                                    return `<span class="badge badge-dot badge-success">{{ __('contactus::inputs.status.options.REVIEWED') }}</span>`;
                                } 
                                return `<span class="badge badge-dot badge-danger">${Row.sys_status}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "created_at",
                            name       : 'created_at',
                            render     : function(data,type,Row,meta) {
                                return `<span>${Row.created_at}</span>`;
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
                                                            <li><a href="javascript:;" data-toggle="modal" data-target="#vue-details-form" onclick="loadContactRequest(${Row.id})"><em class="icon ni ni-eye"></em><span>{{ __('contactus::strings.contact_us.read') }}</span></a></li>
                                                            <li class="divider"></li>
                                                            ${(! Row.actions['delete']      ? `` : `<li><a href="javascript:;" onclick="BS.ajaxDelete('{{ route('ContactUsController@postDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete"></em><span>${Row.actions['delete']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['restore']     ? `` : `<li><a href="javascript:;" onclick="BS.ajaxRestore('{{ route('ContactUsController@postRestore') }}', ${Row.id}, datatable)"><em class="icon ni ni-redo"></em><span>${Row.actions['restore']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['permaDelete'] ? `` : `<li><a href="javascript:;" onclick="BS.ajaxPermaDelete('{{ route('ContactUsController@postPermaDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete text-danger"></em><span>${Row.actions['permaDelete']['label']}</span></a></li>`)}
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
        const CrudApp = new Vue({
            el: '#vue-details-form',
            data: () => {
                return {
                    is_loading      : false,
                    contact_request : null,
                }
            },
            methods: {
                getDetails: function(CONTACT_REQUEST_ID) {
                    let vm = this;
                    this.contact_request = null;
                    this.is_loading      = true;
                    $.post('{{ route('ContactUsController@read') }}/' + CONTACT_REQUEST_ID, {_token: '{{ csrf_token() }}'}, function(response) {
                        console.log(response)
                        vm.contact_request = response;
                        vm.is_loading      = false;
                    })
                }
            }
        });
        function loadContactRequest(CONTACT_REQUEST_ID) {
            CrudApp.getDetails(CONTACT_REQUEST_ID)
        }
        $(document).on('change', '[name=sys_status]', function() {
            $.post('{{ route('ContactUsController@postStatus') }}', {
                _token: '{{ csrf_token() }}', 
                model : $(this).attr('cid'),
                status: $(this).val(),
            }, function(data) {
                if (data['redirect_url'] || data['redirect']) {
                    window.location = data['redirect_url'] || data['redirect'];
                    return;
                }
                if (data['toastr']) {
                    NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                }
                BS.reloadDatatable(datatable);
            })
        });
    </script>
@endpush