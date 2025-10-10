<?php

namespace App\Form;

use App\Entity\Waterbody;
use App\Entity\WaterbodyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WaterbodyTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom du plan d'eau"
            ])
            ->add('latitude')
            ->add('longitude')
            ->add('region', TextType::class, [
                'label' => 'Region'
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('type', EntityType::class, [
                'class' => self::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Waterbody::class,
        ]);
    }
}
