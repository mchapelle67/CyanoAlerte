<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextType::class, [
                'label' => "Raison du signalement",
                'attr' => [
                    'placeholder' => 'ex: Lac de Kruth'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => "Details supplémentaires (facultatif)",
                'attr' => [
                    'rows' => 4,
                    'placeholder' => "Ajouter des informations complémentaires ..."
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer le signalement",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
