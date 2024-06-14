<?php

namespace App\Controller;

use App\Entity\Building;
use App\Repository\VoteRepository;
use App\Repository\SurveyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SurveyController extends AbstractController
{
    #[Route('/building/{slug}/survey', name: 'survey_index')]
    #[IsGranted('ROLE_USER')]
    public function index(Building $building, SurveyRepository $surveyRepository, VoteRepository $voteRepository): Response
    {
        $user = $this->getUser();
        if ($user->getBuilding()->getId() !== $building->getId()) {
            throw $this->createNotFoundException('Vous n\'avez pas accès à ce bâtiment.');
        }
        // Récupérer les nouvelles associées au bâtiment
        $surveys = $surveyRepository->findAllSurveysByBuilding($building);

        // Récupérer les votes de l'utilisateur pour les sondages actuels
        $userVotes = [];
        if ($user) {
            foreach ($surveys as $survey) {
                $vote = $voteRepository->findOneBy(['person' => $user, 'survey' => $survey]);
                if ($vote) {
                    $userVotes[$survey->getId()] = $vote->getAnswer();
                }
            }
        }

        return $this->render('survey/index.html.twig', [
            'building' => $building,
            'surveys' => $surveys,
            'userVotes' => $userVotes
        ]);
    }
}
