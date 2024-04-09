<?php

namespace Humbrain\Framework\renderer;

class PHPRenderer implements RendererInterface
{
    const DEFAULT_NAMESPACE = '__MAIN';
    private array $paths = [];
    private array $globals = [];

    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) :
            $this->addPath(self::DEFAULT_NAMESPACE, $defaultPath);
        endif;
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) :
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        else :
            $this->paths[$namespace] = $path;
        endif;
    }

    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) :
            $path = $this->replaceNamespace($view) . '.php';
        else :
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        endif;
        ob_start();
        extract($params);
        extract($this->globals);
        require($path);
        return ob_get_clean();
    }

    public function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    public function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }

    public function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    public function addGlobal(string $key, mixed $value): void
    {
        $this->globals[$key] = $value;
    }
}
