<?php

namespace Orchestra\Testbench\Tests\Databases;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\after_resolving;

class MigrateWithLaravelTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    /**
     * @test
     *
     * @define-db loadApplicationMigrations
     */
    public function it_loads_the_migrations()
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            'name' => 'Orchestra',
            'email' => 'crynobone@gmail.com',
            'password' => \Hash::make('456'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $users = DB::table('users')->where('id', '=', 1)->first();

        $this->assertEquals('crynobone@gmail.com', $users->email);
        $this->assertTrue(Hash::check('456', $users->password));
    }

    /** @test */
    #[DefineDatabase('runApplicationMigrations')]
    public function it_runs_the_migrations()
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            'name' => 'Orchestra',
            'email' => 'crynobone@gmail.com',
            'password' => Hash::make('456'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $users = DB::table('users')->where('id', '=', 1)->first();

        $this->assertEquals('crynobone@gmail.com', $users->email);
        $this->assertTrue(Hash::check('456', $users->password));
    }

    public function loadApplicationMigrations()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    public function runApplicationMigrations()
    {
        after_resolving($this->app, 'migrator', function ($migrator) {
            $migrator->path(base_path('migrations'));
        });

        $this->runLaravelMigrations(['--database' => 'testing']);
    }
}
