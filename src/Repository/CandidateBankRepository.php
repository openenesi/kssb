<?php

namespace App\Repository;

use App\Entity\CandidateBank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CandidateBank|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidateBank|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidateBank[]    findAll()
 * @method CandidateBank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidateBankRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CandidateBank::class);
    }

//    /**
//     * @return CandidateBank[] Returns an array of CandidateBank objects
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
    public function findOneBySomeField($value): ?CandidateBank
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
