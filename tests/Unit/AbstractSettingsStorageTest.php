<?php

namespace lroman242\LaravelSettings;

use lroman242\LaravelSettings\Exceptions\SettingNotFoundException;
use lroman242\LaravelSettings\Tests\TestCase;

abstract class AbstractSettingsStorageTest extends TestCase
{
    public function testHas()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_has', 'test_has_value');

        $this->assertTrue($settings->has('test_has'));

        $this->assertFalse($settings->has('test_has_not_found'));
        $this->expectException(SettingNotFoundException::class);
        $settings->isActive('test_has_not_found');
    }

    public function testStringValue()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_string', 'test_value');

        $this->assertTrue($settings->has('test_string'));
        $this->assertInternalType('string', $settings->get('test_string'));
    }

    public function testIntegerValue()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_integer', 5);

        $this->assertTrue($settings->has('test_integer'));
        $this->assertInternalType('integer', $settings->get('test_integer'));
    }

    public function testArrayValue()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_array', ['1', 5, null]);

        $this->assertTrue($settings->has('test_array'));
        $this->assertInternalType('array', $settings->get('test_array'));
    }

    public function testNullValue()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_null', null);

        $this->assertTrue($settings->has('test_null'));
        $this->assertNull($settings->get('test_null'));
    }

    public function testBooleanValue()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_false', false);

        $this->assertTrue($settings->has('test_false'));
        $this->assertFalse($settings->get('test_false'));

        $settings->set('test_true', true);

        $this->assertTrue($settings->has('test_true'));
        $this->assertTrue($settings->get('test_true'));
    }

    public function testActive()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_active', 'test_value', 'global', true);

        $this->assertTrue($settings->has('test_active'));
        $this->assertTrue($settings->isActive('test_active'));

        $this->assertFalse($settings->has('test_active_not_found'));
        $this->expectException(SettingNotFoundException::class);
        $settings->isActive('test_active_not_found');
    }

    public function testInactive()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_inactive', 'test_value', 'global', false);

        $this->assertTrue($settings->has('test_inactive'));
        $this->assertFalse($settings->isActive('test_inactive'));

        $this->assertFalse($settings->has('test_inactive_not_found'));
        $this->expectException(SettingNotFoundException::class);
        $settings->isActive('test_inactive_not_found');
    }

    public function testActivate()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_activate', 'test_value', 'global', false);

        $this->assertTrue($settings->has('test_activate'));
        $this->assertFalse($settings->isActive('test_activate'));
        $settings->activate('test_activate');

        $this->assertTrue($settings->has('test_activate'));
        $this->assertTrue($settings->isActive('test_activate'));

        $this->assertFalse($settings->has('test_activate_not_found'));
        $this->expectException(SettingNotFoundException::class);
        $settings->isActive('test_activate_not_found');
    }

    public function testDeactivate()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_deactivate', 'test_value', 'global', true);

        $this->assertTrue($settings->has('test_deactivate'));
        $this->assertTrue($settings->isActive('test_deactivate'));
        $settings->deactivate('test_deactivate');

        $this->assertTrue($settings->has('test_deactivate'));
        $this->assertFalse($settings->isActive('test_deactivate'));

        $this->assertFalse($settings->has('test_deactivate_not_found'));
        $this->expectException(SettingNotFoundException::class);
        $settings->isActive('test_deactivate_not_found');
    }

    public function testDefault()
    {
        $settings = $this->app->make('Settings');

        $this->assertFalse($settings->has('test_default'));
        $this->assertEquals($settings->get('test_default', 'global', 'test_default_value'), 'test_default_value');
    }

    public function testDelete()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_delete', 'test_delete_value');

        $this->assertTrue($settings->has('test_delete'));

        $settings->delete('test_delete');

        $this->assertFalse($settings->has('test_delete'));
        $this->assertNull($settings->get('test_delete'));
    }

    public function testUpdate()
    {
        $settings = $this->app->make('Settings');

        $settings->set('test_update', 'test_value');

        $this->assertTrue($settings->has('test_update'));
        $this->assertEquals('test_value', $settings->get('test_update'));

        $settings->update('test_update', 'new_test_value');

        $this->assertNotEquals('test_value', $settings->get('test_update'));
        $this->assertEquals('new_test_value', $settings->get('test_update'));

        $this->assertFalse($settings->has('test_update_not_found'));
        $this->expectException(SettingNotFoundException::class);
        $settings->isActive('test_update_not_found');
    }
}