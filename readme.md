## Laravel Debugbar

Right now this is just a ServiceProvider to add https://github.com/maximebf/php-debugbar via a ServiceProvider, and add the content to the request.

Includes 2 Collectors:
 - RouteCollector: Show information about the current Route
 - ViewsCollector: Show the currently loaded views an it's data.

## Installation

Require this package in your composer.json and run composer update (or run `composer require barryvdh/laravel-debugbar:dev-master` directly):

    "barryvdh/laravel-debugbar": "dev-master"

After updating composer, add the ServiceProvider to the providers array in app/config/app.php

    'Barryvdh\Debugbar\ServiceProvider',

You need to publish the assets from this package.

    $ php artisan asset:publish barryvdh/laravel-debugbar

The profiler is enabled by default, if you have app.debug=true. You can override that in the config files.
You can also set in your config if you want to include the vendor files also (FontAwesome and jQuery). If you have them, set it to false.

    $ php artisan config:publish barryvdh/laravel-debugbar

You can also set to show all events (disabled by default)