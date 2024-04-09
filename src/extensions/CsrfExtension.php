<?php

namespace Humbrain\Framework\extensions;

use Humbrain\Framework\middleware\CsrfMiddleware;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{
    private CsrfMiddleware $csrfMiddleware;

    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    final public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => ['html']])
        ];
    }

    final public function csrfInput(): string
    {
        return '<input type="hidden" 
            name="' . $this->csrfMiddleware->getFormKey() . '" 
            value="' . $this->csrfMiddleware->generateToken() . '"/>';
    }
}
