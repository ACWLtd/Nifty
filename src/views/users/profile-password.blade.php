@extends('cms::_template')

@section('title')Users @stop

@section('page-css')
    {!! HTML::script('packages/kjamesy/cms/js/jQuery-1.10.2.min.js') !!}
@stop

@section('page-title') <h3><i class="fa fa-user"></i> Users</h3> @stop

@section('page')
    <div class="col-sm-12">
        <div class="box info">
            <header>
                <div class="icons">
                    <i class="fa fa-user"></i>
                </div>
                <h5>Your Profile</h5>
                <div class="toolbar">
                    <a class="btn btn-metis-1 btn-sm btn-flat" href="{!! URL::route('users.profile') !!}"><i class="fa fa-user"></i> Your Profile</a>
                </div>
            </header>
        </div>
    </div>
    <div class="col-sm-6">
        @if ( Session::has('success') )
            <div class="alert alert-dismissable alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!! Session::get('success') !!}
            </div>
        @endif 

        @if ( Session::has('validationerror') )
            <div class="alert alert-dismissable alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Please check the issues highlighted
            </div>
        @endif 

        {!! Form::open(['route' => 'users.profile.password.update']) !!}
                <div class="form-group {!! session('existing_pass_error') ? 'has-error' : '' !!}">
                    {!! Form::label('existing_password', session('existing_pass_error') ? session('existing_pass_error') : 'Existing Password', ['class' => 'control-label']) !!}
                    {!! Form::password('existing_password', ['class' => 'form-control']) !!}
                </div> 
                <div class="form-group {!! $errors->first('new_password') ? 'has-error' : '' !!}">
                    {!! Form::label('new_password', $errors->first('new_password'), ['class' => 'control-label']) !!}
                    {!! Form::password('new_password', ['class' => 'form-control']) !!}
                </div>
                <div class="form-group {!! $errors->first('new_password') ? 'has-error' : '' !!}">
                    {!! Form::label('new_password_confirmation', $errors->first('new_password') ?  $errors->first('new_password') : 'New Password Confirmation', ['class' => 'control-label']) !!}
                    {!! Form::password('new_password_confirmation', ['id' => 'new_password_confirmation', 'class' => 'form-control']) !!}
                </div> 
                <div class="form-group">
                    <p class="form-control-static">
                        <button type="submit" class="btn btn-metis-5 btn-rect btn-lg"><i class="fa fa-floppy-o"></i> Save</button>
                    </p>
                </div>
        {!! Form::close() !!}
    </div>     
@stop