# Nifty CMS

A nifty little CMS written in Laravel 4.2 and AngularJS - intended for use in own projects


## Steps to Follow
Install a fresh copy of Laravel 4.2.* and configure database and mail settings.

Add the following to the composer.json file, before the 'require' section

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

Add the following to the composer.json file, inside 'require'

```shell
"kjamesy/cms": "dev-master",
"kjamesy/utility": "dev-master" 
``` 

Run ``` composer update ```

Add the Service Providers to the providers array in ```app/config/app.php``` file:

```php
'Sentinel\SentinelServiceProvider',
'Kjamesy\Utility\UtilityServiceProvider',
'Kjamesy\Cms\CmsServiceProvider',
```   

Ensure you have a route named 'home' in your ```app/routes.php``` file. You could modify the default route to:

```php  
Route::get('/', ['as' => 'home', function() {
	return View::make('hello');
}]);
``` 

Run the migrations:

```shell
php artisan migrate --package=kjamesy/cms
```

Publish the package assets: 

```shell
php artisan asset:publish kjamesy/cms
```

__Optional:__ You can publish the package config file if you want to change the default admin routes:

```shell
	php artisan config:publish kjamesy/cms
``` 

__Optional:__ You can also publish the package view files if you want to modify the views:

```shell
	php artisan view:publish kjamesy/cms
```  

Assuming default configuration, visit  ```/admin``` and login with Username ```jamesy``` and Password ```password```