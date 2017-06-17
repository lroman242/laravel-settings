<?php
namespace lroman242\LaravelSettings;

use \Exception;
use lroman242\LaravelSettings\Exceptions\SettingNotFoundException;

/**
 *   Settings Management Class
 */
class Settings
{
    /**
     * Fully-qualified class name of an $instance variable
     *
     * @var SettingsStorageInterface
     */
    protected $storage;

    /**
     * Init settings storage
     *
     * @param SettingsStorageInterface $storage
     */
    function __construct(SettingsStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Save / Update settings record
     *
     * @param string  $name   setting name
     * @param string  $value  value
     * @param string  $module module name if NULL global settings
     * @param boolean $active if setting is active
     *
     * @return SettingsStorageInterface
     */
    public function set($name, $value, $module = 'global', $active = true)
    {
        if ($this->has($name, $module)) {
            return $this->update($name, $value, $module, $active);
        }

        $this->create()->save($name, $value, $module, $active);

        return $this->storage;
    }

    /**
     * Check if setting exists
     *
     * @param  string $name   setting name
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function has($name, $module = 'global')
    {
        try {
            $this->getModelInstance($name, $module);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Getting setting value
     *
     * @param  string $name    setting name
     * @param string  $module  module name if NULL global settings
     * @param  string $default default value if setting don't exists
     *
     * @return mixed value, default or NULL
     */
    public function get($name, $module = 'global', $default = null)
    {
        if ($this->has($name, $module)) {
            return $this->storage->value;
        } elseif (!is_null($default)) {
            return $default;
        }

        return null;
    }

    /**
     * Update an existing record
     *
     * @param  string  $name   setting name
     * @param  string  $value  value
     * @param  string  $module module name if NULL global settings
     * @param  boolean $active if setting is active
     *
     * @return SettingsStorageInterface
     */
    public function update($name, $value, $module = 'global', $active = true)
    {
        $this->getModelInstance($name, $module);
        $this->save($name, $value, $module, $active);

        return $this->storage;
    }

    /**
     * Check if setting is active
     *
     * @param  string $name   setting name
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function isActive($name, $module = 'global')
    {
        $this->getModelInstance($name, $module);

        return boolval($this->storage->active);
    }

    /**
     * Make setting active
     *
     * @param  string $name   active
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function activate($name, $module = 'global')
    {
        $this->getModelInstance($name, $module);
        $this->storage->active = true;
        $this->storage->save();

        return true;
    }

    /**
     * Make setting inactive
     *
     * @param  string $name   setting name
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function deactivate($name, $module = 'global')
    {
        $this->getModelInstance($name, $module);
        $this->storage->active = false;
        $this->storage->save();

        return true;
    }

    /**
     * Delete setting record
     *
     * @param  string $name   setting name
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function delete($name, $module = 'global')
    {
        $this->getModelInstance($name, $module);
        $this->storage->delete();

        return true;
    }

    /**
     * Save data in db
     *
     * @param  string  $name   name
     * @param  string  $value  value
     * @param  string  $module module name if NULL global settings
     * @param  boolean $active if setting is active
     *
     * @return Settings
     */
    private function save($name, $value, $module = 'global', $active = true)
    {
        $this->storage->name = $name;
        $this->storage->value = $value;
        $this->storage->active = $active;
        $this->storage->module = $module;

        $this->storage->save();

        return $this;
    }

    /**
     * Looking for settings by name and module
     *
     * @param  string $name   settings name
     * @param  string $module module name if NULL global settings
     *
     * @return Settings
     *
     * @throws Exception
     */
    private function getModelInstance($name = null, $module = 'global')
    {
        try {
            $instance = clone $this->storage;
            $this->storage = $instance->where('name', $name)->where('module', $module)->firstOrFail();
        } catch (Exception $e) {
            $this->throwException($name, $module);
        }

        return $this;
    }

    /**
     * Create new setting model instance
     *
     * @return Settings
     */
    private function create()
    {
        $this->storage = clone $this->storage;

        return $this;
    }

    /**
     * throw Exception
     *
     * @param  string $name   setting name
     * @param  string $module module name if NULL global settings
     *
     * @throws Exception
     */
    private function throwException($name, $module = 'global')
    {
        $message = $module === 'global' ? 'laravel-settings::errors.setting.not_found' : 'laravel-settings::errors.setting.not_found_in_module';

        throw new SettingNotFoundException(trans($message, ['name' => $name, 'module' => $module]));
    }
}