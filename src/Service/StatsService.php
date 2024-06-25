<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class  StatsService{

    public function __construct(private EntityManagerInterface $manager)
    {}

    /**
     * Fonction qui recupere le nombre de batiment enregistre
     *
     * @return integer
     */
    public function getBuildingCount(): int
    {
        return $this->manager->createQuery("SELECT COUNT(b) FROM App\Entity\Building b")->getSingleScalarResult();
    }

    /**
     * Fonction qui recupere le nombre d'appartement enregistre
     *
     * @return integer
     */
    public function getApartmentCount(): int
    {
        return $this->manager->createQuery("SELECT COUNT(a) FROM App\Entity\Apartment a")->getSingleScalarResult();
    }

    /**
     * Permet de recup toutes les personnes
     *
     * @return integer
     */
    public function getPersonCount(): int 
    {
        return $this->manager->createQuery("SELECT COUNT(p) FROM App\Entity\Person p")->getSingleScalarResult();
    }

    public function getContactCount($building = null): int
    {
        $query = $this->manager->createQuery(
            "SELECT COUNT(c) FROM App\Entity\Contact c WHERE c.building = :building"
        );

        $query->setParameter('building', $building);

        return $query->getSingleScalarResult();
    }

    /**
     * Permet de retourner le nombre de personne dans un building specifique
     *
     * @param [type] $building
     * @return integer
     */
    public function getPersonCountByBuilding($building): int
    {
        return $this->manager->createQuery("SELECT COUNT(p) FROM App\Entity\Person p WHERE p.building = :building")
            ->setParameter('building', $building)
            ->getSingleScalarResult();
    }

    public function getOwnerCountByBuilding($building): int
    {
        return $this->manager->createQuery("SELECT COUNT(o) FROM App\Entity\Owner o WHERE o.apartment IN (
            SELECT a FROM App\Entity\Apartment a WHERE a.building = :building
        )")
        ->setParameter('building', $building)
        ->getSingleScalarResult();
    }

    public function getSurveyCountByBuilding($building): int
    {
        return $this->manager->createQuery("SELECT COUNT(s) FROM App\Entity\Survey s WHERE s.building = :building")
            ->setParameter('building', $building)
            ->getSingleScalarResult();
    }

    public function getNewsCountByBuilding($building): int
    {
        return $this->manager->createQuery("SELECT COUNT(n) FROM App\Entity\News n WHERE n.building = :building")
            ->setParameter('building', $building)
            ->getSingleScalarResult();
    }

    public function getEventCountByBuilding($building): int
    {
        return $this->manager->createQuery("SELECT COUNT(e) FROM App\Entity\Event e WHERE e.building = :building")
            ->setParameter('building', $building)
            ->getSingleScalarResult();
    }

    public function getIssueCountByBuilding($building): int
    {
        return $this->manager->createQuery("SELECT COUNT(i) FROM App\Entity\Issue i WHERE i.building = :building")
            ->setParameter('building', $building)
            ->getSingleScalarResult();
    }
}