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

use Closure;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewServiceProvider;

/**
 * This is the blade view class.
 *
 * @author Daniel Gerdgren <tditlu@users.noreply.github.com>
 * @author Oskar Joelson <oskar@joelson.org>
 */
class Blade
{
    /**
     * Path to compiled views.
     *
     * @var string
     */
    private $cachePath;

    /**
     * The compiler implementation.
     *
     * @var \Illuminate\View\Compilers\CompilerInterface
     */
    private $compiler;

    /**
     * The container.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The dispatcher.
     *
     * @var \Illuminate\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * The container.
     *
     * @var \Closure
     */
    protected $engineResolver;

    /**
     * Blade constructor.
     *
     * @param string|array       $viewPaths
     * @param string             $cachePath
     * @param bool               $createCacheDirectory
     */
    public function __construct($viewPaths, string $cachePath, bool $createCacheDirectory = true)
    {
        $this->cachePath = $cachePath;
        $this->container = new Container();

        if (is_multisite()) {
            $this->cachePath .= '/' . intval(get_current_blog_id());
        }

        $this->filesystem = new Filesystem();
        $this->container->bindIf('files', function () {
            return $this->filesystem;
        }, true);

        $this->dispatcher = new Dispatcher();
        $this->container->bindIf('events', function () {
            return $this->dispatcher;
        }, true);

        $this->container->bindIf('config', function () use ($createCacheDirectory, $viewPaths) {
            $this->cleanCacheDirectory();

            if ($createCacheDirectory && !$this->filesystem->isDirectory($this->cachePath)) {
                $this->filesystem->makeDirectory($this->cachePath, 0775, true, true);
            }

            return [
                'view.paths' => (array) $viewPaths,
                'view.compiled' => $this->cachePath,
            ];
        }, true);

        (new ViewServiceProvider($this->container))->register();

        $this->engineResolver = $this->container->make('view.engine.resolver');
        $this->compiler = $this->engineResolver->resolve('blade')->getCompiler();
    }

    /**
     * Undefined methods are proxied to the compiler
     * and the view factory for API ease of use.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->compiler, $name)) {
            return $this->compiler->{$name}(...$arguments);
        }

        if (method_exists($this->container['view'], $name)) {
            return $this->container['view']->{$name}(...$arguments);
        }

        return null;
    }

    public function cleanCacheDirectory()
    {
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            $gitIgnoreContents = null;
            if ($this->filesystem->exists($this->cachePath . '/.gitignore')) {
                $gitIgnoreContents = $this->filesystem->get($this->cachePath . '/.gitignore'); // @codeCoverageIgnore
            }

            $this->filesystem->cleanDirectory($this->cachePath);

            if ($gitIgnoreContents !== null) {
                $this->filesystem->put($this->cachePath . '/.gitignore', $gitIgnoreContents); // @codeCoverageIgnore
            }
        }
    }

    public function render(string $view, array $data = []): string
    {
        return $this->container['view']->make($view, $data)->render();
    }

    public function renderController(string $view, string $controllerClass, array $additionalData = []): string
    {
        return $this->render($view, BladeControllerLoader::dataFromController($controllerClass, $additionalData));
    }
}
