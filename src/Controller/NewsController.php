<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Building;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsController extends AbstractController
{
    #[Route('/building/{slug}/news', name: 'news_index')]
    public function index(Building $building, NewsRepository $newsRepository): Response
    {
        // Récupérer les nouvelles associées au bâtiment
        $news = $newsRepository->findAllNewsByDate($building);

        return $this->render('news/index.html.twig', [
            'building' => $building,
            'news' => $news,
        ]);
    }

    #[Route('/building/{building_slug}/news/{news_id}', name: 'news_show')]
    public function show(string $building_slug, int $news_id, NewsRepository $newsRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le bâtiment en fonction du slug
        $buildingRepository = $entityManager->getRepository(Building::class);
        $building = $buildingRepository->findOneBy(['slug' => $building_slug]);

        if (!$building) {
            throw $this->createNotFoundException('Building not found for slug ' . $building_slug);
        }

        // Récupérer la nouvelle en fonction de l'ID
        $news = $newsRepository->find($news_id);

        if (!$news) {
            throw $this->createNotFoundException('News not found for ID ' . $news_id);
        }

        // Vérifier si la nouvelle appartient au bâtiment spécifié
        if ($news->getBuilding()->getId() !== $building->getId()) {
            throw $this->createNotFoundException('You are not allowed to view this news.');
        }

        return $this->render('news/show.html.twig', [
            'building' => $building,
            'news' => $news,
        ]);
    }

}
