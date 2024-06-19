<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperadminContactController extends AbstractController
{
    /**
     * Affiche tous les messages envoye qui ne comportent pas d'idBuilding 
     */
    #[Route('/superadmin/contact/{page<\d+>?1}', name: 'superadmin_contact_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        // Définir les critères pour la requête
        $criteria = ['building' => null];

        // Configurer le service de pagination
        $pagination->setEntityClass(Contact::class)
                   ->setPage($page)
                   ->setLimit(9)
                   ->setCriteria($criteria);

        return $this->render('superadmin/contact/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Permet de voir le message en entier
     */
    #[Route('/superadmin/contact/{id}/view', name: 'superadmin_contact_view')]
    public function view(Contact $contact): Response
    {
        return $this->render('superadmin/contact/view.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * Permet de supprimer un message
     */
    #[Route('/superadmin/contact/{id}/delete', name: 'superadmin_contact_delete')]
    public function delete(Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Le message a été supprimé avec succès.');

        return $this->redirectToRoute('superadmin_contact_index');
    }
}
