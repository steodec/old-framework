<?php

namespace Humbrain\Framework\renderer;

interface RendererInterface
{
    public function addPath(string $namespace, ?string $path = null): void;

    public function render(string $view, array $params = []): string;

    public function addGlobal(string $key, string $value): void;
}
