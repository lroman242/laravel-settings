<?php

namespace lroman242\LaravelSettings\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends \Tests\TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->getEnvironmentSetUp();

        $this->getPackageAliases();

        $this->getPackageProviders();

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
     */
    protected function getEnvironmentSetUp()
    {
        $this->app['config']->set('app.debug', true);
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageAliases()
    {
        return [
            'Settings' => \lroman242\LaravelSettings\Facades\Settings::class,
        ];
    }

    protected function getPackageProviders()
    {
        return [
            \lroman242\LaravelSettings\SettingsServiceProvider::class,
        ];
    }
}