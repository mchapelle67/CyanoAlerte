<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('toxicity_label', [$this, 'toxicityLabel']),
        ];
    }

    public function toxicityLabel(mixed $level): string
    {
        return match ((int) $level) {
            1 => 'Faible',
            2 => 'Modérée',
            3 => 'Risque élevé',
            default => 'Non défini',
        };
    }
}
