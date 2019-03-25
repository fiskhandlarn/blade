# blade

```php
add_filter('blade/view/paths', function ($paths) {
    $paths = (array) $paths;

    $paths[] = base_path('resources/views2');

    return $paths;
});
```

```php
add_filter('blade/cache/path', function ($path) {
    $uploadDir = wp_upload_dir();
    return $uploadDir['basedir'] . '/.bladecache/';
});
```

```php
add_filter('blade/cache/create', '__return_false');
```
