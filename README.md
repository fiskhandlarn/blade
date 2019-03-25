# blade

```php
add_filter('blade/viewpaths', function ($paths) {
    $paths = (array) $paths;

    $paths[] = base_path('resources/views2');

    return $paths;
});
```

```php
add_filter('blade/cachepath', function ($path) {
    $uploadDir = wp_upload_dir();
    return $uploadDir['basedir'] . '/.bladecache/';
});
```
