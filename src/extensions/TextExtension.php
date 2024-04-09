<?php

namespace Humbrain\Framework\extensions;

use Twig\Extension\AbstractExtension;

class TextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new \Twig\TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    public function excerpt(?string $content, int $limit = 100): string
    {
        if ($content === null) :
            return '';
        endif;
        if (mb_strlen($content) <= $limit) :
            return $content;
        endif;
        $excerpt = mb_substr($content, 0, $limit);
        $lastSpace = mb_strrpos($excerpt, ' ');
        return mb_substr($excerpt, 0, $lastSpace) . '...';
    }
}
