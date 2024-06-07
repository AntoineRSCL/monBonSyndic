<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NewsType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Titre de la nouvelle ..."))
            ->add('content', TextareaType::class, $this->getConfiguration("Contenu", "Contenu de la nouvelle ..."))
            ->add('picture', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => false, // Ne pas mapper directement le champ 'picture' à l'entité News
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
