<?php
namespace lroman242\LaravelSettings;

use lroman242\LaravelSettings\Exceptions\SettingNotFoundException;

class JsonSettingsStorage implements SettingsStorageInterface
{
    protected $path;
    protected $data;

    public $name;
    public $value;
    public $active;
    public $module;

    /**
     * Create new storage instance
     *
     * @param null $path
     */
    function __construct($path = null)
    {
        $this->path = !empty($path) ? $path : config('settings.path');

        if(!file_exists($this->path)){
            file_put_contents($this->path, json_encode(array()));
        }

        $this->data = $this->newInstance();
    }

    /**
     * Create new storage instance to work with
     *
     * @return array
     */
    protected function newInstance()
    {
        $data = json_decode(file_get_contents($this->path), true);
        if(!is_array($data) || empty($data))
           $data = [];

        return $data;
    }

    /**
     * Add a basic where clause to the query
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function where($key, $value)
    {
        $this->data = array_filter($this->data, function($item) use ($key, $value){
            return (bool)($item[$key] == $value);
        });

        return $this;
    }

    /**
     * Execute search for a single record by existed 'where' rules
     *
     * @return $this
     *
     * @throws SettingNotFoundException
     */
    public function firstOrFail()
    {
        $item = reset($this->data);
        $this->data = $this->newInstance();

        if(empty($item)){
            $message = $item['module'] === 'global' ? 'laravel-settings::errors.setting.not_found' : 'laravel-settings::errors.setting.not_found_in_module';
            throw new SettingNotFoundException(trans($message, ['name' => $item['name'], 'module' => $item['module']]));
        }

        $this->module = $item['module'];
        $this->name = $item['name'];
        $this->value = unserialize($item['value']);
        $this->active = (bool) $item['active'];

        return $this;
    }

    /**
     * Save setting record (update or create) to file
     */
    public function save()
    {
        $data = $this->newInstance();
        $item = reset(
            array_filter($data, function($item){
                return (bool)($item['name'] == $this->name && $item['module'] == $this->module);
            })
        );

        if(!empty($item)) {
            $data = array_map(function ($item){
                if ($this->module == $item['module'] && $this->name == $item['name']) {
                    $item['value'] = serialize($this->value);
                    $item['active'] = (bool) $this->active;
                }
                return $item;
            }, $data);
        }else {
            array_push($data, [
                'name' => $this->name,
                'value' => serialize($this->value),
                'active' => (bool) $this->active,
                'module' => $this->module,
            ]);
        }

        //unique
        $existed = array_map(function($item){
            return $item['name'].$item['module'];
        }, $data);

        $data = array_filter($data, function($item) use (&$existed) {
            if($key = array_search($item['name'].$item['module'], $existed)){
                unset($existed[$key]);
                return true;
            }else{
                return false;
            }
        });
        //unique end

        $this->setInstance($data);
        $this->data = $this->newInstance();
    }

    /**
     * Delete a record from the file
     *
     * @return $this
     */
    public function delete()
    {
        $data = $this->newInstance();
        $data = array_filter($data, function($item){
            return !(bool)($this->module == $item['module'] && $this->name == $item['name']);
        });

        $this->setInstance($data);
        $this->data = $this->newInstance();
        $this->name = null;
        $this->value = null;
        $this->active = null;
        $this->module = null;

        return $this;
    }

    /**
     * Write data to storage file
     *
     * @param array $data
     */
    protected function setInstance(array $data)
    {
        file_put_contents($this->path, json_encode($data));
    }
}