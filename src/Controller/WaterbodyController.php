<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WaterbodyController extends AbstractController
{
    #[Route('/waterbody', name: 'app_waterbody')]
    public function index(): Response
    {
        return $this->render('waterbody/index.html.twig', [
            'controller_name' => 'WaterbodyController',
        ]);
    }
}
