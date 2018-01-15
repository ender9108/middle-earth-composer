<?php

use EnderLab\MiddleEarth\Application\AppFactory;
use GuzzleHttp\Psr7\Response;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

chdir(dirname(__DIR__));

// autoload
include 'vendor/autoload.php';

$app = AppFactory::create('config/');

$app->enableRouterHandler();
$app->enableDispatcherHandler();

$app->pipe(function (ServerRequestInterface $request, RequestHandlerInterface $requestHandler) {
    return new Response(404, [], '<center><h1>404 not found !!</h1></center>');
});

$app->run();
