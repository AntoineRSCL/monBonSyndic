<?php
// src/Form/SurveyType.php

namespace App\Form;

use App\Entity\Survey;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SurveyType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, $this->getConfiguration("Question", "Question du sondage ..."))
            ->add('picture', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => false,
                'attr' => ['placeholder' => "Image pour le sondage ..."]
            ])
            ->add('description', TextareaType::class, $this->getConfiguration("Description", "Description du sondage ..."));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Survey::class,
        ]);
    }
}
