<?php

namespace App\Form;

use App\Entity\Building;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('number', TextType::class, ['label' => 'Numéro'])
            ->add('zip', TextType::class, ['label' => 'Code postal'])
            ->add('locality', TextType::class, ['label' => 'Localité'])
            ->add('quota', TextType::class, ['label' => 'Quota'])
            ->add('picture', FileType::class, [
                'label' => 'Photo',
                'required' => false, // Rendre le champ facultatif
                'mapped' => false, // Ne pas mapper directement le champ 'picture' à l'entité Building
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
        ]);
    }
}
