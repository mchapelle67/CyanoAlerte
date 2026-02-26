<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'BackButton', template: 'components/Button/BackButton.html.twig')]
class BackButton
{
    public string $text = 'Retour';
    public string $type = 'button';
    public string $class = '';
}
