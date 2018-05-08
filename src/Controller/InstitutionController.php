<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Institution;

class InstitutionController extends Controller
{
    
    
    /**
     * @Route("/institution/options/{category}", name="institution_options")
     */
    public function institutionOptions(Request $request, $category = "") {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine();
            $rep = $em->getRepository(Institution::class);
            $category = substr($category, 1);
            $list = $rep->findByInstitutionCategory($category, array('institutionName'=>'ASC'));
            //var_dump($list[0]); exit();
//        if(is_array($list)){
//            $list = $list[0];
//        }

            return $this->render('institution/institution_options.html.twig', [
                        'institutions' => $list,
            ]);
        }
    }

}
