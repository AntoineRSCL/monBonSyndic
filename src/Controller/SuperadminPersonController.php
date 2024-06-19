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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SuperadminPersonController extends AbstractController
{
    /**
     * Permet de voir toutes les personnes
     */
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

    /**
     * Permet de rajouter un admin au site
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param BuildingRepository $buildingRepository
     * @return Response
     */
    #[Route('/superadmin/person/new', name: 'superadmin_person_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, BuildingRepository $buildingRepository, UserPasswordHasherInterface $hasher): Response
    {
        $person = new Person();

        // Création du formulaire en passant l'option include_building à true
        $form = $this->createForm(PersonType::class, $person, ['include_building' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du bâtiment depuis le formulaire
            $building = $form->get('building')->getData();

            // Générer un username avec les 3 premières lettres du nom du bâtiment et 3 caractères aléatoires
            $username = 'admin_' . strtolower($this->getThreeLetters($building->getName())) . $this->generateRandomString(5);

            // Affectation du username à l'objet Person
            $person->setUsername($username)
                ->setPassword($hasher->hashPassword($person, "admin"));

            // Définition des rôles (si nécessaire)
            $person->setRoles(["ROLE_ADMIN"]);

            // Persist et flush de l'entité Person
            $entityManager->persist($person);
            $entityManager->flush();

            $this->addFlash('success', 'L\'admin a été créé avec succès.');

            return $this->redirectToRoute('superadmin_person_index');
        }

        return $this->render('superadmin/person/new.html.twig', [
            'myForm' => $form->createView(),
        ]);
    }

    // Méthode pour obtenir les trois premières lettres en minuscules d'une chaîne
    private function getThreeLetters(string $string): string
    {
        return mb_substr(strtolower(preg_replace('/\s+/', '', $string)), 0, 3);
    }

    // Méthode pour générer une chaîne de caractères aléatoires
    private function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * Permet de modifier le role ds personnes
     */
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

    /**
     * Permet de supprimer une personne
     */
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

    /**
     * Peremet de faire une recherche sur nom prenom ou immeuble
     *
     * @param Request $request
     * @param PersonRepository $personRepository
     * @param BuildingRepository $buildingRepository
     * @return Response
     */
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
