<?php

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