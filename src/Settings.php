<?php
namespace lroman242\LaravelSettings;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * @param string $name setting name
     * @param string $value value
     * @param string $module module name if NULL global settings
     * @param boolean $active if setting is active
     *
     * @return SettingsStorageInterface
     */
    public function set($name, $value, $module = 'global', $active = TRUE)
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
     * @param  string $name setting name
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function has($name, $module = 'global')
    {
        try {
            $this->getModelInstance($name, $module);

            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * Getting setting value
     *
     * @param  string $name setting name
     * @param string $module module name if NULL global settings
     * @param  string $default default value if setting don't exists
     *
     * @return mixed value, default or NULL
     */
    public function get($name, $module = 'global', $default = NULL)
    {
        if ($this->has($name, $module)) {
            return $this->storage->value;
        } elseif (!is_null($default)) {
            return $default;
        }

        return NULL;
    }

    /**
     * Update an existing record
     *
     * @param  string $name setting name
     * @param  string $value value
     * @param  string $module module name if NULL global settings
     * @param  boolean $active if setting is active
     *
     * @return SettingsStorageInterface
     */
    public function update($name, $value, $module = 'global',  $active = TRUE)
    {
        $this->getModelInstance($name, $module);
        $this->save($name, $value, $module, $active);

        return $this->storage;
    }

    /**
     * Check if setting is active
     *
     * @param  string $name setting name
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
     * @param  string $name active
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function activate($name, $module = 'global')
    {
        $this->getModelInstance($name, $module);
        $this->storage->active = TRUE;
        $this->storage->save();

        return TRUE;
    }

    /**
     * Make setting inactive
     *
     * @param  string $name setting name
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function deactivate($name, $module = 'global')
    {
        $this->getModelInstance($name, $module);
        $this->storage->active = FALSE;
        $this->storage->save();

        return TRUE;
    }

    /**
     * Delete setting record
     *
     * @param  string $name setting name
     * @param  string $module module name if NULL global settings
     *
     * @return boolean
     */
    public function delete($name, $module = 'global')
    {
        $this->getModelInstance($name, $module);
        $this->storage->delete();

        return TRUE;
    }

    /**
     * Save data in db
     *
     * @param  string $name name
     * @param  string $value value
     * @param  string $module module name if NULL global settings
     * @param  boolean $active if setting is active
     *
     * @return Settings
     */
    private function save($name, $value, $module = 'global', $active = TRUE)
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
     * @param  string $name settings name
     * @param  string $module module name if NULL global settings
     *
     * @return Settings
     *
     * @throws Exception
     */
    private function getModelInstance($name = NULL, $module = 'global')
    {
        try {
            $this->storage = (new $this->storage)->where('name', $name)->where('module', $module)->firstOrFail();

            return $this;
        } catch (ModelNotFoundException $e) {
            $this->throwException($name, $module);
        }
    }

    /**
     * Create new setting model instance
     *
     * @return Settings
     */
    private function create()
    {
        $this->storage = new $this->storage;

        return $this;
    }

    /**
     * throw Exception
     *
     * @param  string $name setting name
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