<?php

namespace App\Controller;

use App\Entity\Building;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    #[Route('/building/{slug}/event', name: 'events_index')]
    public function index(Building $building, EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAllEventsByDateAndBuilding($building);

        return $this->render('event/index.html.twig', [
            'building' => $building,
            'events' => $events,
        ]);
    }

    #[Route('/building/{building_slug}/event/{event_id}', name: 'event_show')]
    public function show(string $building_slug, int $event_id, EventRepository $eventRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le bâtiment en fonction du slug
        $buildingRepository = $entityManager->getRepository(Building::class);
        $building = $buildingRepository->findOneBy(['slug' => $building_slug]);

        if (!$building) {
            throw $this->createNotFoundException('Building not found for slug ' . $building_slug);
        }

        // Récupérer la nouvelle en fonction de l'ID
        $event = $eventRepository->find($event_id);

        if (!$event) {
            throw $this->createNotFoundException('News not found for ID ' . $event);
        }

        // Vérifier si la nouvelle appartient au bâtiment spécifié
        if ($event->getBuilding()->getId() !== $building->getId()) {
            throw $this->createNotFoundException('You are not allowed to view this news.');
        }

        return $this->render('event/show.html.twig', [
            'building' => $building,
            'event' => $event,
        ]);
    }
}
