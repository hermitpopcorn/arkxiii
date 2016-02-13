@extends('barebones')

@section('title', 'Login')

@section('content')
<div class='logo small-centered column'>
    <img src='{{ URL::asset('img/login-logo.png') }}' />
</div>
<div class='small-12 medium-8 large-6 small-centered column'>
    <div class='panel'>
        @if(Session::has('message'))
        <div class='alert-box warning'>{{ Session::get('message') }}</div>
        @endif
        <form action='{{ route('login') }}' method='POST'>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class='row'>
                <div class='small-12 column'>
                    <label>Username
                        <input type='text' name='username' />
                    </label>
                </div>
                <div class='small-12 column'>
                    <label>Password
                        <input type='password' name='password' />
                    </label>
                </div>
                <div class='small-12 column'>
                    <input id="remember_me" type="checkbox" name='remember_me'><label for="remember_me">Ingat</label>
                </div>
                <div class='small-12 text-center column'>
                    <input type='submit' class='button' value='Login'></input>
                </div>
            </div>
        </form>
    </div>
    
    <div class='text-center column'><a href="{{ route('tentang') }}">Tentang</a> &bull; <a>GitHub</a></div>
</div>
@endsection