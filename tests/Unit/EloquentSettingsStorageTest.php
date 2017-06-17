<?php

namespace lroman242\LaravelSettings;


class EloquentSettingsStorageTest extends AbstractSettingsStorageTest
{
    protected function getEnvironmentSetUp()
    {
        parent::getEnvironmentSetUp();

        $this->app['config']->set('settings.driver', 'eloquent');
    }
}