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

use Illuminate\Container\Container;
use Sober\Controller\Utils;

/**
 * This is the loader class for blade controllers
 *
 * @author Oskar Joelson <oskar@joelson.org>
 */
class BladeControllerLoader
{
    private static $instance = null;

    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    private static function instance(): BladeControllerLoader
    {
        if (self::$instance === null) {
            self::$instance = new BladeControllerLoader(
                rtrim(apply_filters('blade/controller/namespace', 'App\Controllers'))
            );
        }

        return self::$instance;
    }

    public static function dataFromController(string $controllerClass, array $additionalData = []): ?array
    {
        try {
            $class = self::getClassToRun(self::instance()->namespace, $controllerClass);
        } catch (\Exception $exception) {
            // class not found
            throw $exception;
            return null;
        }

        $container = Container::getInstance();

        // Recreate the class so that $post is included
        $controller = $container->make($class);

        // Params
        $controller->__setParams();

        // Lifecycle
        $controller->__before();

        // Data
        $controller->__setData($additionalData);

        // Lifecycle
        $controller->__after();

        // Return
        return $controller->__getData();
    }

    private static function getClassToRun(string $namespace, string $class): string
    {
        try {
            $reflection = new \ReflectionClass($namespace . '\\' . $class);
        } catch (\Exception $exception) {
            // class not found
            throw new \Exception("No such class found in namespace $namespace: $class");
            return null;
        }

        $filename = $reflection->getFileName();

        // Exclude non-Controller classes
        if (!$reflection->isSubclassOf('Fiskhandlarn\BladeController')) {
            throw new \Exception("Class does not extend BladeController: $namespace\\$class");
            return null;
        }

        return $namespace . '\\' . pathinfo($filename, PATHINFO_FILENAME);
    }
}
