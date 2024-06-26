<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperadminDashboardController extends AbstractController
{
    /**
     * Fonction pour afficher le dashboard avec des stats
     *
     * @param StatsService $statsService
     * @return Response
     */
    #[Route('/superadmin', name: 'superadmin_dashboard')]
    public function index(StatsService $statsService): Response
    {
        $building = $statsService->getBuildingCount();
        $apartment = $statsService->getApartmentCount();
        $person = $statsService->getPersonCount();
        $contact = $statsService->getContactCount();
        

        return $this->render('superadmin/dashboard/index.html.twig', [
            'stats' => [
                'building' => $building,
                'apartment' => $apartment,
                'contact' => $contact,
                'person' => $person
            ]
        ]);
    }
}
