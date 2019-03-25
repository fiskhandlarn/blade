<?php

/*
 * This file is part of sotagency/blade.
 *
 * (c) Oskar Joelson <oskar.joelson@sot.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Sotagency\Blade;

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     *
     * @return string
     */
    function base_path(string $path = ''): string
    {
        $container = Container::getInstance();

        $path = $path ? DIRECTORY_SEPARATOR.$path : $path;

        return sprintf('%s%s', $container->getBasePath(), $path);
    }
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
        static $blade;
        if (!isset($blade)) {
            $blade = new Blade(
                apply_filters('blade/view/paths', base_path('resources/views')),
                apply_filters('blade/cache/path', base_path('storage/views')),
                apply_filters('blade/cache/create', true)
            );
        }

        $ret = (string) $blade->render($view, $data);

        if ($echo) {
            echo $ret;
        }

        return $ret;
    }
}
