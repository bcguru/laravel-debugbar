<?php namespace Barryvdh\Debugbar;

use Barryvdh\Debugbar\Storage\FilesystemStorage;
use DebugBar\Storage\FileStorage;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('barryvdh/laravel-debugbar');

        if($this->app['config']->get('laravel-debugbar::config.enabled')){

            /** @var LaravelDebugbar $debugbar */
            $debugbar = $this->app['debugbar'];
            if($this->app['config']->get('laravel-debugbar::config.storage.enabled')){
                $path = $this->app['config']->get('laravel-debugbar::config.storage.path');
                $storage = new FilesystemStorage($this->app['files'], $path);
                $debugbar->setStorage($storage);
            }

            if(!$this->app->runningInConsole() && $this->app['request']->segment(1) !== '_debugbar'){
                $debugbar->boot();
            }else{
                $debugbar->disable();
            }

        }

        $this->commands('command.debugbar.publish');
        $this->commands('command.debugbar.clear');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $app = $this->app;
        $this->app['debugbar'] = $this->app->share(function ($app){
                $debugbar = new LaravelDebugBar($app);

                $sessionManager = $app['session'];
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);

                return $debugbar;
            });

        $this->app['command.debugbar.publish'] = $this->app->share(function($app)
            {
                //Make sure the asset publisher is registered.
                $app->register('Illuminate\Foundation\Providers\PublisherServiceProvider');
                return new Console\PublishCommand($app['asset.publisher']);
            });

        $this->app['command.debugbar.clear'] = $this->app->share(function($app)
            {
                return new Console\ClearCommand($app['debugbar']);
            });

        if(version_compare($app::VERSION, '4.1', '>=')){
            $app->middleware('Barryvdh\Debugbar\Middleware', array($app));
        }else{
            $app->after(function ($request, $response) use($app)
            {
                $debugbar = $app['debugbar'];
                $debugbar->modifyResponse($request, $response);
            });
        }

        $this->app['router']->get('_debugbar/open', function() use($app){

                $debugbar = $app['debugbar'];

                $openHandler = new \DebugBar\OpenHandler($debugbar);

                $data = $openHandler->handle(null, false, false);
                return \Response::make($data, 200, array(
                        'Content-Type'=> 'application/json'
                    ));
            });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('debugbar', 'command.debugbar.publish', 'command.debugbar.clear');
    }

}
