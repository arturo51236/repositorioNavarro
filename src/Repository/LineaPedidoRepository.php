<?php

namespace App\Repository;

use App\Entity\LineaPedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LineaPedido>
 *
 * @method LineaPedido|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineaPedido|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineaPedido[]    findAll()
 * @method LineaPedido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineaPedidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineaPedido::class);
    }

    /**
     * @return LineaPedido[] Devuelve un array con todas las lineas de pedido de un producto concreto
     */
    public function findByIdProducto($id): array
    {
        return $this->createQueryBuilder('l')
            ->join('l.producto', 'p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
        ;
    }
}
