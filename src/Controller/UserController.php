<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller {

    use \App\Utility\StepUtils;
    use \App\Utility\Utils;

    /**
     * @Route("/apply/form_1", name="form_1")
     */
    public function form_1(Request $request, \Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        if ($session->getApplicationSessionStatus() == "closed") {
            return $this->render('default/closed.html.twig', array('page' => 'scholarship', 'session' => $session));
        }
        if ($session->getApplicationSessionStatus() == "not-ready") {
            return $this->render('default/notready.html.twig', array('page' => 'scholarship', 'session' => $session));
        }

        $r = $this->ensureStep("form_1");
        if ($r) {
            return $r;
        }

        $arr_data = array('page' => 'scholarship', 'step' => 'candidate', 'session' => $session);
        $user = new User();
        $plainPassword = "pyramid"; //substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        $encoded = $encoder->encodePassword($user, $plainPassword);

        $user->setPassword($encoded);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        //check if account already exists
        $rep = $this->getDoctrine()->getRepository(User::class);
        if ($rep->findBy(array("email" => $user->getEmail(), "bvn" => $user->getBvn(), "mobileNo" => $user->getMobileNo()))) {
            $arr_data['exists'] = true;
            $arr_data['user'] = $user;
            return $this->render('apply/form_1.html.twig', $arr_data);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getEmail() != $request->request->get("user")['email2']) {
                $form->get("email2")->addError(new \Symfony\Component\Form\FormError("Emails must match."));
                $form->addError(new \Symfony\Component\Form\FormError("Fix this error(s)!"));
            } else {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $message = (new \Swift_Message('Account Details (KSSB ' . $session->getScholarshipSession() . '/' . ($session->getScholarshipSession() + 1) . ')'))
                        ->setFrom($session->getEmail())
                        ->setTo($user->getEmail())
                        ->setBody(
                        $this->renderView(
                                // templates/emails/registration.html.twig
                                'emails/accountdetails.html.twig', array('username' => $user->getUsername(), 'password' => $plainPassword, 'session' => $session)
                        ), 'text/html'
                );
                $mailer->send($message);
                $arr_data['user'] = $user;

                return $this->render('apply/form_1.html.twig', $arr_data);
            }
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $form->addError(new \Symfony\Component\Form\FormError("Fix these error(s)!"));
        }

        $arr_data['form'] = $form->createView();
        return $this->render('apply/form_1.html.twig', $arr_data);
    }

    /**
     * @Route("/apply/rc/{maddr}", name="resendcredentials")
     */
    public function resendCred(\Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder, $maddr) {
        //echo "here"; exit();
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        if ($session->getApplicationSessionStatus() == "closed") {
            return $this->render('default/closed.html.twig', array('page' => 'scholarship', 'session' => $session));
        }
        if ($session->getApplicationSessionStatus() == "not-ready") {
            return $this->render('default/notready.html.twig', array('page' => 'scholarship', 'session' => $session));
        }

        $arr_data = array('page' => 'scholarship', 'step' => 'candidate', 'session' => $session);
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $user = $rep->findByEmail($maddr);
        $user= $user[0];
        $arr_data['user']= $user;
        $plainPassword =  "pyramid"; //substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        $encoded = $encoder->encodePassword($user, $plainPassword);

        $user->setPassword($encoded);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        
        $arr_data['resend']= true;
        
        $message = (new \Swift_Message('Account Details (KSSB ' . $session->getScholarshipSession() . '/' . ($session->getScholarshipSession() + 1) . ')'))
                ->setFrom($session->getEmail())
                ->setTo($user->getEmail())
                ->setBody(
                $this->renderView(
                        // templates/emails/registration.html.twig
                        'emails/accountdetails.html.twig', array('username' => $user->getUsername(), 'password' => $plainPassword, 'session' => $session)
                ), 'text/html'
        );
        $mailer->send($message);
        //var_dump($arr_data); exit();
        return $this->render('apply/form_1.html.twig', $arr_data);
    }

    /**
     * @Route("/apply/choice", name="choice")
     */
    public function choice(Request $request) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        return $this->render('apply/form_0.html.twig', array('page' => 'scholarship', 'session' => $session));
    }

}
