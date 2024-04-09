<?php

namespace Humbrain\Framework\extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MethodExtension extends AbstractExtension
{

    const METHOD = [
        'PUT', 'DELETE', 'PATCH'
    ];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('method', [$this, 'method'], ['is_safe' => ['html']])
        ];
    }

    public function method(string $method): string
    {
        if (!in_array($method, self::METHOD)) :
            return '';
        endif;
        return "<input type='hidden' name='_method' value='$method'>";
    }
}
