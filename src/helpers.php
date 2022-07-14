<?php

/**
 * This file is part of blade.
 *
 * blade is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * blade is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with blade.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * @author Oskar Joelson <oskar@joelson.org>
 */

declare(strict_types=1);

use Fiskhandlarn\BladeFacade;

if (!\function_exists('blade')) {
    /**
     * Render blade templates.
     *
     * @param string $view
     * @param array $data
     * @param bool $echo
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

if (!\function_exists('blade_controller')) {
    /**
     * Render blade templates with data from controller class.
     *
     * @param string $view
     * @param string $controllerClass
     * @param array $additionalData
     * @param bool $echo
     *
     * @return string
     */
    function blade_controller(
        string $view,
        string $controllerClass,
        array $additionalData = [],
        bool $echo = true
    ): string {
        $ret = (string) BladeFacade::renderController($view, $controllerClass, $additionalData);

        if ($echo) {
            echo $ret;
        }

        return $ret;
    }
}

if (!\function_exists('blade_directive')) {
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

if (!\function_exists('blade_composer')) {
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

if (!\function_exists('blade_share')) {
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
