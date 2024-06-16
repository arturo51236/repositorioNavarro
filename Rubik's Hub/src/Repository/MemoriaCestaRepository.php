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

    /**
     * @return MemoriaCesta Devuelve la linea de pedido (sin confirmar) a partir de un uuid concreto
     */
    public function findByUuid($uuid): ?MemoriaCesta
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.uuid = :val')
            ->setParameter('val', $uuid)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return MemoriaCesta[] Devuelve un array de lineas de pedido (sin confirmar) de un usuario concreto
     */
    public function findByUuidUsuario($uuid): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.usuario', 'u')
            ->andWhere('u.uuid = :val')
            ->setParameter('val', $uuid)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return MemoriaCesta[] Devuelve un array con todas las lineas de pedido (sin confirmar) de un producto concreto
     */
    public function findByIdProducto($id): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.producto', 'p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
        ;
    }
}
