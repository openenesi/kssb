<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BankRepository", readOnly=true)
 */
class Bank {

    public function __construct() {
        $this->candidates = new ArrayCollection();
    }
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="bank_id", type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Invalid Bank Name.")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @ORM\Column(type="string", length=50, unique=true, nullable=false) 
     */
    private $bankName;
    
    /**
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @ORM\Column(type="string", length=5, unique=true, nullable=true) 
     */    
    private $bankCode;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CandidateBank", mappedBy="bank")
     */
    private $candidates;

    public function getId() {
        return $this->id;
    }

    public function getBankName() {
        return $this->bankName;
    }

    public function setBankName($bankname) {
        $this->bankName = $bankname;
    }

    public function getBankCode() {
        return $this->bankCode;
    }

    public function setBankCode($bankcode) {
        $this->bankCode = $bankcode;
    }
    
    public function getCandidates(){
        return $this->candidates;
    }
    public function setCandidates($cand){
        if(!$this->candidates->contains($cand)){
            $this->candidates->add($cand);
            $cand->setBank($this);
        }
    }

}
