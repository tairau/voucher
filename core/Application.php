<?php

declare(strict_types = 1);

namespace Core;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Message\Response\JsonResponse;
use Sunrise\Http\Router\Exception\MethodNotAllowedException;
use Sunrise\Http\Router\Exception\RouteNotFoundException;
use Sunrise\Http\Router\Middleware\CallableMiddleware;
use Sunrise\Http\Router\Router;
use Throwable;

use function Sunrise\Http\Router\emit;

class Application extends Kernel
{
    public function run()
    {
        session_start();

        $request = $this->container->get(ServerRequestInterface::class);
        $router = $this->container->get(Router::class);

        $router->addMiddleware(
            new CallableMiddleware(function ($request, $handler) {
                try {
                    return $handler->handle($request);
                } catch (MethodNotAllowedException) {
                    return $this->container
                        ->get(ResponseFactoryInterface::class)
                        ->createResponse(405);
                } catch (RouteNotFoundException) {
                    return $this->container
                        ->get(ResponseFactoryInterface::class)
                        ->createResponse(404);
                } catch (Throwable $e) {
                    $response = new JsonResponse(500, [
                            'message' => $e->getMessage(),
                            'trace'   => $e->getTrace(),
                        ]
                    );

                    return $response;
                }
            })
        );

        emit($router->run($request));
    }
}
