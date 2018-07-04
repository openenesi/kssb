<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountCounterRepository")
 */
class AccountCounter {

    public function __construct() {
        
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="account_counter_id", type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="integer", nullable=false) 
     */
    private $accounts;

    /**
     * @Assert\DateTime(message="Invalid value for date Initial.")
     * @ORM\Column(type="datetime", unique=false, nullable=false) 
     */
    private $dateInitial;

    /**
     * @Assert\DateTime(message="Invalid value for date Last.")
     * @ORM\Column(type="datetime", unique=false, nullable=false) 
     */
    private $dateLast;

    public function getId() {
        return $this->id;
    }

    public function getAccounts() {
        return $this->accounts;
    }
    public function addAccount() {
        $this->accounts= $this->accounts + 1;
    }

    public function setAccounts($v) {
        $this->accounts = $v;
    }

    public function getDateInitial() {
        return $this->dateInitial;
    }

    public function setDateInitial($dt) {
        if (!$dt instanceof \DateTime) {
            $dt = new \DateTime($dt);
        }
        $this->dateInitial = $dt;
    }

    public function getDateLast() {
        return $this->dateLast;
    }

    public function setDateLast($dt) {
        if (!$dt instanceof \DateTime) {
            $dt = new \DateTime($dt);
        }
        $this->dateLast = $dt;
    }

    public function resetCounter() {
        $this->accounts = 1;
        $this->dateInitial = new \DateTime();
        $this->dateLast = $this->dateInitial;
    }

    public function incrementCounter() {
        if ($this->getDiffMinutes() > 60) {
            $this->resetCounter();
        } else {
            $this->accounts = ($this->accounts + 1);
            $this->dateLast = new \DateTime();
        }
    }

    public function thresholdExceeded() {
        $acc = $this->getAccounts();
        if($acc >50){
        $diffminutes = $this->getDiffMinutes();
        //var_dump($diffminutes); exit();
        if ($diffminutes < 61) {
                return true;
            }
        }
        return false;
    }

    public function getDiffMinutes() {
        $diff = $this->dateInitial->diff(new \DateTime());
        $hours = abs($diff->format('%h'));
        $minutes = abs($diff->format('%i'));
        return ($hours * 60 + $minutes);
    }

}
