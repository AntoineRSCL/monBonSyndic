<?php

namespace App\Repository;

use App\Entity\Apartment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Apartment>
 */
class ApartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apartment::class);
    }

    /**
     * Fonction pour recherche sur la reference
     *
     * @param string|null $query
     * @param integer|null $buildingId
     * @return array
     */
    public function search(?string $query, ?int $buildingId): array 
    {
        $qb = $this->createQueryBuilder('a');

        if ($query) {
            $qb->andWhere('a.reference LIKE :query')
               ->setParameter('query', '%'.$query.'%');
        }

        if ($buildingId !== null && $buildingId !== '') {
            $qb->andWhere('a.building = :buildingId')
               ->setParameter('buildingId', $buildingId);
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Apartment[] Returns an array of Apartment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Apartment
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
