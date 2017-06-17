<?php
namespace lroman242\LaravelSettings;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Query\Builder;
use lroman242\LaravelSettings\Exceptions\SettingNotFoundException;

class DbSettingsStorage implements SettingsStorageInterface
{
    /**
     * @var string Db Connection Name
     */
    protected $connection;

    /**
     * @var string Db Table Name
     */
    protected $table;

    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var ConnectionResolverInterface Database Manager
     */
    protected $db;

    public $name;
    public $value;
    public $active;
    public $module;

    /**
     * Create new storage instancecd
     *
     * @param ConnectionResolverInterface $db
     * @param string|null $connection
     * @param string $table
     */
    function __construct(ConnectionResolverInterface $db, $connection = null, $table = 'settings')
    {
        $this->connection = $connection !== null ? $connection : config('settings.connection');
        $this->table = !empty($table) ? $table : config('settings.table');
        $this->db = $db;
        $this->query = $this->newInstance();
    }

    /**
     * Create new connection instance to work with
     *
     * @return mixed
     */
    protected function newInstance()
    {
        $this->query = $this->db->connection($this->connection)->table($this->table);
        return $this->query;
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
        $this->query->where($key, $value);
        return $this;
    }

    /**
     * Execute a query for a single record by existed 'where' rules
     *
     * @throws SettingNotFoundException
     *
     * @return DbSettingsStorage
     */
    public function firstOrFail()
    {
        $item = $this->query->first();
        $this->query = $this->newInstance();

        if (empty($item)) {
            throw new SettingNotFoundException();
        }

        $this->module = $item->module;
        $this->name = $item->name;
        $this->value = unserialize($item->value);
        $this->active = (bool) $item->active;

        return $this;
    }

    /**
     * Save setting record (update or create) to database
     *
     * @return bool
     */
    public function save()
    {
        $query = $this->newInstance();

        return $query->updateOrInsert(
            [
                'name' => $this->name,
                'module' => $this->module,
                'updated_at' => new \DateTime()
            ],
            [
                'name' => $this->name,
                'value' => serialize($this->value),
                'active' => (bool) $this->active,
                'module' => $this->module,
                'created_at' => new \DateTime(),
            ]
        );
    }

    /**
     * Delete a record from the database
     *
     * @return bool
     */
    public function delete()
    {
        try {
            $query = $this->newInstance();
            $affectedRows = $query->where('module', $this->module)->where('name', $this->name)->delete();

            $this->query = $this->newInstance();
            $this->name = null;
            $this->value = null;
            $this->active = null;
            $this->module = null;

            return (bool) ($affectedRows > 0);
        }catch (\Exception $e){
            return FALSE;
        }
    }
}