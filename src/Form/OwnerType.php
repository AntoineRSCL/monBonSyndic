<?php

namespace App\Form;

use App\Entity\Owner;
use App\Entity\Person;
use App\Entity\Apartment;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class OwnerType extends ApplicationType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $admin = $this->security->getUser();
        $building = $admin->getBuilding();

        $builder
            ->add('startDate', DateType::class, $this->getConfiguration("Date de début", "Date de début de la location"))
            ->add('endDate', DateType::class, $this->getConfiguration("Date de fin", "Date de fin de la location"))
            ->add('apartment', EntityType::class, [
                'class' => Apartment::class,
                'query_builder' => function (EntityRepository $er) use ($building) {
                    return $er->createQueryBuilder('a')
                        ->where('a.building = :building')
                        ->setParameter('building', $building);
                },
                'choice_label' => 'reference',
                'label' => 'Appartement',
                'attr' => ['class' => 'form-control']
            ])
            ->add('person', EntityType::class, [
                'class' => Person::class,
                'query_builder' => function (EntityRepository $er) use ($building) {
                    return $er->createQueryBuilder('p')
                        ->where('p.building = :building')
                        ->setParameter('building', $building);
                },
                'choice_label' => function (Person $person) {
                    return $person->getFullName();
                },
                'label' => 'Personne',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Owner::class,
        ]);
    }
}
