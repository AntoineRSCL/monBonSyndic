<?php

namespace App\Form;

use App\Entity\Building;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', TextType::class, $this->getConfiguration("Nom", "Nom du bâtiment ..."))
            ->add('address', TextType::class, $this->getConfiguration("Adresse", "Adresse du bâtiment ..."))
            ->add('number', TextType::class, $this->getConfiguration("Numéro", "Numéro du bâtiment ..."))
            ->add('zip', TextType::class, $this->getConfiguration("Code postal", "Code postal du bâtiment ..."))
            ->add('locality', TextType::class, $this->getConfiguration("Localité", "Localité du bâtiment ..."))
            ->add('quota', TextType::class, $this->getConfiguration("Quota", "Quota du bâtiment ..."))
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
