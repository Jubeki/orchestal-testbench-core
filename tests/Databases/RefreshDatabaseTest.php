<?php

namespace Orchestra\Testbench\Tests\Databases;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class RefreshDatabaseTest extends TestCase
{
    use RefreshDatabase, WithLaravelMigrations, WithWorkbench;

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

    /** @test */
    public function it_runs_the_migrations()
    {
        $users = DB::table('testbench_users')->where('id', '=', 1)->first();

        $this->assertEquals('crynobone@gmail.com', $users->email);
        $this->assertTrue(Hash::check('123', $users->password));

        $this->assertEquals([
            'id',
            'email',
            'password',
            'created_at',
            'updated_at',
        ], Schema::getColumnListing('testbench_users'));
    }
}
