<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\Str;

final class LoadMigrationsFromArray
{
    /**
     * The migrations.
     *
     * @var array<int, string>
     */
    public $migrations;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  array<int, string>  $migrations
     */
    public function __construct(array $migrations)
    {
        $this->migrations = $migrations;
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $paths = Collection::make($this->migrations)
            ->when(is_dir($app->basePath('migrations')) && Env::get('TESTBENCH_WITHOUT_DEFAULT_MIGRATIONS') !== true, function ($migrations) use ($app) {
                return $migrations->push($app->basePath('migrations'));
            })->filter(function ($migration) {
                return \is_string($migration);
            })->transform(function ($migration) use ($app) {
                return Str::startsWith('./', $migration)
                    ? $app->basePath(str_replace('./', '/', $migration))
                    : $migration;
            })->all();

        $this->callAfterResolvingMigrator($app, function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }

    /**
     * Setup an after resolving listener, or fire immediately if already resolved.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  callable  $callback
     * @return void
     */
    protected function callAfterResolvingMigrator($app, $callback)
    {
        /** @phpstan-ignore-next-line */
        $app->afterResolving('migrator', $callback);

        if ($app->resolved('migrator')) {
            $callback($app->make('migrator'), $app);
        }
    }
}