<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Cocur\Slugify\Slugify;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminEventController extends AbstractController
{
    /**
     * Fonction pour afficher tous les evenements
     */
    #[Route('/admin/event/{page<\d+>?1}', name: 'admin_event_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $admin = $this->getUser();
        
        // Récupérer le bâtiment associé à l'utilisateur connecté
        $building = $admin->getBuilding();
        
        // Ajouter une condition pour filtrer les personnes par bâtiment
        $criteria = ['building' => $building];

        // Configurer le service de pagination
        $pagination->setEntityClass(Event::class)
                   ->setPage($page)
                   ->setLimit(9)
                   ->setCriteria($criteria); // Utiliser setCriteria pour filtrer par bâtiment

        return $this->render('admin/event/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Fonction pour rajouter un eveneement
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/event/new', name: 'admin_event_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $admin = $this->getUser();
            $building = $admin->getBuilding();
            $event->setBuilding($building);

            // Handle file upload
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $slugify = new Slugify();
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugify->slugify($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                // Move the file to the directory where pictures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                // Update the 'picture' property to store the file name instead of its contents
                $event->setPicture($newFilename);
            }

            $manager->persist($event);
            $manager->flush();

            $this->addFlash('success', 'L\'évenement n° '.$event->getId().' a été créée avec succès.');

            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Fonction pour editer un evenement
     */
    #[Route('/admin/event/{id}/edit', name: 'admin_event_edit')]
    public function edit(Event $event, Request $request, EntityManagerInterface $manager): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $event->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet evenement.');
        }

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                // Supprimer l'ancienne image s'il en existe une
                $oldPicture = $event->getPicture();
                if ($oldPicture) {
                    $oldPicturePath = $this->getParameter('pictures_directory').'/'.$oldPicture;
                    if (file_exists($oldPicturePath)) {
                        unlink($oldPicturePath);
                    }
                }
                
                $slugify = new Slugify();
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugify->slugify($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                // Move the file to the directory where pictures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                // Update the 'picture' property to store the file name instead of its contents
                $event->setPicture($newFilename);
            }

            $manager->flush();

            $this->addFlash('success', 'L evenement n'.$event->getId().' a été modifiée avec succès.');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    /**
     * Fonction pour supprimer un evenement
     */
    #[Route('/admin/event/{id}/delete', name: 'admin_event_delete')]
    public function delete(Event $event, EntityManagerInterface $manager, Request $request): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $event->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet evenement.');
        }

        // Supprimer l'image associée s'il en existe une
        $picture = $event->getPicture();
        if ($picture) {
            $picturePath = $this->getParameter('pictures_directory').'/'.$picture;
            if (file_exists($picturePath)) {
                unlink($picturePath);
            }
        }

        $this->addFlash(
            'success',
            'L evenement n°<strong>'.$event->getId().'</strong> a bien été supprimé'
        );
        $manager->remove($event);
        $manager->flush();

        return $this->redirectToRoute('admin_event_index');
    }
}
