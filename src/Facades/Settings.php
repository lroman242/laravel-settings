<?php
namespace lroman242\LaravelSettings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \lroman242\LaravelSettings\Settings
 */
class Settings extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Settings';
    }
}