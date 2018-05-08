<?php

namespace App\Repository;

use App\Entity\CandidateInstitution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CandidateInstitution|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidateInstitution|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidateInstitution[]    findAll()
 * @method CandidateInstitution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidateInstitutionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CandidateInstitution::class);
    }

//    /**
//     * @return CandidateInstitution[] Returns an array of CandidateInstitution objects
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
    public function findOneBySomeField($value): ?CandidateInstitution
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
