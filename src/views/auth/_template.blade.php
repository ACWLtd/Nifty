<!DOCTYPE html>
<html>
<head>
    <title>Nifty | Auth</title>
    {!! HTML::style('packages/kjamesy/cms/bootstrap-4.0.0-alpha/css/bootstrap.min.css') !!}
    <style type="text/css">
        body {
            font-family: calibri, arial, sans-serif;
        }
        .form-signin {
            margin: 0 auto;
            max-width: 600px;
            padding: 15px;
        }
        .form-group {
            margin-bottom: 8px;
        }
        .form-control, .submit, .alert {
            border-radius: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        @yield('page')
    </div>
</div>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
{!! HTML::script('packages/kjamesy/cms/bootstrap-4.0.0-alpha/js/bootstrap.min.js') !!}
</body>
</html>
