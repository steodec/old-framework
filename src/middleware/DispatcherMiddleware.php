<?php

namespace Humbrain\Framework\middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Humbrain\Framework\router\Route;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class DispatcherMiddleware
{

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequest $request, callable $next)
    {
        $route = $request->getAttribute(Route::class);
        if (is_null($route)) :
            return $next($request);
        endif;
        if (is_string($route->getCallback())) :
            $callback = $this->container->get($route->getCallback());
        else :
            $callback = $route->getCallback();
        endif;
        $result = call_user_func_array($callback, [$request]);
        if (is_string($result)) :
            return new Response(200, [], $result);
        elseif ($result instanceof ResponseInterface) :
            return $result;
        endif;
        return $next($request);
    }
}
