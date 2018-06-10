<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="candidate")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email is already in use.")
 * @UniqueEntity(fields="bvn", message="BVN is already taken.")
 * @UniqueEntity(fields="mobileNo", message="Mobile number is already in use.")
 * @UniqueEntity(fields="appId", message="Application Id has already been assigned.")
 */
class User implements UserInterface, \Serializable {

    public function __construct() {
        $this->isActive = true;
        $this->paid= false;
        $this->dateCreated = new \DateTime();
// may not be needed, see section on salt below
// $this->salt = md5(uniqid('', true));
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="user_id", type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Your unique email is required.")
     * @Assert\Email(message="A valid email address is required.")
     * @ORM\Column(type="string", length=30, unique=true, nullable=false)
     */
    private $username;

    /**
     * @Assert\NotBlank(message="Your password required.")
     * @Assert\Type(type="string", message="A valid password is required.")
     * @ORM\Column(name="pwd", type="string", length=64, nullable=false)
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Matric/Registration number is required")
     * @Assert\Type(type="string", message="Invalid type. required string")
     * @Assert\Length(min=3, max=30, minMessage="Matric/Registration number should not be less than 3 characters", maxMessage="Matric/Registration number should not be more than 30 characters")
     * @ORM\Column(type="string", length=30, unique=false, nullable=false)
     */
    private $matricNo;

    /**
     * @Assert\NotBlank(message="Your unique BVN is required.")
     * @Assert\Type(type="numeric", message="Invalid value. Only digits allowed.")
     * @Assert\Length(min=11, max=11, exactMessage="Your BVN must be composed of only 11 digits.")
     * @ORM\Column(type="string", length=11, unique=true, nullable=false, options={"unsigned":true, "fixed":true})
     */
    private $bvn;

    /**
     * @Assert\NotBlank(message="Your unique mobile number is required.")
     * @Assert\Type(type="numeric", message="Invalid value. Only digits allowed.")
     * @Assert\Length(min=11, max=11, exactMessage="Your mobile number must be composed of only 11 digits.")
     * @ORM\Column(type="string", length=11, unique=true, nullable=false)
     */
    private $mobileNo;

    /**
     * @Assert\NotBlank(message="Your unique email is required.")
     * @Assert\Email(message="A valid email address is required.")
     * @ORM\Column(type="string", length=30, unique=true, nullable=false)
     */
    private $email;
    
    /**
     * @Assert\Type(type="bool")
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $paid;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CandidateBank")
     * @ORM\JoinColumn(name="candidatebank_id", referencedColumnName="candidatebank_id", nullable=true)
     */
    private $candidateBank;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CandidatePersonal")
     * @ORM\JoinColumn(name="candidatepersonal_id", referencedColumnName="candidatepersonal_id", nullable=true)
     */
    private $candidatePersonal;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CandidateInstitution")
     * @ORM\JoinColumn(name="candidateinstitution_id", referencedColumnName="candidateinstitution_id", nullable=true)
     */
    private $candidateInstitution;

    /**
     * @Assert\Type(type="integer")
     * @ORM\Column(type="integer", unique=true, nullable=true, options={"unsigned":true}) 
     */
    private $appId;

    /**
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=30, unique=true, nullable=true) 
     */
    private $trxnRef;

    /**
     * @Assert\NotBlank(message="Date created is required.")
     * @Assert\DateTime(message="Invalid value for date created.")
     * @ORM\Column(type="datetime", unique=false, nullable=false) 
     */
    private $dateCreated;
    /**
     * @Assert\DateTime(message="Invalid value for date paid.")
     * @Assert\GreaterThanOrEqual(propertyPath="dateCreated", message="Date paid is not accepted")
     * @ORM\Column(type="datetime", unique=false, nullable=true) 
     */
    private $datePaid;
    /**
     * @Assert\DateTime(message="Invalid value for date paid.")
     * @Assert\GreaterThanOrEqual(propertyPath="datePaid", message="Date of completion is not accepted")
     * @ORM\Column(type="datetime", unique=false, nullable=true) 
     */
    private $dateCompleted;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function getId() {
        return $this->id;
    }

    public function getMatricNo() {
        return $this->matricNo;
    }

    public function setMatricNo($matricno) {
        $this->matricNo = $matricno;
    }

    public function getBvn() {
        return $this->bvn;
    }

    public function setBvn($bvn) {
        $this->bvn = $bvn;
    }

    public function getPaid(){
        return $this->paid;
        //return true;
    }
    
    public function setPaid($paid){
        $this->paid = $paid;
    }
    
    public function getMobileNo() {
        return $this->mobileNo;
    }

    public function setMobileNo($mobileno) {
        $this->mobileNo = $mobileno;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $email = trim(strtolower($email));
        $this->email = $email;
        $this->username= $email;
    }

    public function getCandidatePersonal() {
        return $this->candidatePersonal;
    }

    public function setCandidatePersonal($personal) {
        $this->candidatePersonal = $personal;
        $this->mobileNo= $personal->getMobileNo();
    }

    public function getCandidateInstitution() {
        return $this->candidateInstitution;
    }

    public function setCandidateInstitution($inst) {
        $this->candidateInstitution = $inst;
        $this->matricNo= $inst->getMatricNo();
    }

    public function getCandidateBank() {
        return $this->candidateBank;
    }

    public function setCandidateBank($bank) {
        $this->candidateBank = $bank;
        $this->bvn=$bank->getBvn();
    }

    public function getAppId() {
        return $this->appId;
    }

    public function setAppId($id) {
        $this->appId = $id;
    }
    
    public function getTrxnRef(){
        return $this->trxnRef;
    }
    
    public function setTrxnRef($ref){
        $this->trxnRef = $ref;
    }

    public function getDateCreated(){
        return $this->dateCreated;
    }
    
    public function setDateCreated($dt){
        $this->dateCreated = $dt;
    }
    public function getDatePaid(){
        return $this->datePaid;
    }
    
    public function setDatePaid($dt){
        if (!($dt instanceof \DateTime)) {
            $dt = new \DateTime($dt);
        }
        $this->datePaid = $dt;
    }
    public function getDateCompleted(){
        return $this->dateCompleted;
    }
    
    public function setDateCompleted($dt){
        if (!($dt instanceof \DateTime)) {
            $dt = new \DateTime($dt);
        }
        $this->dateCompleted = $dt;
    }
    
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = trim(strtolower($username));
    }

    public function getSalt() {
// you *may* need a real salt depending on your encoder
// see section on salt below
        return null;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($pass) {
        $this->password = $pass;
    }

    public function getRoles() {
        return array('ROLE_CANDIDATE');
    }

    public function eraseCredentials() {
        
    }
    
    /** @see \Serializable::serialize() */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->appId,
            $this->bvn,
            $this->matricNo,
            $this->mobileNo,
            $this->isActive,
            $this->email,
            $this->paid,
                // see section on salt below
// $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized) {
        list (
                $this->id,
                $this->username,
                $this->password,
                $this->appId,
                $this->bvn,
                $this->matricNo,
                $this->mobileNo,
                $this->isActive,
                $this->email,
                $this->paid,
                // see section on salt below
// $this->salt
                ) = unserialize($serialized);
    }

}
