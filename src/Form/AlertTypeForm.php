<?php

namespace App\Form;

use App\Entity\Alert;
use App\Form\WaterbodyForm;
use App\Entity\ToxicityLevel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AlertTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'required' => false, 
                'label' => "Description des symptômes", 
                'attr' => [
                    'rows' => 4, 
                    'placeholder' => "Décrivez ce que vous avez observé: couleur de l'eau, présence d'écume, odeur, mortalisé de poisson..."
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Email", 
                'attr' => [
                    'placeholder' => "ex: email@email.com"
                ]
            ])
            ->add('waterbody', WaterbodyForm::class)
            ->add('toxicity_level', EntityType::class, [     
                'class' => ToxicityLevel::class,    
                'choice_label' => 'level',          
                'label' => "Niveau de suspicion"
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Signaler",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alert::class,
        ]);
    }
}
