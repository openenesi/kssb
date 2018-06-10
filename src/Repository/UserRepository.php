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
        $query = "SELECT COUNT(o) as applicantcount from \App\Entity\User o " . (($status == 'paid') ? ("where o.paid = 1") : (($status == 'completed') ? ("where o.appId >0") : ("")));
        $query1 = $this->getEntityManager()->createQuery($query);

        $result = $query1->setMaxResults(1)->getResult();
        $result = $result[0]['applicantcount'];
        return $result;
    }

    public function fetchApplicantRecords(array $options) {
        $output = array();
        $output['draw'] = $options['draw'];

        $prependsql = " from \App\Entity\User o "
                . "LEFT JOIN \App\Entity\CandidatePersonal p WITH o.candidatePersonal = p.id "
                . "LEFT JOIN \App\Entity\CandidateInstitution i WITH o.candidateInstitution = i.id "
                . "LEFT JOIN \App\Entity\CandidateBank b WITH o.candidateBank = b.id "
                . "LEFT JOIN \App\Entity\Ward w WITH p.ward = w.id "
                . "LEFT JOIN \App\Entity\Lga l WITH w.lga = l.id "
                . "LEFT JOIN \App\Entity\Institution n WITH i.institution = n.id "
                . "LEFT JOIN \App\Entity\Bank a WITH b.bank = a.id ";

        $query = "";

        $query1 = "SELECT COUNT(o) as applicantcount " . $prependsql;

        $query2 = "SELECT o.id, p.surname, p.firstName, p.otherNames, o.appId, l.lgaName, w.wardName, o.paid, p.title, o.dateCreated as dateCreated, o.datePaid as datePaid, o.dateCompleted as dateCompleted " ;

        $status = ($options['status'] == 'all') ? (false) : (true);
        $institution = ($options['institution'] == 'all') ? (false) : (true);
        $lga = ($options['lga'] == 'all') ? (false) : (true);
        $bank = ($options['bank'] == 'all') ? (false) : (true);
        $search = (!isset($options['search']) || $options['search'] == '' || count($options['search'])== 0 || trim($options['search']['value']) == '') ? (false) : (true);
        $where = ($status || $institution || $lga || $bank || $search) ? (true) : (false);
        $and = false;

        $query .= ($where) ? (" WHERE ") : ("");

        if ($status) {
            $query .= ($options['status'] == 'paid') ? (" o.paid=1 ") : (($options['status'] == 'notpaid') ? (" o.paid=0 ") : (($options['status']=='notcompleted')?(" o.appId IS NULL"):(" o.appId>0")));
            $and = true;
        }

        if ($institution) {
            $query .= (($and) ? (" AND ") : ("") ) . " i.institution = " . $options['institution'];
            $and = true;
        }
        if ($lga) {
            $query .= (($and) ? (" AND ") : ("") ) . " w.lga = " . $options['lga'];
            $and = true;
        }
        if ($bank) {
            $query .= (($and) ? (" AND ") : ("")) . " b.bank = " . $options['bank'];
            $and = true;
        }
        if($search && is_array($options['search']) && $options['search']['value'] !== ''){
            $query .= (($and) ? (" AND ") : ("")) . " (".
                    "o.appId LIKE '%".$options['search']['value']."%' OR ".
                    "p.firstName LIKE '%".$options['search']['value']."%' OR ".
                    "p.surname LIKE '%".$options['search']['value']."%' OR ".
                    "p.otherNames LIKE '%".$options['search']['value']."%' OR ".
                    "w.wardName LIKE '%".$options['search']['value']."%' OR ".
                    "l.lgaName LIKE '%".$options['search']['value']."%'".
                    ")";
            $and = true;
        }

        $query1 .= $query;
        $query2 .= $prependsql.$query . " ORDER BY o.appId DESC";
        
        //return($query2);
        
        /* if (isset($options['start']) && $options['length'] != -1) {
          $query2 .= " LIMIT " . intval($options['start']) . ", " . intval($options['length']);
          } */

        $querycount = $this->getEntityManager()->createQuery($query1);
        //var_dump($query1); exit();

        $result = $querycount->setMaxResults(1)->getResult();
        $result = $result[0]['applicantcount'];
        $output['recordsTotal'] = $result;
        $output['recordsFiltered'] = $result;

        $queryrec = $this->getEntityManager()->createQuery($query2);
        //var_dump($query2); exit();

        try {
            $queryrec->setMaxResults(intval($options['length']));
            $queryrec->setFirstResult(intval($options['start']));
            $result2 = $queryrec->getResult();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
        }


        $output['data'] = $result2;

        return $output;
    }

}
