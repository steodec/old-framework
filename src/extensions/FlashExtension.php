<?php

namespace Humbrain\Framework\extensions;

use Humbrain\Framework\sessions\FlashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashExtension extends AbstractExtension
{
    public function __construct(private readonly FlashService $flash)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [$this, 'getFlash'])
        ];
    }

    public function getFlash(string $key): ?string
    {
        return $this->flash->get($key);
    }
}
