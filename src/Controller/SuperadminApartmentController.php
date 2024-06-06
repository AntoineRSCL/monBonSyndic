<?php

namespace App\Controller;

use App\Entity\Apartment;
use App\Form\ApartmentType;
use App\Service\PaginationService;
use App\Repository\BuildingRepository;
use App\Repository\ApartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperadminApartmentController extends AbstractController
{
    #[Route('superadmin/apartment/{page<\d+>?1}', name: 'superadmin_apartment_index')]
    public function index(PaginationService $pagination, int $page, BuildingRepository $buildingRepository): Response
    {
        // Configurer le service de pagination
        $pagination->setEntityClass(Apartment::class)
                   ->setPage($page)
                   ->setLimit(9);

        $buildingId = null;                   
        $buildings = $buildingRepository->findAll();

        return $this->render('superadmin/apartment/index.html.twig', [
            'pagination' => $pagination,
            'buildings' => $buildings,
            'selectedBuilding' =>  $buildingId
        ]);
    }

    #[Route('/superadmin/apartment/new', name: 'superadmin_apartment_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, BuildingRepository $buildingRepository): Response
    {
        $apartment = new Apartment();
        $form = $this->createForm(ApartmentType::class, $apartment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($apartment);
            $entityManager->flush();

            $this->addFlash('success', 'L\'appartement a été créé avec succès.');

            return $this->redirectToRoute('superadmin_apartment_index');
        }

        return $this->render('superadmin/apartment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/superadmin/apartment/{id}/edit', name: 'superadmin_apartment_edit')]
    public function edit(Apartment $apartment, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApartmentType::class, $apartment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('superadmin_apartment_index');
        }

        return $this->render('superadmin/apartment/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/superadmin/apartment/{id}/delete', name: 'superadmin_apartment_delete')]
    public function delete(Apartment $apartment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($apartment);
        $entityManager->flush();

        $this->addFlash('success', 'L\'appartement a été supprimé avec succès.');

        return $this->redirectToRoute('superadmin_apartment_index');
    }

    #[Route('/superadmin/apartment/search', name: 'superadmin_apartment_search')]
    public function search(Request $request, ApartmentRepository $apartmentRepository, BuildingRepository $buildingRepository): Response
    {
        $query = $request->query->get('q');
        $buildingId = $request->query->get('building');

        // Convertir la valeur de $buildingId en entier
        $buildingId = $buildingId !== null ? (int)$buildingId : null;

        if (!$query && !$buildingId) {
            return $this->redirectToRoute('superadmin_apartment_index');
        }

        $results = $apartmentRepository->search($query, $buildingId);
        $buildings = $buildingRepository->findAll();

        return $this->render('superadmin/apartment/search.html.twig', [
            'results' => $results,
            'query' => $query,
            'buildings' => $buildings,
            'selectedBuilding' => $buildingId,
        ]);
    }
}
