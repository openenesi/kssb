<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LgaRepository", readOnly=true)
 */
class Lga {
    
    public function __construct() {
        $this->wards = new ArrayCollection();
    }
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="lga_id", type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=30, unique=false, nullable=false) 
     */
    private $lgaName;

    /**
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Ward", mappedBy="lga")
     */
    private $wards;

    public function getId() {
        return $this->id;
    }

    public function getLgaName() {
        return $this->lgaName;
    }

    public function setLgaName($name) {
        $this->lgaName = $name;
    }

    public function getWards() {
        return $this->wards;
    }

    public function setWards($ward) {
        if (!$this->wards->contains($ward)) {
            $this->wards->add($ward);
            $ward->setLga($this);
        }
    }

}
