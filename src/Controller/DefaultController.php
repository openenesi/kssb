<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    use \App\Utility\Utils;
    use \App\Utility\StepUtils;

    /**
     * @Route("/", name="home")
     */
    public function index() {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        return $this->render('default/index.html.twig', ['page' => 'home',
                    'controller_name' => 'DefaultController', 'session' => $session
        ]);
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faq() {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        return $this->render('default/faq.html.twig', array('page' => 'faq', 'session' => $session));
    }

    /**
     * @Route("/scholarship", name="scholarship")
     */
    public function scholarship() {
        /*$rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $users = $rep->findAll();
        if(count($users) > 99){
        return $this->redirectToRoute("bursary");
        }*/
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        return $this->render('default/scholarship.html.twig', array('page' => 'scholarship', 'session' => $session));
    }

    /**
     * @Route("/bursary", name="bursary")
     */
    public function bursary() {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        return $this->render('default/bursary.html.twig', array('page' => 'bursary', 'session' => $session));
    }

    /**
     * @Route("/aboutus", name="aboutus")
     */
    public function aboutUs(Request $request) {
        return $this->render('default/about.html.twig', array("page" => "aboutus"));
    }
    
    /**
     * @Route("/gallery", name="gallery")
     */
    public function gallery(Request $request) {
        return $this->render('default/gallery.html.twig', array("page" => "gallery"));
    }
    
    /**
     * @Route("/board", name="board")
     */
    public function board(Request $request) {
        return $this->render('default/board.html.twig', array("page" => "board"));
    }

    /**
     * @Route("/contactus", name="contactus")
     */
    public function contactUs(Request $request, \Swift_Mailer $mailer) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        $cs = new \App\Utility\ContactUs();
        $form = $this->getForm($cs);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = (new \Swift_Message((($cs->getSubject()) ?: ("No Subject")) . " (" . $cs->getFullName() . ")"))
                    ->setFrom($session->getEmail())
                    ->setTo($session->getEmail())
                    ->setBody($cs->getMessage() . "\n Sent from: " . $cs->getEmail());
            $mailer->send($message);
            $this->addFlash("msg_sent", "Your message has been sent!");
            $form = $this->getForm(new \App\Utility\ContactUs());
        }

        return $this->render('default/contactus.html.twig', array('page' => 'contactus', 'session' => $session, 'form' => $form->createView()));
    }

    private function getForm($model) {
        $form = $this->createFormBuilder($model)
                ->setAction($this->generateUrl('contactus'))
                ->add('fullName', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array("label" => "Full Name:", "attr" => array("placeholder" => "Your full name")))
                ->add('email', \Symfony\Component\Form\Extension\Core\Type\EmailType::class, array("label" => "Email:", "attr" => array("placeholder" => "a@b.c")))
                ->add('subject', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array("required" => false, "label" => "Subject:", "attr" => array("placeholder" => "subject")))
                ->add('message', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array("label" => "Message:", "attr" => array("placeholder" => "Message")))
                ->add('send', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Send'))
                ->getForm();
        return $form;
    }

    /**
     * @Route("/apply/form_6/{testified}", name="form_6")
     */
    public function form_6(Request $request, \Swift_Mailer $mailer, $testified = null) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        //var_dump($session); exit();

        $r = $this->ensureStep("form_6");
        if ($r) {
            return $r;
        }

        $user = $this->getUser();
        if ($user->getAppId()) {
            return $this->render('apply/form_6.html.twig', array('page' => 'scholarship', 'step' => 'finish', 'testified' => true, 'appId' => $this->generateAppId($user->getAppId(), $session), 'candidate' => $user, 'session' => $session));
        }

        if ($session->getApplicationSessionStatus() == "closed") {
            return $this->render('default/closed.html.twig', array('page' => 'scholarship', 'session' => $session));
        }
        if ($session->getApplicationSessionStatus() == "not-ready") {
            return $this->render('default/notready.html.twig', array('page' => 'scholarship', 'session' => $session));
        }

        if (isset($testified) && $testified = "testified") {
            if ($user->getPaid() && $user->getCandidatePersonal() && $user->getCandidateBank() && $user->getCandidateInstitution() && !$user->getAppId()) {
                $userRep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
                $lastAppId = $userRep->getLastAppId();
                $lastAppId++;
                $user->setAppId($lastAppId);
                $user->setDateCompleted(new \DateTime());
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $message = (new \Swift_Message('Application Complete (KSSB ' . $session->getScholarshipSession() . '/' . ($session->getScholarshipSession() + 1) . ')'))
                            ->setFrom($session->getEmail())
                            ->setTo($user->getEmail())
                            ->setBody(
                            $this->renderView(
                                    // templates/emails/registration.html.twig
                                    'emails/applicationcomplete.html.twig', array('appId' => $this->generateAppId($user->getAppId(), $session), 'session' => $session)
                            ), 'text/html'
                    );
                    $mailer->send($message);

                    return $this->render('apply/form_6.html.twig', array('page' => 'scholarship', 'step' => 'finish', 'testified' => true, 'appId' => $this->generateAppId($user->getAppId(), $session), 'candidate' => $user, 'session' => $session));
                } catch (Exception $e) {
                    return $this->render('apply/form_6.html.twig', array('page' => 'scholarship', 'step' => 'finish', 'testified' => false, 'attempted' => true, 'candidate' => $user, 'session' => $session));
                }
            } else {
                return $this->render('apply/form_6.html.twig', array('page' => 'scholarship', 'step' => 'finish', 'testified' => false, 'attempted' => true, 'candidate' => $user, 'session' => $session));
            }
        }
        return $this->render('apply/form_6.html.twig', array('page' => 'scholarship', 'step' => 'finish', 'testified' => false, 'attempted' => false, 'candidate' => $user, 'session' => $session));
    }

    /**
     * @Route("/apply/appform", name="appform")
     */
    public function appform(Request $request) {
        $user = $this->getUser();
        if ($user->getAppId() == null) {
            return $this->redirectToRoute('form_6');
        }
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        $appId = $this->generateAppId($user->getAppId(), $session);
        return $this->render('apply/appform.html.twig', array('a' => $user->getAppId(), 'candidate' => $user, 'session' => $session, 'appId' => $appId));
    }

    /**
     * @Route("/apply/tempemail", name="tempemail")
     */
    public function tempEmail(Request $request) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        return $this->render('emails/applicationcomplete.html.twig', array('appId' => 'SIP/201700003', 'session' => $session));
    }

}
