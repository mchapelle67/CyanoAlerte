<?php

namespace App\Service;

class Slug 
{
    public function __construct(
        private readonly \Symfony\Component\String\Slugger\SluggerInterface $slugger,
    ) {
    }
    
    public function addSlugs(array $alerts): array
    {
        foreach ($alerts as $index => $alert) {
            if (is_array($alert)) {
                $alerts[$index]['detail_slug'] = $this->makeSlug($alert);
            }
        }

        return $alerts;
    }

    public function makeSlug(array $alert): string
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