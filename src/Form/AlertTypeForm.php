<?php

namespace App\Form;

use App\Entity\Alert;
use App\Form\WaterbodyForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AlertTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('toxicity_alert', TextType::class, [
                'label' => "Niveau d'alerte"
            ])
            ->add('source', TextType::class, [
                'required' => false,
                'label' => "Source de l'information"
            ])
            ->add('description', TextType::class, [
                'required' => false, 
                'label' => "Description", 
                'attr' => [
                    'rows' => 3
                ]
            ])
            ->add('waterbody', WaterbodyForm::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alert::class,
        ]);
    }
}
