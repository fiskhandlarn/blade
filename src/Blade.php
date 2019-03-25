<?php

namespace Sotagency;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewServiceProvider;

/**
 * This is the blade view class.
 *
 * @author Daniel Gerdgren <daniel.gerdgren@hoy.se>
 * @author Oskar Joelson <oskar.joelson@wearemore.se>
 */
class Blade
{

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
     */
    public function __construct($viewPaths, $cachePath)
    {
        $this->viewPaths = $viewPaths;
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

        $this->container->bindIf('config', function () {
            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                $this->filesystem->cleanDirectory($this->cachePath);
            }

            if (!$this->filesystem->isDirectory($this->cachePath)) {
                $this->filesystem->makeDirectory($this->cachePath, 0775, true, true);
            }

            return [
                'view.paths' => (array) $this->viewPaths,
                'view.compiled' => $this->cachePath,
            ];
        }, true);

        (new ViewServiceProvider($this->container))->register();

        $this->engineResolver = $this->container->make('view.engine.resolver');
    }

    public function render($view, $data = [])
    {
        return $this->container['view']->make($view, $data)->render();
    }
}
