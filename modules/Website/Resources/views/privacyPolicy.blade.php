@extends('website::pages-layout', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@section('content')
     <section class="section section-feature">
        <div class="container">
            @if($Sections->byKey('privacy-policy', 'title') && $Sections->byKey('privacy-policy', 'content'))
                <h2 class="title title-lg text-center mb-5">
                    {{ $Sections->byKey('privacy-policy', 'title') }}
                </h2>
                <div>{!! $Sections->byKey('privacy-policy', 'content') !!}</div>
            @endif
        </div>
    </section>
@endsection