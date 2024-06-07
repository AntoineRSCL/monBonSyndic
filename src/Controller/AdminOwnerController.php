<?php

namespace App\Controller;

use App\Entity\Owner;
use App\Form\OwnerType;
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
        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // Récupérer l'utilisateur actuellement connecté
            $admin = $this->getUser();
            // Récupérer le bâtiment associé à l'utilisateur connecté
            $building = $admin->getBuilding();

            $manager->persist($owner);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash(
                'success',
                "Le bail pour l'appartement <strong>" . $owner->getApartment()->getReference() . "</strong> avec la personne <strong>" . $owner->getPerson()->getFullName() . "</strong> a bien été créé."
            );

            return $this->redirectToRoute('admin_owner_index',[
                
            ]);
        }

        return $this->render("admin/owner/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    #[Route('admin/owner/edit/{id}', name: 'admin_owner_edit')]
    public function edit(Owner $owner, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash(
                'success',
                "Le bail pour l'appartement <strong>" . $owner->getApartment()->getReference() . "</strong> avec la personne <strong>" . $owner->getPerson()->getFullName() . "</strong> a bien été mis à jour."
            );

            return $this->redirectToRoute('admin_owner_index');
        }

        return $this->render('admin/owner/edit.html.twig', [
            'myForm' => $form->createView(),
            'owner' => $owner
        ]);
    }

    #[Route('admin/owner/delete/{id}', name: 'admin_owner_delete')]
    public function delete(Owner $owner, EntityManagerInterface $manager): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $owner->getApartment()->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce propriétaire.');
        }

        $manager->remove($owner);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le bail pour l'appartement <strong>" . $owner->getApartment()->getReference() . "</strong> avec la personne <strong>" . $owner->getPerson()->getFullName() . "</strong> a bien été supprimé."
        );

        return $this->redirectToRoute('admin_owner_index');
    }
}
