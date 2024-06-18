<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\Building;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PersonType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration("Nom", "Nom de la personne ..."))
            ->add('firstname', TextType::class, $this->getConfiguration("Prénom", "Prénom de la personne ..."))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Adresse e-mail de la personne ..."))
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false, // Définir le champ comme non obligatoire
                'attr' => ['placeholder' => "Numéro de téléphone de la personne ..."] // Placeholder pour indiquer le format attendu
            ])
            ->add('address', TextType::class, $this->getConfiguration("Adresse", "Adresse de la personne ..."))
            ->add('number', TextType::class, $this->getConfiguration("Numéro", "Numéro de l'adresse ..."))
            ->add('zip', TextType::class, $this->getConfiguration("Code postal", "Code postal de la personne ..."))
            ->add('locality', TextType::class, $this->getConfiguration("Localité", "Localité de la personne ..."))
            ->add('country', TextType::class, $this->getConfiguration("Pays", "Pays de la personne ..."))
            ->add('optin', CheckboxType::class, [
                'label' => 'Accepter les newsletters',
                'required' => false,
            ])
        ;

        // Ajoutez le champ building uniquement si l'option include_building est true
        if ($options['include_building']) {
            $builder->add('building', EntityType::class, [
                'class' => Building::class,
                'choice_label' => 'name', // Assurez-vous que votre entité Building a une propriété 'name'
                'label' => 'Bâtiment',
                'placeholder' => 'Sélectionnez un bâtiment',
                'required' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'include_building' => false, // Défaut à false
        ]);
    }
}
