@extends('ui::layouts.auth')

@push('meta.title')
    {{ __('ui::strings.signin') }} | 
@endpush

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">{{ __('ui::strings.signin') }}</h5>
            <div class="nk-block-des">
                <p>{{ __('ui::strings.signin_note') }}</p>
            </div>
        </div>
    </div>
    <form action="{{ route('AuthController@postLogin') }}" method="POST" onsubmit="BS.AJAX_FORM(event)" data-keep-loading-on-success="true">
        @csrf
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="default-01">{{ __('ui::inputs.your_email_or_phone_or_username.label') }}</label>
                {{-- <a class="link link-primary link-sm" tabindex="-1" href="#">Need Help</a> --}}
            </div>
            <input type="text" name="email_or_phone_or_username" class="form-control form-control-lg" id="default-01" placeholder="{{ __('ui::inputs.your_email_or_phone_or_username.placeholder') }}">
        </div>
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password">{{ __('ui::inputs.your_password.label') }}</label>
                <a class="link link-primary link-sm" tabindex="-1" href="{{ route('AuthController@reset') }}">{{ __('ui::strings.forgot_your_password') }}</a>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="javascript:;" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="{{ __('ui::inputs.your_password.placeholder') }}">
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-primary btn-block" type="submit">
                <span> {{ __('ui::strings.signin') }}</span>
            </button>
        </div>
    </form>
    @if (config('systemcore.adminRegistration.active'))
        <div class="form-note-s2 pt-4"> 
            {{ __('ui::strings.dont_have_an_account_yet') }} <a href="{{ route('AuthController@register') }}">{{ __('ui::strings.create_an_account') }}</a>
        </div>
    @endif
@endsection