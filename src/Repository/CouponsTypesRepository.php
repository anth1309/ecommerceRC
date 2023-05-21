<?php

namespace App\Repository;

use App\Entity\CouponsTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CouponsTypes>
 *
 * @method CouponsTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method CouponsTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method CouponsTypes[]    findAll()
 * @method CouponsTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponsTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CouponsTypes::class);
    }

    public function save(CouponsTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CouponsTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
