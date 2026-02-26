<?php

namespace App\Service;

use App\Service\Api\HomeApiClient;

class AlertsDataProvider
{
    public function __construct(
        private readonly HomeApiClient $homeApiClient,
    ) {
    }

    public function getAlertsData(): array
    {
        $alerts = $this->homeApiClient->fetchAlerts();

        return [
            'alerts' => $alerts
        ];
    }
}
