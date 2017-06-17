<?php
namespace lroman242\LaravelSettings;

use lroman242\LaravelSettings\Models\Settings as SettingsModel;

class EloquentSettingsStorage extends SettingsModel implements SettingsStorageInterface
{
    /**
     * Create new storage instance
     *
     * @param bool|string $connection
     * @param string      $table
     * @param array       $attributes
     */
    public function __construct($connection = false, $table = 'settings', array $attributes = [])
    {
        parent::__construct($attributes);
        $connection = $connection !== false ? $connection : config('settings.connection');
        $table = !empty($table) ? $table : config('settings.table');

        $this->setConnection($connection);
        $this->setTable($table);
    }

    /**
     * Create a new instance of the given storage
     *
     * @param array      $attributes
     * @param bool|false $exists
     *
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = new static($this->connection, $this->table, $attributes);
        $model->exists = $exists;

        return $model;
    }

    /**
     * Execute a query for a single record by existed 'where' rules
     *
     * @return mixed
     */
    public function firstOrFail()
    {
        return static::__call(__FUNCTION__, func_get_args());
    }

    /**
     * Add a basic where clause to the query
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function where($key, $value)
    {
        return static::__call(__FUNCTION__, func_get_args());
    }
}