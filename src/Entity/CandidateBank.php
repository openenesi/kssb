<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidateBankRepository")
 * @UniqueEntity(fields="bvn", message="BVN is already in use")
 * @UniqueEntity(fields="accountNo", message="Account number is already in use.")
 */
class CandidateBank
{
    use \App\Utility\Utils;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="candidatebank_id", type="integer")
     */
    private $id;
    
    /**
     * @Assert\NotBlank(message="Bank field is required.")
     * @ORM\ManyToOne(targetEntity="App\Entity\Bank", inversedBy="candidates")
     * @ORM\JoinColumn(name="bank_id", referencedColumnName="bank_id", nullable=false)
     */
    private $bank;
    
    /**
     * @Assert\NotBlank(message="Your unique BVN is required.")
     * @Assert\Type(type="numeric", message="Invalid value. Only digits allowed")
     * @Assert\Length(min=11, max=11, exactMessage="Your BVN must be composed of only 11 digits.")
     * @ORM\Column(type="string", length=11, unique=true, nullable=false, options={"unsigned":true, "fixed":true}) 
     */
    private $bvn;
    
    /**
     * @Assert\NotBlank(message="Account name is required.")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=80, minMessage="Account name too short.", maxMessage="Account name too long.")
     * @ORM\Column(type="string", length=80, unique=false, nullable=false) 
     */    
    private $accountName;
    
    /**
     * @Assert\NotBlank(message="Account number is required.")
     * @Assert\Type(type="numeric", message="Invalid value. Only digits allowed")
     * @Assert\Length(min=10, max=10, exactMessage="NUBAN account number must be composed of only 10 digits.")
     * @ORM\Column(type="string", length=10, unique=true, nullable=false, options={"unsigned":true, "fixed":true}) 
     */
    private $accountNo;
    
    /**
     * @Assert\Choice(callback="getUtilBankAccTypes", message="Invalid value for account type")
     * @ORM\Column(type="string", length=30, unique=false, nullable=true) 
     */
    private $accountType;
    
    /**
     * @Assert\Type(type="numeric", message="Invalid value. Only digits allowed")
     * @ORM\Column(type="integer", unique=false, nullable=true, options={"unsigned":true}) 
     */
    private $sortCode;

    public function getId()
    {
        return $this->id;
    }
    
    public function getBvn() {
        return $this->bvn;
    }

    public function setBvn($bvn) {
        $this->bvn = $bvn;
    }
    
    public function getAccountName(){
        return $this->accountName;
    }
    public function setAccountName($accname){
        $this->accountName = $accname;
    }
    
    public function getAccountNo(){
        return $this->accountNo;
    }
    public function setAccountNo($accno){
        $this->accountNo = $accno;
    }
    
    public function getAccountType(){
        return $this->accountType;
    }
    public function setAccountType($acctype){
        $acctype = trim($acctype);
        if (!in_array($acctype, CandidateBank::getUtilBankAccTypes())) {
            throw new \InvalidArgumentException("Invalid account type");
        }
        $this->accountType = $acctype;
    }
    
    public function getSortCode(){
        return $this->sortCode;
    }
    public function setSortCode($sortcode){
        $this->sortCode= $sortcode;
    }
    
    public function getBank(){
        return $this->bank;
    }
    public function setBank($bank){
        $this->bank= $bank;
    }
    
}
