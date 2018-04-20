<?php

/*
 * This file is part of sotagency/blade.
 *
 * (c) Oskar Joelson <oskar.joelson@sot.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Plugin Name: Blade
 * Description: A theme support plugin for WordPlate.
 * Author: Sot
 * Author URI: https://sot.se
 * Version: 0.1
 * Plugin URI: https://github.com/sotagency/blade#readme
 */

declare(strict_types=1);

use Jenssegers\Blade\Blade;

if (!function_exists('view')) {

    /**
     * Return a view with data.
     *
     * @param string $name
     * @param array $data
     *
     * @return string
     */
    function view(string $name, array $data = [], $echo=true): string
    {
        static $blade;

        if (!$blade) {
            $blade = new Blade(
                base_path('resources/views'),
                base_path('storage/views')
            );
        }

        $ret = (string) $blade->make($name, $data);

        if ($echo) {
            echo $ret;
        }

        return $ret;
    }
}
