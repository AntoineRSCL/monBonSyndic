<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Form\PersonRoleType;
use App\Service\PaginationService;
use App\Repository\PersonRepository;
use App\Repository\BuildingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperadminPersonController extends AbstractController
{
    #[Route('/superadmin/person/{page<\d+>?1}', name: 'superadmin_person_index')]
    public function index(PaginationService $pagination, int $page, BuildingRepository $buildingRepository): Response
    {
        $pagination->setEntityClass(Person::class)
                   ->setPage($page)
                   ->setLimit(9);

        $buildingId = null;                   
        $buildings = $buildingRepository->findAll();

        return $this->render('superadmin/person/index.html.twig', [
            'pagination' => $pagination,
            'buildings' => $buildings,
            'selectedBuilding' =>  $buildingId
        ]);
    }

    #[Route('/superadmin/person/new', name: 'superadmin_person_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, BuildingRepository $buildingRepository): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person, ['include_building' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $person->setRoles(["ROLE_ADMIN"]);

            $entityManager->persist($person);
            $entityManager->flush();

            $this->addFlash('success', 'L\'admin a été créé avec succès.');

            return $this->redirectToRoute('superadmin_person_index');
        }

        return $this->render('superadmin/person/new.html.twig', [
            'myForm' => $form->createView(),
        ]);
    }

    #[Route('/superadmin/person/{id}/edit', name: 'superadmin_person_edit')]
    public function edit(Person $person, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PersonRoleType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', 'Le rôle de l\'utilisateur a été mis à jour avec succès.');

            return $this->redirectToRoute('superadmin_person_index');
        }

        return $this->render('superadmin/person/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/superadmin/person/{id}/delete', name: 'superadmin_person_delete')]
    public function delete(Person $person, EntityManagerInterface $manager): Response
    {
        $superadmin = $this->getUser();
        if ($superadmin->getId() === $person->getId()) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas vous supprimer vous-même.'
            );
    
            return $this->redirectToRoute('superadmin_person_index');
        }

        $this->addFlash(
            'success',
            'La personne n°<strong>'.$person->getId().'</strong> a bien été supprimée'
        );

        $manager->remove($person);
        $manager->flush();

        return $this->redirectToRoute('superadmin_person_index');
    }

    #[Route('/superadmin/person/search', name: 'superadmin_person_search')]
    public function search(Request $request, PersonRepository $personRepository, BuildingRepository $buildingRepository): Response
    {
        $query = $request->query->get('q', '');
        $buildingId = $request->query->get('building', null);

        if (!$query) {
            return $this->redirectToRoute('superadmin_person_index');
        }

        $building = null;
        if ($buildingId) {
            $building = $buildingRepository->find($buildingId);
        }

        $results = $personRepository->search($query, $building);

        return $this->render('superadmin/person/index.html.twig', [
            'persons' => $results,
            'query' => $query,
            'selectedBuilding' => $buildingId,
            // Passer les bâtiments pour le formulaire de recherche
            'buildings' => $buildingRepository->findAll(),
        ]);
    }
}
