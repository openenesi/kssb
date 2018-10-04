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

        $query2 = "SELECT o.id, p.surname, p.firstName, p.otherNames, o.appId, o.email, l.lgaName, w.wardName, o.paid, p.title, o.dateCreated as dateCreated, o.datePaid as datePaid, o.dateCompleted as dateCompleted ";

        $status = ($options['status'] == 'all') ? (false) : (true);
        $institution = ($options['institution'] == 'all') ? (false) : (true);
        $lga = ($options['lga'] == 'all') ? (false) : (true);
        $bank = ($options['bank'] == 'all') ? (false) : (true);
        $search = (!isset($options['search']) || $options['search'] == '' || count($options['search']) == 0 || trim($options['search']['value']) == '') ? (false) : (true);
        $where = ($status || $institution || $lga || $bank || $search) ? (true) : (false);
        $and = false;

        $query .= ($where) ? (" WHERE ") : ("");

        if ($status) {
            $query .= ($options['status'] == 'paid') ? (" o.paid=1 ") : (($options['status'] == 'notpaid') ? (" o.paid=0 ") : (($options['status'] == 'notcompleted') ? (" o.appId IS NULL") : (($options['status'] == 'notcompletedpaid') ? ("o.paid=1 AND o.appId IS NULL") : (" o.appId>0"))));
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
        if ($search && is_array($options['search']) && $options['search']['value'] !== '') {
            $query .= (($and) ? (" AND ") : ("")) . " (" .
                    "o.appId LIKE '%" . $options['search']['value'] . "%' OR " .
                    "p.firstName LIKE '%" . $options['search']['value'] . "%' OR " .
                    "p.surname LIKE '%" . $options['search']['value'] . "%' OR " .
                    "p.otherNames LIKE '%" . $options['search']['value'] . "%' OR " .
                    "w.wardName LIKE '%" . $options['search']['value'] . "%' OR " .
                    "l.lgaName LIKE '%" . $options['search']['value'] . "%'" .
                    ")";
            $and = true;
        }

        $query1 .= $query;
        $query2 .= $prependsql . $query . " ORDER BY o.appId DESC";

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

    public function fetchApplicantInfo(array $options) {

        $prependsql = " from \App\Entity\User o "
                . "LEFT JOIN \App\Entity\CandidatePersonal p WITH o.candidatePersonal = p.id "
                . "LEFT JOIN \App\Entity\CandidateInstitution i WITH o.candidateInstitution = i.id "
                . "LEFT JOIN \App\Entity\CandidateBank b WITH o.candidateBank = b.id "
                . "LEFT JOIN \App\Entity\Ward w WITH p.ward = w.id "
                . "LEFT JOIN \App\Entity\Lga l WITH w.lga = l.id "
                . "LEFT JOIN \App\Entity\Institution n WITH i.institution = n.id "
                . "LEFT JOIN \App\Entity\Bank a WITH b.bank = a.id ";

        $query = "";

        $query2 = "SELECT p.surname, p.firstName, p.otherNames, ";
        switch ($options['info']) {
            case 'email':
                $query2 .= ' o.email as email ';
                break;
            case 'gsm':
                $query2 .= ' o.mobileNo as gsm ';
                break;
            case 'regno':
                $query2 .= ' o.matricNo as regno ';
                break;
        }

        $status = ($options['status'] == 'all') ? (false) : (true);
        $institution = ($options['institution'] == 'all') ? (false) : (true);
        $lga = ($options['lga'] == 'all') ? (false) : (true);
        $bank = ($options['bank'] == 'all') ? (false) : (true);
        $where = ($status || $institution || $lga || $bank ) ? (true) : (false);
        $and = false;

        $query .= ($where) ? (" WHERE ") : ("");

        if ($status) {
            $query .= ($options['status'] == 'paid') ? (" o.paid=1 ") : (($options['status'] == 'notpaid') ? (" o.paid=0 ") : (($options['status'] == 'notcompleted') ? (" o.appId IS NULL") : (($options['status'] == 'notcompletedpaid') ? ("o.paid=1 AND o.appId IS NULL") : (" o.appId>0"))));
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

        $query2 .= $prependsql . $query . " ORDER BY o.appId DESC";

        //return($query2);

        /* if (isset($options['start']) && $options['length'] != -1) {
          $query2 .= " LIMIT " . intval($options['start']) . ", " . intval($options['length']);
          } */


        $queryrec = $this->getEntityManager()->createQuery($query2);
        //var_dump($query2); exit();

        try {
            $result2 = $queryrec->getResult();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
        }

        return $result2;
    }

    public function fetchApplicantSummary(array $options) {
        $output = array();
        $draw = null;
        if (isset($options['draw'])) {
            $draw = $options['draw'];
        }

        $prependsql = " from \App\Entity\User o "
                . "LEFT JOIN \App\Entity\CandidatePersonal p WITH o.candidatePersonal = p.id "
                . "LEFT JOIN \App\Entity\CandidateInstitution i WITH o.candidateInstitution = i.id "
                . "LEFT JOIN \App\Entity\CandidateBank b WITH o.candidateBank = b.id "
                . "LEFT JOIN \App\Entity\Ward w WITH p.ward = w.id "
                . "LEFT JOIN \App\Entity\Lga l WITH w.lga = l.id "
                . "LEFT JOIN \App\Entity\Institution n WITH i.institution = n.id "
                . "LEFT JOIN \App\Entity\Bank a WITH b.bank = a.id ";

        $query = "";
        $groupcol = ' o.appId ';
        if (isset($options['grouping'])) {
            switch ($options['grouping']) {
                case 'bank':
                    $groupcol = ' a.bankName ';
                    break;
                case 'lga':
                    $groupcol = ' l.lgaName ';
                    break;
                case 'ward':
                    $groupcol = ' w.wardName ';
                    break;
                case 'inst':
                    $groupcol = ' n.institutionName ';
                    break;
                case 'inst_cat':
                    $groupcol = ' n.institutionCategory ';
                    break;
            }
        }

        //$query2 = "SELECT o.id, p.surname, p.firstName, p.otherNames, p.gender, o.appId,  n.institutionCategory, n.institutionName, a.bankName, b.accountNo, b.bvn, i.matricNo, i.courseOfStudy, i.level";
        $query2 = "SELECT o.id, a.bankName, n.institutionName, n.institutionCategory, l.lgaName, w.wardName, ";

        foreach ($options['cols'] as $col) {
            switch ($col) {
                case "appid":
                    $query2 .= " o.appId, ";
                    break;
                case "name":
                    $query2 .= " p.surname, p.firstName, p.otherNames, ";
                    break;
                case "sex":
                    $query2 .= " p.gender,";
                    break;
                case "matricno":
                    $query2 .= " i.matricNo, ";
                    break;
                case "gsm":
                    $query2 .= " p.mobileNo, ";
                    break;
                case "email":
                    $query2 .= " p.email, ";
                    break;
//                case "bank":
//                    $query2 .=" a.bankName, ";
//                    break;
                case "accno":
                    $query2 .= " b.accountNo, ";
                    break;
                case "bvn":
                    $query2 .= " b.bvn, ";
                    break;
//                case "institution":
//                    $query2 .=" n.institutionName, ";
//                    break;
//                case "inst_cat":
//                    $query2 .=" n.institutionCategory, ";
//                    break;
                case "course":
                    $query2 .= " i.courseOfStudy, ";
                    break;
                case "level":
                    $query2 .= " i.level, ";
                    break;
//                case "lga":
//                    $query2 .=" l.lgaName, ";
//                    break;
//                case "ward":
//                    $query2 .=" w.wardName, ";
//                    break;
            }
        }
        $query2 = trim($query2, " ,");

        $status = true;
        $instcat = ($options['instcat'] == 'all') ? (false) : (true);
        $institution = ($options['institution'] == 'all') ? (false) : (true);
        $ward = ($options['ward'] == 'all') ? (false) : (true);
        $lga = ($options['lga'] == 'all') ? (false) : (true);
        $bank = ($options['bank'] == 'all') ? (false) : (true);
        $where = ($status || $instcat || $ward || $institution || $lga || $bank) ? (true) : (false);
        $and = false;

        $query .= ($where) ? (" WHERE ") : ("");

        if ($status) {
            $query .= " o.appId>0";
            $and = true;
        }

        if ($instcat) {
            $query .= (($and) ? (" AND ") : ("") ) . " n.institutionCategory = " . $options['instcat'];
            $and = true;
        }

        if ($institution) {
            $query .= (($and) ? (" AND ") : ("") ) . " i.institution = " . $options['institution'];
            $and = true;
        }
        if ($ward) {
            $query .= (($and) ? (" AND ") : ("") ) . " p.ward = " . $options['ward'];
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
        if($options['frotooption']=='range'){
            $rangearr = explode('-', $options['froto']);
            $froarr = explode('/', $rangearr[0]);
            $fro = trim($froarr[2])."-".trim($froarr[1])."-".trim($froarr[0])." 00:00:00";
            $toarr = explode('/', $rangearr[1]);
            $to = trim($toarr[2])."-".trim($toarr[1])."-".trim($toarr[0])." 23:59:59";
            
            $query .= (($and) ? (" AND ") : ("") ) . " (o.dateCompleted >= '".$fro."' AND o.dateCompleted <= '" . $to."') ";
            $and = true;
        }
        $query2 .= $prependsql . $query . " ORDER BY " . $groupcol . " ASC";

        $queryrec = $this->getEntityManager()->createQuery($query2);
        //var_dump($query2); exit();

        try {
            if ($draw) {
                $queryrec->setMaxResults(1000);
                $queryrec->setFirstResult(($draw * 1000));
            }
            $result2 = $queryrec->getResult();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
        }


        $output['data'] = $result2;

        return $output;
    }

    public function fetchDistributionByDistrict() {

        $west = "5,6,9,10,11,12,13";
        $east = "3,4,7,8,14,18,19,20,21";
        $central = "1,2,15,16,17";

        $sql_west = "select count(w) west from \App\Entity\User w "
                . "LEFT JOIN \App\Entity\CandidatePersonal p WITH w.candidatePersonal = p.id "
                . "LEFT JOIN \App\Entity\Ward a WITH p.ward = a.id "
                . "LEFT JOIN \App\Entity\Lga l WITH a.lga = l.id "
                . " where l.id in ($west) ";
        $queryrec_west = $this->getEntityManager()->createQuery($sql_west);
        $sql_east = "select count(e) east from \App\Entity\User e "
                . "LEFT JOIN \App\Entity\CandidatePersonal p WITH e.candidatePersonal = p.id "
                . "LEFT JOIN \App\Entity\Ward a WITH p.ward = a.id "
                . "LEFT JOIN \App\Entity\Lga l WITH a.lga = l.id "
                . " where l.id in ($east) ";
        $queryrec_east = $this->getEntityManager()->createQuery($sql_east);
        $sql_central = "select count(c) central from \App\Entity\User c "
                . "LEFT JOIN \App\Entity\CandidatePersonal p WITH c.candidatePersonal = p.id "
                . "LEFT JOIN \App\Entity\Ward a WITH p.ward = a.id "
                . "LEFT JOIN \App\Entity\Lga l WITH a.lga = l.id "
                . " where l.id in ($central) ";
        $queryrec_central = $this->getEntityManager()->createQuery($sql_central);

        try {
            $result_west = $queryrec_west->getResult();
            $result_east = $queryrec_east->getResult();
            $result_central = $queryrec_central->getResult();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
        }

        return array("west" => $result_west[0]['west'], "east" => $result_east[0]['east'], "central" => $result_central[0]['central']);
    }

}
