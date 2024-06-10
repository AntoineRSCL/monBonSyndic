<?php

namespace App\Form;

use App\Entity\Person;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, $this->getConfiguration("Nom d'utilisateur", "Votre nom d'utilisateur..."))
            ->add('name', TextType::class, $this->getConfiguration("Nom", "Votre nom..."))
            ->add('firstname', TextType::class, $this->getConfiguration("Prénom", "Votre prénom..."))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Votre adresse email..."))
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['placeholder' => "Votre numéro de téléphone..."]
            ])
            ->add('address', TextType::class, $this->getConfiguration("Adresse", "Votre adresse..."))
            ->add('number', TextType::class, $this->getConfiguration("Numéro", "Le numéro de votre adresse..."))
            ->add('zip', TextType::class, $this->getConfiguration("Code postal", "Votre code postal..."))
            ->add('locality', TextType::class, $this->getConfiguration("Localité", "Votre localité..."))
            ->add('country', TextType::class, $this->getConfiguration("Pays", "Votre pays..."))
            ->add('optin', CheckboxType::class, [
                'label' => 'Accepter les newsletters',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
