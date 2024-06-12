<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Building;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findLastFourEventsByBuilding(Building $building): array
    {
        return $this->createQueryBuilder('e')
        ->andWhere('e.building = :building')
        ->andWhere('e.date > :currentDate')
        ->setParameter('building', $building)
        ->setParameter('currentDate', new \DateTime()) // Utilise la date actuelle pour filtrer
        ->orderBy('e.date', 'ASC') // Trie par date croissante
        ->setMaxResults(4) // Limite les résultats à 4 événements
        ->getQuery()
        ->getResult();
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
