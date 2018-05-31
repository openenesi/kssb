<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/kssbadmin", name="admin")
     */
    public function index(Request $request)
    {
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $userstat = array();
        $userstat['registered']= $rep->countAllApplicants();
        $userstat['paid']= $rep->countAllApplicants('paid');
        $userstat['completed']= $rep->countAllApplicants('completed');
        
        return $this->render('admin/index.html.twig', [
            'page'=>'index',
            'contentTitle'=>'Dashboard',
            'titleIcon'=>'<i class="fa fa-dashboard fa-fw"></i>',
            'controller_name' => 'AdminController',
            'userstat'=>$userstat
        ]);
    }
}
