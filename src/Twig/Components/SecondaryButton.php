<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'SecondaryButton', template: 'components/Button/SecondaryButton.html.twig')]
class SecondaryButton
{
    public string $text = 'Découvrir';
    public string $class = '';
    public string $href = '#';
}
