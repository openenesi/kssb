<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller {

    /**
     * @Route("/kssbadmin", name="admin")
     */
    public function index(Request $request) {
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $userstat = array();
        $userstat['registered'] = $rep->countAllApplicants();
        $userstat['paid'] = $rep->countAllApplicants('paid');
        $userstat['completed'] = $rep->countAllApplicants('completed');

        return $this->render('admin/index.html.twig', [
                    'page' => 'index',
                    'contentTitle' => 'Dashboard',
                    'titleIcon' => '<i class="fa fa-dashboard fa-fw"></i>',
                    'controller_name' => 'AdminController',
                    'userstat' => $userstat
        ]);
    }

    /**
     * @Route("/kssbadmin/countregistered", name="countregisteredajax")
     */
    public function getCountRegistered(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
            $registered = $rep->countAllApplicants();
            return new Response($registered);
        }
    }
    /**
     * @Route("/kssbadmin/countpaid", name="countpaidajax")
     */
    public function getCountPaid(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
            $paid = $rep->countAllApplicants('paid');
            return new Response($paid);
        }
    }
    /**
     * @Route("/kssbadmin/countcompleted", name="countcompletedajax")
     */
    public function getCountCompleted(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
            $completed = $rep->countAllApplicants('completed');
            return new Response($completed);
        }
    }

}
