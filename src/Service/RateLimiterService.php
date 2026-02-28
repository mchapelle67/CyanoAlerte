<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimiterService
{
    private $alertLimiter;

    public function __construct(
        #[Autowire(service: 'limiter.alert_form')] RateLimiterFactory $alertLimiter
    )
    {
        $this->alertLimiter = $alertLimiter;
    }

    public function checkRateLimit(Request $request): ?string
    {
        // utilise l'IP du client comme identifiant unique
        $limiter = $this->alertLimiter->create($request->getClientIp());
        
        // tente de consommer 1 jeton
        if (!$limiter->consume(1)->isAccepted()) {
            return 'Trop de formulaires envoyés. Veuillez patienter avant de soumettre un nouveau signalement.';
        }
        
        return null;
    }
}