<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WardRepository", readOnly=true)
 */
class Ward {
    
    public function __construct() {
        $this->candidates = new ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="ward_id", type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=30, unique=false, nullable=false) 
     */
    private $wardName;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CandidatePersonal", mappedBy="ward")
     */
    private $candidates;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Lga", inversedBy="wards")
     * @ORM\JoinColumn(name="lga_id", referencedColumnName="lga_id")
     */
    private $lga;

    public function getId() {
        return $this->id;
    }

    public function getWardName() {
        return $this->wardName;
    }

    public function setWardName($name) {
        $this->wardName = $name;
    }

    public function getCandidates() {
        return $this->candidates;
    }

    public function setCandidates($cand) {
        if (!$this->candidates->contains($cand)) {
            $this->candidates->add($cand);
            $cand->setWard($this);
        }
    }
    
    public function getLga(){
        return $this->lga;
    }
    public function setLga($lga){
        $this->lga=$lga;
    }

}
