@php
    $options = array_merge([
        'title'     => __('ui::strings.dashboard'),
        'items'     => [
            // [
            //     'title' => 'Breadcrump Label',
            //     'link'  => 'javascript:;',
            // ]
        ],
    ], isset($options) ? $options : [])
@endphp
@section('breadcrumb')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <div class="nk-block-head-sub">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item">
                                <a href="{{ route('AdminDashboardController@index') }}">
                                    <em class="ti ti-home"></em> {{ __('ui::strings.system_home') }}
                                </a>
                            </li>
                            @foreach ($options['items'] as $item)
                                @if ($loop->first)
                                    @push('meta.title')
                                        {{ $item['title'] }} | 
                                    @endpush
                                @endif
                                {{-- @push('meta.title')
                                    {{ $item['title'] }} | 
                                @endpush --}}
                                @if( request()->url() == $item['link'] )
                                    <li class="breadcrumb-item active">{{ $item['title'] }}</li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ $item['link'] }}">{{ $item['title'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </nav>
                </div>
                <h2 class="nk-block-title fw-normal">
                    {{ $options['title'] }}
                </h2>
            </div>
            <div class="nk-block-head-content">
                <h2 class="nk-block-title fw-normal">
                    <a href="#" class="btn btn-custom-1">{{__('contentmanager::main.communicate_to_contract')}}</a>
                    <a href="#" class="btn btn-custom-2">{{__('contentmanager::main.add_new_contract')}}</a>
                </h2>
            </div>
            {{ $slot }}
        </div>
    </div>
@endsection
