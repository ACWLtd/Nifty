<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">       
        <meta content="Configure new site" name="description">
        <meta content="James Ilaki" name="author">
        <link href="{{asset('favicon.png')}}" rel="shortcut icon">
        <title>Nifty | @yield('title') </title>
        {!! HTML::style('packages/kjamesy/cms/bootstrap3.1.1/css/bootstrap.min.css') !!}
        <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
        {!! HTML::style('packages/kjamesy/cms/template/css/main.css') !!}
        {!! HTML::style('packages/kjamesy/cms/template/css/theme.css') !!}
        <style>
            body {
                font-family: calibri, Helvetica, sans-serif;
                font-size: 16px;
                background: url("{!! asset('packages/kjamesy/cms/template/img/pattern/denim.png') !!}");
            }
            #wrap{
                background: url("{!! asset('packages/kjamesy/cms/template/img/pattern/denim.png') !!}");
            }
            .user-media, #menu, #menu .nav-header, #menu > li > a, .head .main-bar h3, .wrap, header.head, #footer, #footer a {
                color: #FFF;
                background: url("{!! asset('packages/kjamesy/cms/template/img/pattern/denim.png') !!}");
            }
            #menu li > ul, #menu > li.active > a {
                background: url("{!! asset('packages/kjamesy/cms/template/img/pattern/classy_fabric.png') !!}");
            }
            .search-bar {
                border: medium none !important;
                padding: 0 !important;
            }
            abbr[title], abbr[data-original-title] {
                border-bottom: medium none;
            }
            .form-control, .alert {
                border-radius: 0;
            }
            .form-control, .form-control:focus, .has-error .form-control:focus {
                box-shadow: none;
            }
        </style>
        @yield('page-css')
        <!--[if lt IE 9]>
            {!! HTML::script('packages/kjamesy/cms/bootstrap3.1.1/js/html5shiv.js') !!}
            {!! HTML::script('packages/kjamesy/cms/bootstrap3.1.1/js/respond.min.js') !!}
        <![endif]-->
    </head>
    <body>
        <div id="wrap">
            <div id="top">
                <header class="head">
                    <header class="navbar-header" style="min-width: 190px;">
                        <a href="{!! URL::route('home') !!}" class="navbar-brand" style="display: block; width:100%">
                            <img src="{!! asset('packages/kjamesy/cms/template/img/logo.png') !!}" alt="Nifty">
                        </a>
                    </header>
                    <div class="topnav">
                        <div class="btn-toolbar">
                            <div class="btn-group">
                                <a href="{!! URL::route('auth.logout') !!}" data-toggle="tooltip" data-original-title="Logout" data-placement="bottom" class="btn btn-danger btn-sm btn-rect">
                                    <i class="fa fa-power-off"></i>
                                </a>
                            </div>
                        </div>
                    </div><!-- /.topnav -->
                    <div class="search-bar" style="width: auto; max-width: 220px;">
                        <a data-original-title="Show/Hide Menu" data-placement="bottom" data-tooltip="tooltip" class="accordion-toggle btn btn-primary btn-sm visible-xs" data-toggle="collapse" href="#menu" id="menu-toggle">
                            <i class="fa fa-expand"></i>
                        </a> 
                    </div>
                    <div class="main-bar">
                        @yield('page-title')
                    </div><!-- /.main-bar -->
                </header>
            </div><!-- /#top -->
            <div id="left">
                @include('cms::partials.sidebar')
            </div><!-- /#left -->
            <div id="main-content">
                <div class="outer">
                    <div class="inner">
                        @yield('page')
                    </div><!-- end .inner -->
                </div> <!-- end .outer --> 
            </div> <!-- end #main-content -->
        </div><!-- /#wrap -->
        <div id="footer">
            <p><a href="http://acw.uk.com" target="_blank"> {!!date('Y') !!} &copy;James @ACW</a></p>
        </div>

        {!! HTML::script('packages/kjamesy/cms/bootstrap3.1.1/js/bootstrap.min.js') !!}
        {!! HTML::script('packages/kjamesy/cms/template/js/main.min.js') !!}

        @yield('page-js')
        {!! HTML::script('packages/kjamesy/cms/js/back.js') !!}
    </body>
</html>