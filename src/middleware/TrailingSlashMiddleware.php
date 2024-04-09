<?php

namespace Humbrain\Framework\middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class TrailingSlashMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && str_ends_with($uri, '/')) {
            $uri = substr($uri, 0, -1);
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', $uri);
        }
        return $next($request);
    }
}
