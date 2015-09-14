# Nifty CMS

A nifty little CMS written in Laravel 5.1 and AngularJS - intended for use in own projects

## Changelog
  * Compatibility with Laravel >= 5.1.1
  * Removed authentication dependency on ```rydurham/Sentinel```
  * Authentication now relying on framework's built-in authentication - extended to include user roles
  * Use of middleware to manage authorisation in the backend
  * Refreshed backend design


## Steps to Follow
  1. Install a fresh copy of Laravel >= 5.1 and configure database and mail settings.

  2. Add the following to the composer.json file

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

  3. Add the following to the composer.json file, inside ```require```

     ```shell
     "kjamesy/cms": "1.1.*",
     "kjamesy/utility": "dev-master" 
     ``` 

  4. Add the following inside composer.json file:

     ```shell
     "minimum-stability": "dev",
     "prefer-stable" : true
     ``` 

  5. Run ``` composer update ```

  6. Add the Service Providers to the providers array in ```config/app.php``` file:

     ```php
     Kjamesy\Cms\CmsServiceProvider::class,
     ``` 
  
     **PS: You may want to add the CmsServiceProvider BEFORE the ```RouteServiceProvider``` so the package's routes take precendence over those in your app.**

  7. Ensure you have a route named *home* in your ```app/Http/routes.php``` file. You could modify the default route to:

     ```php  
     Route::get('/', ['as' => 'home', function() {
	     return View::make('welcome');
     }]);
     ``` 

  8. Remove the framework default migrations inside ```database/migrations``` 

  9. Publish the package files (migrations, views and assets): 

     ```shell
     php artisan vendor:publish
     ```

  10. Run the migrations:

     ```shell
     php artisan migrate
     ```

  11. Inside ```config/auth.php``` change *model* value to ```Kjamesy\Cms\Models\User::class,``` and *email* (inside *password*) to ```'cms::emails.password',```
  
  12. Inside ```app/Http/Middleware/RedirectIfAuthenticated.php``` change ```redirect('home');``` to ```redirect(route('home'));```
  
  13. Assuming default configuration, visit  ```/admin``` and login with username ```jamesy``` and Password ```password```
   
  14. You now deserve a biscuit, go on!