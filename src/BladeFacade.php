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
                apply_filters('blade/view/paths', base_path('resources/views')),
                apply_filters('blade/cache/path', base_path('storage/views')),
                apply_filters('blade/cache/create', true)
            );
        }

        return self::$instance;
    }

    public static function composer($views, $callback): void
    {
        self::instance()->composer($views, $callback);
    }

    public static function directive(string $name, callable $handler): void
    {
        self::instance()->directive($name, $handler);
    }

    public static function render(string $view, array $data = []): string
    {
        return self::instance()->render($view, $data);
    }

    public static function share($key, $value = null)
    {
        return self::instance()->share($key, $value);
    }
}
