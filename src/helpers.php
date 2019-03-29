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

use Fiskhandlarn\BladeFacade;

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
        $ret = (string) BladeFacade::render($view, $data);

        if ($echo) {
            echo $ret;
        }

        return $ret;
    }
}

if (!function_exists('blade_controller')) {
    /**
     * Render blade templates with data from controller class.
     *
     * @param string $view
     * @param string $controllerClass
     * @param bool $data
     *
     * @return string
     */
    function blade_controller(string $view, string $controllerClass, array $additionalData = [], bool $echo = true): string
    {
        $ret = (string) BladeFacade::renderController($view, $controllerClass, $additionalData);

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
        BladeFacade::directive($name, $handler);
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
        BladeFacade::composer($views, $callback);
    }
}

if (!function_exists('blade_share')) {
    /**
     * Register global shared data.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     *
     * @return mixed
     */
    function blade_share($key, $value = null)
    {
        return BladeFacade::share($key, $value);
    }
}
