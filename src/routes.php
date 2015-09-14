<?php

Route::group(['prefix' => 'auth', 'namespace' => 'Kjamesy\Cms\Controllers\Auth', 'as' => 'auth.'], function() {
    Route::get('register', ['as' => 'register', 'uses' => 'AuthController@getRegister']);
    Route::post('register', ['as' => 'post_register', 'uses' => 'AuthController@postRegister']);

    Route::get('login', ['as' => 'login', 'uses' => 'AuthController@getLogin']);
    Route::post('login', ['as' => 'post_login', 'uses' => 'AuthController@postLogin']);
    Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);

    Route::get('password/email', ['as' => 'password_email', 'uses' => 'PasswordController@getEmail']);
    Route::post('password/email', ['as' => 'post_password_email', 'uses' => 'PasswordController@postEmail']);
    Route::get('password/reset/{token}', ['as' => 'password_reset', 'uses' => 'PasswordController@getReset']);
    Route::post('password/reset', ['as' => 'post_password_reset', 'uses' => 'PasswordController@postReset']);

});

$admin = 'admin';
$pages = 'admin/pages';
$posts = 'admin/posts';
$locales = 'admin/locales';
$galleries = 'admin/galleries';
$events = 'admin/events';
$users = 'admin/users';

Route::get('debug', function() {
    $user = Auth::user(); //\Kjamesy\Cms\Models\User::first();
//    $user->password = bcrypt('password2');


});

Route::group(['middleware' => 'auth', 'namespace' => 'Kjamesy\Cms\Controllers'], function() use($admin, $pages, $posts, $locales, $galleries, $events, $users) {

    Route::get($admin, ['as' => 'admin', function() {
        return Redirect::route('pages.landing');
    }]);

    /*****************PAGES*******************/
    Route::get($pages, ['as' => 'pages.landing', 'uses' => 'PageController@index']);
    Route::resource($pages . '/page-resource', 'PageResourceController');
    Route::get($pages . '/get-parent-options', ['as' => 'pages.parents', 'uses' => 'PageController@get_parent_options']);
    Route::get($pages . '/{id}/preview', ['as' => 'pages.preview', 'uses' => 'PageController@preview']);
    Route::get($pages . '/{localeId}/get-locale', ['as' => 'pages.get-locale', 'uses' => 'PageController@get_locale']);
    Route::get($pages . '/{pageId}/{localeId}/get-translation', ['as' => 'pages.get-translation', 'uses' => 'PageController@get_translation']);

    /*****************POSTS*******************/
    Route::get($posts, ['as' => 'posts.landing', 'uses' => 'PostController@index']);
    Route::resource($posts . '/post-resource', 'PostResourceController');
    Route::get($posts . '/get-category-options', ['as' => 'posts.categories', 'uses' => 'PostController@get_category_options']);
    Route::get($posts . '/{id}/preview', ['as' => 'posts.preview', 'uses' => 'PostController@preview']);
    Route::get($posts . '/{localeId}/get-locale', ['as' => 'posts.get-locale', 'uses' => 'PostController@get_locale']);
    Route::get($posts . '/{postId}/{localeId}/get-translation', ['as' => 'posts.get-translation', 'uses' => 'PostController@get_translation']);

    /*****************CATEGORIES*******************/
    Route::get($posts . '/categories', ['as' => 'categories.landing', 'uses' => 'CategoryController@index']);
    Route::resource($posts . '/categories/category-resource', 'CategoryResourceController');
    Route::get($posts . '/categories/create', ['as' => 'categories.create', 'uses' => 'CategoryController@create']);
    Route::get($posts . '/categories/{localeId}/get-locale', ['as' => 'categories.get-locale', 'uses' => 'CategoryController@get_locale']);
    Route::get($posts . '/categories/{categoryId}/{localeId}/get-translation', ['as' => 'categories.get-translation', 'uses' => 'CategoryController@get_translation']);

    /*****************LOCALES*******************/
    Route::get($locales, ['as' => 'locales.landing', 'uses' => 'LocaleController@index']);
    Route::resource($locales . '/locale-resource', 'LocaleResourceController');

    /*****************GALLERIES*******************/
    Route::get($galleries, ['as' => 'galleries.landing', 'uses' => 'GalleryController@index']);
    Route::resource($galleries . '/gallery-resource', 'GalleryResourceController');
    Route::get($galleries . '/images/{id}/show-image', ['as' => 'galleries.show-image', 'uses' => 'GalleryController@show_image']);

    /*****************EVENTS*******************/
    Route::get($events, ['as' => 'events.landing', 'uses' => 'EventController@index']);
    Route::resource($events . '/event-resource', 'EventResourceController');

    /**********************USERS************************/
    Route::get($users, ['as' => 'users.landing', 'middleware' => 'manage_content', 'uses' => 'UserController@index']);
    Route::resource($users . '/users-resource', 'UserResourceController');
    Route::get($users . '/profile', ['as' => 'users.profile', 'uses' => 'UserController@get_profile']);
    Route::get($users . '/profile/password', ['as' => 'users.profile.password', 'uses' => 'UserController@get_profile_password']);

//    Route::group(['before' => 'cms/csrf'], function() use($pages, $posts, $locales, $galleries, $events, $users) {

        /*****************PAGES*******************/
        Route::post($pages . '/{action}/bulk-actions', ['as' => 'pages.bulk-actions', 'uses' => 'PageController@do_bulk_actions']);
        Route::post($pages . '/translations/store', ['as' => 'pages.store-translations', 'uses' => 'PageController@save_translation']);
        Route::post($pages . '/translations/update', ['as' => 'pages.update-translations', 'uses' => 'PageController@update_translation']);
        Route::post($pages . '/translations/destroy', ['as' => 'pages.destroy-translations', 'uses' => 'PageController@destroy_translation']);
        Route::post($pages . '/custom-field/{type}/store', ['as' => 'pages.store-custom-field', 'uses' => 'PageController@store_custom_field']);
        Route::post($pages . '/custom-field/{type}/update', ['as' => 'pages.update-custom-field', 'uses' => 'PageController@update_custom_field']);
        Route::post($pages . '/custom-field/{type}/destroy', ['as' => 'pages.destroy-custom-field', 'uses' => 'PageController@destroy_custom_field']);

        /*****************POSTS*******************/
        Route::post($posts . '/{action}/bulk-actions', ['as' => 'posts.bulk-actions', 'uses' => 'PostController@do_bulk_actions']);
        Route::post($posts . '/translations/store', ['as' => 'posts.store-translations', 'uses' => 'PostController@save_translation']);
        Route::post($posts . '/translations/update', ['as' => 'posts.update-translations', 'uses' => 'PostController@update_translation']);
        Route::post($posts . '/translations/destroy', ['as' => 'posts.destroy-translations', 'uses' => 'PostController@destroy_translation']);
        Route::post($posts . '/custom-field/{type}/store', ['as' => 'posts.store-custom-field', 'uses' => 'PostController@store_custom_field']);
        Route::post($posts . '/custom-field/{type}/update', ['as' => 'posts.update-custom-field', 'uses' => 'PostController@update_custom_field']);
        Route::post($posts . '/custom-field/{type}/destroy', ['as' => 'posts.destroy-custom-field', 'uses' => 'PostController@destroy_custom_field']);

        /*****************CATEGORIES*******************/
        Route::post($posts . '/categories/translations/store', ['as' => 'categories.store-translations', 'uses' => 'CategoryController@save_translation']);
        Route::post($posts . '/categories/translations/update', ['as' => 'categories.update-translations', 'uses' => 'CategoryController@update_translation']);
        Route::post($posts . '/categories/translations/destroy', ['as' => 'categories.destroy-translations', 'uses' => 'CategoryController@destroy_translation']);
        Route::post($posts . '/categories/destroy', ['as' => 'categories.destroy', 'uses' => 'CategoryController@destroy']);

        /*****************LOCALES*******************/
        Route::post($locales . '/destroy', ['as' => 'locales.destroy', 'uses' => 'LocaleController@destroy']);

        /*****************GALLERIES*******************/
        Route::post($galleries . '/destroy', ['as' => 'galleries.destroy', 'uses' => 'GalleryController@destroy']);
        Route::post($galleries . '/images/store', ['as' => 'galleries.store-image', 'uses' => 'GalleryController@store_image']);
        Route::post($galleries . '/images/update', ['as' => 'galleries.update-image', 'uses' => 'GalleryController@update_image']);
        Route::post($galleries . '/images/destroy', ['as' => 'galleries.destroy-image', 'uses' => 'GalleryController@destroy_image']);
        Route::post($galleries . '/images/process-translation', ['as' => 'galleries.process-image-translation', 'uses' => 'GalleryController@process_translation']);
        Route::post($galleries . '/images/destroy-translation', ['as' => 'galleries.destroy-image-translation', 'uses' => 'GalleryController@destroy_translation']);

        /*****************PAGES*******************/
        Route::post($events . '/{action}/bulk-actions', ['as' => 'events.bulk-actions', 'uses' => 'EventController@do_bulk_actions']);

        /**********************USERS************************/
        Route::post($users . '/profile/update', ['as' => 'users.profile.update', 'uses' => 'UserController@update_profile']);
        Route::post($users . '/profile/password/update', ['as' => 'users.profile.password.update', 'uses' => 'UserController@update_profile_password']);
        Route::post($users . '/{action}/do-action', ['as' => 'users.do-action', 'uses' => 'UserController@do_action']);
//    });

});
