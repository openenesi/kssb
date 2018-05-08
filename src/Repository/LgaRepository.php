<?php

namespace App\Repository;

use App\Entity\Lga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Lga|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lga|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lga[]    findAll()
 * @method Lga[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LgaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Lga::class);
    }

//    /**
//     * @return Lga[] Returns an array of Lga objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lga
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    
        public function findByLga($lgaid)
    {
        return $this->createQueryBuilder('l')
            ->where('l.lga = :lga')
            ->setParameter('lga', $lgaid)
            ->orderBy('l.wardName', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
*/
}
