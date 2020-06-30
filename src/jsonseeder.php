<?php

return [
    /*
      * Directory where to find/store seeds
      */
    'directory' => env('JSON_SEEDS_DIRECTORY', 'database/json'),

    /*
     * Ignore these tables when creating seeds
     */
    'ignore-tables' => [
        'migrations',
        'failed_jobs',
        'password_resets',
    ],

    /*
     * Do not create a seed when the table is empty
     */
    'ignore-empty-tables' => false,
];
