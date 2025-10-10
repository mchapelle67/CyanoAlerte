<?php

namespace App\Form;

use App\Entity\Waterbody;
use App\Entity\WaterbodyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

class WaterbodyForm extends AbstractType
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
                'class' => WaterbodyType::class,    
                'choice_label' => 'type',          
                'label' => 'Type de plan d\'eau'
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
