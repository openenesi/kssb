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
    private $startDate;

    /**
     *
     * @ORM\Column(type="datetime", unique=false, nullable=true) 
     */
    private $endDate;

    /**
     *
     * @ORM\Column(type="decimal", precision=7, scale=2, unique=false, nullable=true, options={"unsigned":true}) 
     */
    private $registrationCost;

    /**
     *
     * @ORM\Column(type="integer", unique=false, nullable=false, options={"unsigned":true}) 
     */
    private $scholarshipSession;

    /**
     * @ORM\Column(type="string", length=11, unique=true, nullable=false)
     */
    private $mobileNo;

    /**
     * @ORM\Column(type="string", length=30, unique=true, nullable=false)
     */
    private $email;

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
        if($now < $this->getStartDate()){
            //notready
            return "not-ready";
        }elseif ($now > $this->getEndDate()){
            //closed
            return "closed";
            
        }else{
            //ongoing
            return "ongoing";
        }
    }
}
