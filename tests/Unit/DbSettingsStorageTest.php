<?php

namespace lroman242\LaravelSettings;


class DbSettingsStorageTest extends AbstractSettingsStorageTest
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('settings.driver', 'db');
    }
}