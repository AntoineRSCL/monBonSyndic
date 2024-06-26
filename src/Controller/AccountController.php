<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Form\IssueType;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Repository\IssueRepository;
use App\Repository\OwnerRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;

class AccountController extends AbstractController
{
    /**
     * Fonction pour se connecter au site
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        $loginError = null;

        if($error instanceof TooManyLoginAttemptsAuthenticationException)
        {
            // l'ereur est due à la limitation de tentative de connexion
            $loginError = "Trop de tentatives de connexion. Réessayez plus tard";
        }
        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
            'loginError' => $loginError
        ]);
    }

    /**
     * Fonction pour se deconnecter
     *
     * @return void
     */
    #[Route("/logout", name: "account_logout")]
    public function logout(): void
    {

    }

    /**
     * Fonction pour voir son profil
     *
     * @return Response
     */
    #[Route("/account", name:"account_index")]
    #[IsGranted('ROLE_USER')]
    public function myAccount(OwnerRepository $ownerRepository, IssueRepository $issueRepository): Response
    {
        $person = $this->getUser();

        $owner = $ownerRepository->getPersonOwner($person);
        $issue = $issueRepository->getPersonIssue($person);

        return $this->render('account/profil.html.twig', [
            'user' => $this->getUser(),
            'owners' => $owner,
            'issues' => $issue
        ]);
    }

    /**
     * Fonction pour modif ses donnees
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/profile", name:"account_profile")]
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser(); // permet de récup l'utilisateur connecté

        $form = $this->createForm(AccountType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $user->setSlug('');

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données ont été enregistrées avec succés"
            );

            return $this->redirectToRoute('account_index');
        }

        return $this->render("account/profile.html.twig",[
            'myForm' => $form->createView()
        ]);

    }

    /**
     * Fonction pour changer son mot de passe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/account/passwordupdate", name:"account_password")]
    #[IsGranted('ROLE_USER')]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // vérifier si le mot de passe correspond à l'ancien
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword()))
            {
                // gestion de l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié'
                );

                return $this->redirectToRoute('account_index');
            }

        }

        return $this->render("account/password.html.twig", [
            'myForm' => $form->createView()
        ]);

    }

    #[Route("/account/problem", name:"account_issue")]
    #[IsGranted('ROLE_USER')]
    public function createIssue(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $issue = new Issue();

        $building = $user->getBuilding();

        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $issue->setBuilding($building)
                ->setPerson($user)
                ->setDate(new \DateTime())
                ->setStatus("Envoyé");

            $manager->persist($issue);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le problème a bien été envoyé"
            );

            return $this->redirectToRoute('account_index');
        }

        return $this->render("account/issue.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

}
