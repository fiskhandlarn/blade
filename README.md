# blade

A library for using [Laravel Blade](https://laravel.com/docs/5.7/blade) templates in [WordPlate](https://wordplate.github.io/).

## Installation

Require this package, with [Composer](https://getcomposer.org), in the root directory of your project.

```bash
$ composer require fiskhandlarn/blade
```

## Usage

### Render

Use helper function `blade`:

```php
blade('index', ['machine' => 'Voight-Kampff']);
```

(This renders and echoes the template `/resources/views/index.blade.php` and caches it to `/storage/views`.)

... or instantiate `Blade` by passing the folder(s) where your view files are located, and a cache folder. Render a template by calling the `render` method.

```php
use Fiskhandlarn\Blade;

$blade = new Blade(get_stylesheet_directory() . '/views', get_stylesheet_directory() . '/cache');

echo $blade->render('index', ['machine' => 'Voight-Kampff']);
```

### Custom directive

Create a custom [directive](https://laravel.com/docs/5.7/blade#extending-blade) with helper function `blade_directive`:

```php
blade_directive('datetime', function ($expression) {
    return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
});
```

... or use the `directive` method on a `Blade` object:

```php
$blade->directive('datetime', function ($expression) {
    return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
});
```

Then you can use the directive in your templates:

```php
{{-- In your Blade template --}}
@php $dateObj = new DateTime('2019-11-01 00:02:42') @endphp
@datetime($dateObj)
```

### Custom composer

Create a custom [composer](https://laravel.com/docs/5.7/views#view-composers) with helper function `blade_composer`:

```php
// Make variable available in all views
blade_composer('*', function ($view) {
    $view->with(['badge' => 'B26354']);
});
```

... or use the `composer` method on a `Blade` object:

```php
// Make variable available in all views
$blade->composer('*', function ($view) {
    $view->with(['badge' => 'B26354']);
});
```

### Share variables

Share variables across all templates with helper function `blade_share`:

```php
// Make variable available in all views
blade_share(['badge' => 'B26354']);
```

... or use the `share` method on a `Blade` object:

```php
$blade->share(['badge' => 'B26354']);
```

### Extension

The `Blade` class passes all method calls to the internal compiler (see [documentation](https://laravel.com/docs/5.7/blade)) or view factory (see [documentation](https://laravel.com/docs/5.7/views) for info on `exists`, `first` and `creator`).

## Cache

If `WP_DEBUG` is set to `true` templates will always be rendered and updated.

## Multisite

If run on a WordPress Multisite the cached files will be separated in subfolders by each site's blog id.

## Filters used by `blade` helper

Use the `blade/view/paths` filter to customize the base paths where your templates are stored. (Default value is `/resources/views`.)

```php
add_filter('blade/view/paths', function ($paths) {
    $paths = (array) $paths;

    $paths[] = get_stylesheet_directory() . '/views';

    return $paths;
});
```

Use the `blade/cache/path` filter to customize the cache folder path. (Default value is `/storage/views`.)

```php
add_filter('blade/cache/path', function ($path) {
    $uploadDir = wp_upload_dir();
    return $uploadDir['basedir'] . '/.bladecache/';
});
```

Use the `blade/cache/path` filter to control creation of cache folder. (Default value is `true`.)

```php
add_filter('blade/cache/create', '__return_false');
```
