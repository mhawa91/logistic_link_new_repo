@php
    $OPTIONS = array_merge([
        'callable'     => null,
        'id'           => null,
        'jsDatatable'  => '',
        'searchModal'  => null,
        'attributes'   => [],
        'class'        => [],
        'size'         => 'compact',
        'columns'      => [],
        'rows'         => [],
        'tools'        => [],
        'scroll'       => false,
        'scroll_height'=> '490px',
    ], isset($options) ? $options : []);
    $CLASSES = implode(' ', $OPTIONS['class']);

    $ATTRIBUTES    = '';
    foreach ($OPTIONS['attributes'] as $key => $value) {
        $ATTRIBUTES .= ' ' . $key . '="' . $value . '"';
    }

    foreach ($OPTIONS['columns'] as $key => $value) {
        $OPTIONS['columns'][ $key ] = array_merge([
            'label' => '', 
            'class' => []
        ], $value);
    }

    $OPTIONS['tools'] = array_merge([
        'search'    => true,
        'available' => true,
        'trashed'   => true,
        'refresh'   => true,
        'export'    => true,
    ], $OPTIONS['tools']);
@endphp

<div class="card card-bordered card-stretch" id="{{ $OPTIONS['callable'] }}" style="{!! $OPTIONS['scroll'] ? 'overflow-y: scroll; height: ' . $OPTIONS['scroll_height'] .';' : '' !!}">
    <div class="card-inner-group">
        <div class="card-inner position-relative card-tools-toggle">
            @isset($title)
                {!! $title !!}
            @endisset
            <div class="card-title-group">
                <div class="card-tools">
                    {{-- <div class="btn-wrap">
                        <span class="d-none d-md-block">
                            <button class="btn btn-sm btn-dim btn-outline-light">
                                <em class="fas fa-sync m-1"></em> Refresh
                            </button>
                        </span>
                        <span class="d-md-none">
                            <button class="btn btn-dim btn-outline-light btn-icon">
                                <em class="icon fas fa-sync"></em>
                            </button>
                        </span>
                    </div> --}}

                    @if (isset($headerTools))
                        {!! $headerTools !!}
                    @endif
                </div>
                @if (in_array(true, $OPTIONS['tools']) || $OPTIONS['searchModal'])
                    <div class="card-tools mr-n1">
                        <ul class="btn-toolbar gx-1">
                            @if ($OPTIONS['tools']['search'])
                                <li>
                                    <a href="javascript:;" class="btn btn-icon search-toggle toggle-search" data-target="search" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.search') }}">
                                        <em class="icon ni ni-search"></em>
                                    </a>
                                </li>
                            @endif
                            @if ($OPTIONS['searchModal'])
                                <li>
                                    <div data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.advanced_filter') }}">
                                        <a href="javascript:;" class="btn btn-trigger btn-icon" data-toggle="modal" data-target="#{{ $OPTIONS['searchModal'] }}">
                                            <em class="icon ni ni-filter-alt"></em>
                                        </a>
                                    </div>
                                </li>
                            @endif
                            @if ($OPTIONS['tools']['search'] || $OPTIONS['searchModal'])
                                <li class="btn-toolbar-sep"></li>
                            @endif
                            @if (in_array(true, $OPTIONS['tools']))
                                <li>
                                    <div class="toggle-wrap">
                                        <a href="javascript:;" class="btn btn-icon btn-trigger toggle" data-target="cardTools">
                                            <em class="icon ni ni-menu-right"></em>
                                        </a>
                                        <div class="toggle-content" data-content="cardTools">
                                            <ul class="btn-toolbar gx-1">
                                                <li class="toggle-close">
                                                    <a href="javascript:;" class="btn btn-icon btn-trigger toggle" data-target="cardTools">
                                                        <em class="icon ni ni-arrow-left"></em>
                                                    </a>
                                                </li>
                                                @if ($OPTIONS['tools']['available'])
                                                    <li>
                                                        <a href="javascript:;" onclick="{{ $OPTIONS['callable'] }}Instance.setRecordsType('AVAILABLE')" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.available') }}">
                                                            <em class="icon ni ni-list"></em>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($OPTIONS['tools']['trashed'])
                                                    <li>
                                                        <a href="javascript:;" onclick="{{ $OPTIONS['callable'] }}Instance.setRecordsType('TRASHED')" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.trashed') }}">
                                                            <em class="icon ni ni-trash"></em>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($OPTIONS['tools']['refresh'])
                                                    <li>
                                                        <a href="javascript:;" onclick="BS.reloadDatatable({{ $OPTIONS['jsDatatable'] }})" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.refresh') }}">
                                                            <em class="icon ni ni-reload"></em>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($OPTIONS['tools']['export'])
                                                    <li>
                                                        <a href="javascript:;" onclick="{!! $OPTIONS['tools']['export'] !!}" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.export') }}">
                                                            <em class="icon ni ni-download"></em>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
            @if ($OPTIONS['tools']['search'])
                <div class="card-search search-wrap" data-search="search">
                    <div class="card-body">
                        <div class="search-content">
                            <a href="javascript:;" class="search-back btn btn-icon toggle-search datatable-search-cancel" data-target="search">
                                <em class="icon ni ni-arrow-left"></em>
                            </a>
                            <input type="text" class="form-control border-transparent form-focus-none datatable-search-input" placeholder="{{ __('ui::strings.search_for_somthing') }}">
                            <button class="search-submit btn btn-icon datatable-search-button">
                                <em class="icon ni ni-search"></em>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-inner p-0 pb-3 bs-datatable-card table-responsive">
            @if (isset($before))
                {!! $before !!}
            @endif
            <table
                @if($OPTIONS['id']) id="{{ $OPTIONS['id'] }}" @endif 
                class="bs-datatable bs-hidden-search nowrap nk-tb-list nk-tb-ulist table-responsive {!! $CLASSES !!} {{ $OPTIONS['size'] == 'compact' ? 'is-compact' : '' }}" 
                data-auto-responsive="false" 
                {!! $ATTRIBUTES !!}>
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        @foreach ($OPTIONS['columns'] as $key => $value)
                            <th class="nk-tb-col {!! implode(' ', $value['class']) !!}">{!! $value['label'] !!}</th>
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('script')
    <script>
        var {{ $OPTIONS['callable'] }} = function() {
            var self           = this;
            this.recordsType   = 'AVAILABLE';
            this.searchTimeout = null;
            $('#{{ $OPTIONS['callable'] }}').on('keyup', '.datatable-search-input', function(e) {
                self.datatableKeywordSearch($(this));
            });
            $('#{{ $OPTIONS['callable'] }}').on('click', '.datatable-search-button', function(e) {
                self.datatableKeywordSearch($('#{{ $OPTIONS['callable'] }}').find('.datatable-search-input'));
            });
            $('#{{ $OPTIONS['callable'] }}').on('click', '.datatable-search-cancel', function(e) {
                $('#{{ $OPTIONS['callable'] }}').find('.datatable-search-input').val('');
                self.datatableKeywordSearch($('#{{ $OPTIONS['callable'] }}').find('.datatable-search-input'));
            });
        };
        {{ $OPTIONS['callable'] }}.prototype.datatableKeywordSearch = function(elem) {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(() => {
                BS.resetForm(null, $('#{{ $OPTIONS['searchModal'] ?? 'advanced-dd-search-modal' }}').find('form'));
                {{ $OPTIONS['jsDatatable'] }}.search( $(elem).val() ).draw();
            }, 500);
        };
        {{ $OPTIONS['callable'] }}.prototype.setRecordsType = function(status) {
            this.recordsType = status;
            {{ $OPTIONS['jsDatatable'] }}.ajax.reload();
        };
        {{ $OPTIONS['callable'] }}.prototype.getSearchValue = function() {
            return $('#{{ $OPTIONS['callable'] }}').find('.datatable-search-input').val();
        };
        {{ $OPTIONS['callable'] }}.prototype.getRecordsType = function() {
            return this.recordsType;
        };
        var {{ $OPTIONS['callable'] }}Instance = new {{ $OPTIONS['callable'] }};
        BS.REGISTER('{{ $OPTIONS['callable'] }}', {{ $OPTIONS['callable'] }}Instance);
    </script>
    @if (isset($script))
        {!! $script !!}
    @endif
@endpush
