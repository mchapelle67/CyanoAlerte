<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'TerciaryButton', template: 'components/Button/TerciaryButton.html.twig')]
class TerciaryButton
{
    public string $label = 'Retour';
    public string $type = 'button';
    public string $class = '';
    public string $direction = 'left';
}
