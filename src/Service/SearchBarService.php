<?php

namespace App\Service;

class SearchBarService
{
    public function filterAlerts(array $alerts, array $filters): array
    {
        return array_values(array_filter($alerts, function ($alert) use ($filters) {
            $isArray = is_array($alert);

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