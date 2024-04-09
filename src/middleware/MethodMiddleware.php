<?php

namespace Humbrain\Framework\middleware;

class MethodMiddleware
{
    public function __invoke($request, $next)
    {
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_method', $parsedBody)
            && in_array($parsedBody['_method'], ['DELETE', 'PUT', 'PATCH'])) :
            $request = $request->withMethod($parsedBody['_method']);
        endif;
        return $next($request);
    }
}
