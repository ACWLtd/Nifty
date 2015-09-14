@extends('cms::auth._template')

@section('page')
    <div class="form-signin">
        <h2 class="form-signin-heading">Reset Your Password</h2>

        @include('cms::partials.validation-errors')

        @if ( session('success') )
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {!! session('success') !!}
            </div>
        @endif
        {!! Form::open(['route' => 'auth.post_password_reset']) !!}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group {{ $errors->first('email') ? 'has-error' : '' }}">
                {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                {!! Form::text('email', old('email'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group {{ $errors->first('password') ? 'has-error' : '' }}">
                {!! Form::label('password', 'Password', ['class' => 'control-label']) !!}
                {!! Form::password('password', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group {{ $errors->first('password') ? 'has-error' : '' }}">
                {!! Form::label('password_confirmation', 'Password Confirmation', ['class' => 'control-label']) !!}
                {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
            </div>
            <button type="submit" class="btn btn-lg btn-danger btn-block submit">Reset Password</button>
        {!! Form::close() !!}
        <br />
        Remembered password? <a href="{{ route('auth.login') }}">Login</a>
    </div>
@stop

