@extends('ui::layouts.auth')

@push('meta.title')
    {{ __('ui::strings.signup') }} | 
@endpush

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">{{ __('ui::strings.signup_confirm') }}</h5>
            <div class="nk-block-des">
                <p>{{ __('ui::strings.signup_confirm_note') }}</p>
            </div>
        </div>
    </div><!-- .nk-block-head -->
    <form action="{{ route('AuthController@postConfirmRegister') }}" onsubmit="BS.AJAX_FORM(event)" data-keep-loading-on-success="true" method="POST">
        @csrf

        <input type="hidden" name="hash" value="{{ $hash }}">
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="otp_code">{{ __('ui::inputs.otp_code.label') }}</label>
            </div>
            <input type="text" name="code" class="form-control form-control-lg" id="otp_code" placeholder="{{ __('ui::inputs.otp_code.placeholder') }}">
        </div>

        <div class="form-group">
            {{-- <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('ui::strings.signup') }}</button> --}}

            <button class="btn btn-lg btn-primary btn-block" type="submit">
                <span> {{ __('ui::strings.confirm') }}</span>
            </button>
        </div>
    </form><!-- form -->
    <div class="form-note-s2 pt-4"> 
        {!! __('ui::strings.back_to', ['page' => '<a href="'. route('AuthController@login') .'">'. __('ui::strings.signin') .'</a>']) !!}
    </div>
@endsection