<?php
namespace lroman242\LaravelSettings;

use Illuminate\Support\Manager;

class SettingsManager extends Manager
{
    /**
     * Getting default driver name from config
     *
     * @return mixed
     */
    public function getDefaultDriver()
    {
        return $this->getConfig('driver');
    }

    /**
     * Create instance of eloquent driver
     *
     * @return EloquentSettingsStorage
     */
    public function createEloquentDriver()
    {
        $connectionName = $this->getConfig('connection');
        $table = $this->getConfig('table');

        return new EloquentSettingsStorage($connectionName, $table);
    }

    /**
     * Create instance of DB driver
     *
     * @return DbSettingsStorage
     */
    public function createDbDriver()
    {
        $connectionName = $this->getConfig('connection');
        $table = $this->getConfig('table');

        return new DbSettingsStorage($this->app['db'], $connectionName, $table);
    }

    /**
     * Create instance of json driver
     *
     * @return JsonSettingsStorage
     */
    public function createJsonDriver()
    {
        $path = $this->getConfig('path');

        return new JsonSettingsStorage($path);
    }

    /**
     * Helper to get settings config values
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    protected function getConfig($key, $default = null)
    {
        return $this->app['config']->get("settings.$key", $default);
    }
}