<?php

namespace App\Form;

use App\Validator\HoneyPot;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HoneyPotType extends EmailType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "mapped" => false,
            "required" => false,
            "row_attr" => [
                'style' => "display:none;"
            ],
            "constraints" => [
                new HoneyPot()
            ]
        ]);
    }
}