<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utility;

/**
 * Description of StepUtils
 *
 * @author enesi
 */
trait StepUtils {

    //put your code here
    public function ensureStep($step) {

        if ($this->getUser()) {
            $user = $this->getUser();
            
            if($user->getAppId()){
                
                return ($step!="form_6")?($this->redirectToRoute("form_6")):(null);
            }
            
            if ($step == "form_2" || !$user->getPaid()) { 
                return ($step!="form_2")?($this->redirectToRoute("form_2")):(null);
            } else if ($step == "form_3" || !$user->getCandidatePersonal()) { 
                return ($step!="form_3")?($this->redirectToRoute("form_3")):(null);
            } else if ($step == "form_4" || !$user->getCandidateInstitution()) { 
                return ($step!="form_4")?($this->redirectToRoute("form_4")):(null);
            } else if ($step == "form_5" || !$user->getCandidateBank()) {
                return ($step!="form_5")?($this->redirectToRoute("form_5")):(null);
            } elseif ($step == "form_6" || !$user->getAppId()) {
                return ($step!="form_6")?($this->redirectToRoute("form_6")):(null);
            }
            
        } else {
            
            if ($step != "form_1") {
                return $this->redirectToRoute("form_1");
            }
        }
    }

}
