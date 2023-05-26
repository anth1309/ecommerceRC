<?php

namespace App\Repository;

use App\Entity\DelivrysAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DelivrysAddress>
 *
 * @method DelivrysAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method DelivrysAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method DelivrysAddress[]    findAll()
 * @method DelivrysAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DelivrysAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DelivrysAddress::class);
    }

    public function save(DelivrysAddress $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DelivrysAddress $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DelivrysAddress[] Returns an array of DelivrysAddress objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DelivrysAddress
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
