<?php

namespace App\Form;

use App\Entity\Alert;
use App\Form\WaterbodyForm;
use App\Entity\ToxicityLevel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AlertTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source', TextType::class, [
                'required' => false,
                'label' => "Source de l'information"
            ])
            ->add('description', TextType::class, [
                'required' => false, 
                'label' => "Description des symptômes", 
                'attr' => [
                    'rows' => 3
                ]
            ])
            ->add('waterbody', WaterbodyForm::class)
            ->add('toxicity_level', EntityType::class, [     
                'class' => ToxicityLevel::class,    
                'choice_label' => 'level',          
                'label' => "Niveau de suspicion"
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer l'alerte",
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
