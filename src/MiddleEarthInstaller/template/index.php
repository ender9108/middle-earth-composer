<?php

use EnderLab\MiddleEarth\Application\AppFactory;
use GuzzleHttp\Psr7\Response;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

chdir(dirname(__DIR__));

// autoload
include 'vendor/autoload.php';

$app = AppFactory::create('config/');

$app->get('/', function (ServerRequestInterface $request, RequestHandlerInterface $requestHandler) {
    $response = $requestHandler->handle($request);
    $response->getBody()->write('<center><h1>Welcome to middle earth framework !!!</h1></center>');

    return $response;
});

$app->enableRouterHandler();
$app->enableDispatcherHandler();

$app->pipe(function (ServerRequestInterface $request, RequestHandlerInterface $requestHandler) {
    return new Response(404, [], '<center><h1>404 not found !!</h1></center>');
});

$app->run();
