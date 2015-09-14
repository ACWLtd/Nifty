@extends('cms::_template')

@section('title')Users @stop

@section('page-css')
    {!! HTML::script('packages/kjamesy/cms/js/jQuery-1.10.2.min.js') !!}
@stop

@section('page-title') <h3><i class="fa fa-user"></i> Users</h3> @stop

@section('page')
    <div class="col-lg-12">
        <div class="box info">
            <header>
                <div class="icons">
                    <i class="fa fa-user"></i>
                </div>
                <h5>Your Profile</h5>
                <div class="toolbar">
                    <a class="btn btn-metis-1 btn-sm btn-flat" href="{!! URL::route('users.profile.password') !!}"><i class="fa fa-lock"></i> Password</a>
                </div>
            </header>
        </div>
    </div>
    <div class="col-md-12">
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

        {!! Form::model($user, ['route' => 'users.profile.update', 'class' => 'form-horizontal']) !!}
            <div class="col-md-8">
                <div class="form-group {!! $errors->first('first_name') ? 'has-error' : '' !!}">
                    {!! Form::label('first_name', $errors->first('first_name'), ['class' => 'control-label col-sm-2']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('first_name', Input::old('first_name'), ['id' => 'first_name', 'class' => 'form-control']) !!}
                    </div>                    
                </div> 
                <div class="form-group {!! $errors->first('last_name') ? 'has-error' : '' !!}">
                    {!! Form::label('last_name', $errors->first('last_name'), ['class' => 'control-label col-sm-2']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('last_name', Input::old('last_name'), ['id' => 'last_name', 'class' => 'form-control']) !!}
                    </div>                    
                </div>
                <div class="form-group {!! $errors->first('email') ? 'has-error' : '' !!}">
                    {!! Form::label('email', $errors->first('email'), ['class' => 'control-label col-sm-2']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('email', Input::old('email'), ['id' => 'email', 'class' => 'form-control']) !!}
                    </div>                    
                </div>
                <div class="form-group {!! $errors->first('username') ? 'has-error' : '' !!}">
                    {!! Form::label('username', $errors->first('username'), ['class' => 'control-label col-sm-2']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('username', Input::old('username'), ['id' => 'username', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">User Since</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <abbr title='{!! $userCreatedAt !!}'>{!! $userSince !!}</abbr>
                        </p>
                    </div>                    
                </div> 
                <div class="form-group">
                    <label class="col-sm-2 control-label">Logged-in Since</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <abbr title='{!! $loggedInAt !!}'>{!! $logged_in_for !!}</abbr>
                        </p>
                    </div>                    
                </div>
                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <p class="form-control-static">
                            <button type="submit" class="btn btn-metis-5 btn-rect btn-lg"><i class="fa fa-floppy-o"></i> Save</button>
                        </p>
                    </div>                    
                </div> 
            </div>
        {!! Form::close() !!}
    </div>     
@stop