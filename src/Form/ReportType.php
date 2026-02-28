<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', ChoiceType::class, [
                'label' => "Raison du signalement *",
                'choices' => [
                    'Contenu inapproprié' => 'inappropriate_content',
                    'Information erronée' => 'false_information',
                    'Doublon' => 'duplicate',
                    'Problème résolu' => 'resolved_issue',
                    'Spam ou publicité' => 'spam_or_pub',
                    'Autre' => 'other',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('comment', TextareaType::class, [
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
