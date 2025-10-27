<?php

namespace App\Controller;

use App\Service\ApiService;
use App\Service\FormService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController 
{
    #[Route('/cyanoalerte/accueil', name: 'app_home')]
    public function index(FormService $formService, Request $request): Response
    {
        $form = $formService->createAlertForm();
        $result = $formService->handleAlertForm($request);
        
        if ($result['success']) {
            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('app_home');
        } else {
            $this->addFlash('error', $result['message']);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'alert_form' => $form->createView()
        ]);
    }
}
