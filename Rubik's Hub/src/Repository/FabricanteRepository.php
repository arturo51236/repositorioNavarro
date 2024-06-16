<?php

namespace App\Repository;

use App\Entity\Fabricante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fabricante>
 *
 * @method Fabricante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fabricante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fabricante[]    findAll()
 * @method Fabricante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FabricanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fabricante::class);
    }
}
