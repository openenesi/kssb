<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InstitutionRepository", readOnly=true)
 */
class Institution {

    use \App\Utility\Utils;

    public function __construct() {
        $this->candidates = new ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="institution_id", type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=70, unique=true, nullable=false) 
     */
    private $institutionName;

    /**
     *
     * @ORM\Column(type="string", length=50, unique=false, nullable=false) 
     */
    private $institutionCategory;

    /**
     *
     * @ORM\Column(type="string", length=20, unique=false, nullable=false) 
     */
    private $institutionOwner;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CandidateInstitution", mappedBy="institution")
     */
    private $candidates;

    public function getId() {
        return $this->id;
    }

    public function getInstitutionName() {
        return $this->institutionName;
    }

    public function setInstitutionName($name) {
        $this->institutionName = $name;
    }

    public function getInstitutionCategory() {
        return $this->institutionCategory;
    }

    public function setInstitutionCategory($category) {
        $cat = trim($category);
        if (!in_array($cat, Institution::getUtilInstitutionCategories())) {
            throw new \InvalidArgumentException("Invalid institution category");
        }

        $this->institutionCategory = $cat;
    }

    public function getInstitutionOwner() {
        return $this->institutionOwner;
    }

    public function setInstitutionOwner($own) {
        $owner = trim($own);
        if (!in_array($owner, Institution::getUtilInstitutionOwners())) {
            throw new \InvalidArgumentException("Invalid institution owner");
        }

        $this->institutionOwner = $owner;
    }

    public function getCandidates() {
        return $this->candidates;
    }

    public function setCandidates($cand) {
        if (!$this->candidates->contains($cand)) {
            $this->candidates->add($cand);
            $cand->setInstitution($this);
        }
    }

}
