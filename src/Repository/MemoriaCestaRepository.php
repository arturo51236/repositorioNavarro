<?php

namespace App\Repository;

use App\Entity\MemoriaCesta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MemoriaCesta>
 *
 * @method MemoriaCesta|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemoriaCesta|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemoriaCesta[]    findAll()
 * @method MemoriaCesta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemoriaCestaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemoriaCesta::class);
    }

//    /**
//     * @return MemoriaCesta[] Returns an array of MemoriaCesta objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MemoriaCesta
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
