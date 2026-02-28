<?php

namespace App\Controller;

use App\Service\Data\AlertsDataProvider;
use App\Service\Form\AlertFormService;
use App\Service\Form\ReportFormService;
use App\Service\SlugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AlertController extends AbstractController
{
    #[Route('/alertes', name: 'app_alerts')]
    public function alertsAll(AlertFormService $formService, Request $request, AlertsDataProvider $alertsDataProvider, SlugService $slugService): Response
    {
        $result = $formService->handleAlertForm($request);
        $alertsData = $alertsDataProvider->getAlertsData();
        $alertsData['alerts'] = $slugService->addSlugs($alertsData['alerts'] ?? []);
        
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

    #[Route('/alerte/{slug}', name: 'app_alert_detail')]
    public function alertDetail(string $slug, AlertFormService $formService, AlertsDataProvider $alertsDataProvider, Request $request, SlugService $slugService, ReportFormService $reportFormService): Response
    {
        $result = $formService->handleAlertForm($request);
        $reportResult = $reportFormService->sendReport($request);

        // Gestion du feedback pour le formulaire d'alerte
        if ($result['success']) {
            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('app_home');
        }
        if ($result['message']) {
            $this->addFlash('error', $result['message']);
        }

        // Gestion du feedback pour le formulaire de signalement
        if (isset($reportResult['success']) && $reportResult['success']) {
            $this->addFlash('success', $reportResult['message']);
            return $this->redirectToRoute('app_alert_detail', ['slug' => $slug]);
        }
        if (isset($reportResult['message']) && $reportResult['message']) {
            $this->addFlash('error', $reportResult['message']);
        }

        $alertsData = $alertsDataProvider->getAlertsData();
        $alert = $slugService->findAlertBySlug($alertsData['alerts'] ?? [], $slug);

        if (!$alert) {
            $this->addFlash('error', 'Alerte inconnue.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('alert/alertDetail.html.twig', [
            'controller_name' => 'AlertController',
            'slug' => $slug,
            'alert_form' => $result['form']->createView(),
            'alert' => $alert,
            'report_form' => isset($reportResult['form']) ? $reportResult['form']->createView() : $reportFormService->createReportForm()->createView(),
        ]);
    }
}
