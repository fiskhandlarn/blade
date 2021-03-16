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

declare(strict_types=1);

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
                apply_filters('blade/view/paths', self::basePath('resources/views')),
                apply_filters('blade/cache/path', self::basePath('storage/views')),
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
    public static function basePath(string $path = ''): string
    {
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
