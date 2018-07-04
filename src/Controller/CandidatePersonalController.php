<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CandidatePersonal;
use App\Form\CandidatePersonalType;

class CandidatePersonalController extends Controller {

    use \App\Utility\StepUtils;
    use \App\Utility\Utils;

    /**
     * @Route("/apply/form_3", name="form_3")
     */
    public function form_3(Request $request) {
        $session = new \App\Entity\ScholarshipSession(); //$this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        if ($session->getApplicationSessionStatus() == "closed") {
            return $this->render('default/closed.html.twig', array('page' => 'scholarship', 'session' => $session));
        }
        if ($session->getApplicationSessionStatus() == "not-ready") {
            return $this->render('default/notready.html.twig', array('page' => 'scholarship', 'session' => $session));
        }

        $r = $this->ensureStep("form_3");
        if ($r) {
            return $r;
        }
        $user = $this->getUser();
        if ($user->getCandidatePersonal()) {
            $candidatepersonal = $user->getCandidatePersonal();
            $candidatepersonal->minDateOfBirth = (new \DateTime())->sub(new \DateInterval("P15Y"));
        } else {
            $candidatepersonal = new CandidatePersonal();
        }
        // var_dump($request->request->get("candidate_personal")['lga']);exit();
        $arrOpt = array("candidate" => $user);
        if ($request->request->get("candidate_personal")['lga']) {
            $lgarep = $this->getDoctrine()->getRepository(\App\Entity\Lga::class);
            $lga = $lgarep->find($request->request->get("candidate_personal")['lga']);
            $arrOpt['lga'] = $lga;
        } elseif ($candidatepersonal->getWard()) {
            $arrOpt['lga'] = $candidatepersonal->getWard()->getLga();
            //var_dump($candidatepersonal->getWard()->getLga()); exit();
        }
        
        $arr_data = array('page' => 'scholarship', 'step' => 'personal', 'candidate' => $user, 'session' => $session);
        
        $form = $this->createForm(CandidatePersonalType::class, $candidatepersonal, $arrOpt);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($candidatepersonal);
            $user->setCandidatePersonal($candidatepersonal);
            $em->persist($user);
            $em->flush();
            //if ($form->get("save")->isClicked()) {
            return $this->redirectToRoute("form_4");
            //}else{                
            //  return $this->render('apply/form_3.html.twig', array('page' => 'scholarship', 'step' => 'personal', 'save'=>'saved', 'form' => $form->createView()));
            //}
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $form->addError(new \Symfony\Component\Form\FormError("Fix this error(s)!"));
            if(!$user->getCandidatePersonal() || $user->getCandidatePersonal()->getPassport()== null){
                $arr_data['passporterror']= true;
            }
        }

        $arr_data['form']= $form->createView();

        return $this->render('apply/form_3.html.twig', $arr_data);
    }

    /**
     * @Route("/apply/pu", name="handlepassport")
     */
    public function handlePassport(Request $request) {

        $imagedefault = "default.jpeg";

        $session = new \App\Entity\ScholarshipSession(); //$this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));

        $user = $this->getUser();

        $arr_data = array('candidate' => $user, 'session' => $session);

        $passport = new \App\Utility\Passport();

        $form = $this->getImageForm($passport);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $passport->getPassport();
            $filename = "user".$user->getId() . "." . $file->guessExtension();
            // Move the file to the directory where brochures are$file->move(
            $file->move($this->getParameter('passport_directory'), $filename);
            if ($user->getCandidatePersonal()) {
                $candidatepersonal = $user->getCandidatePersonal();
                $candidatepersonal->setPassport($filename);
                $em = $this->getDoctrine()->getManager();
                $em->persist($candidatepersonal);
                $em->flush();
            }
            $arr_data['passport'] = $filename;
        }
        $arr_data['form'] = $form->createView();
        return $this->render('apply/passport.html.twig', $arr_data);
    }

    private function getImageForm($passport) {
        $form = $this->createFormBuilder($passport /* , array('attr' => array('target' => 'imageview')) */)
                /* ->setAction($this->generateUrl('handlepassport')) */
                ->add('passport', \Symfony\Component\Form\Extension\Core\Type\FileType::class, array('label' => 'Max 100kb JPEG file',
                    "attr" => array("class" => "custom-file-input"), "label_attr" => array("class" => "custom-file-label", "style" => "font-size:.8em;")))
                ->add('upload', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Upload'))
                ->getForm();
        return $form;
    }

}
