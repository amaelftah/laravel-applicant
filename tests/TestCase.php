<?php

namespace Te7aHoudini\LaravelApplicant\Tests;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Te7aHoudini\LaravelApplicant\Tests\Models\User;
use Te7aHoudini\LaravelApplicant\Tests\Models\Group;
use Te7aHoudini\LaravelApplicant\LaravelApplicantServiceProvider;

class TestCase extends BaseTestCase
{
    public $user;

    public $group;

    /**
     * Setup the test environment.
     */
    protected function setUp() :void
    {
        parent::setUp();

        $this->app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        $this->app['db']->connection()->getSchemaBuilder()->create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        $this->user = User::create(['name' => 'testUser']);
        $this->group = Group::create(['name' => 'testGroup']);

        include_once __DIR__.'/../database/migrations/create_applications_table.php.stub';

        (new \CreateApplicationsTable())->up();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelApplicantServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
