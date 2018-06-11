<?php

namespace App\Repository;

use App\Entity\AccountCounter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AccountCounter|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountCounter|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountCounter[]    findAll()
 * @method AccountCounter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountCounterRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, AccountCounter::class);
    }

//    /**
//     * @return AccountCounter[] Returns an array of AccountCounter objects
//     */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('b')
      ->andWhere('b.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('b.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?AccountCounter
      {
      return $this->createQueryBuilder('b')
      ->andWhere('b.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function findAccountCounter() {
        $result = $this->createQueryBuilder('s')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
        return $result[0];
    }

}
