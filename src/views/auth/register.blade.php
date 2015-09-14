@extends('cms::auth._template')

@section('page')
    <div class="form-signin">
        <h2 class="form-signin-heading">User Registration</h2>

        @include('cms::partials.validation-errors')

        @if ( session('success') )
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {!! session('success') !!}
            </div>
        @endif
        {!! Form::open(['route' => 'auth.post_register']) !!}
            <div class="form-group {{ $errors->first('first_name') ? 'has-error' : '' }}">
                {!! Form::label('first_name', 'First Name', ['class' => 'control-label']) !!}
                {!! Form::text('first_name', old('first_name'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group {{ $errors->first('last_name') ? 'has-error' : '' }}">
                {!! Form::label('last_name', 'Last Name', ['class' => 'control-label']) !!}
                {!! Form::text('last_name', old('last_name'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group {{ $errors->first('email') ? 'has-error' : '' }}">
                {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                {!! Form::text('email', old('email'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group {{ $errors->first('username') ? 'has-error' : '' }}">
                {!! Form::label('username', 'Username', ['class' => 'control-label']) !!}
                {!! Form::text('username', old('username'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group {{ $errors->first('password') ? 'has-error' : '' }}">
                {!! Form::label('password', 'Password', ['class' => 'control-label']) !!}
                {!! Form::password('password', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group {{ $errors->first('password') ? 'has-error' : '' }}">
                {!! Form::label('password_confirmation', 'Password Confirmation', ['class' => 'control-label']) !!}
                {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
            </div>
            <button type="submit" class="btn btn-lg btn-danger btn-block submit">Submit</button>
        {!! Form::close() !!}
        <br />
        Already registered? <a href="{{ route('auth.login') }}">Login</a>
    </div>
@stop

