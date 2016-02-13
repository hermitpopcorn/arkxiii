<!DOCTYPE html>
<html>
    <head>
        <title>@yield('title')</title>
        <link rel='stylesheet' type='text/css' href='{{ URL::asset('css/normalize.css') }}' />
        <link rel='stylesheet' type='text/css' href='{{ URL::asset('css/foundation.min.css') }}' />
        <link rel='stylesheet' type='text/css' href='{{ URL::asset('css/app.css') }}' />
    </head>
    <body>
        <div class='center'>
            @yield('content')
        </div>
        
        <script type='text/javascript' src='{{ URL::asset('js/vendor/jquery.js') }}'></script>
        <script type='text/javascript' src='{{ URL::asset('js/foundation.min.js') }}'></script>
        <script type="text/javascript" src="{{ URL::asset("js/vendor/modernizr.js") }}"></script>
        <script>$(document).foundation();</script>
    </body>
</html>