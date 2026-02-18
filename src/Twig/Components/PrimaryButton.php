<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'PrimaryButton', template: 'components/Button/PrimaryButton.html.twig')]
class PrimaryButton
{
    public string $id = '';
    public string $text = 'Découvrir';
    public ?string $icon = null;
    public string $type = 'button';
    public string $modalToggle = '';
    public string $class = '';
}
