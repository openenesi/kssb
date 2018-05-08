<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\CandidateBank;
use App\Form\CandidateBankType;
use Symfony\Component\HttpFoundation\Request;

class CandidateBankController extends Controller {

    use \App\Utility\StepUtils;
    use \App\Utility\Utils;

    /**
     * @Route("/apply/form_5", name="form_5")
     */
    public function form_5(Request $request) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        if ($session->getApplicationSessionStatus() == "closed") {
            return $this->render('default/closed.html.twig', array('page' => 'scholarship', 'session' => $session));
        }
        if ($session->getApplicationSessionStatus() == "not-ready") {
            return $this->render('default/notready.html.twig', array('page' => 'scholarship', 'session' => $session));
        }

        $r = $this->ensureStep("form_5");
        if ($r) {
            return $r;
        }
        $user = $this->getUser();
        if ($user->getCandidateBank()) {
            $candidatebank = $user->getCandidateBank();
            $candidatebank->dt = new \DateTime();
        } else {
            $candidatebank = new CandidateBank();
        }

        $arrOpt = array("candidate" => $user);

        $form = $this->createForm(CandidateBankType::class, $candidatebank, $arrOpt);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($candidatebank->getAccountNo() != $request->request->get("candidate_bank")['accountNo2']) {
                $form->get("accountNo2")->addError(new \Symfony\Component\Form\FormError("Account No.s must match."));
            } else {
                $em = $this->getDoctrine()->getManager();
                $em->persist($candidatebank);
                $user->setCandidateBank($candidatebank);
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute("form_6");
            }
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $form->addError(new \Symfony\Component\Form\FormError("Fix this error(s)!"));
        }
        $arrdata = array('page' => 'scholarship', 'step' => 'bank', 'form' => $form->createView(), 'candidate' => $user, 'session' => $session);

        return $this->render('apply/form_5.html.twig', $arrdata);
    }

}
