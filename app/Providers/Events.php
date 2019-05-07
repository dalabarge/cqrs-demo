<?php

namespace App\Providers;

use ArtisanSdk\Contract\Listener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class Events extends ServiceProvider
{
    /**
     * The event packages.
     *
     * @var array
     */
    protected $packages = [
        // 'Example' => __DIR__.'/../Example/Events',
    ];

    /**
     * The events that are loggable.
     */
    protected $loggable = [
        'App\\*',
    ];

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Register the application's event listeners.
     */
    public function boot()
    {
        $this->listen();
        $this->subscribe();
        $this->logger();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }

    /**
     * Register the listeners for the application.
     */
    protected function listen()
    {
        $this->load(array_values($this->packages));

        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    /**
     * Register the subscriptions for the application.
     */
    protected function subscribe()
    {
        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * Subscribe to all app events and log in debug mode.
     */
    protected function logger()
    {
        if (config('app.debug')) {
            Event::listen($this->loggable, function ($name, $event) {
                Log::debug($name.': '.json_encode(head($event)));
            });
        }
    }

    /**
     * Register all of the event subscribers in the given directory.
     *
     * @param array|string $paths
     */
    protected function load($paths)
    {
        $paths = array_unique(is_array($paths) ? $paths : (array) $paths);

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        foreach ($paths as &$path) {
            $path = realpath($path);
        }

        $namespace = $this->app->getNamespace();

        foreach ((new Finder())->in($paths)->files() as $event) {
            $event = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($event->getPathname(), app_path().DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($event, Listener::class) &&
                ! (new ReflectionClass($event))->isAbstract()) {
                $listeners = array_get($this->listen, $event, []);
                $listeners = array_merge($listeners, $event::listeners());
                array_set($this->listen, $event, array_unique($listeners));
            }
        }
    }
}
