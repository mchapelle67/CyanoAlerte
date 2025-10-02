<?php

namespace App\Controller;

use App\Service\ApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ApiService $apiService): Response
    {
        $departments = $apiService->getAllDepartments();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'departments' => $departments
        ]);
    }
}
