<?php

$routesConfig = Config::get('cms::config.routes');

if (is_array($routesConfig['login']))
    $login = $routesConfig['login']['route'];
else
    $login = $routesConfig['login'];

if (is_array($routesConfig['logout']))
    $logout = $routesConfig['logout']['route'];
else
    $logout = $routesConfig['logout'];

if (is_array($routesConfig['pages']))
    $pages = $routesConfig['pages']['route'];
else
    $pages = $routesConfig['pages'];

if (is_array($routesConfig['posts']))
    $posts = $routesConfig['posts']['route'];
else
    $posts = $routesConfig['posts'];

if (is_array($routesConfig['locales']))
    $locales = $routesConfig['locales']['route'];
else
    $locales = $routesConfig['locales'];

if (is_array($routesConfig['events']))
    $events = $routesConfig['events']['route'];
else
    $events = $routesConfig['events'];

if (is_array($routesConfig['users']))
    $users = $routesConfig['users']['route'];
else
    $users = $routesConfig['users'];

Route::get('login', ['as' => 'login', 'uses' => 'Kjamesy\Cms\Controllers\AuthController@login']);

Route::post('login', ['as' => 'do-login', 'before' => 'cms/csrf', 'uses' => 'Kjamesy\Cms\Controllers\AuthController@do_login']);

Route::group(['before' => 'Sentinel\auth'], function() use($routesConfig, $pages, $posts, $locales, $events, $users) {

    Route::get('admin', ['as' => 'admin', function() use($routesConfig) {
//        var_dump($routesConfig);
        return Redirect::route('pages.landing');
    }]);

    /*****************PAGES*******************/
    Route::get($pages, ['as' => 'pages.landing', 'uses' => 'Kjamesy\Cms\Controllers\PageController@index']);
    Route::resource($pages . '/page-resource', 'Kjamesy\Cms\Controllers\PageResourceController');
    Route::get($pages . '/get-parent-options', ['as' => 'pages.parents', 'uses' => 'Kjamesy\Cms\Controllers\PageController@get_parent_options']);
    Route::get($pages . '/{id}/preview', ['as' => 'pages.preview', 'uses' => 'Kjamesy\Cms\Controllers\PageController@preview']);
    Route::get($pages . '/{localeId}/get-locale', ['as' => 'pages.get-locale', 'uses' => 'Kjamesy\Cms\Controllers\PageController@get_locale']);
    Route::get($pages . '/{pageId}/{localeId}/get-translation', ['as' => 'pages.get-translation', 'uses' => 'Kjamesy\Cms\Controllers\PageController@get_translation']);

    /*****************POSTS*******************/
    Route::get($posts, ['as' => 'posts.landing', 'uses' => 'Kjamesy\Cms\Controllers\PostController@index']);
    Route::resource($posts . '/post-resource', 'Kjamesy\Cms\Controllers\PostResourceController');
    Route::get($posts . '/get-category-options', ['as' => 'posts.categories', 'uses' => 'Kjamesy\Cms\Controllers\PostController@get_category_options']);
    Route::get($posts . '/{id}/preview', ['as' => 'posts.preview', 'uses' => 'Kjamesy\Cms\Controllers\PostController@preview']);
    Route::get($posts . '/{localeId}/get-locale', ['as' => 'posts.get-locale', 'uses' => 'Kjamesy\Cms\Controllers\PostController@get_locale']);
    Route::get($posts . '/{postId}/{localeId}/get-translation', ['as' => 'posts.get-translation', 'uses' => 'Kjamesy\Cms\Controllers\PostController@get_translation']);

    /*****************CATEGORIES*******************/
    Route::get($posts . '/categories', ['as' => 'categories.landing', 'uses' => 'Kjamesy\Cms\Controllers\CategoryController@index']);
    Route::resource($posts . '/categories/category-resource', 'Kjamesy\Cms\Controllers\CategoryResourceController');
    Route::get($posts . '/categories/create', ['as' => 'categories.create', 'uses' => 'Kjamesy\Cms\Controllers\CategoryController@create']);
    Route::get($posts . '/categories/{localeId}/get-locale', ['as' => 'categories.get-locale', 'uses' => 'Kjamesy\Cms\Controllers\CategoryController@get_locale']);
    Route::get($posts . '/categories/{categoryId}/{localeId}/get-translation', ['as' => 'categories.get-translation', 'uses' => 'Kjamesy\Cms\Controllers\CategoryController@get_translation']);

    /*****************LOCALES*******************/
    Route::get($locales, ['as' => 'locales.landing', 'uses' => 'Kjamesy\Cms\Controllers\LocaleController@index']);
    Route::resource($locales . '/locale-resource', 'Kjamesy\Cms\Controllers\LocaleResourceController');

    /*****************EVENTS*******************/
    Route::get($events, ['as' => 'events.landing', 'uses' => 'Kjamesy\Cms\Controllers\EventController@index']);
    Route::resource($events . '/event-resource', 'Kjamesy\Cms\Controllers\EventResourceController');

    /**********************USERS************************/
    Route::get($users, ['as' => 'users.landing', 'uses' => 'Kjamesy\Cms\Controllers\UserController@index']);
    Route::resource($users . '/users-resource', 'Kjamesy\Cms\Controllers\UserResourceController');
    Route::get($users . '/profile', ['as' => 'users.profile', 'uses' => 'Kjamesy\Cms\Controllers\UserController@get_profile']);
    Route::get($users . '/profile/password', ['as' => 'users.profile.password', 'uses' => 'Kjamesy\Cms\Controllers\UserController@get_profile_password']);

    Route::group(['before' => 'cms/csrf'], function() use($pages, $posts, $locales, $events, $users) {

        /*****************PAGES*******************/
        Route::post($pages . '/{action}/bulk-actions', ['as' => 'pages.bulk-actions', 'uses' => 'Kjamesy\Cms\Controllers\PageController@do_bulk_actions']);
        Route::post($pages . '/translations/store', ['as' => 'pages.store-translations', 'uses' => 'Kjamesy\Cms\Controllers\PageController@save_translation']);
        Route::post($pages . '/translations/update', ['as' => 'pages.update-translations', 'uses' => 'Kjamesy\Cms\Controllers\PageController@update_translation']);
        Route::post($pages . '/translations/destroy', ['as' => 'pages.destroy-translations', 'uses' => 'Kjamesy\Cms\Controllers\PageController@destroy_translation']);
        Route::post($pages . '/custom-field/{type}/store', ['as' => 'pages.store-custom-field', 'uses' => 'Kjamesy\Cms\Controllers\PageController@store_custom_field']);
        Route::post($pages . '/custom-field/{type}/update', ['as' => 'pages.update-custom-field', 'uses' => 'Kjamesy\Cms\Controllers\PageController@update_custom_field']);
        Route::post($pages . '/custom-field/{type}/destroy', ['as' => 'pages.destroy-custom-field', 'uses' => 'Kjamesy\Cms\Controllers\PageController@destroy_custom_field']);

        /*****************POSTS*******************/
        Route::post($posts . '/{action}/bulk-actions', ['as' => 'posts.bulk-actions', 'uses' => 'Kjamesy\Cms\Controllers\PostController@do_bulk_actions']);
        Route::post($posts . '/translations/store', ['as' => 'posts.store-translations', 'uses' => 'Kjamesy\Cms\Controllers\PostController@save_translation']);
        Route::post($posts . '/translations/update', ['as' => 'posts.update-translations', 'uses' => 'Kjamesy\Cms\Controllers\PostController@update_translation']);
        Route::post($posts . '/translations/destroy', ['as' => 'posts.destroy-translations', 'uses' => 'Kjamesy\Cms\Controllers\PostController@destroy_translation']);
        Route::post($posts . '/custom-field/{type}/store', ['as' => 'posts.store-custom-field', 'uses' => 'Kjamesy\Cms\Controllers\PostController@store_custom_field']);
        Route::post($posts . '/custom-field/{type}/update', ['as' => 'posts.update-custom-field', 'uses' => 'Kjamesy\Cms\Controllers\PostController@update_custom_field']);
        Route::post($posts . '/custom-field/{type}/destroy', ['as' => 'posts.destroy-custom-field', 'uses' => 'Kjamesy\Cms\Controllers\PostController@destroy_custom_field']);

        /*****************CATEGORIES*******************/
        Route::post($posts . '/categories/translations/store', ['as' => 'categories.store-translations', 'uses' => 'Kjamesy\Cms\Controllers\CategoryController@save_translation']);
        Route::post($posts . '/categories/translations/update', ['as' => 'categories.update-translations', 'uses' => 'Kjamesy\Cms\Controllers\CategoryController@update_translation']);
        Route::post($posts . '/categories/translations/destroy', ['as' => 'categories.destroy-translations', 'uses' => 'Kjamesy\Cms\Controllers\CategoryController@destroy_translation']);
        Route::post($posts . '/categories/destroy', ['as' => 'categories.destroy', 'uses' => 'Kjamesy\Cms\Controllers\CategoryController@destroy']);

        /*****************LOCALES*******************/
        Route::post($locales . '/destroy', ['as' => 'locales.destroy', 'uses' => 'Kjamesy\Cms\Controllers\LocaleController@destroy']);

        /*****************PAGES*******************/
        Route::post($events . '/{action}/bulk-actions', ['as' => 'events.bulk-actions', 'uses' => 'Kjamesy\Cms\Controllers\EventController@do_bulk_actions']);

        /**********************USERS************************/
        Route::post($users . '/profile/update', ['as' => 'users.profile.update', 'uses' => 'Kjamesy\Cms\Controllers\UserController@update_profile']);
        Route::post($users . '/profile/password/update', ['as' => 'users.profile.password.update', 'uses' => 'Kjamesy\Cms\Controllers\UserController@update_profile_password']);
        Route::post($users . '/{action}/do-action', ['as' => 'users.do-action', 'uses' => 'Kjamesy\Cms\Controllers\UserController@do_action']);
    });

    Route::get('logout', ['as' => 'logout', 'uses' => 'Kjamesy\Cms\Controllers\AuthController@logout']);
});
