<?php

namespace App\Service\Form;

use App\Entity\Alert;
use App\Entity\Picture;
use App\Form\AlertTypeForm;
use App\Service\MailerService;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class FormService
{
    private $formFactory;
    private $entityManager;
    private $mailerService;
    private $photoDirectory;
    private $alertLimiter;


    public function __construct(
        EntityManagerInterface $entityManager, 
        FormFactoryInterface $formFactory, 
        MailerService $mailerService,
        #[Autowire('%kernel.project_dir%/public/uploads/photos')] string $photoDirectory,
        #[Autowire(service: 'limiter.alert_form')] RateLimiterFactory $alertLimiter
    )
    {
        // pour créer le form (pas de fonction hérité de abstractController dans un service)
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
        $this->photoDirectory = $photoDirectory;
        $this->alertLimiter = $alertLimiter;
        
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
            
            // vérifier le rate limit
            $rateLimitError = $this->checkRateLimit($request);
            if ($rateLimitError) {
                return [
                    'success' => false,
                    'form' => $form,
                    'message' => $rateLimitError
                ];
            }
            
            // gère l'upload de photos
            $uploadError = $this->handlePhotoUpload($alert, $form);
            if ($uploadError) {
                // Ajoute l'erreur au champ photos
                $form->get('waterbody')->get('photos')->addError(new FormError($uploadError));
                return [
                    'success' => false,
                    'form' => $form,
                    'message' => $uploadError
                ];
            }
            
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
            // $this->mailerService->sendEmail(
            //     $alert->getEmail(),
            //     'Merci pour votre contribution !',
            //     'emails/twig/creatorAlert.html.twig',
            //     'emails/txt/creatorAlert.txt.twig',
            //     ['alert' => $alert] 
            // );

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
    
    private function handlePhotoUpload(Alert $alert, FormInterface $form): ?string    
    {
        /** @var UploadedFile[] $photosFile */
        $photosFile = $form->get('waterbody')->get('photos')->getData();
            
        // si une photo est uploadée
        if ($photosFile) {
            // on boucle pour récupérer toutes les photos
            foreach ($photosFile as $photoFile) {
                // hash le nom en slug, nécessaire pour éviter les problèmes de sécurité
                $safeFilename = hash_file('sha256', $photoFile->getPathname());
                // on récupère l'extension
                $extension = $photoFile->guessExtension();
                // on génère un nom de fichier unique en ajoutant un id pour éviter les conflits
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;
                
                // on déplace le fichier dans le répertoire des photos
                try {
                    $photoFile->move($this->photoDirectory, $newFilename);
                } catch (FileException $e) {
                    return 'Erreur lors de l\'upload de la photo : ' . $e->getMessage(); 
                }
                   
                                // on crée une nouvelle entité Photo
                $photo = new Picture();
                $photo->setUrl($newFilename);
                                $alert->addPicture($photo);
            }
        }
        return null; 
    }

    private function checkRateLimit(Request $request): ?string
    {
        // utilise l'IP du client comme identifiant unique
        $limiter = $this->alertLimiter->create($request->getClientIp());
        
        // tente de consommer 1 jeton
        if (!$limiter->consume(1)->isAccepted()) {
            return 'Trop de formulaires envoyés. Veuillez patienter avant de soumettre un nouveau signalement.';
        }
        
        return null;
    }
}
