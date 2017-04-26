<?php

return [
    /**
     * Database table name in
     */
    'table' => 'settings',

    /**
     * DB Connection which will be used to get settings
     */
    'connection' => null,

    /**
     * Driver to manage settings
     * Available: json, db, eloquent
     */
    'driver' => 'eloquent',

    /**
     * Path to save json storage file (if driver is json)
     */
    'path' => storage_path('app/vendor/settings/settings.json'),

];