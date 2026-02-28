<?php

namespace App\Controller;

use App\Service\Data\HomeDataProvider;
use App\Service\Form\AlertFormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController 
{
    #[Route('/accueil', name: 'app_home')]
    public function index(AlertFormService $formService, Request $request, HomeDataProvider $homeDataProvider): Response
    {
        $result = $formService->handleAlertForm($request);
        $homeData = $homeDataProvider->getHomeData();
        
        if ($result['success']) {
            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('app_home');
        }
        
        if ($result['message']) {
            $this->addFlash('error', $result['message']);
        }

        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'alert_form' => $result['form']->createView(),
            'homeData' => $homeData
        ]);
    }
}
