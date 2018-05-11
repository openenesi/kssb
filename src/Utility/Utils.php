<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utility;

/**
 * Description of UtilityConstants
 *
 * @author enesi
 */
trait Utils {

    //put your code here
    public static function getUtilGender() {
        return array("Male" => "male", "Female" => "female");
    }

    public static function getUtilTitles() {
        return array("Mr.", "Mrs.", "Mallam", "Mallama", "Hajiya", "Alhaji", "Miss");
    }

    public static function getUtilBankAccTypes() {
        return array("Savings" => "savings", "Current" => "current");
    }

    public static function getUtilMaritalStatus() {
        return array("Single" => "single", "Married" => "married");
    }

    public static function getUtilAccommodationTypes() {
        return array("Off-Campus" => "off-campus", "Hostel" => "hostel");
    }

    public static function getUtilInstitutionCategories() {
        return array("University" => "university", "Polytechnic" => "polytechnic", "Specialized Institution" => "specialized_institution", "College Of Education" => "college_of_education", "College Of Agriculture" => "college_of_agriculture", "College of Health" => "college_of_health");
    }

    public static function getUtilInstitutionOwners() {
        return array("Private" => "private", "Federal" => "federal", "State" => "state");
    }

    private function generateAppId($appid, $session=null) {
        $id = "SIP/".$session->getScholarshipSession();
        if ($appid < 10) {
            $id .= "0000";
        } elseif ($appid < 100) {
            $id .= "000";
        } elseif ($appid < 1000) {
            $id .= "00";
        } elseif ($appid < 10000) {
            $id .= "0";
        }
        $id .= $appid;
        return $id;
    }
    
    private function getScholarshipSession($er){
        return $er->findSessionConfig();
    }

}
