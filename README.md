# Nifty CMS

A nifty little CMS written in Laravel 5 and AngularJS - intended for use in own projects

## Steps to Follow
Install a fresh copy of Laravel 5.0.* and configure database and mail settings.

Add the following to the composer.json file

```shell
"repositories": [   
    {
      	"type": "git",
        "url": "https://github.com/ACWLtd/Nifty"
    },
    {
        "type": "git",
        "url": "https://github.com/ACWLtd/Utility"
    }        
],	
```  

Add the following to the composer.json file, inside ```require```

```shell
"kjamesy/cms": "dev-master",
"kjamesy/utility": "dev-master" 
``` 

Change ```minimum-stability``` inside composer.json file to ```dev``` and add the following:

```shell
"prefer-stable" : true
``` 

Run ``` composer update ```

Add the Service Providers to the providers array in ```app/config/app.php``` file:

```php
'Kjamesy\Cms\CmsServiceProvider',
```   

Ensure you have a route named 'home' in your ```app/routes.php``` file. You could modify the default route to:

```php  
Route::get('/', ['as' => 'home', function() {
	return View::make('hello');
}]);
``` 

Publish the package files (migrations, views and assets): 

```shell
php artisan vendor:publish
```

Run the migrations:

```shell
php artisan migrate
```

Assuming default configuration, visit  ```/admin``` and login with Username ```jamesy``` and Password ```password```