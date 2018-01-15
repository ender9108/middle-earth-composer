<?php
use EnderLab\MiddleEarth\Application\App;

return [
    'app.env'                   => \DI\env('ENV', App::ENV_PROD),
    'app.cache.path'            => 'tmp/cache',
    'app.enableErrorHandler'    => \DI\env('ERROR', true),

    'template.path'             => 'app/Templates/',
    'template.ext'              => 'php',
    'template.engine'           => \DI\object(Engine::class)->constructor(
        \DI\get('template.path')
    )->method(
        'setFileExtension',
        \DI\get('template.ext')
    )
];
