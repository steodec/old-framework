<?php

namespace Humbrain\Framework\renderer;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory
{
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $debug = $container->get('env') !== 'PROD';
        $viewPath = $container->get('views.path');
        $loader = new FilesystemLoader($viewPath);
        $twig = new Environment(
            $loader,
            [
                'cache' => $debug ? false : dirname(__DIR__) . '/tmp/views',
                'debug' => $debug,
                'auto_reload' => $debug,
            ]
        );
        $twig->addExtension(new DebugExtension());
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
        return new TwigRenderer($loader, $twig);
    }
}
