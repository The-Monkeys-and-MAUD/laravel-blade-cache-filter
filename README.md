![The Monkeys](http://www.themonkeys.com.au/img/monkey_logo.png)

Laravel Blade Cache Filter
==========================

A simple caching filter that caches Laravel's Response objects.

Installation
------------
To get the latest version of the filter simply require it in your composer.json file by running:

```bash
composer require themonkeys/blade-cache-filter:dev-master --no-update
composer update themonkeys/blade-cache-filter
```

Once the filter is installed you need to register the service provider with the application.
Open up `app/config/app.php` and find the `providers` key.

```php
'providers' => array(
    'Themonkeys\BladeCacheFilter\BladeCacheFilterServiceProvider',
)
```

Now add the filter to the bottom of your `app/filters.php` file:

```php
if (Config::get('blade-cache-filter::bladeCacheExpiry') > 0) {
    Route::filter('cache', 'BladeCacheFilter');
}
```

The filter caches your responses using the standard Laravel cache subsystem, so you might want to configure that
too by following the [Laravel documentation](http://laravel.com/docs/cache). Laravel comes configured with sensible
defaults that work, so you can leave that part until later if you prefer.

Usage
-----

After following the instructions above, the filter is installed but not attached to any routes. To add caching to a
route, you need to add the `'cache'` filter both `'before'` and `'after'` the route in your `app/routes.php` file:

```php
Route::get('/url', array('before' => 'cache', 'after' => 'cache', 'uses' => 'Controller@method'));
```

To add caching to a collection of routes, use a route group:

```php
Route::group(array('before' => 'cache', 'after' => 'cache'), function() {
    Route::get('/url1', 'Controller@method1');
    Route::get('/url2', 'Controller@method2');
});
```


Configuration
-------------

To configure the package, you can use the following command to copy the configuration file to
`app/config/packages/themonkeys/blade-cache-filter`.

```sh
php artisan config:publish themonkeys/blade-cache-filter
```

Or you can just create a new file in that folder and only override the settings you need.

The settings themselves are documented inside `config.php`. The default configuration is for the filter to do nothing,
so you'll need to at least set the `'bladeCacheExpiry'` property to a positive number of minutes in the environments
where you want caching to be enabled.


Contribute
----------

In lieu of a formal styleguide, take care to maintain the existing coding style.

License
-------

MIT License
(c) [The Monkeys](http://www.themonkeys.com.au/)
