<?php

namespace App\Repository;

use App\Entity\Survey;
use App\Entity\Building;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Survey>
 */
class SurveyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Survey::class);
    }

    public function findLastTwoSurveysByBuilding(Building $building): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.building = :building')
            ->setParameter('building', $building)
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();
    }

    public function findAllSurveysByBuilding(Building $building): array 
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.building = :building')
            ->setParameter('building', $building)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Survey[] Returns an array of Survey objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Survey
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
