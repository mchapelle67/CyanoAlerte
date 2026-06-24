<?php

namespace App\Service;

use App\Service\Data\AlertsDataProvider;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SearchBarService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ?AlertsDataProvider $alertsDataProvider = null,
    ) {
    }
    
    public function getDepartmentChoices(): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://geo.api.gouv.fr/departements', [
                'query' => ['fields' => 'code,nom'],
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 5,
            ]);

            $data = $response->toArray(false);
        } catch (\Throwable $e) {
            return [];
        }

        if (!is_array($data) || !array_is_list($data)) {
            return [];
        }

        $choices = [];
        foreach ($data as $dept) {
            if (!is_array($dept)) {
                continue;
            }

            $code = $dept['code'] ?? null;
            $nom = $dept['nom'] ?? null;

            if ($code && $nom) {
                $label = sprintf('%s (%s)', $nom, $code);
                // use department name as the value so it matches waterbody.department in your data
                $choices[$label] = (string) $nom;
            }
        }

        // Optionnel : trier par label alphabétiquement
        ksort($choices, SORT_NATURAL | SORT_FLAG_CASE);

        if (empty($choices) && $this->alertsDataProvider !== null) {
            $alertsData = $this->alertsDataProvider->getAlertsData();
            $rawAlerts = $alertsData['alerts'] ?? [];

            $fallback = [];
            foreach ($rawAlerts as $a) {
                if (is_array($a)) {
                    $dept = $a['waterbody']['department'] ?? null;
                } else {
                    $dept = $a->getWaterbody()?->getDepartment() ?? null;
                }

                if (is_scalar($dept)) {
                    $dept = trim((string) $dept);
                    if ($dept !== '') {
                        $fallback[$dept] = $dept;
                    }
                }
            }

            if (!empty($fallback)) {
                // turn into label=>value using name as value
                $choices = [];
                foreach ($fallback as $name) {
                    $choices[$name] = $name;
                }
                ksort($choices, SORT_NATURAL | SORT_FLAG_CASE);
            }
        }

        return $choices;
    }

    /**
     * Filtre les alertes (tab or entities) selon les critères : q, toxicity, period, department
     * - $alerts can be arrays (API) or objects (entities)
     */
    public function filterAlerts(array $alerts, array $filters): array
    {
        return array_values(array_filter($alerts, function ($alert) use ($filters) {
            $isArray = is_array($alert);

            // --- Search by q (name or city)
            if (!empty($filters['q'])) {
                $search = mb_strtolower((string) $filters['q']);

                if ($isArray) {
                    $city = mb_strtolower((string) ($alert['waterbody']['city'] ?? ''));
                    $waterbodyName = mb_strtolower((string) ($alert['waterbody']['name'] ?? ''));
                } else {
                    $city = mb_strtolower($alert->getWaterbody()?->getCity() ?? '');
                    $waterbodyName = mb_strtolower($alert->getWaterbody()?->getName() ?? '');
                }

                if (!str_contains($city, $search) && !str_contains($waterbodyName, $search)) {
                    return false;
                }
            }

            // --- Filter by toxicity
            if (!empty($filters['toxicity'])) {
                $filterToxicity = $filters['toxicity'];
                $filterToxicityId = is_object($filterToxicity) ? $filterToxicity->getId() : (int) $filterToxicity;

                if ($isArray) {
                    $alertToxicityId = isset($alert['toxicity_level']['id']) ? (int) $alert['toxicity_level']['id'] : null;
                } else {
                    $alertToxicityId = $alert->getToxicityLevel()?->getId();
                }

                if ($alertToxicityId !== $filterToxicityId) {
                    return false;
                }
            }

            // --- Filter by department
            if (!empty($filters['department'])) {
                $filterDept = (string) $filters['department'];
                $filterDeptNormalized = mb_strtolower(trim($filterDept));

                // --- Filter by department (simple substring match only)
                $filterDept = mb_strtolower(trim((string) $filters['department']));

                if ($isArray) {
                    $wbDept = $alert['waterbody']['department'] ?? null;
                    if (is_array($wbDept)) {
                        $alertDeptName = isset($wbDept['nom']) ? (string) $wbDept['nom'] : (isset($wbDept['department']) ? (string) $wbDept['department'] : null);
                    } else {
                        $alertDeptName = is_string($wbDept) ? $wbDept : null;
                    }
                } else {
                    $wb = $alert->getWaterbody();
                    $alertDeptName = $wb ? $wb->getDepartment() : null;
                }

                if ($alertDeptName === null || mb_stripos(mb_strtolower((string) $alertDeptName), $filterDept) === false) {
                    return false;
                }
            }

            // --- Filter by period
            if (!empty($filters['period'])) {
                if ($isArray) {
                    $createdAtRaw = $alert['created_at'] ?? null;
                    $createdAt = $createdAtRaw ? new \DateTimeImmutable((string) $createdAtRaw) : null;
                } else {
                    $createdAt = $alert->getCreatedAt();
                }

                if (!$createdAt) {
                    return false;
                }

                $limitDate = match ($filters['period']) {
                    '24h' => new \DateTimeImmutable('-24 hours'),
                    '3d' => new \DateTimeImmutable('-3 days'),
                    '7d' => new \DateTimeImmutable('-7 days'),
                    '30d' => new \DateTimeImmutable('-30 days'),
                    default => null,
                };

                if ($limitDate && $createdAt < $limitDate) {
                    return false;
                }
            }

            return true;
        }));
    }
}
