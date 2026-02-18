<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Card', template: 'components/Card/card.html.twig')]
class Card
{
    public string $icon = '';
    public string $title = '';
    public string $subtitle = '';
    public int $number = 0;
    public string $color = '';

}
