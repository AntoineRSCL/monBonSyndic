<?php

namespace App\Controller;

use App\Entity\Building;
use App\Repository\NewsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildingController extends AbstractController
{
    /**
     * Route pour les immeubles on recupere toutes les donnees pour chaque page
     */
    #[Route('/building/{slug}', name: 'building_index')]
    public function index(Building $building, NewsRepository $newsRepository): Response
    {
        $user = $this->getUser();
        $userConnected = null;

        // Vérifiez si l'utilisateur est connecté
        if ($user && $user->getBuilding() && $user->getBuilding()->getId() === $building->getId()) {
            $userConnected = $user->getBuilding();
        }

        // Récupérez les deux dernières actualités du bâtiment
        $latestNews = $newsRepository->findLastTwoNewsByBuilding($building);

        return $this->render('building/index.html.twig', [
            'building' => $building,
            'userConnected' => $userConnected,
            'latestNews' => $latestNews
        ]);
    }
}
