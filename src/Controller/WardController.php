<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Ward;

class WardController extends Controller {

//    /**
//     * @Route("/ward", name="ward")
//     */
//    public function index() {
//        return $this->render('ward/index.html.twig', [
//                    'controller_name' => 'WardController',
//        ]);
//    }
//
    /**
     * @Route("/ward/options/{lgaid}", name="ward_options", requirements={"lgaid":"\d+"})
     */
    public function wardOptions(Request $request, $lgaid = null) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine();
            $rep = $em->getRepository(Ward::class);

            $list = $rep->findByLga($lgaid, array("wardName"=>"ASC"));
            //var_dump($list[0]); exit();
//        if(is_array($list)){
//            $list = $list[0];
//        }

            return $this->render('ward/ward_options.html.twig', [
                        'wards' => $list,
            ]);
        }
    }

}
