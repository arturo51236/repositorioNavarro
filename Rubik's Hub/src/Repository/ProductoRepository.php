<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Producto>
 *
 * @method Producto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Producto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Producto[]    findAll()
 * @method Producto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    /**
     * @return Producto[] Devuelve un array de productos de una categoría concreta
     */
    public function findByCategoria($uuid): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.categoria', 'c')
            ->andWhere('c.uuid = :val')
            ->setParameter('val', $uuid)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Producto[] Devuelve un array de productos con diseño propio
     */
    public function findByDiseno_propio(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.diseno_propio = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Producto Devuelve un producto a partir de su uuid
     */
    public function findByUuid($uuid): ?Producto
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.uuid = :val')
            ->setParameter('val', $uuid)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
