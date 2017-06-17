<?php

namespace lroman242\LaravelSettings;


use lroman242\LaravelSettings\Tests\TestCase;

class SettingsManagerTest extends TestCase
{
    public function testEloquentDriver()
    {
        $this->app['config']->set('settings.driver', 'eloquent');
        $this->app->make('lroman242\LaravelSettings\SettingsManager');
        $this->assertInstanceOf(EloquentSettingsStorage::class, $this->app->make('lroman242\LaravelSettings\SettingsStorage'));
    }

    public function testDBDriver()
    {
        $this->app['config']->set('settings.driver', 'db');
        $this->app->make('lroman242\LaravelSettings\SettingsManager');
        $this->assertInstanceOf(DbSettingsStorage::class, $this->app->make('lroman242\LaravelSettings\SettingsStorage'));
    }

    public function testJsonDriver()
    {
        $this->app['config']->set('settings.driver', 'json');
        $this->app->make('lroman242\LaravelSettings\SettingsManager');
        $this->assertInstanceOf(JsonSettingsStorage::class, $this->app->make('lroman242\LaravelSettings\SettingsStorage'));
    }
}