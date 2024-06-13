<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Building;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact_index')]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact); 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($contact);
            $manager->flush();
            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('contact_index');
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/building/{slug}/contact', name: 'contact_building')]
    public function contactImmeuble(Building $building, Request $request,EntityManagerInterface $manager): Response
    {   
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact); 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $contact->setBuilding($building);
            $manager->persist($contact);
            $manager->flush();
            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('contact_building', ['slug' => $building->getSlug()]);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'building' => $building // Passer l'objet building au template
        ]);
    }

}
