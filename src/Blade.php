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
     * Blase constructor.
     */
    public function __construct()
    {
        $this->container = new Container();

        $this->filesystem = new Filesystem();
        $this->container->bindIf('files', function () {
            return $this->filesystem;
        }, true);

        $this->dispatcher = new Dispatcher();
        $this->container->bindIf('events', function () {
            return $this->dispatcher;
        }, true);

        $this->container->bindIf('config', function () {
            $blogId = intval(get_current_blog_id());

            if (defined('BLADE_CACHE_DIR') && !empty(BLADE_CACHE_DIR)) {
                $cachePath = rtrim(BLADE_CACHE_DIR, '/').'/'.$blogId;
            } else {
                $uploadDir = wp_upload_dir();
                $cachePath = $uploadDir['basedir'].'/.cache/'.$blogId;
            }

            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                $this->filesystem->cleanDirectory($cachePath);
            }

            if (!$this->filesystem->isDirectory($cachePath)) {
                $this->filesystem->makeDirectory($cachePath, 0775, true, true);
            }

            return [
                'view.paths' => [base_path('resources/views')],
                'view.compiled' => $cachePath,
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
