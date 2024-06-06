<?php

// src/Form/ApartmentType.php
namespace App\Form;

use App\Entity\Building;
use App\Entity\Apartment;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ApartmentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('building', EntityType::class, $this->getConfiguration('Building', 'Choisissez un bâtiment', [
                'class' => Building::class,
                'choice_label' => 'name', // Assuming 'name' is the property to display
            ]))
            ->add('reference', TextType::class, $this->getConfiguration('Reference', 'Entrez la référence'))
            ->add('floor', IntegerType::class, $this->getConfiguration('Floor', 'Entrez l\'étage'))
            ->add('quota1', IntegerType::class, $this->getConfiguration('Quota 1', 'Entrez le quota 1'))
            ->add('quota2', IntegerType::class, $this->getConfiguration('Quota 2', 'Entrez le quota 2'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Apartment::class,
        ]);
    }
}
