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
        return $this->render('admin/index.html.twig', [
            'page'=>'admin',
            'controller_name' => 'AdminController',
        ]);
    }
}
