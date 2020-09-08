<?php

namespace Orchestra\Testbench\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Factory as ModelFactory;

trait WithFactories
{
    /**
     * Load model factories from path.
     *
     * @param  string  $path
     *
     * @return $this
     */
    protected function withFactories(string $path)
    {
        return $this->loadFactoriesUsing($this->app, $path);
    }

    /**
     * Load model factories from path using Application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string  $path
     *
     * @return $this
     */
    protected function loadFactoriesUsing($app, string $path)
    {
        if (! \class_exists(ModelFactory::class)) {
            throw new Exception('Missing `laravel/legacy-factories` in composer.json');
        }

        $app->make(ModelFactory::class)->load($path);

        return $this;
    }
}
