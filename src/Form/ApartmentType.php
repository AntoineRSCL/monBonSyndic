<?php

// src/Form/ApartmentType.php
namespace App\Form;

use App\Entity\Apartment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => 'Reference', // Changer le label si nécessaire
            ])
            ->add('floor', IntegerType::class, [
                'label' => 'Floor', // Changer le label si nécessaire
            ])
            ->add('quota1', IntegerType::class, [
                'label' => 'Quota 1', // Changer le label si nécessaire
            ])
            ->add('quota2', IntegerType::class, [
                'label' => 'Quota 2', // Changer le label si nécessaire
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Apartment::class,
        ]);
    }
}
