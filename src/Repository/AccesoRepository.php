<?php

namespace App\Repository;

use App\Entity\Acceso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Acceso>
 *
 * @method Acceso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Acceso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Acceso[]    findAll()
 * @method Acceso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccesoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Acceso::class);
    }

//    /**
//     * @return Acceso[] Returns an array of Acceso objects
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

//    public function findOneBySomeField($value): ?Acceso
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
