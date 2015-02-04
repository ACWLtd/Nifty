@extends('backend._template')

@section('title')Dashboard @stop

@section('page-css')

@stop

@section('page-title') <h3><i class="fa fa-dashboard"></i> Dashboard</h3> @stop

@section('page')
    <div class="col-lg-12">
        <div class="box info">
            <header>
                <div class="icons">
                    <i class="fa fa-flag-o"></i>
                </div>
                <h5>Dashboard</h5>
            </header>
        </div><!-- /.box -->
    </div>
    <div class="col col-xs-12"> 
        <div class="row">
            <div class="text-center">
                <h2><i class="fa fa-group"></i> Users</h2>
                @foreach ($numbers[0] as $aUser) 
                    <div class="row">
                        <a class="quick-btn" href="#">
                            <i class="fa fa-user fa-2x"></i>
                            <span>{{ $aUser->first_name . ' ' . Str::upper( Str::limit($aUser->last_name, 1 ,'') ) }}</span> 
                        </a>                    
                        <a class="quick-btn" href="#">
                            <i class="fa fa-quote-left fa-2x"></i>
                            <span>Posts</span> 
                            <span class="label label-danger">{{ count($aUser->posts) }}</span> 
                        </a> 
                    </div> 
                @endforeach                
            </div>
            <hr>
        </div>               
    </div> 
@stop

@section('page-js')

@stop