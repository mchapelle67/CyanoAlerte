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
                'order[created_at]' => 'desc',
                'itemsPerPage' => 100,
            ],
        ]);

        $data = $response->toArray(false);

        if (isset($data['hydra:member']) && is_array($data['hydra:member'])) {
            return $data['hydra:member'];
        }

        if (isset($data['member']) && is_array($data['member'])) {
            return $data['member'];
        }

        return is_array($data) && array_is_list($data) ? $data : [];
    }
}