<?php

namespace App\Controller;

use App\Repository\AlertRepository;
use App\Service\AlertsDataProvider;
use App\Service\FormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AlertController extends AbstractController
{
    #[Route('/alertes', name: 'app_alerts')]
    public function alertsAll(FormService $formService, Request $request, AlertsDataProvider $alertsDataProvider): Response
    {
        $result = $formService->handleAlertForm($request);
        $alertsData = $alertsDataProvider->getAlertsData();
        
        if ($result['success']) {
            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('app_home');
        }
        
        if ($result['message']) {
            $this->addFlash('error', $result['message']);
        }

        return $this->render('alert/alertsAll.html.twig', [
            'controller_name' => 'AlertController',
            'alert_form' => $result['form']->createView(),
            'alertsData' => $alertsData
        ]);
    }

    #[Route('/alerte/{id}', name: 'app_alert_detail', requirements: ['id' => '\\d+'])]
    public function alertDetail(Request $request, AlertRepository $alertRepository): Response
    {
        $id = $request->attributes->get('id');
        $alert = $alertRepository->find($id);

        if (!$alert) {
            $this->addFlash('error', 'Alerte inconnue.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('alert/alertDetail.html.twig', [
            'controller_name' => 'AlertController',
            'id' => $id,
            'alert' => $alert
        ]);
    }
}
