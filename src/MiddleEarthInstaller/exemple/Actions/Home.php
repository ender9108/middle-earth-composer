<?php
namespace App\Actions;

use EnderLab\Middleware\BaseMiddleware;
use GuzzleHttp\Psr7\Response;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Home extends BaseMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $templateEngine = $this->container->get('template.engine');

        $response = new Response();
        $response->getBody()->write(
            $templateEngine->render(
                'home',
                [
                    'title' => 'Test Middle-Earth-Framework',
                    'message' => 'Hello world !!!'
                ]
            )
        );

        return $response;
    }
}