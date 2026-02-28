<?php

namespace App\Service\Form;

use App\Form\ReportType;
use App\Service\MailerService;
use App\Service\RateLimiterService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ReportFormService
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly MailerService $mailerService,
        private readonly RateLimiterService $rateLimiterService,
        )
    {
    }

    public function createReportForm(): FormInterface 
    {
        return $this->formFactory->create(ReportType::class);
    }
    
    public function sendReport(Request $request): array 
    {
        $form = $this->createReportForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reportData = $form->getData();
            
            // vérifier le rate limit
            $rateLimitError = $this->rateLimiterService->checkRateLimit($request);
            if ($rateLimitError) {
                return [
                    'success' => false,
                    'form' => $form,
                    'message' => $rateLimitError
                ];
            }

            // envoyer un email de notification à l'admin
            $this->mailerService->sendEmail(
                'manon.chp68@gmail.com',
                'Une alerte a été signalée',
                'emails/twig/adminReport.html.twig',
                'emails/txt/adminReport.txt.twig',
                ['report' => $reportData]
            );

            return [
                'success' => true,
                'report' => $reportData,
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