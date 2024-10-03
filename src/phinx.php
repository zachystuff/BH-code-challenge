<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'pgsql',
            'host' => 'postgres',
            'name' => 'main',
            'user' => 'myuser',
            'pass' => 'mypassword',
            'port' => '5432',
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];
