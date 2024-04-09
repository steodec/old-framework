<?php

namespace Humbrain\Framework\modules;

use Humbrain\Framework\renderer\RendererInterface;
use Humbrain\Framework\router\Router;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Module
{

    const DEFINITIONS = null;
    const MIGRATIONS = null;
    const SEEDS = null;

    protected Router $router;
    protected RendererInterface $renderer;

    public function __construct(ContainerInterface $container)
    {

        try {
            $this->router = $container->get(Router::class);
            $this->renderer = $container->get(RendererInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return;
        }
    }
}
