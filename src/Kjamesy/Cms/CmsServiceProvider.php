<?php namespace Kjamesy\Cms;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Bootstrap the application events
     * @return void
     */
    public  function boot()
    {
        $this->package('kjamesy/cms');

        include __DIR__.'/../../routes.php';
        include __DIR__.'/../../filters.php';
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['cms'] = $this->app->share(function($app)
        {
            return new Cms;
        });
        $this->app->booting(function()
        {
            $loader = AliasLoader::getInstance();
            $loader->alias('Cms', 'Kjamesy\Cms\Facades\Cms');
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['cms'];
	}

}
