<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminContactController extends AbstractController
{
    #[Route('/admin/contact/{page<\d+>?1}', name: 'admin_contact_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $admin = $this->getUser();
        
        // Récupérer le bâtiment associé à l'utilisateur connecté
        $building = $admin->getBuilding();
        
        // Ajouter une condition pour filtrer les personnes par bâtiment
        $criteria = ['building' => $building];

        // Configurer le service de pagination
        $pagination->setEntityClass(Contact::class)
                   ->setPage($page)
                   ->setLimit(9)
                   ->setCriteria($criteria); // Utiliser setCriteria pour filtrer par bâtiment

        return $this->render('admin/contact/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/admin/contact/{id}/view', name: 'admin_contact_view')]
    public function view(Contact $contact): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $contact->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à consulter ce message');
        }

        return $this->render('admin/contact/view.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * Permet de supprimer un message
     */
    #[Route('/admin/contact/{id}/delete', name: 'admin_contact_delete')]
    public function delete(Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $contact->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce message.');
        }

        $entityManager->remove($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Le message a été supprimé avec succès.');

        return $this->redirectToRoute('admin_contact_index');
    }
}
