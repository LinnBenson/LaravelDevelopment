@php
    $frame = \App\Services\ViewService::renderFrame();
    $themeStyle = '';
    foreach( $frame->theme as $key => $value ) {
        if( str_starts_with( $key, '--' ) ) {
            $themeStyle .= "{$key}: {$value}; ";
        }
    }
@endphp
<html lang="{{$frame->locale}}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="Cache-Control" content="no-siteapp">
        <link rel="icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon.ico" />
        <link rel="apple-touch-icon-precomposed" href="/favicon.ico" />
        <meta name="msapplication-TileImage" content="/favicon.ico" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="full-screen" content="yes">
        <meta name="browsermode" content="application">
        <meta name="x5-fullscreen" content="true">
        <meta name="x5-page-mode" content="app">
        <link rel="stylesheet" href="{{asset( 'css/rely.css' )}}">
        @if( isset( $frame->theme['css'] ) && is_string( $frame->theme['css'] ) )
            <link rel="stylesheet" href="{{$frame->theme['css']}}">
        @endif
        <script src="{{asset( 'js/jquery-4.0.0.min.js' )}}"></script>
        <title>@hasSection( 'title' )@yield( 'title' ) - @endif{{ setting( 'app.title' ) }}</title>
        <style>
            :root { {{$themeStyle}} }
        </style>
        <script>
            window['setting'] = { 'rid': `{{request()->attributes->get('rid')}}`, 'locale': `{{str_replace( '_', '-', $frame->locale )}}` };
        </script>
        @stack( 'head' )
    </head>
    <body>
        @yield( 'body' )
        <script>
            @stack( 'script', '' )
        </script>
    </body>
</html>