<!DOCTYPE html>
<html>
    <head>
        <title>@yield('title')</title>
        <link rel="stylesheet" type="text/css" href="{{ URL::asset("css/normalize.css") }}" />
        <link rel="stylesheet" type="text/css" href="{{ URL::asset("css/foundation.min.css") }}" />
        <link rel="stylesheet" type="text/css" href="{{ URL::asset("css/font-awesome.min.css") }}" />
        <link rel="stylesheet" type="text/css" href="{{ URL::asset("css/app.css") }}" />
        <script type="text/javascript" src="{{ URL::asset("js/vendor/jquery.js") }}"></script>
        <script type="text/javascript" src="{{ URL::asset("js/foundation.min.js") }}"></script>
        <script type="text/javascript" src="{{ URL::asset("js/vendor/modernizr.js") }}"></script>
    </head>
    <body>
        <div class="container">
            @include('topbar')
            
            @include('sidebar')
            
            <div class="content">
                <noscript>
                    <div data-alert class='alert-box alert'>
                        <span class='alert-icon'><i class='alert-icon fa fa-warning'></i></span>
                        <span class='alert-text'>JavaScript browser Anda mati. Tolong hidupkan terlebih dahulu, karena aplikasi ini mengandalkan fitur JavaScript untuk bekerja.</span>
                    </div>
                </noscript>

                @if (Session::get('alert'))
                <div data-alert class='alert-box alert'>
                    <span class='alert-icon'><i class='alert-icon fa fa-warning'></i></span>
                    <span class='alert-text'>{{ Session::get('alert') }}</span>
                </div>
                @endif
                @if (Session::get('warning'))
                <div data-alert class='alert-box warning'>
                    <span class='alert-icon'><i class='alert-icon fa fa-info-circle'></i></span>
                    <span class='alert-text'>{{ Session::get('warning') }}</span>
                </div>
                @endif
                
                @yield('content')
            
                <footer>
                    <a href="{{ route('tentang') }}">Tentang</a> &bull; <a href="https://github.com/hermitpopcorn/arkxiii">GitHub</a>
                </footer>
            </div>
        </div>
        
        <script>$(document).foundation();</script>
    </body>
</html>