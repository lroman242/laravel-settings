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

        if (!file_exists(dirname($this->path))) {
            mkdir(dirname($this->path), 0777, true);
        }

        if (!file_exists($this->path)) {
            file_put_contents($this->path, json_encode([]));
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

        if (!is_array($data) || empty($data)) {
            $data = [];
        }

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
        $this->data = array_filter($this->data, function ($item) use ($key, $value) {
            return (bool) ($item[$key] == $value);
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

        if (empty($item)) {
            throw new SettingNotFoundException();
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

        $findData = array_filter($data, function ($item) {
            return (bool) ($item['name'] == $this->name && $item['module'] == $this->module);
        });

        $item = reset($findData);

        if (!empty($item)) {
            $data = array_map(function ($item) {
                if ($this->module == $item['module'] && $this->name == $item['name']) {
                    $item['value'] = serialize($this->value);
                    $item['active'] = (bool) $this->active;
                    $item['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');
                }

                return $item;
            }, $data);
        } else {
            array_push($data, [
                'name'       => $this->name,
                'value'      => serialize($this->value),
                'active'     => (bool) $this->active,
                'module'     => $this->module,
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                'updated_at' => null,
            ]);
        }

        //unique
        $existed = array_map(function ($item) {
            return $item['name'] . $item['module'];
        }, $data);

        $data = array_filter($data, function ($item) use (&$existed) {
            $key = array_search($item['name'] . $item['module'], $existed);
            if ($key !== false) {
                unset($existed[$key]);

                return true;
            } else {
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
        $data = array_filter($data, function ($item) {
            return !(bool) ($this->module == $item['module'] && $this->name == $item['name']);
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