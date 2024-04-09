<?php

namespace Humbrain\Framework\extensions;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(DateTime $date, string $format = "d/m/Y H:i"): string
    {
        return "<time class='timeago' datetime='{$date->format(DateTime::ATOM)}'>{$date->format($format)}</time>";
    }
}
