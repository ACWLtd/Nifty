<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="Configure new site" name="description">
        <meta content="James Ilaki" name="author">
        <title>Nifty | Login</title>
        <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
        {{ HTML::style('packages/kjamesy/cms/template/css/main.css') }}
        {{ HTML::style('packages/kjamesy/cms/template/css/theme.css') }}
        {{ HTML::style('packages/kjamesy/cms/template/lib/magic/magic.css') }}

        <!--[if lt IE 9]>
        {{ HTML::script('packages/kjamesy/cms/bootstrap/js/html5shiv.js') }}
        {{ HTML::script('packages/kjamesy/cms/bootstrap/js/respond.min.js') }}
        <![endif]-->
        <style>
            .login .form-signin #email {
                border-radius: 4px 4px 0 0;
                margin-bottom: -1px;
            }
        </style>
    </head>
    <body class="login" style="background: url('{{ asset('packages/kjamesy/cms/template/img/pattern/bedge_grunge.png') }}') repeat scroll 0 0 #444444;">
        <div class="container">
            <div class="text-center">
                <a href="{{ URL::route('home') }}"><img src="{{ asset('packages/kjamesy/cms/template/img/logo.png') }}" alt="Nifty"></a>
            </div>
            <div class="tab-content">
                <div id="login" class="tab-pane active">
                    {{ Form::open(['route' => 'do-login', 'class' => 'form-signin', 'id' => 'loginForm']) }}
                        <p class="text-center">Please login to continue</p>

                        @if ( Session::has('error') || $errors->has('email') || $errors->has('password') )
                            <div class="alert alert-dismissable alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                @if ( $errors->has('email') )<div> {{ $errors->first('email')  }}</div> @endif
                                @if ( $errors->has('password') )<div> {{ $errors->first('password') }}</div>@endif
                                @if ( Session::has('error') ) <div> {{ Session::get('error') }}</div>@endif
                            </div>
                        @endif

                        {{ Form::text('email', Input::old('email'), ['id' => 'email', 'class' => 'form-control', 'placeholder' => 'Username or Email', 'autofocus' => 'autofocus']) }}
                        {{ Form::password('password', ['id' => 'password', 'class' => 'form-control', 'placeholder' => 'Password',]) }}
                        <div class="checkbox">
                            <label>
                                <input name="rememberMe" value="rememberMe" type="checkbox"> Remember me
                            </label>
                        </div>
                        <button class="btn btn-lg btn-block btn-metis-5 btn-rect" type="submit">Sign in</button>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="text-center">
                <ul class="list-inline">
                    {{--<li> <a class="text-muted" href="#forgot" data-toggle="tab">Forgot Password</a>  </li>--}}
                </ul>
            </div>
        </div><!-- /container -->
        {{ HTML::script('packages/kjamesy/cms/js/jQuery-1.10.2.min.js') }}
        {{ HTML::script('packages/kjamesy/cms/bootstrap/js/bootstrap.min.js') }}
        {{ HTML::script('packages/kjamesy/cms/template/js/main.min.js') }}
    </body>
</html>