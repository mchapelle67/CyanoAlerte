<?php

namespace App\Service;

use App\Entity\Alert;
use App\Form\AlertTypeForm;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;


class FormService
{

    private $formFactory;
    private $entityManager;
    private $mailerService;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, MailerService $mailerService)
    {
        // pour créer le form (pas de fonction hérité de abstractController dans un service)
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
    }

    public function createAlertForm(): FormInterface 
    {
        $alert = new Alert();
        return $this->formFactory->create(AlertTypeForm::class, $alert);
    }
    
    public function handleAlertForm(Request $request): array
    {
        $form = $this->createAlertForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $alert = $form->getData();
            $this->entityManager->persist($alert);
            $this->entityManager->flush();

            // // Envoyer un email de notification à l'admin
            $this->mailerService->sendEmail(
                'admin@cyanoalerte.fr',
                'Nouveau signalement',
                'emails/twig/adminAlert.html.twig',
                'emails/txt/adminAlert.txt.twig',
                ['alert' => $alert]  
            );

            // Envoyer un email de notification au créateur du signalement
            $this->mailerService->sendEmail(
                $alert->getEmail(),
                'Merci pour votre contribution !',
                'emails/twig/creatorAlert.html.twig',
                'emails/txt/creatorAlert.txt.twig',
                ['alert' => $alert] 
            );

            return [
                'success' => true,
                'alert' => $alert,
                'message' => 'Votre signalement a bien été pris en compte.'
            ];
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            return [
                'success' => false,
                'form' => $form,
                'message' => "Votre signalement a rencontré une erreur. Veuillez nous contacter si l'erreur persiste."
            ];
        }
        
        // Formulaire non soumis
        return [
            'success' => false,
            'form' => $form,
            'message' => null
        ];
    }
}