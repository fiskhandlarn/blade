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
                apply_filters('blade/viewpaths', base_path('resources/views')),
                apply_filters('blade/cachepath', base_path('storage/views'))
            );
        }

        $ret = (string) $blade->render($view, $data);

        if ($echo) {
            echo $ret;
        }

        return $ret;
    }
}
