<?php

namespace App\Utility;

use Symfony\Component\Validator\Constraints as Assert;

class Passport {

    /**
     * @Assert\NotBlank(message="Passport is required.")
     * @Assert\Image(mimeTypes={"image/jpeg"}, mimeTypesMessage="Only JPEG image is allowed", maxSize="100k", maxSizeMessage="Image too large. Max size of 100k allowed.")
     */
    private $passport;

    public function getPassport() {
        return $this->passport;
    }

    public function setPassport($pass) {
        $this->passport = $pass;
    }

}
