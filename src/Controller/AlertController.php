<?php

namespace App\Controller;

use App\Repository\AlertRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AlertController extends AbstractController
{
    #[Route('/alertes', name: 'app_alerts')]
    public function alertAll(AlertRepository $alertRepository): Response
    {
        $alerts = $alertRepository->findBy([], ['created_at' => 'DESC']);

        return $this->render('alert/alerts.html.twig', [
            'controller_name' => 'AlertController',
            'alerts' => $alerts
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
