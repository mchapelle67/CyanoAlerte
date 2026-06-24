<?php

namespace App\Form;

use App\Entity\ToxicityLevel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', SearchType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('toxicity', EntityType::class, [
                'class' => ToxicityLevel::class,
                'choice_label' => function (?ToxicityLevel $level) {
                    if (!$level) {
                        return '';
                    }

                    return match ($level->getId()) {
                        1 => 'Niveau faible',
                        2 => 'Niveau modéré',
                        3 => 'Niveau fort',
                        default => 'Niveau ' . $level->getLevel(),
                    };
                },
                'required' => false,
                'placeholder' => 'Tous les niveaux',
            ])
            ->add('period', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Toutes les périodes',
                'choices' => [
                    'Moins de 24h' => '24h',
                    'Moins de 3 jours' => '3d',
                    'Moins de 7 jours' => '7d',
                    'Moins de 30 jours' => '30d',
                ],
            ])
            ->add('department', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Tous les départements',
                'choices' => $options['departments'] ?? [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'departments' => [],
        ]);
    }
}