<?php

namespace App\Controller;

use App\Entity\Building;
use App\Repository\NewsRepository;
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
}
