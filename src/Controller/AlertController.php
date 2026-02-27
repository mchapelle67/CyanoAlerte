<?php

namespace App\Controller;

use App\Service\AlertsDataProvider;
use App\Service\FormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class AlertController extends AbstractController
{
    public function __construct(
        private readonly SluggerInterface $slugger,
    ) {
    }

    #[Route('/alertes', name: 'app_alerts')]
    public function alertsAll(FormService $formService, Request $request, AlertsDataProvider $alertsDataProvider): Response
    {
        $result = $formService->handleAlertForm($request);
        $alertsData = $alertsDataProvider->getAlertsData();
        $alertsData['alerts'] = $this->addSlugs($alertsData['alerts'] ?? []);
        
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
    public function alertDetail(FormService $formService, AlertsDataProvider $alertsDataProvider, Request $request): Response
    {
        $result = $formService->handleAlertForm($request);

        if ($result['success']) {
            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('app_home');
        }

        if ($result['message']) {
            $this->addFlash('error', $result['message']);
        }

        $slug = (string) $request->attributes->get('slug');
        $alertsData = $alertsDataProvider->getAlertsData();
        $alerts = $this->addSlugs($alertsData['alerts'] ?? []);
        $alert = null;

        foreach ($alerts as $item) {
            if (($item['detail_slug'] ?? null) === $slug) {
                $alert = $item;
                break;
            }
        }

        if (!$alert) {
            $this->addFlash('error', 'Alerte inconnue.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('alert/alertDetail.html.twig', [
            'controller_name' => 'AlertController',
            'slug' => $slug,
            'alert_form' => $result['form']->createView(),
            'alert' => $alert,
        ]);
    }

    private function addSlugs(array $alerts): array
    {
        foreach ($alerts as $index => $alert) {
            if (is_array($alert)) {
                $alerts[$index]['detail_slug'] = $this->makeSlug($alert);
            }
        }

        return $alerts;
    }

    private function makeSlug(array $alert): string
    {
        $waterbody = is_array($alert['waterbody'] ?? null) ? $alert['waterbody'] : [];
        $name = (string) ($waterbody['name'] ?? 'plan-eau');
        $department = (string) ($waterbody['department'] ?? 'departement');

        try {
            $createdAt = new \DateTimeImmutable((string) ($alert['created_at'] ?? 'now'));
            $timestamp = $createdAt->format('Y-m-d-H-i-s');
        } catch (\Exception) {
            $timestamp = 'date-inconnue';
        }

        $base = sprintf('%s-%s-%s', $name, $department, $timestamp);

        return strtolower($this->slugger->slug($base)->toString());
    }
}
