<?php

namespace Humbrain\Framework\middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CombinedMiddlewareDelegate implements RequestHandlerInterface
{

    /** @var string[] */
    private array $middlewares = [];
    private int $index = 0;
    private ContainerInterface $container;
    private RequestHandlerInterface $handler;

    public function __construct(ContainerInterface $container, array $middlewares, RequestHandlerInterface $handler)
    {
        $this->middlewares = $middlewares;
        $this->container = $container;
        $this->handler = $handler;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            return $this->handler->handle($request);
        } elseif (is_callable($middleware)) {
            $response = call_user_func_array($middleware, [$request, [$this, 'handle']]);
            if (is_string($response)) {
                return new Response(200, [], $response);
            }
            return $response;
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
        return new Response(500, [], 'Internal Server Error');
    }

    /**
     * @return object|string|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getMiddleware(): object|string|null
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
