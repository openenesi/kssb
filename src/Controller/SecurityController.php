<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller {

    use \App\Utility\Utils;

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils) {
        $session = new \App\Entity\ScholarshipSession(); //$this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if (!$this->getUser()) {
            //echo "here"; exit();
            $arr_data = array(
                'page' => 'login',
                'last_username' => $lastUsername,
                'session' => $session,
            );
            if (!empty($error)) {
                //var_dump($error->getMessage()); exit();
                $arr_data['loginerror'] = "Invalid username or password!";
            }
            return $this->render('security/login.html.twig', $arr_data);
        }
        return $this->redirectToRoute("form_1");
    }
    /**
     * @Route("/kssbadmin/adminlogin", name="adminlogin")
     */
    public function adminLogin(Request $request, AuthenticationUtils $authenticationUtils) {
        $session = new \App\Entity\ScholarshipSession(); //$this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if (!$this->getUser()) {
            //echo "here"; exit();
            $arr_data = array(
                'page' => 'login',
                'last_username' => $lastUsername,
                'session' => $session,
            );
            if (!empty($error)) {
                //var_dump($error->getMessage()); exit();
                $arr_data['loginerror'] = "Invalid username or password!";
            }
            return $this->render('admin/adminlogin.html.twig', $arr_data);
        }
        return $this->redirectToRoute("admin");
    }

    /**
     * @Route("/logincheck", name="logincheck")
     */
    public function loginCheck(Request $request, AuthenticationUtils $authenticationUtils) {
        //echo "here"; exit;
        //$this->forward("\App\Controller\SecurityController::login");
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if (!$this->getUser()) {
            //echo "here"; exit();
            $arr_data = array(
                'page' => 'scholarship',
                'last_username' => $lastUsername,
            );
            if (!empty($error)) {
                //var_dump($error);
                $arr_data['loginerror'] = $error->getMessage();
            } return $this->render('security/login.html.twig', $arr_data);
        }
        return $this->redirectToRoute("choice");
    }

    /**
     * @Route("/kssbadmin/adminlogincheck", name="adminlogincheck")
     */
    public function adminLoginCheck(Request $request, AuthenticationUtils $authenticationUtils) {
        //echo "here"; exit;
        //$this->forward("\App\Controller\SecurityController::login");
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if (!$this->getUser()) {
            //echo "here"; exit();
            $arr_data = array(
                'page' => 'scholarship',
                'last_username' => $lastUsername,
            );
            if (!empty($error)) {
                //var_dump($error);
                $arr_data['loginerror'] = $error->getMessage();
            } return $this->render('admin/adminlogin.html.twig', $arr_data);
        }
        return $this->redirectToRoute("admin");
    }

    /**
     * @Route("/login_form/{last_username}/{loginerror}", name="login_form")
     */
    public function login_form(Request $request, $last_username = null, $loginerror = null) {
        if ($loginerror != null) {
            $response = $this->render("security/login_form.html.twig", array("last_username" => $last_username, "loginerror" => $loginerror));
        } elseif ($last_username != null) {
            $response = $this->render("security/login_form.html.twig", array("last_username" => $last_username));
        } else {
            $response = $this->render("security/login_form.html.twig");
        }
        $response->setSharedMaxAge(0);
        return $response;
    }

    /**
     * @Route("/kssbadmin/login_form/{last_username}/{loginerror}", name="admin_login_form")
     */
    public function admin_login_form(Request $request, $last_username = null, $loginerror = null) {
        if ($loginerror != null) {
            $response = $this->render("admin/admin_login_form.html.twig", array("last_username" => $last_username, "loginerror" => $loginerror));
        } elseif ($last_username != null) {
            $response = $this->render("admin/admin_login_form.html.twig", array("last_username" => $last_username));
        } else {
            $response = $this->render("admin/admin_login_form.html.twig");
        }
        $response->setSharedMaxAge(0);
        return $response;
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request) {
        
    }
    /**
     * @Route("/kssbadmin/logout", name="adminlogout")
     */
    public function adminLogout(Request $request) {
        
    }

}
