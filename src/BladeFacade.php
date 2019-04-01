<?php

namespace Fiskhandlarn;

use Fiskhandlarn\Blade;

/**
 * This is the blade facade class.
 *
 * @author Daniel Gerdgren <tditlu@users.noreply.github.com>
 * @author Oskar Joelson <oskar@joelson.org>
 */
class BladeFacade
{
    private static $instance = null;

    private static function instance(): Blade
    {
        if (self::$instance === null) {
            self::$instance = new Blade(
                apply_filters('blade/view/paths', self::base_path('resources/views')),
                apply_filters('blade/cache/path', self::base_path('storage/views')),
                apply_filters('blade/cache/create', true)
            );
        }

        return self::$instance;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::instance()->$method(...$args);
    }

    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     *
     * @return string
     */
    public static function base_path(string $path = ''): string
    {
        if (class_exists('WordPlate\Application')) {
            return base_path($path); // @codeCoverageIgnore
        }

        // https://stackoverflow.com/a/45364136/1109380
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        $vendorDir = dirname($reflection->getFileName(), 2);

        $path = $path ? DIRECTORY_SEPARATOR . $path : $path;

        return sprintf('%s%s', realpath($vendorDir . '/../'), $path);
    }

    public static function cleanCacheDirectory()
    {
        self::instance()->cleanCacheDirectory();
    }
}
