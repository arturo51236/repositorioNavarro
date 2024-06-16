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
}
