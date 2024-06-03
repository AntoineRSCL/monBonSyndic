<?php

namespace App\Controller;

use App\Entity\Owner;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminOwnerController extends AbstractController
{
    #[Route('/admin/owner/{page<\d+>?1}', name: 'admin_owner_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $admin = $this->getUser();
        
        // Récupérer le bâtiment associé à l'utilisateur connecté
        $building = $admin->getBuilding();
        
        // Ajouter une condition pour filtrer les personnes par bâtiment
        $criteria = ['apartment.building' => $building];

        $pagination->setEntityClass(Owner::class)
                   ->setPage($page)
                   ->setLimit(9)
                   ->setCriteria($criteria); // Utiliser setCriteria pour filtrer par bâtiment

        return $this->render('admin/owner/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('admin/owner/new', name: 'admin_owner_new')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $owner = new Owner();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // Récupérer l'utilisateur actuellement connecté
            $admin = $this->getUser();
            // Récupérer le bâtiment associé à l'utilisateur connecté
            $building = $admin->getBuilding();

            $person->setBuilding($building)
                ->setRoles(["ROLE_USER"]);
            $manager->persist($person);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash(
                'success',
                "La personne <strong>".$person->getFullName()."</strong> a bien été crée"
            );

            return $this->redirectToRoute('admin_person_index',[
                
            ]);
        }

        return $this->render("admin/person/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }
}
