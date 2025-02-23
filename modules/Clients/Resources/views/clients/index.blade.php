@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('clients::strings.clients.list'),
        'items' => [
            [
                'title' => __('clients::strings.clients.menu'),
                'link'  => route('ClientsController@index')
            ],
            [
                'title' => __('clients::strings.clients.list'),
                'link'  => route('ClientsController@index')
            ]
        ],
    ]
])
    @if ($Service->can('create'))
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
                    <em class="icon ni ni-menu-alt-r"></em>
                </a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="{{ route('ClientsController@create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em><span>{{ __('clients::strings.clients.create') }}</span>
                            </a>
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
                'size'      => '',
                'fit'       => false,
            ]
        ])
            <div class="row">
                <div class="col-md-12">
                    @if (\Module::installedAndEnabled('Branches'))
                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'user_branch_id_select',
                                'jsSelect2'         => 'branchSelect2',
                                'view'              => 'INLINE',
                                'name'              => 'branch_id',
                                'label'             => __('branches::inputs.user_branch_id.label'),
                                'placeholder'       => __('branches::inputs.user_branch_id.placeholder'),
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
                    @endif
                    
                    @include('ui::components.select', [
                        'options' => [
                            'view'              => 'INLINE',
                            'name'              => 'type',
                            'label'             => __('admin::inputs.type.label'),
                            'placeholder'       => __('admin::inputs.type.placeholder'),
                            'description'       => __('admin::inputs.type.description'),
                            'attributes'        => [],
                            'value'             => '0',
                            'search'            => true,
                            'multiple'          => true,
                            'nullable'          => true,
                            'nullable_value'    => '0',
                            'options'           => $Roles,
                            'option_text'       => function($key, $option)                   { return $option->__('title', app()->getLocale()); },
                            'option_value'      => function($key, $option)                   { return $option->name; },
                            'option_attributes' => function($key, $option)                   { return []; },
                            'selected'          => function($options, $key, $option, $value) { return $option->name == $value; },
                            'disabled'          => function($options, $key, $option, $value) { return false; },
                        ],
                    ])
                </div>
            </div>
            <hr class="preview-hr">
            <div class="row">
                <div class="col-md-12">

                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'name',
                            'label'       => __('admin::inputs.name.label'),
                            'description' => __('admin::inputs.name.description'),
                        ],
                    ])

                    {{-- @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'username',
                            'label'       => __('admin::inputs.username.label'),
                            'description' => __('admin::inputs.username.description'),
                        ],
                    ]) --}}

                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'email',
                            'label'       => __('admin::inputs.email.label'),
                            'description' => __('admin::inputs.email.description'),
                        ],
                    ])

                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'phone_number',
                            'label'       => __('admin::inputs.phone_number.label'),
                            'description' => __('admin::inputs.phone_number.description'),
                        ],
                    ])

                    <hr class="preview-hr">

                    <div class="row" id="vue-form">
                        <div class="col-md-12">
                            @include('ui::components.select', [
                                'options' => [
                                    'id'                => 'country_select',
                                    'jsSelect2'         => 'countryJsSelect2',
                                    'view'              => 'INLINE',
                                    'name'              => 'country_id',
                                    'label'             => __('systemcore::inputs.country.label'),
                                    'placeholder'       => __('systemcore::inputs.country.placeholder'),
                                    'attributes'        => [],
                                    'value'             => null,
                                    'search'            => true,
                                    'multiple'          => false,
                                    'options'           => [],
                                    'option_text'       => function($key, $option)                   { return $option['native']; },
                                    'option_value'      => function($key, $option)                   { return $key; },
                                    'option_attributes' => function($key, $option)                   { return []; },
                                    'selected'          => function($options, $key, $option, $value) { return $key == $value; },
                                    'disabled'          => function($options, $key, $option, $value) { return false; },
                                ],
                            ])

                            @include('ui::components.select', [
                                'options' => [
                                    'id'                => 'city_select',
                                    'jsSelect2'         => 'cityJsSelect2',
                                    'view'              => 'INLINE',
                                    'name'              => 'city_id',
                                    'label'             => __('systemcore::inputs.city.label'),
                                    'placeholder'       => __('systemcore::inputs.city.placeholder'),
                                    'attributes'        => [],
                                    'value'             => null,
                                    'search'            => true,
                                    'multiple'          => false,
                                    'options'           => [],
                                    'option_text'       => function($key, $option)                   { return $option['native']; },
                                    'option_value'      => function($key, $option)                   { return $key; },
                                    'option_attributes' => function($key, $option)                   { return []; },
                                    'selected'          => function($options, $key, $option, $value) { return $key == $value; },
                                    'disabled'          => function($options, $key, $option, $value) { return false; },
                                ],
                            ])
                        </div>
                    </div>
                </div>
            </div>
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
                ],
                'columns'      => [
                    [
                        'label' => '<span class="sub-text">' . __('ui::inputs.id.label') . '</span>', 
                        'class' => ['fit']
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('admin::inputs.name.label') . '</span>', 
                        'class' => []
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('admin::inputs.email.label') . '</span>', 
                        'class' => []
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('admin::inputs.phone_number.label') . '</span>', 
                        'class' => []
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('admin::inputs.username.label') . '</span>', 
                        'class' => []
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('branches::inputs.user_branch_id.label') . '</span>', 
                        'class' => []
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('admin::inputs.type.label') . '</span>', 
                        'class' => ['fit'],
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('admin::inputs.status.label') . '</span>', 
                        'class' => ['fit'],
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('ui::inputs.created_at.label') . '</span>',
                        'class' => ['fit'],
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('ui::inputs.updated_at.label') . '</span>',
                        'class' => [],
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
                        url  : "{!! route('ClientsController@postIndex') !!}",
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
                            visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<span class="text-danger">${Row.id}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : "name",
                            name       : 'name',
                            render     : function(data,type,Row,meta) {
                                let _img = '<span>AB</span>';
                                if (Row.avatar_src) {
                                    _img = `<img src="${Row.avatar_src}" alt="${Row.name}">`;
                                }
                                return `
                                    <div class="user-card">
                                        <div class="user-avatar sq sm bg-dark">
                                            ${_img}
                                        </div>
                                        <div class="user-name">
                                            <span>${Row.name}</span>
                                            <br>
                                            <span dir="ltr">${Row.email || '---------'}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "email",
                            name       : 'email',
                            render     : function(data,type,Row,meta) {
                                return `${Row.email || '-------' }`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "phone_number",
                            name       : 'phone_number',
                            visible    : true,
                            render     : function(data,type,Row,meta) {
                                return `<span dir="ltr">${Row.phone_number || '-------' }</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "username",
                            name       : 'username',
                            visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `${Row.username || '-------' }`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : {!! \Module::installedAndEnabled('Branches') ? '"branch_id"' : 'false' !!},
                            name       : {!! \Module::installedAndEnabled('Branches') ? '"branch_id"' : 'false' !!},
                            orderable  : {!! \Module::installedAndEnabled('Branches') ? 'true'        : 'false' !!},
                            searchable : {!! \Module::installedAndEnabled('Branches') ? 'true'        : 'false' !!},
                            visible    : {!! \Module::installedAndEnabled('Branches') ? 'true'        : 'false' !!},
                            render     : function(data,type,Row,meta) {
                                if (! Row.branch) {
                                    return '-------';
                                }
                                return `<span class="badge badge-dot badge-danger">${Row.branch ? Row.branch.title : '-------' }</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "type",
                            name       : 'type',
                            render     : function(data,type,Row,meta) {
                                return `<span class="badge badge-dot badge-warning">${Row.type ? Row.type.title : 'N/A'}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "sys_status",
                            name       : 'sys_status',
                            render     : function(data,type,Row,meta) {
                                if (Row.sys_status == 'ACTIVE') {
                                    return `<span class="badge badge-dot badge-success">{{ __('ui::inputs.status.options.ACTIVE') }}</span>`;
                                } 
                                else if (Row.sys_status == 'PENDING_REVIEW') {
                                    return `<span class="badge badge-dot badge-warning">{{ __('ui::inputs.status.options.PENDING_REVIEW') }}</span>`;
                                } 
                                else if (Row.sys_status == 'SUSPENDED') {
                                    return `<span class="badge badge-dot badge-danger">{{ __('ui::inputs.status.options.SUSPENDED') }}</span>`;
                                } 
                                return `<span class="badge badge-dot badge-danger">${Row.sys_status}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "ist_created_at",
                            name       : 'ist_created_at',
                            render     : function(data,type,Row,meta) {
                                return `<span>${Row.ist_created_at}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "updated_at",
                            name       : 'updated_at',
                            visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<span>${Row.updated_at}</span>`;
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
                                                            ${(! Row.actions['delete']      ? `` : `<li><a href="javascript:;" onclick="BS.ajaxDelete('{{ route('ClientsController@postDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete"></em><span>${Row.actions['delete']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['restore']     ? `` : `<li><a href="javascript:;" onclick="BS.ajaxRestore('{{ route('ClientsController@postRestore') }}', ${Row.id}, datatable)"><em class="icon ni ni-redo"></em><span>${Row.actions['restore']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['permaDelete'] ? `` : `<li><a href="javascript:;" onclick="BS.ajaxPermaDelete('{{ route('ClientsController@postPermaDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete text-danger"></em><span>${Row.actions['permaDelete']['label']}</span></a></li>`)}
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
    @if (\Module::installedAndEnabled('Branches'))
        <script>
            var jsSelect2 = $('#user_branch_id_select').select2({
                ajax: {
                    delay: 250,
                    url: function (params) {
                        return '{{ route('BranchesController@select') }}';
                    },
                    data: function (params) {
                        var query = {
                            search      : params.term,
                            type_in     : ['BRANCH'],
                            page        : params.page || 1
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return data;
                    }
                }
            })
            .on("select2:select", function() {});
        </script>
    @endif

    <script>
        const CrudApp = new Vue({
            el: '#vue-form',
            data: {
                country : null,
                city    : null,
            }
        });
    </script>
    <script>

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
            CrudApp.country = _data?.id ? _data : null;
        });

        var cityJsSelect2 = $('#city_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('CitiesController@select') }}';
                },
                data: function (params) {
                    var query = {
                        nullable    : 'true',
                        search      : params.term,
                        page        : params.page || 1,
                        country_id  : CrudApp.country?.id || null
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
            // CrudApp.country = _data?.id ? _data : null;
        });

        var categoriesSelect2 = $('#categories_select').select2({
            tags: true,
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('ContentController@select', ['type' => 'business-categories']) }}';
                },
                data: function (params) {
                    var query = {
                        search      : params.term,
                        page        : params.page || 1,
                        type        : 'business-categories'
                    }
                    return query;
                },
                processResults: function (data) {
                    return data;
                }
            }
        })
        .on("select2:select", function() {

        });
    </script>
@endpush