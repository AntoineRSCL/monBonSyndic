<?php

namespace App\Controller;

use App\Entity\Survey;
use App\Form\SurveyType;
use Cocur\Slugify\Slugify;
use App\Repository\VoteRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminSurveyController extends AbstractController
{
    /**
     * Fonction pour afficher tous les sondages de son bat
     */
    #[Route('/admin/survey/{page<\d+>?1}', name: 'admin_survey_index')]
    public function index(PaginationService $pagination, VoteRepository $voteRepository, int $page): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $admin = $this->getUser();
        
        // Récupérer le bâtiment associé à l'utilisateur connecté
        $building = $admin->getBuilding();
        
        // Ajouter une condition pour filtrer les personnes par bâtiment
        $criteria = ['building' => $building];

        // Configurer le service de pagination
        $pagination->setEntityClass(Survey::class)
                   ->setPage($page)
                   ->setLimit(9)
                   ->setCriteria($criteria); // Utiliser setCriteria pour filtrer par bâtiment


        $surveys = $pagination->getData();
        // Calculer les pourcentages de vote pour chaque sondage
        foreach ($surveys as $survey) {
            $votes = $voteRepository->findBy(['survey' => $survey]);
            $totalVotes = count($votes);

            if ($totalVotes > 0) {
                $voteCounts = [
                    'Pour' => 0,
                    'Contre' => 0,
                    'Abstention' => 0
                ];

                foreach ($votes as $vote) {
                    if (isset($voteCounts[$vote->getAnswer()])) {
                        $voteCounts[$vote->getAnswer()]++;
                    }
                }

                $survey->votePercentages = [
                    'Pour' => round(($voteCounts['Pour'] / $totalVotes) * 100, 2),
                    'Contre' => round(($voteCounts['Contre'] / $totalVotes) * 100, 2),
                    'Abstention' => round(($voteCounts['Abstention'] / $totalVotes) * 100, 2),
                ];
            } else {
                $survey->votePercentages = [
                    'Pour' => 0,
                    'Contre' => 0,
                    'Abstention' => 0,
                ];
            }
        }

        return $this->render('admin/survey/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Fonction pour ajouter un sondage a son bat
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/survey/new', name: 'admin_survey_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $survey = new Survey();
        $form = $this->createForm(SurveyType::class, $survey);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $admin = $this->getUser();
            $building = $admin->getBuilding();
            $survey->setBuilding($building);

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
                $survey->setPicture($newFilename);
            }
            $manager->persist($survey);
            $manager->flush();

            $this->addFlash('success', 'Le sondage '.$survey->getId().' a été créée avec succès.');
            return $this->redirectToRoute('admin_survey_index');
        }

        return $this->render('admin/survey/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Fonction pour editer un sondage de son bat
     */
    #[Route('/admin/survey/{id}/edit', name: 'admin_survey_edit')]
    public function edit(Survey $survey, Request $request, EntityManagerInterface $manager): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $survey->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce sondage.');
        }
        $form = $this->createForm(SurveyType::class, $survey);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                // Supprimer l'ancienne image s'il en existe une
                $oldPicture = $survey->getPicture();
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
                $survey->setPicture($newFilename);
            }
            $manager->flush();

            $this->addFlash('success', 'Le sondage '.$survey->getId().' a été modifiée avec succès.');
            return $this->redirectToRoute('admin_survey_index');
        }

        return $this->render('admin/survey/edit.html.twig', [
            'form' => $form->createView(),
            'survey' => $survey,
        ]);
    }

    /**
     * Fonction pour suppr un sondage de son bat
     */
    #[Route('/admin/survey/{id}/delete', name: 'admin_survey_delete')]
    public function delete(Survey $survey, EntityManagerInterface $manager, Request $request): Response
    {
        $admin = $this->getUser();
        if ($admin->getBuilding()->getId() !== $survey->getBuilding()->getId()) {
            // Redirigez vers une page d'erreur ou effectuez toute autre action appropriée
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce sondage.');
        }

        // Supprimer l'image associée s'il en existe une
        $picture = $news->getPicture();
        if ($picture) {
            $picturePath = $this->getParameter('pictures_directory').'/'.$picture;
            if (file_exists($picturePath)) {
                unlink($picturePath);
            }
        }

        $this->addFlash(
            'success',
            'Le sondage n°<strong>'.$survey->getId().'</strong> a bien été supprimée'
        );
        $manager->remove($survey);
        $manager->flush();

        return $this->redirectToRoute('admin_survey_index');
    }
}
