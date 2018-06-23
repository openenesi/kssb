<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidateInstitutionRepository")
 * @UniqueEntity(fields={"matricNo", "institution"}, message="Matric/Registration number must be unique per institution")
 */
class CandidateInstitution {

    use \App\Utility\Utils;
    
    public function __construct() {
        $this->dt = new \DateTime();
    }
    
    public $dt;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="candidateinstitution_id", type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Matric/Registration number is required")
     * @Assert\Type(type="string", message="Invalid type. required string")
     * @Assert\Length(min=3, max=50, minMessage="Matric/Registration number should not be less than 3 characters", maxMessage="Matric/Registration number should not be more than 50 characters")
     * @ORM\Column(type="string", length=50, unique=false, nullable=false) 
     */
    private $matricNo;

    /**
     * @Assert\NotBlank(message="Address of institution is required")
     * @Assert\Type(type="string", message="Invalid type. Required string")
     * @Assert\Length(min=3, max=60, minMessage="Institution address should not be less than 3 characters", maxMessage="Institution address should not be more than 60 characters")
     * @ORM\Column(type="string", length=60, unique=false, nullable=false) 
     */
    private $institutionAddr;
    
    /**
     * @Assert\NotBlank(message="Date of admission is required.")
     * @Assert\Date(message="Valid admission date is required")
     * @Assert\LessThanOrEqual(propertyPath="dt", message="Invalid date of admission")
     * @ORM\Column(type="date", unique=false, nullable=false) 
     */
    private $admissionDate;
    
    /**
     * @Assert\NotBlank(message="Year of graduation is required")
     * @Assert\Type(type="numeric", message="Invalid expected year of graduation")
     * @ORM\Column(type="integer", unique=false, nullable=false) 
     */
    private $graduationYear;
    
    /**
     * @Assert\NotBlank(message="Institution is required.")
     * @ORM\ManyToOne(targetEntity="App\Entity\Institution", inversedBy="candidates")
     * @ORM\JoinColumn(name="institution_id", referencedColumnName="institution_id", nullable=false)
     */
    private $institution;

    /**
     * @Assert\NotBlank(message="Faculty/School is required")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=60, minMessage="Value supplied is too short.", maxMessage="Value supplied is too long. Maximum of 60 chars allowed.")
     * @ORM\Column(type="string", length=60, unique=false, nullable=false) 
     */
    private $faculty;
    
    /**
     * @Assert\NotBlank(message="Department is required")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=60, minMessage="Value supplied is too short.", maxMessage="Value supplied is too long. Maximum of 60 chars allowed.")
     * @ORM\Column(type="string", length=60, unique=false, nullable=false) 
     */
    private $department;
    
    /**
     * @Assert\NotBlank(message="Course of study is required")
     * @Assert\Type(type="string", message="Invalid type. Required string.")
     * @Assert\Length(min=1, max=60, minMessage="Value supplied is too short.", maxMessage="Value supplied is too long. Maximum of 60 chars allowed.")
     * @ORM\Column(type="string", length=60, unique=false, nullable=false) 
     */
    private $courseOfStudy;
    
    /**
     * @Assert\NotBlank(message="Your current level is required")
     * @Assert\Length(min=1, max=25, minMessage="Value supplied is too short.", maxMessage="Value supplied is too long. Maximum of 25 chars allowed.")
     * @ORM\Column(type="string", length=25, unique=false, nullable=false) 
     */
    private $level;
    
    /**
     * @Assert\NotBlank(message="Course duration is required")
     * @Assert\Type(type="integer", message="Invalid type. Required number.")
     * @Assert\GreaterThan(value=0, message="Course duration cannot be less than 1 year")
     * @Assert\LessThanOrEqual(value=7, message="Course duration cannot be greater than 7 years")
     * @ORM\Column(type="integer", unique=false, nullable=false) 
     */
    private $courseDuration;
    
    /**
     * @Assert\NotBlank(message="Accommodation is required.")
     * @Assert\Choice(callback="getUtilAccommodationTypes", message="Invalid choice for accommodation.")
     * @ORM\Column(type="string", length=20, unique=false, nullable=false) 
     */
    private $accommodationType;
    
    public function getId() {
        return $this->id;
    }

    public function getMatricNo() {
        return $this->matricNo;
    }

    public function setMatricNo($matricno) {
        $this->matricNo = $matricno;
    }

    public function getInstitutionAddr() {
        return $this->institutionAddr;
    }

    public function setInstitutionAddr($addr) {
        $this->institutionAddr = $addr;
    }

    public function getAdmissionDate() {
        return $this->admissionDate;
    }

    public function setAdmissionDate($dt) {
        $this->admissionDate = $dt;
    }

    public function getGraduationYear() {
        return $this->graduationYear;
    }

    public function setGraduationYear($yr) {
        $this->graduationYear = $yr;
    }

    public function getFaculty() {
        return $this->faculty;
    }

    public function setFaculty($fac) {
        $this->faculty = $fac;
    }

    public function getCourseOfStudy() {
        return $this->courseOfStudy;
    }

    public function setCourseOfStudy($cos) {
        $this->courseOfStudy = $cos;
    }

    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
    }

    public function getCourseDuration() {
        return $this->courseDuration;
    }

    public function setCourseDuration($dur) {
        $this->courseDuration = $dur;
    }

    public function getAccommodationType() {
        return $this->accommodationType;
    }

    public function setAccommodationType($accomm) {
        $accom = trim($accomm);
        if (!in_array($accom, CandidateInstitution::getUtilAccommodationTypes())) {
            throw new \InvalidArgumentException("Invalid accommodation type");
        }
        $this->accommodationType = $accom;
    }

    public function getDepartment() {
        return $this->department;
    }

    public function setDepartment($dept) {
        $this->department = $dept;
    }
    
    public function getInstitution(){
        return $this->institution;
    }
    public function setInstitution($inst){
        $this->institution = $inst;
    }

}