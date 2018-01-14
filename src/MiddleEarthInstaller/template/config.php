<?php

use EnderLab\MiddleEarth\Application\App;

return [
    'app.env'                => \DI\env('ENV', App::ENV_PROD),
    'app.enableErrorHandler' => \DI\env('ERROR', true)
];
