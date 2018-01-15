<?php

return [
    'directories' => [
        'app',
        'app/Actions',
        'app/Templates',
        'config',
        'public',
        'public/js',
        'public/css',
        'tmp',
        'tmp/log',
        'tmp/cache'
    ],
    'clean' => [
        'tmp/log',
        'tmp/cache'
    ],
    'template-file' => [
        'template/config.php' => 'config/config.php',
        'template/index.php'  => 'public/index.php',
        'template/router.php' => 'config/router.php'
    ]
];
