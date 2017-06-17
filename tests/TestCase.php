<?php

namespace lroman242\LaravelSettings\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if ($this->app['config']->get('settings.driver', 'eloquent') != 'json') {
            $this->migrateDatabase();
        }
    }

    protected function migrateDatabase()
    {
        /** @var \Illuminate\Database\Schema\Builder $schemaBuilder */
        $schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();

        $schemaBuilder->create(Config::get('settings.table', 'settings'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('module')->nullable()->default('global');
            $table->string('name');
            $table->longText('value')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['module', 'name']);
        });
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('settings.table', 'settings');
        $app['config']->set('settings.connection', null);
        $app['config']->set('settings.driver', 'eloquent');
        $app['config']->set('settings.path', storage_path('app/vendor/settings/settings.json'));
    }

    protected function getPackageAliases($app)
    {
        return [
            'Settings' => \lroman242\LaravelSettings\Facades\Settings::class,
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            \lroman242\LaravelSettings\SettingsServiceProvider::class,
        ];
    }
}