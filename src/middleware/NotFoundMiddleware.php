<?php

namespace Humbrain\Framework\middleware;

use GuzzleHttp\Psr7\Response;

class NotFoundMiddleware
{
    public function __invoke($request)
    {
        return new Response(404, [], '<h1>Error 404</h1>');
    }
}
