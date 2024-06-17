<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('home.html.twig', [
            
        ]);
    }

    #[Route('/syndic', name: 'syndic')]
    public function syndic(): Response
    {
        return $this->render('syndic/index.html.twig', [
            
        ]);
    }

    /**
     * Permet d'afficher la page de mentions légales
     *
     * @return Response
     */
    #[Route('/legals', name:"legals")]
    public function legals(): Response
    {
        return $this->render('legals/index.html.twig');
    }
}
