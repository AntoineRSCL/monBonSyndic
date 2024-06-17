<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Person;
use App\Entity\Building;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class IssueType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Sujet du problème ..."))
            ->add('description', TextareaType::class, $this->getConfiguration("Description", "Description du problème ..."))
            ->add('urgency', ChoiceType::class, [
                'label' => 'Urgence',
                'choices' => [
                    'Faible' => 'low',
                    'Moyenne' => 'medium',
                    'Haute' => 'high',
                    'Critique' => 'critical'
                ],
                'placeholder' => 'Sélectionner une urgence'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Issue::class,
        ]);
    }
}
