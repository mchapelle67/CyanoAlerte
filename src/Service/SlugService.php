<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SlugService 
{
    public function __construct(
        private readonly SluggerInterface $slugger,
    ) 
    {
    }
    public function addSlugs(array $alerts): array
    {
        foreach ($alerts as $index => $alert) {
            if (is_array($alert)) {
                $alerts[$index]['detail_slug'] = $this->makeSlug($alert, $index);
            }
        }

        return $alerts;
    }

    public function findAlertBySlug(array $alerts, string $slug): ?array
    {
        foreach ($this->addSlugs($alerts) as $alert) {
            if (($alert['detail_slug'] ?? null) === $slug) {
                return $alert;
            }
        }

        return null;
    }

    public function makeSlug(array $alert, ?int $index = null): string
    {
        $waterbody = is_array($alert['waterbody'] ?? null) ? $alert['waterbody'] : [];
        $name = (string) ($waterbody['name'] ?? 'plan-eau');
        $department = (string) ($waterbody['department'] ?? 'departement');
        $identifier = $this->resolveStableIdentifier($alert, $index);

        $base = sprintf('%s-%s-%s', $name, $department, $identifier);

        return strtolower($this->slugger->slug($base)->toString());
    }

    private function resolveStableIdentifier(array $alert, ?int $index = null): string
    {
        $createdAtRaw = $alert['created_at'] ?? $alert['createdAt'] ?? null;
        if (is_string($createdAtRaw) && $createdAtRaw !== '') {
            try {
                return (new \DateTimeImmutable($createdAtRaw))->format('Y-m-d-H-i-s');
            } catch (\Exception) {
            }
        }

        $id = $alert['id'] ?? null;
        if (is_scalar($id) && (string) $id !== '') {
            return 'id-' . (string) $id;
        }

        return 'idx-' . (string) ($index ?? 0);
    }
}