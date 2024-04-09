<?php

namespace Humbrain\Framework\renderer;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{

    private Environment $twig;
    private FilesystemLoader $loader;

    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        try {
            $this->loader->addPath($path, $namespace);
        } catch (LoaderError $e) {
            echo $e->getMessage();
        }
    }

    public function render(string $view, array $params = []): string
    {
        try {
            return $this->twig->render($view . '.twig', $params);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return $e->getMessage();
        }
    }

    public function addGlobal(string $key, string $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
