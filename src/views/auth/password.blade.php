@extends('cms::auth._template')

@section('page')
    <div class="form-signin">
        <h2 class="form-signin-heading">Password Reset</h2>

        @include('cms::partials.validation-errors')

        @if ( session('status') )
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {!! session('status') !!}
            </div>
        @endif
        {!! Form::open(['route' => 'auth.post_password_email']) !!}
        <div class="form-group {{ $errors->first('email') ? 'has-error' : '' }}">
            {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
            {!! Form::text('email', old('email'), ['class' => 'form-control']) !!}
        </div>
        <button type="submit" class="btn btn-lg btn-danger btn-block submit">Send Password Reset Link</button>
        {!! Form::close() !!}
        <br />
        Remembered password? <a href="{{ route('auth.login') }}">Login</a> | Not registered? <a href="{{ route('auth.register') }}">Register</a>
    </div>
@stop

