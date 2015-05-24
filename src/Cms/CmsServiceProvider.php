<?php namespace Kjamesy\Cms;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public  function boot()
    {
        include __DIR__.'/../routes.php';
        include __DIR__.'/../filters.php';

        /*
         * First check whether the views have been published
         */
        if ( is_dir(base_path() . '/resources/views/vendor/kjamesy/cms') ) {
            $this->loadViewsFrom(base_path('resources/views/vendor/kjamesy/cms'), 'cms');
        }
        else {
            $this->loadViewsFrom(__DIR__ . '/../views', 'cms');
        }

        $this->mergeConfigFrom(__DIR__.'/../config/cms.php', 'cms');

        /*
         * Publish Migrations
         */
        $this->publishes([
            __DIR__.'/../migrations/' => database_path('/migrations')
        ], 'migrations');

        /*
         * Publish views
         */
        $this->publishes([
            __DIR__.'/../views' => base_path('resources/views/vendor/kjamesy/cms'),
        ]);

        /*
         * Publish Assets
         */
        $this->publishes([
            __DIR__.'/../../public' => public_path('packages/kjamesy/cms'),
        ], 'public');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register('Sentinel\SentinelServiceProvider');
        $this->app->register('Illuminate\Html\HtmlServiceProvider');
        $this->app->register('Kjamesy\Utility\UtilityServiceProvider');

        $loader = AliasLoader::getInstance();
        $loader->alias('Form', 'Illuminate\Html\FormFacade');
        $loader->alias('HTML', 'Illuminate\Html\HtmlFacade');


    }

}