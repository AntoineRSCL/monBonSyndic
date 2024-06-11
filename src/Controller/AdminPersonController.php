<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Service\PaginationService;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminPersonController extends AbstractController
{
    /**
     * Sert a afficher toutes les personnes
     */
    #[Route('/admin/person/{page<\d+>?1}', name: 'admin_person_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $admin = $this->getUser();
        
        // Récupérer le bâtiment associé à l'utilisateur connecté
        $building = $admin->getBuilding();
        
        // Ajouter une condition pour filtrer les personnes par bâtiment
        $criteria = ['building' => $building];

        // Configurer le service de pagination
        $pagination->setEntityClass(Person::class)
                   ->setPage($page)
                   ->setLimit(9)
                   ->setCriteria($criteria); // Utiliser setCriteria pour filtrer par bâtiment

        return $this->render('admin/person/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * SErt a ajouter une nouvelle personne
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('admin/person/new', name: 'admin_person_new')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // Récupérer l'utilisateur actuellement connecté
            $admin = $this->getUser();
            // Récupérer le bâtiment associé à l'utilisateur connecté
            $building = $admin->getBuilding();

            $person->setBuilding($building)
                ->setRoles(["ROLE_USER"])
                ->setPassword($this->passwordHasher->hashPassword($person, 'password'));
            $manager->persist($person);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash(
                'success',
                "La personne <strong>".$person->getFullName()."</strong> a bien été crée"
            );

            return $this->redirectToRoute('admin_person_index');
        }

        return $this->render("admin/person/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Sert a editer une personne de son batiment
     */
    #[Route("/admin/person/{id}/edit", name: "admin_person_edit")]
    public function edit(Person $person, Request $request, EntityManagerInterface $manager): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $person->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette personne.');
        }

        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($person);
            $manager->flush();

            $this->addFlash('success',"Les infos de ".$person->getFullName()." a bien été modifié");

            return $this->redirectToRoute('admin_person_index');
        }


        return $this->render("admin/person/edit.html.twig", [
            "person" => $person,
            "myForm" => $form->createView()
        ]);
    }

    /**
     * Sert a supprimer une personne de son batiment
     */
    #[Route("/admin/person/{id}/delete", name:"admin_person_delete")]
    public function delete(Person $person, EntityManagerInterface $manager): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $person->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette personne.');
        }

        $this->addFlash(
            'success',
            'La personne n°<strong>'.$person->getId().'</strong> a bien été supprimée'
        );
        $manager->remove($person);
        $manager->flush();

        return $this->redirectToRoute('admin_person_index');
    }

    /**
     * Fonction pour recherche une personne en fonction nom et prenom
     *
     * @param Request $request
     * @param PersonRepository $personRepository
     * @return Response
     */
    #[Route("/admin/person/search", name:"admin_person_search")]
    public function search(Request $request, PersonRepository $personRepository): Response
    {
        $query = $request->query->get('q');

        if (!$query) {
            return $this->redirectToRoute('admin_person_index');
        }

        // Récupérer l'administrateur connecté et son bâtiment
        $admin = $this->getUser();
        $building = $admin->getBuilding();

        // Utilisez la méthode search du repository pour obtenir les résultats de recherche
        $results = $query ? $personRepository->search($query, $building) : [];

        return $this->render('admin/person/search.html.twig', [
            'results' => $results,
        ]);
    }


}

