@extends('cms::auth._template')

@section('page')
    <div class="form-signin">
        <h2 class="form-signin-heading">Login</h2>

        @include('cms::partials.validation-errors')

        @if ( session('success') )
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {!! session('success') !!}
            </div>
        @endif
        {!! Form::open(['route' => 'auth.post_login']) !!}
            <div class="form-group {{ $errors->first('username') ? 'has-error' : '' }}">
                {!! Form::label('username', 'Username/Email', ['class' => 'sr-only control-label']) !!}
                {!! Form::text('username', old('username'), ['placeholder' => 'Username/Email', 'class' => 'form-control']) !!}
            </div>
            <div class="form-group {{ $errors->first('password') ? 'has-error' : '' }}">
                {!! Form::label('password', 'Password', ['class' => 'sr-only control-label']) !!}
                {!! Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) !!}
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
            </div>
            <button type="submit" class="btn btn-lg btn-danger btn-block submit">Login</button>
        {!! Form::close() !!}
        <br />
        Forgot password? <a href="{{ route('auth.password_email') }}">Reset password</a> | Not registered? <a href="{{ route('auth.register') }}">Register</a>
    </div>
@stop

