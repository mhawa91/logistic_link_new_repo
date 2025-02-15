@extends('ui::layouts.email', [
    'title' => 'Welcome To ' . __( config('app.name') ),
])

@section('content')
    <h1 align="center" style="color:#222222; font-weight:bold; font-size:24px; margin:10px 0 16px 0">
        Welcome to {{ config('app.name') }}
    </h1>
    <p align="center" style="margin-bottom:5px; font-size:16px"></p>
    <p align="center" style="word-wrap:break-word; word-break:break-all">
        We build and analyze for a safe investment environment
    </p>
    <p align="center" style="word-wrap:break-word; word-break:break-all">
        Please complete the account verification process to be one of us
        <a href="{{ $url ?? '#' }}" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" data-linkindex="1">
            from here
        </a> 
    </p>
@endsection