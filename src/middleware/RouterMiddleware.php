<?php

namespace Humbrain\Framework\middleware;

use GuzzleHttp\Psr7\ServerRequest;
use Humbrain\Framework\router\Router;

class RouterMiddleware
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function __invoke(ServerRequest $request, callable $next)
    {
        $route = $this->router->match($request);
        if (is_null($route)) :
            return $next($request);
        endif;
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $request = $request->withAttribute(get_class($route), $route);
        return $next($request);
    }
}
