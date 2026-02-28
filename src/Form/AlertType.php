<?php

namespace App\Form;

use App\Entity\Alert;
use App\Entity\ToxicityLevel;
use App\Form\WaterbodyForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class AlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => "Description des symptômes",
                'attr' => [
                    'rows' => 4,
                    'placeholder' => "Décrivez ce que vous avez observé: couleur de l'eau, présence d'écume, odeur, mortalité de poissons..."
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
            ->add('photos', FileType::class, [
                'label' => 'Photos',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '10M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif',
                                    'image/webp'
                                ],
                                'mimeTypesMessage' => '<span class="my-2 text-red-700">Veuillez uploader une image valide (jpg, jpeg, png, gif, webp)</span>',
                                'maxSizeMessage' => '<span class="my-2 text-red-700">Le fichier est trop volumineux ({{ size }} {{ suffix }}). Maximum autorisé : {{ limit }} {{ suffix }}.</span>'
                            ]),
                        ],
                    ]),
                ]
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
