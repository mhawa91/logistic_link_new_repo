<base href="{{ url('/') }}">
<meta charset="utf-8">
<meta name="author" content="@stack('meta.author') {{ __( config('app.name') ) }}">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="@stack('meta.description')">
<link rel="shortcut icon" href="{{ asset( config('ui.logos.' . $_LOCALE_ . '.favicon.publicPath') ) }}">
<title>@stack('meta.title') {{ __( config('app.name') ) }}</title>