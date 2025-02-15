@extends('ui::layouts.auth')

@push('meta.title')
    {{ __('ui::strings.signup') }} | 
@endpush

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">{{ __('ui::strings.signup') }}</h5>
            <div class="nk-block-des">
                <p>{{ __('ui::strings.signup_note') }}</p>
            </div>
        </div>
    </div><!-- .nk-block-head -->
    <form action="{{ route('AuthController@postRegister') }}" onsubmit="BS.AJAX_FORM(event)" data-keep-loading-on-success="true" method="POST">
        @csrf
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="full-name">{{ __('ui::inputs.full_name.label') }}</label>
            </div>
            <input type="text" name="full_name" class="form-control form-control-lg" id="full-name" placeholder="{{ __('ui::inputs.full_name.placeholder') }}">
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="default-01">{{ __('ui::inputs.your_email.label') }}</label>
            </div>
            <input type="text" name="email" class="form-control form-control-lg" id="default-01" placeholder="{{ __('ui::inputs.your_email.placeholder') }}">
        </div>
        
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="your_password">{{ __('ui::inputs.your_password.label') }}</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="javascript:;" class="form-icon form-icon-right passcode-switch lg" data-target="your_password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password" class="form-control form-control-lg" id="your_password" placeholder="{{ __('ui::inputs.your_password.placeholder') }}">
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="your_password_confirmation">{{ __('ui::inputs.your_password_confirmation.label') }}</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="javascript:;" class="form-icon form-icon-right passcode-switch lg" data-target="your_password_confirmation">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password_confirmation" class="form-control form-control-lg" id="your_password_confirmation" placeholder="{{ __('ui::inputs.your_password_confirmation.placeholder') }}">
            </div>
        </div>

        <div class="form-group">
            {{-- <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('ui::strings.signup') }}</button> --}}

            <button class="btn btn-lg btn-primary btn-block" type="submit">
                <span> {{ __('ui::strings.signup') }}</span>
            </button>
        </div>
    </form><!-- form -->
    <div class="form-note-s2 pt-4"> 
        {!! __('ui::strings.back_to', ['page' => '<a href="'. route('AuthController@login') .'">'. __('ui::strings.signin') .'</a>']) !!}
    </div>
@endsection