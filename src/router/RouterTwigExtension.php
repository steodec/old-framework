<?php

namespace Humbrain\Framework\router;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouterTwigExtension extends AbstractExtension
{

    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'pathFor']),
            new TwigFunction('is_subpath', [$this, 'isSubpath']),
        ];
    }

    public function isSubpath(string $path): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = $this->pathFor($path);
        if ($path === '/') {
            return $uri === $path;
        }
        return str_starts_with($uri, $path);
    }

    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateUri($path, $params) ?? "";
    }
}
