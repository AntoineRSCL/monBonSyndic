<?php

namespace App\Controller;

use App\Entity\Building;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    #[Route('/building/{slug}/news', name: 'news_index')]
    public function index(Building $building): Response
    {
        // Récupérer les nouvelles associées au bâtiment
        $news = $building->getNews();

        return $this->render('news/index.html.twig', [
            'building' => $building,
            'news' => $news,
        ]);
    }
}
