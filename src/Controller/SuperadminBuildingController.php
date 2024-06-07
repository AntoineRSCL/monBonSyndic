<?php

namespace App\Controller;

use App\Entity\Building;
use App\Form\BuildingType;
use Cocur\Slugify\Slugify;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperadminBuildingController extends AbstractController
{
    /**
     * Fonction pour afficher tous les immeuble
     */
    #[Route('superadmin/building/{page<\d+>?1}', name: 'superadmin_building_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        // Configurer le service de pagination
        $pagination->setEntityClass(Building::class)
                   ->setPage($page)
                   ->setLimit(9);

        return $this->render('superadmin/building/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Fonction pour ajouter un immeuble
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('superadmin/building/new', name: 'superadmin_building_new')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $building = new Building();
        $form = $this->createForm(BuildingType::class, $building);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                $building->setPicture($newFilename);
            }


            $manager->persist($building);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le bâtiment <strong>" . $building->getName() . "</strong> a bien été crée."
            );

            return $this->redirectToRoute('superadmin_building_index');
        }

        return $this->render('superadmin/building/new.html.twig', [
            'myForm' => $form->createView(),
        ]);
    }

    /**
     * Fonction pour editer un immebule
     */
    #[Route('superadmin/building/{id}/edit', name: 'superadmin_building_edit')]
    public function edit(Building $building, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(BuildingType::class, $building);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                // Supprimer l'ancienne image s'il en existe une
                $oldPicture = $building->getPicture();
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
                $building->setPicture($newFilename);
            }

            $manager->flush();

            $this->addFlash(
                'success',
                "Le bâtiment <strong>" . $building->getName() . "</strong> a bien été modifié."
            );

            return $this->redirectToRoute('superadmin_building_index');
        }

        return $this->render("superadmin/building/edit.html.twig", [
            'myForm' => $form->createView(),
            'building' => $building,
        ]);
    }

    /**
     * Fonction pour supprimer un immeuble
     */
    #[Route('superadmin/building/{id}/delete', name: 'superadmin_building_delete')]
    public function delete(Building $building, EntityManagerInterface $manager): Response
    {
        // Supprimer l'image associée au bâtiment s'il en existe une
        $picture = $building->getPicture();
        if ($picture) {
            $picturePath = $this->getParameter('pictures_directory').'/'.$picture;
            if (file_exists($picturePath)) {
                unlink($picturePath);
            }
        }

        // Supprimer le bâtiment
        $manager->remove($building);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le bâtiment <strong>" . $building->getName() . "</strong> a bien été supprimé."
        );

        return $this->redirectToRoute('superadmin_building_index');
    }

}
