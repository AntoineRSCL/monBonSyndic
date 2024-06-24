<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminIssueController extends AbstractController
{
    #[Route('/admin/issue/{page<\d+>?1}', name: 'admin_issue_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $admin = $this->getUser();
        
        // Récupérer le bâtiment associé à l'utilisateur connecté
        $building = $admin->getBuilding();
        
        // Ajouter une condition pour filtrer les personnes par bâtiment
        $criteria = ['building' => $building];

        // Configurer le service de pagination
        $pagination->setEntityClass(Issue::class)
                   ->setPage($page)
                   ->setLimit(9)
                   ->setCriteria($criteria); // Utiliser setCriteria pour filtrer par bâtiment

        return $this->render('admin/issue/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/admin/issue/{id}/view', name: 'admin_issue_view')]
    public function view(Issue $issue): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $issue->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à consulter ce problème');
        }

        return $this->render('admin/issue/view.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route('/admin/issue/{id}/edit-status', name: 'admin_issue_edit_status')]
    public function editStatus(Issue $issue): Response
    {
        return $this->render('admin/issue/edit_status.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route('/admin/issue/{id}/update-status', name: 'admin_issue_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, Issue $issue, EntityManagerInterface $entityManager): Response
    {
        $newStatus = $request->request->get('status');

        if (in_array($newStatus, ['envoyé', 'en cours', 'terminé', 'En attente de traitement'])) {
            $issue->setStatus($newStatus);
            $entityManager->flush();

            $this->addFlash('success', 'Le statut du problème a été mis à jour avec succès.');
        } else {
            $this->addFlash('error', 'Statut invalide.');
        }

        return $this->redirectToRoute('admin_issue_view', ['id' => $issue->getId()]);
    }

    #[Route('/admin/issue/{id}/delete', name: 'admin_issue_delete')]
    public function delete(Issue $issue, EntityManagerInterface $entityManager): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $issue->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce problème.');
        }

        $entityManager->remove($issue);
        $entityManager->flush();

        $this->addFlash('success', 'Le message a été supprimé avec succès.');

        return $this->redirectToRoute('admin_issue_index');
    }
}
