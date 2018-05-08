<?php

namespace App\Utility;

use Symfony\Component\Validator\Constraints as Assert;

class ContactUs {

    /**
     * @Assert\NotBlank(message="Your full name is required.")
     * @Assert\Type(type="string")
     */
    private $fullName;

    /**
     * @Assert\NotBlank(message="Your email is required.")
     * @Assert\Email(message="Valid email is required.")
     */
    private $email;

    /**
     * @Assert\Type(type="string")
     */
    private $subject;

    /**
     * @Assert\NotBlank(message="Your email is required.")
     * @Assert\Type(type="string")
     */
    private $message;

    // add your own fields

    public function getFullName() {
        return $this->fullName;
    }

    public function setFullName($name) {
        $this->fullName = $name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($msg) {
        $this->message = $msg;
    }

}
