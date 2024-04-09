<?php

namespace Humbrain\Framework\extensions;

use Humbrain\Framework\router\Router;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap5View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagerFantaExtension extends AbstractExtension
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
        ];
    }

    public function paginate(
        Pagerfanta $paginatedResults,
        string $route,
        array $args = [],
        array $queryArgs = []
    ): string {
        $view = new TwitterBootstrap5View();
        return $view->render($paginatedResults, function ($page) use ($args, $route, $queryArgs) {
            if ($page > 1) {
                $queryArgs['p'] = $page;
            }
            return $this->router->generateUri($route, $args, $queryArgs);
        });
    }
}
