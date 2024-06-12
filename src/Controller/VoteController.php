<?php

namespace App\Controller;

use App\Entity\Survey;
use App\Entity\Vote;
use App\Repository\SurveyRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    #[Route('/vote/submit/{surveyId}', name: 'vote_submit', methods: ['POST'])]
    public function submit(int $surveyId, Request $request, SurveyRepository $surveyRepository, VoteRepository $voteRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $survey = $surveyRepository->find($surveyId);
        if (!$survey) {
            throw $this->createNotFoundException('Sondage non trouvé');
        }

        $answer = $request->request->get('answer');
        if (!in_array($answer, ['Pour', 'Contre', 'Abstention'])) {
            throw $this->createAccessDeniedException('Réponse non valide');
        }

        // Rechercher un vote existant
        $vote = $voteRepository->findOneBy(['person' => $user, 'survey' => $survey]);
        if (!$vote) {
            $vote = new Vote();
            $vote->setPerson($user);
            $vote->setSurvey($survey);
        }

        $vote->setAnswer($answer);

        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->redirectToRoute('building_index', [
            'slug' => $survey->getBuilding()->getSlug(),
            '_fragment' => 'sondage'
        ]);
        
    }
}
