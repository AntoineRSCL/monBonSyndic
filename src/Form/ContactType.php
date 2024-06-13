<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Building;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration('Nom', 'Entrez votre nom'))
            ->add('firstName', TextType::class, $this->getConfiguration('Prénom', 'Entrez votre prénom'))
            ->add('email', EmailType::class, $this->getConfiguration('Titre', 'Entrez votre adresse mail'))
            ->add('subject', TextType::class, $this->getConfiguration('Sujet', 'Entrez le sujet de votre message'))
            ->add('message', TextareaType::class, $this->getConfiguration('Contenu', 'Entrez le contenu de votre message'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
