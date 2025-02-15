@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contentmanager::strings.sections.update'),
        'items' => [
            [
                'title' => __('contentmanager::strings.sections.menu'),
                'link'  => route('SectionController@update')
            ],
            [
                'title' => __('contentmanager::strings.sections.update'),
                'link'  => route('SectionController@update')
            ]
        ],
    ]
])
@endcomponent

@section('content')

<div class="nk-block nk-block-lg" id="vue-form">
    <div class="card card-bordered">
        <div class="card-inner">

            @component('ui::components.form', [
                'options' => [
                    'id'                => null,
                    'action'            => route('SectionController@postUpdate'),
                    'method'            => 'POST',
                    'class'             => '',
                    'attributes'        => [
                        'onsubmit'                     => 'BS.AJAX_FORM(event)',
                        'data-keep-loading-on-success' => 'false',
                    ],
                ],
            ])

                <ul class="nav nav-tabs">
                    @foreach ($_ALL_LOCALES_ as $locale => $item)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#locale_{{ $locale }}">
                                <img src="{{ asset( config('ui.logos.' . $locale . '.flag.publicPath') ) }}" alt="" class="language-flag" style="border-radius: 30%;">
                                <span>{{ $item['native'] }}</span>
                            </a>
                        </li>
                    @endforeach                    
                </ul>
                <div class="tab-content">
                    @foreach ($_ALL_LOCALES_ as $locale => $item)
                    
                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="locale_{{ $locale }}">
                            <div id="accordion-{{ $locale }}" class="accordion">
                                @foreach ($inputs as $key => $item)
                                    <div class="accordion-item">
                                        <a href="javascript:;" class="accordion-head" data-toggle="collapse" data-target="#accordion-item-{{ $locale }}-{{ $key }}">
                                            <h6 class="title">
                                                {!! $item['title'] !!}
                                                @if ($item['description'])
                                                    <br>
                                                    <small class="text-muted">{!! $item['description'] !!}</small>
                                                @endif
                                            </h6>
                                            <span class="accordion-icon"></span>
                                        </a>
                                        <div class="accordion-body collapse {{ $loop->first ? 'show' : '' }}" id="accordion-item-{{ $locale }}-{{ $key }}" data-parent="#accordion-{{ $locale }}">
                                            <div class="accordion-inner">
                                                {!! $item['form']( $locale, $service->ofTranslated($item['tag'], null) ) !!}
                                            </div>
                                        </div>
                                    </div>
                                    
                                @endforeach
                            </div>  
                        </div>
                    @endforeach  
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-md btn-primary">
                        <span>{{ __('ui::strings.save') }}</span>
                    </button>
                </div>
            @endcomponent

        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        const CrudApp = new Vue({
            el: '#vue-form',
        });
    </script>
@endpush