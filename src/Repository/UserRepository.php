<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, User::class);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('c.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?User
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */


    public function getLastAppId() {
        $query1 = $this->getEntityManager()->createQuery("SELECT MAX(o.appId) as maxappid from \App\Entity\User o");

        $result = $query1->setMaxResults(1)->getResult();
        $result = $result[0]['maxappid'];
        return $result;
    }

    public function countAllApplicants($status = 'all') {
        $query = "SELECT COUNT(o) as applicantcount from \App\Entity\User o ".(($status=='paid')?("where o.paid = 1"):(($status=='completed')?("where o.appId >0"):("")));
        $query1 = $this->getEntityManager()->createQuery($query);

        $result = $query1->setMaxResults(1)->getResult();
        $result = $result[0]['applicantcount'];
        return $result;
    }

}
