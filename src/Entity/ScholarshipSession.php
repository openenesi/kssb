<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScholarshipSessionRepository", readOnly=true)
 */
class ScholarshipSession {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="datetime", unique=false, nullable=true) 
     */
    private $startDate= "2018-05-27 00:00:00";

    /**
     *
     * @ORM\Column(type="datetime", unique=false, nullable=true) 
     */
    private $endDate="2018-12-31 23:59:59";

    /**
     *
     * @ORM\Column(type="decimal", precision=7, scale=2, unique=false, nullable=true, options={"unsigned":true}) 
     */
    private $registrationCost= 650.00;

    /**
     *
     * @ORM\Column(type="integer", unique=false, nullable=false, options={"unsigned":true}) 
     */
    private $scholarshipSession=2018;

    /**
     * @ORM\Column(type="string", length=11, unique=true, nullable=false)
     */
    private $mobileNo="07016804127";

    /**
     * @ORM\Column(type="string", length=30, unique=true, nullable=false)
     */
    private $email= "info@kssb.kg.gov.ng";

    public function getId() {
        return $this->id;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function setStartDate($dt) {
        $this->startDate = $dt;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function setEndDate($dt) {
        $this->endDate = $dt;
    }

    public function getRegistrationCost() {
        return $this->registrationCost;
    }

    public function setRegistrationCost($cost) {
        $this->registrationCost = $cost;
    }

    public function getEmail(){
        return $this->email;
    }
    
    public function setEmail($email){
        $this->email= $email;
    }
    
    public function getMobileNo(){
        return $this->mobileNo;
    }
    
    public function setMobileNo($no){
        $this->mobileNo = $no;
    }
    
    public function getScholarshipSession(){
        return $this->scholarshipSession;
    }
    
    public function setScholarshipSession($ses){
        $this->scholarshipSession = $ses;
    }
    
    public function getApplicationSessionStatus(){
        $now = new \DateTime();
        //$diff1 = $now->diff($this->getStartDate());
        //$diff2 = $now->diff($this->getEndDate());
        $sd = (($this->getStartDate() instanceof \DateTime)?($this->getStartDate()):(new \DateTime($this->getStartDate())));
        $ed = (($this->getEndDate() instanceof \DateTime)?($this->getEndDate()):(new \DateTime($this->getEndDate())));
        if($now < $sd){
            //notready
            return "not-ready";
        }elseif ($now > $ed){
            //closed
            return "closed";
            
        }else{
            //ongoing
            return "ongoing";
        }
    }
}
