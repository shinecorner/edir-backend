@extends('layouts.login')

@section('content')

    <div class="page-center">
        <div class="page-center-in">
            <div class="container-fluid">
                <form class="sign-box" role="form" method="POST" action="{{ url('/login') }}">
                    {!! csrf_field() !!}

                    <div class="sign-avatar">
                        <img src="/img/avatar-sign.png" alt="">
                    </div>
                    <header class="sign-title">Bitte loggen Sie sich ein</header>

                    <div class="form-group{{ $errors->has('email') ? ' error' : '' }}">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-Mailadresse">

                        @if ($errors->has('email'))
                            <div class="error-list" data-error-list=""><ul><li>{{ $errors->first('email') }}</li></ul></div>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' error' : '' }}">
                        <input type="password" class="form-control" name="password" placeholder="Passwort">

                        @if ($errors->has('password'))
                            <div class="error-list" data-error-list=""><ul><li>{{ $errors->first('password') }}</li></ul></div>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="checkbox float-left">
                            <input type="checkbox" id="signed-in" name="remember"/>
                            <label for="signed-in">Eingeloggt bleiben?</label>
                        </div>
                        <div class="float-right reset" style="padding-top:1.5px;">
                            <a href="{{ url('/password/reset') }}">Passwort vergessen</a>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-rounded">Login</button>
                </form>
            </div>
        </div>
    </div><!--.page-center-->

@endsection
