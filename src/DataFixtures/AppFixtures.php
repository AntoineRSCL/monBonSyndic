<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Owner;
use App\Entity\Person;
use App\Entity\Building;
use App\Entity\Apartment;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_BE');
        $slugify = new Slugify();

        $buildings = [];
        $apartments = [];
        $persons = [];

        //création d'un superadmin
        $superadmin = new Person();
        $superadmin->setName('Baut')
            ->setFirstName('Antoine')
            ->setEmail('abaut2001@gmail.com')
            ->setAddress('Avenue Jean Burgers')
            ->setNumber("3 bte 3")
            ->setZip("1180")
            ->setLocality("Bruxelles")
            ->setCountry("Belgique")
            ->setOptin(true)
            ->setPassword($this->passwordHasher->hashPassword($superadmin, 'superadmin'))
            ->setRoles(["ROLE_SUPERADMIN"]);

        
        $manager->persist($superadmin);

        //Création d'immeuble
        for($i=1; $i<=5; $i++)
        {
            $building = new Building();
            $building->setName($faker->word())
                ->setAddress($faker->streetName())
                ->setNumber($faker->buildingNumber())
                ->setZip($faker->postcode())
                ->setLocality($faker->cityName())
                ->setQuota($faker->numberBetween(5000, 50000));

            $manager->persist($building);

            $buildings[] = $building;
        }


        //Creation d'appartement
        for($i=1; $i<=100; $i++)
        {
            $apartment = new Apartment();
            $reference = $faker->bothify('??##');
            $floor = $faker->randomDigit();
            $quota1 = $faker->numberBetween(10, 250);
            $quota2 = $faker->numberBetween(10, 250);
            $building = $buildings[rand(0, count($buildings)-1)];

            $apartment->setBuilding($building)
                ->setReference($reference)
                ->setFloor($floor)
                ->setQuota1($quota1)
                ->setQuota2($quota2);

            $manager->persist($apartment);

            $apartments[] = $apartment;
        }

        //Creation de Personnes
        for($i=1; $i<=100; $i++)
        {
            $person = new Person();
            $person->setName($faker->lastName())
                ->setFirstName($faker->firstName())
                ->setEmail($faker->email())
                ->setAddress($faker->streetName())
                ->setNumber($faker->buildingNumber())
                ->setZip($faker->postcode())
                ->setLocality($faker->cityName())
                ->setCountry("Belgique")
                ->setOptin($faker->boolean())
                ->setRoles(["ROLE_USER"]);

            $manager->persist($person);

            $persons[] = $person;
        }

        //Creation de Proprietaires
        for($i=1; $i<=150; $i++)
        {
            $owner = new Owner();
            $finish = $faker->boolean();
            $startDate = $faker->dateTimeBetween('-25 years');
            if($finish == false){
                $duration = rand(1,96);
                $endDate = (clone $startDate)->modify("+".$duration." month");

                // Vérifie si l'endDate est supérieure à aujourd'hui, sinon le definir a null
                if ($endDate > new \DateTime()) {
                    $endDate = null;
                }
            }else{
                $endDate = null;
            }
            $person = $persons[rand(0, count($persons)-1)];
            $apartment = $apartments[rand(0, count($apartments)-1)];

            $owner->setApartment($apartment)
                ->setPerson($person)
                ->setStartDate($startDate)
                ->setEndDate($endDate);

            $manager->persist($owner);
        }

        $manager->flush();
    }
}
