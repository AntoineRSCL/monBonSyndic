<?php

namespace App\Controller;

use App\Entity\Building;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildingController extends AbstractController
{
    #[Route('/building/{slug}', name: 'building_index')]
    public function index(Building $building): Response
    {
        return $this->render('building/index.html.twig', [
            'building' => $building
        ]);
    }
}
