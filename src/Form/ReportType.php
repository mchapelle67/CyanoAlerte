<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', RadioType::class, [
                'label' => "Raison du signalement",
                'choices' => [
                    'Contenu inapproprié' => 'inappropriate_content',
                    'Information erronée' => 'false_information',
                    'Doublon' => 'duplicate',
                    'Problème resolue' => 'resolved_issue',
                    'Spam ou publicité' => 'spam_or_pub',
                    'Autre' => 'other',
                ],
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
