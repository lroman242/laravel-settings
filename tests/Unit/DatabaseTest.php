<?php

namespace lroman242\LaravelSettings;

use Illuminate\Support\Facades\Config;
use lroman242\LaravelSettings\Tests\TestCase;

class DatabaseTest extends TestCase
{
    public function testTable()
    {
        /** @var \Illuminate\Database\Schema\Builder $schemaBuilder */
        $schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();

        $this->assertTrue($schemaBuilder->hasTable(Config::get('settings.table', 'settings')));
    }

    public function testColumns()
    {
        /** @var \Illuminate\Database\Schema\Builder $schemaBuilder */
        $schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();

        $this->assertTrue($schemaBuilder->hasColumns(Config::get('settings.table', 'settings'), [
            'id',
            'module',
            'name',
            'value',
            'active',
            'created_at',
            'updated_at',
        ]));
    }
}