<?php

namespace Humbrain\Framework\actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * @Trait RouterAwareAction
 */
trait RouterAwareAction
{
    /**
     * Redirection vers une route
     * @param string $path
     * @param array $param
     * @return ResponseInterface
     */
    public function redirect(string $path, array $param = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $param);
        return (new Response())
            ->withStatus(301)
            ->withHeader('location', $redirectUri);
    }
}
