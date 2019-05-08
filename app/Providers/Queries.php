<?php

namespace App\Providers;

use ArtisanSdk\Contract\Cacheable;
use ArtisanSdk\CQRS\Events\Invalidated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class Queries extends ServiceProvider
{
    /**
     * The query packages.
     *
     * @var array
     */
    protected $packages = [
        'Order' => __DIR__.'/../Order/Queries',
    ];

    /**
     * The queries loaded.
     *
     * @var string[]
     */
    protected $queries = [];

    /**
     * Register the application's event listeners.
     */
    public function boot()
    {
        $this->load(array_values($this->packages));

        Event::listen(Invalidated::class, function ($event) {
            foreach ($this->queries as $query => $tags) {
                if (count(array_intersect($event->tags, $tags)) > 0) {
                    $query::make()->bust();
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
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

        foreach ((new Finder())->in($paths)->files() as $query) {
            $query = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($query->getPathname(), app_path().DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($query, Cacheable::class) &&
                ! (new ReflectionClass($query))->isAbstract()) {
                $this->queries[$query] = (array) (new $query())->tags;
            }
        }
    }
}
