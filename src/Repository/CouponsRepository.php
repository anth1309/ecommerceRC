<?php

namespace App\Repository;

use App\Entity\Coupons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coupons>
 *
 * @method Coupons|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupons|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coupons[]    findAll()
 * @method Coupons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupons::class);
    }

    public function save(Coupons $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Coupons $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
