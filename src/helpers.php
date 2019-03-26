<?php

/*
 * This file is part of fiskhandlarn/blade.
 *
 * (c) Oskar Joelson <oskar@joelson.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Fiskhandlarn\Blade;

function __fiskhandlarn_blade_instance(): Blade
{
    static $blade;

    if (!isset($blade)) {
        $blade = new Blade(
            apply_filters('blade/view/paths', base_path('resources/views')),
            apply_filters('blade/cache/path', base_path('storage/views')),
            apply_filters('blade/cache/create', true)
        );
    }

    return $blade;
}

if (!function_exists('blade')) {
    /**
     * Render blade templates.
     *
     * @param string $view
     * @param array $data
     * @param bool $data
     *
     * @return string
     */
    function blade(string $view, array $data = [], bool $echo = true): string
    {
        $ret = (string) __fiskhandlarn_blade_instance()->render($view, $data);

        if ($echo) {
            echo $ret;
        }

        return $ret;
    }
}

if (!function_exists('blade_directive')) {
    /**
     * Register a global custom directive.
     *
     * @param  string  $name
     * @param  callable  $handler
     *
     * @return void
     */
    function blade_directive(string $name, callable $handler): void
    {
        __fiskhandlarn_blade_instance()->directive($name, $handler);
    }
}

if (!function_exists('blade_composer')) {
    /**
     * Register a global composer.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     *
     * @return void
     */
    function blade_composer($views, $callback): void
    {
        __fiskhandlarn_blade_instance()->composer($views, $callback);
    }
}
