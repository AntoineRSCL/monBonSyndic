<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\News;
use App\Entity\Vote;
use App\Entity\Event;
use App\Entity\Owner;
use App\Entity\Person;
use App\Entity\Survey;
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
        $surveys = [];

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
            ->setUsername("bautAntoine")
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

            // Création d'un admin pour cet immeuble
            $admin = new Person();
            $admin->setName($faker->lastName())
                ->setFirstName($faker->firstName())
                ->setEmail($faker->email())
                ->setAddress($building->getAddress()) // Utilisation de l'adresse de l'immeuble
                ->setNumber($faker->buildingNumber())
                ->setZip($building->getZip()) // Utilisation du code postal de l'immeuble
                ->setLocality($building->getLocality()) // Utilisation de la localité de l'immeuble
                ->setCountry($faker->country())
                ->setOptin(true)
                ->setUsername("admin_" . $i) // Nom d'utilisateur basé sur le nom de l'immeuble
                ->setPassword($this->passwordHasher->hashPassword($admin, 'admin'))
                ->setRoles(["ROLE_ADMIN"]);

            // Assignation de l'immeuble à cet admin
            $admin->setBuilding($building);

            // Persistance de l'admin
            $manager->persist($admin);

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
                ->setBuilding($buildings[rand(0, count($buildings)-1)])
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

        //Creation de sondage
        for($i=1; $i<=5; $i++)
        {
            $survey = new Survey();
            $building = $buildings[rand(0, count($buildings)-1)];

            $survey->setBuilding($building)
                ->setQuestion($faker->word())
                ->setPicture(null)
                ->setDescription($faker->paragraph(2));

                
            $manager->persist($survey);
            
            $surveys[] = $survey;
        }

        //Creation de reponses au sondages
        for($i=1; $i<=150; $i++)
        {
            $vote = new Vote();
            
            $person = $persons[rand(0, count($persons)-1)];
            $survey = $surveys[rand(0, count($surveys)-1)];
            $answers = ['Pour', 'Contre', 'Abstention'];
            $answer = $answers[rand(0, count($answers)-1)];
            $vote->setPerson($person)
                ->setSurvey($survey)
                ->setAnswer($answer);

            $manager->persist($vote);

        }

        //Creation News
        for($i=1; $i<=20; $i++)
        {
            $news = new News();
            $building = $buildings[rand(0, count($buildings)-1)];

            $news->setBuilding($building)
                ->setTitle($faker->sentence(3))
                ->setContent($faker->sentence(150))
                ->setPicture('')
                ->setDate($faker->dateTime());

            $manager->persist($news);
        }

        //Creation Events
        for($i=1; $i<=20; $i++)
        {
            $event = new Event();
            $building = $buildings[rand(0, count($buildings)-1)];

            $event->setBuilding($building)
                ->setTitle($faker->sentence(3))
                ->setDescription($faker->sentence(150))
                ->setPicture('')
                ->setDate($faker->dateTime())
                ->setDuration($faker->randomDigitNotNull()." heures");

            $manager->persist($event);
        }

        $manager->flush();
    }
}
