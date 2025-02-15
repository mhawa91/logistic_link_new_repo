@extends('ui::layouts.auth')

@push('meta.title')
    {{ __('ui::strings.new_password') }} | 
@endpush

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">{{ __('ui::strings.new_password') }}</h5>
            <div class="nk-block-des">
                <p>{{ __('ui::strings.new_password_note') }}</p>
            </div>
        </div>
    </div>
    <form action="{{ route('AuthController@postNewPassword', ['hash' => request()->hash]) }}" method="POST" onsubmit="BS.AJAX_FORM(event)" data-keep-loading-on-success="true">
        @csrf
        <input type="hidden" name="hash" value="{{request()->hash}}">
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="new_password">{{ __('ui::inputs.new_password.label') }}</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="javascript:;" class="form-icon form-icon-right passcode-switch" data-target="new_password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input name="password" type="password" class="form-control form-control-lg" id="new_password" placeholder="{{ __('ui::inputs.new_password.placeholder') }}">
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="new_password_confirmation">{{ __('ui::inputs.new_password_confirmation.label') }}</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="javascript:;" class="form-icon form-icon-right passcode-switch" data-target="new_password_confirmation">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input name="password_confirmation" type="password" class="form-control form-control-lg" id="new_password_confirmation" placeholder="{{ __('ui::inputs.new_password_confirmation.placeholder') }}">
            </div>
        </div>

        <div class="form-group">
            <button class="btn btn-lg btn-primary btn-block" type="submit">
                <span> {{ __('ui::strings.update_new_password') }}</span>
            </button>
        </div>

    </form>
    <div class="form-note-s2 pt-4"> 
        {!! __('ui::strings.back_to', ['page' => '<a href="'. route('AuthController@login') .'">'. __('ui::strings.signin') .'</a>']) !!}
    </div>
@endsection