<?php

namespace App\Repository;

use App\Entity\CandidatePersonal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CandidatePersonal|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidatePersonal|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidatePersonal[]    findAll()
 * @method CandidatePersonal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatePersonalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CandidatePersonal::class);
    }

//    /**
//     * @return CandidatePersonal[] Returns an array of CandidatePersonal objects
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
    public function findOneBySomeField($value): ?CandidatePersonal
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
