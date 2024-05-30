<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(StatsService $statsService): Response
    {

        $admin = $this->getUser();

        $personCount = 0; // Initialiser le nombre de personnes à zéro par défaut

        if ($admin !== null) {
            $building = $admin->getBuilding(); // Récupérer l'immeuble associé à cet admin
            if ($building !== null) {
                // S'il y a un immeuble associé à cet admin, obtenez le nombre de personnes dans cet immeuble
                $personCount = $statsService->getPersonCountByBuilding($building);
                $owner = $statsService->getOwnerCountByBuilding($building);
            }
        }

        //$owner = $statsService->getOwnerCount();

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => [
                'personCount' => $personCount,
                'owner' => $owner
            ]
        ]);
    }
}
