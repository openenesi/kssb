<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\CandidateInstitution;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CandidateInstitutionType;

class CandidateInstitutionController extends Controller {

    use \App\Utility\StepUtils;
    use \App\Utility\Utils;

    /**
     * @Route("/apply/form_4", name="form_4")
     */
    public function form_4(Request $request) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        if ($session->getApplicationSessionStatus() == "closed") {            
            return $this->render('default/closed.html.twig', array('page' => 'scholarship', 'session'=>$session));
        }
        if ($session->getApplicationSessionStatus() == "not-ready") {            
            return $this->render('default/notready.html.twig', array('page' => 'scholarship', 'session'=>$session));
        }

        $r = $this->ensureStep("form_4");
        if ($r) {
            return $r;
        }

        $user = $this->getUser();
        if ($user->getCandidateInstitution()) {
            $candidateinstitution = $user->getCandidateInstitution();
            $candidateinstitution->dt = new \DateTime();
        } else {
            $candidateinstitution = new CandidateInstitution();
        }

        $arrOpt = array("candidate" => $user);
        if ($request->request->get("candidate_institution")['institutionCategory']) {
            $arrOpt['category'] = $request->request->get("candidate_institution")['institutionCategory'];
        } elseif ($candidateinstitution->getInstitution()) {
            $arrOpt['category'] = $candidateinstitution->getInstitution()->getInstitutionCategory();
            //var_dump($candidatepersonal->getWard()->getLga()); exit();
        }
        //var_dump($arrOpt['category']); exit();
        $form = $this->createForm(CandidateInstitutionType::class, $candidateinstitution, $arrOpt);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($candidateinstitution);
            $user->setCandidateInstitution($candidateinstitution);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("form_5");
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $form->addError(new \Symfony\Component\Form\FormError("Fix this error(s)!"));
        }

        return $this->render('apply/form_4.html.twig', array('page' => 'scholarship', 'step' => 'institution', 'form' => $form->createView(), 'candidate'=>$user, 'session'=>$session));
    }

}
