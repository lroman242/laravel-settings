<?php

namespace lroman242\LaravelSettings;


class DbSettingsStorageTest extends AbstractSettingsStorageTest
{
    protected function getEnvironmentSetUp()
    {
        parent::getEnvironmentSetUp();

        $this->app['config']->set('settings.driver', 'db');
        $this->app->make('Settings');
    }
}