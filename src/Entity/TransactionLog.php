<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionLogRepository")
 */
class TransactionLog {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, options={"unsigned":true})
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $currency;

    /**
     * @Assert\DateTime()
     * @ORM\Column(type="datetime") 
     */
    private $trxnDate;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $gatewayResponse;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $domain;

    /**
     * @Assert\Choice({"bank","card"})
     * @ORM\Column(type="string", length=4)
     */
    private $channel;

    /**
     * @Assert\Ip(version="all")
     * @ORM\Column(type="string", length=20)
     */
    private $ipAddress;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $cardType;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $last4;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $bank;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $countryCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $attempts;

    public function getId() {
        return $this->id;
    }

    public function getReference() {
        return $this->reference;
    }

    public function setReference($ref) {
        $this->reference = $ref;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function setAmount($amt) {
        $this->amount = $amt;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    public function getTrxnDate() {
        return $this->trxnDate;
    }

    public function setTrxnDate($trxndt) {
        if (!($trxndt instanceof \DateTime)) {
            $trxndt = new \DateTime($trxndt);
        }
        $this->trxnDate = $trxndt;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getGatewayResponse() {
        return $this->gatewayResponse;
    }

    public function setGatewayResponse($response) {
        $this->gatewayResponse = $response;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($msg) {
        $this->message = $msg;
    }

    public function getDomain() {
        return $this->domain;
    }

    public function setDomain($domain) {
        $this->domain = $domain;
    }

    public function getChannel() {
        return $this->channel;
    }

    public function setChannel($channel) {
        $this->channel = $channel;
    }

    public function getIpAddress() {
        return $this->ipAddress;
    }

    public function setIpAddress($addr) {
        $this->ipAddress = $addr;
    }

    public function getCardType() {
        return $this->cardType;
    }

    public function setCardType($ctype) {
        $this->cardType = $ctype;
    }

    public function getLast4() {
        return $this->last4;
    }

    public function setLast4($last) {
        $this->last4 = $last;
    }

    public function getBank() {
        return $this->bank;
    }

    public function setBank($bank) {
        $this->bank = $bank;
    }

    public function getCountryCode() {
        return $this->countryCode;
    }

    public function setCountryCode($code) {
        $this->countryCode = $code;
    }

    public function getAttempts() {
        return $this->attempts;
    }

    public function setAttempts($attempts) {
        $this->attempts = $attempts;
    }

}
