<?php

namespace App\Service\Api;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeApiClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(API_BASE_URL)%')] private readonly string $apiBaseUrl,
    ) {
    }

    public function fetchAlerts(): array
    {
        $response = $this->httpClient->request('GET', rtrim($this->apiBaseUrl, '/') . '/api/alerts', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => [
                'order[created_at]' => 'DESC',
                'itemsPerPage' => 100,
            ],
        ]);

        $data = $response->toArray(false);
        $alerts = $data['hydra:member'] ?? $data['member'] ?? $data;

        if (!is_array($alerts) || !array_is_list($alerts)) {
            return [];
        }

        usort($alerts, static function (array $left, array $right): int {
            $leftTimestamp = isset($left['created_at']) ? strtotime((string) $left['created_at']) : 0;
            $rightTimestamp = isset($right['created_at']) ? strtotime((string) $right['created_at']) : 0;

            return $rightTimestamp <=> $leftTimestamp;
        });

        return $alerts;
    }
}