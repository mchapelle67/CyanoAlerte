<?php

namespace App\Service\Data;

use App\Service\Api\HomeApiClient;

class HomeDataProvider
{
    public function __construct(
        private readonly HomeApiClient $homeApiClient,
    ) {
    }

    public function getHomeData(): array
    {
        $alerts = $this->homeApiClient->fetchAlerts();

        $lastAlerts = array_slice($alerts, 0, 10);

        $alertsActives = array_filter($alerts, fn($alert) => (int) ($alert['toxicity_level']['level'] ?? 0) >= 1);

        $departments = [];
        foreach ($alerts as $alert) {
            $department = $alert['waterbody']['department'] ?? null;

            // Ignore les valeurs non exploitables (tableau, objet, etc.)
            if (!is_scalar($department)) {
                continue;
            }

            // Normalise la valeur (string + suppression des espaces)
            $department = trim((string) $department);

            // Ignore les départements vides
            if ($department === '') {
                continue;
            }

            // Ajoute le département comme clé pour garantir l'unicité
            $departments[$department] = true;
        }

        $highLevel = array_filter($alerts, fn($alert) => $alert['toxicity_level']['level'] === 3);

        $newAlerts = array_filter($alerts, fn($alert) => $alert['created_at'] >= (new \DateTime('-7 days'))->format('Y-m-d'));

        return [
            'alertsActivesCount' => count($alertsActives),
            'departmentsCount' => count($departments),
            'highLevelCount' => count($highLevel),
            'newAlertsCount' => count($newAlerts),
            'lastAlerts' => $lastAlerts,
        ];
    }
}
