<?php
//$routesConfig = Config::get('Sentinel::config');

Route::filter('cms/csrf', function()
{
    if (Session::token() != Input::get('_token'))
    {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

Route::filter('cms/auth', function ()
{
    if ( ! Sentry::check() || ! Session::get('userId') || ! Sentry::getUser() ) {
        Sentry::logout();
        return Redirect::route('login');
    }
});



//Route::filter('cms/checkLoggedIn', function() use($routesConfig)
//{
//    if ( ! Sentry::check() ) {
//        Session::put( 'originalRequest', Request::fullUrl() );
//        return Redirect::to($routesConfig['routes']['login']['route']);
//    }
//});


//Route::filter('Sentinel\auth', function() use($routesConfig)
//{
//    if ( Sentry::check() )
//        return Redirect::to($routesConfig['routes']['admin']['route']);
//});