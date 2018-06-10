<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidatePersonalRepository")
 * @UniqueEntity(fields="email", message="Email is already in use.")
 * @UniqueEntity(fields="mobileNo", message="Mobile number is already in use.")
 */
class CandidatePersonal {

    use \App\Utility\Utils;

    public function __construct() {
        $db = new \DateTime();
        $this->minDateOfBirth = $db->sub(new \DateInterval("P15Y"));
        //$this->passport = "pending";
    }

    public $minDateOfBirth;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="candidatepersonal_id", type="integer")
     */
    private $id;

    /**
     * @Assert\Choice(callback="getUtilTitles", message="Invalid value for title.")
     * @ORM\Column(type="string", length=15, unique=false, nullable=true) 
     */
    private $title;

    /**
     * @Assert\NotBlank(message="Surname is required.")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=30, minMessage="Value supplied is too short", maxMessage="Value supplied should not exceed 30 chars")
     * @ORM\Column(type="string", length=30, unique=false, nullable=false) 
     */
    private $surname;

    /**
     * @Assert\NotBlank(message="First name is required.")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=30, minMessage="Value supplied is too short", maxMessage="Value supplied should not exceed 30 chars")
     * @ORM\Column(type="string", length=30, unique=false, nullable=false) 
     */
    private $firstName;

    /**
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=0, max=40, minMessage="Value supplied is too short", maxMessage="Value supplied should not exceed 30 chars")
     * @ORM\Column(type="string", length=40, unique=false, nullable=true) 
     */
    private $otherNames;

    /**
     * @Assert\NotBlank(message="Home address is required.")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=100, minMessage="Value supplied is too short", maxMessage="Value supplied should not exceed 100 chars")
     * @ORM\Column(type="string", length=100, unique=false, nullable=false) 
     */
    private $homeAddr;

    /**
     * @Assert\NotBlank(message="Date of birth is required.")
     * @Assert\Date(message="Invalid value for date of birth.")
     * @Assert\LessThanOrEqual(propertyPath="minDateOfBirth", message="Date of birth is not accepted")
     * @ORM\Column(type="date", unique=false, nullable=false) 
     */
    private $dob;

    /**
     * @Assert\NotBlank(message="Gender is required.")
     * @Assert\Choice(callback="getUtilGender", message="Invalid value for gender.")
     * @ORM\Column(type="string", length=15, unique=false, nullable=false) 
     */
    private $gender;

    /**
     * @Assert\NotBlank(message="Marital status is required.")
     * @Assert\Choice(callback="getUtilMaritalStatus", message="Invalid value for marital status.")
     * @ORM\Column(type="string", length=20, unique=false, nullable=false, options={"default":"single"}) 
     */
    private $maritalStatus;

    /**
     * @Assert\NotBlank(message="Your unique mobile number is required.")
     * @Assert\Type(type="numeric", message="Invalid value. Only digits allowed")
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
     * @Assert\NotBlank(message="Next of kin's name is required.")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=70, minMessage="Next of kin's name too short.", maxMessage="Next of kin's name too long.")
     * @ORM\Column(type="string", length=70, unique=false, nullable=false) 
     */
    private $nokName;

    /**
     * @Assert\NotBlank(message="Next of kin's address is required.")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=100, minMessage="Value supplied is too short", maxMessage="Value supplied should not exceed 100 chars")
     * @ORM\Column(type="string", length=100, unique=false, nullable=false) 
     */
    private $nokAddr;

    /**
     * @Assert\Type(type="numeric", message="Invalid value. Only digits allowed.")
     * @Assert\Length(min=11, max=11, exactMessage="Mobile number must be composed of only 11 digits.")
     * @ORM\Column(type="string", length=11, unique=false, nullable=true) 
     */
    private $nokNo;

    /**
     * @Assert\NotBlank(message="Passport is required.")
     * @ORM\Column(type="string", length=30, unique=true, nullable=false) 
     */
    private $passport;

    /**
     * @Assert\NotBlank(message="Local government ward is required.")
     * @ORM\ManyToOne(targetEntity="App\Entity\Ward", inversedBy="candidates")
     * @ORM\JoinColumn(name="ward_id", referencedColumnName="ward_id", nullable = false)
     */
    private $ward;

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setSurname($name) {
        $this->surname = ucfirst(strtolower($name));
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($name) {
        $this->firstName = ucfirst(strtolower($name));
    }

    public function getOtherNames() {
        return $this->otherNames;
    }

    public function setOtherNames($name) {
        $this->otherNames = ucwords(strtolower($name));
    }

    public function getHomeAddr() {
        return $this->homeAddr;
    }

    public function setHomeAddr($addr) {
        $this->homeAddr = $addr;
    }

    public function getDob() {
        return $this->dob;
    }

    public function setDob($dob) {
        $this->dob = $dob;
    }

    public function getGender() {
        return $this->gender;
    }

    public function setGender($gender) {
        $g = trim($gender);
        if (!in_array($g, CandidatePersonal::getUtilGender())) {
            throw new \InvalidArgumentException("Invalid gender.");
        }
        $this->gender = $g;
    }

    public function getMaritalStatus() {
        return $this->maritalStatus;
    }

    public function setMaritalStatus($mstatus) {
        $status = trim($mstatus);
        if (!in_array($status, CandidatePersonal::getUtilMaritalStatus())) {
            throw new \InvalidArgumentException("Invalid marital status");
        }
        $this->maritalStatus = $status;
    }

    public function getMobileNo() {
        return $this->mobileNo;
    }

    public function setMobileNo($no) {
        $this->mobileNo = $no;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        $this->passport = $email;
    }

    public function getNokName() {
        return $this->nokName;
    }

    public function setNokName($name) {
        $this->nokName = ucwords(strtolower($name));
    }

    public function getNokAddr() {
        return $this->nokAddr;
    }

    public function setNokAddr($addr) {
        $this->nokAddr = $addr;
    }

    public function getNokNo() {
        return $this->nokNo;
    }

    public function setNokNo($no) {
        $this->nokNo = $no;
    }

    public function getPassport() {
        return $this->passport;
    }

    public function setPassport($pass) {
        $this->passport = $pass;
    }

    public function getWard() {
        return $this->ward;
    }

    public function setWard($ward) {
        $this->ward = $ward;
    }

    public function getFullName() {
        $fullname = "";

        $fullname .= $this->getSurname();
        $fullname .= " " . $this->getFirstName();
        $fullname .= " " . $this->getOtherNames();

        return $fullname;
    }

}
