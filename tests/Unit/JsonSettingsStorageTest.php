<?php

namespace lroman242\LaravelSettings;


class JsonSettingsStorageTest extends AbstractSettingsStorageTest
{
    protected function getEnvironmentSetUp()
    {
        parent::getEnvironmentSetUp();

        $this->app['config']->set('settings.driver', 'json');
    }
}