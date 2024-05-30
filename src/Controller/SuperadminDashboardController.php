<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperadminDashboardController extends AbstractController
{
    #[Route('/superadmin', name: 'superadmin_dashboard')]
    public function index(StatsService $statsService): Response
    {
        $building = $statsService->getBuildingCount();
        $apartment = $statsService->getApartmentCount();
        

        return $this->render('superadmin/dashboard/index.html.twig', [
            'stats' => [
                'building' => $building,
                'apartment' => $apartment
            ]
        ]);
    }
}
