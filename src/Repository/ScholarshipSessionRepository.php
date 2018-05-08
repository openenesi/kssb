<?php

namespace App\Repository;

use App\Entity\ScholarshipSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ScholarshipSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScholarshipSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScholarshipSession[]    findAll()
 * @method ScholarshipSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScholarshipSessionRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, ScholarshipSession::class);
    }

//    /**
//     * @return ScholarshipSession[] Returns an array of ScholarshipSession objects
//     */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('s.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?ScholarshipSession
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function findSessionConfig() {
        $result= $this->createQueryBuilder('s')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getResult();
        return $result[0];
    }

}
