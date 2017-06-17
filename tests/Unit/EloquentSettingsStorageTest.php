<?php

namespace lroman242\LaravelSettings;


class EloquentSettingsStorageTest extends AbstractSettingsStorageTest
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('settings.driver', 'eloquent');
    }
}