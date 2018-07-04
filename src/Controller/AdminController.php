<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller {

    use \App\Utility\Utils;

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
     * @Route("/kssbadmin/applicant", name="applicant")
     */
    public function applicant(Request $request) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $replga = $this->getDoctrine()->getRepository(\App\Entity\Lga::class);
        $lgas = $replga->findAll();
        $repinstitution = $this->getDoctrine()->getRepository(\App\Entity\Institution::class);
        $institutions = $repinstitution->findAll();
        $repbank = $this->getDoctrine()->getRepository(\App\Entity\Bank::class);
        $banks = $repbank->findAll();
        $pg = ($request->query->get('pg')) ? ($request->query->get('pg')) : ("");

        return $this->render('admin/applicant.html.twig', [
                    'session' => $session,
                    'lgas' => $lgas,
                    'institutions' => $institutions,
                    'banks' => $banks,
                    'page' => 'applicant',
                    'pg' => $pg,
                    'contentTitle' => 'Applicants',
                    'titleIcon' => '<i class="fa fa-user fa-fw"></i>',
        ]);
    }

    /**
     * @Route("/kssbadmin/applicant/data", name="fetchApplicantData")
     */
    public function fetchApplicantData(Request $request) {
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $draw = $request->query->get('draw');
        $start = $request->query->get('start');
        $length = $request->query->get('length');
        $search = $request->query->get('search');

        $status = $request->query->get('status');
        $lga = $request->query->get('lga');
        $institution = $request->query->get('institution');
        $bank = $request->query->get('bank');

        $records = $rep->fetchApplicantRecords(
                array(
                    'draw' => $draw,
                    'start' => $start,
                    'length' => $length,
                    'search' => $search,
                    'status' => $status,
                    'lga' => $lga,
                    'institution' => $institution,
                    'bank' => $bank)
        );


        return new \Symfony\Component\HttpFoundation\JsonResponse($records);
    }

    /**
     * @Route("/kssbadmin/applicant/data/view/{id}", name="viewApplicantData")
     */
    public function viewApplicantData(Request $request, $id=null) {
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $arr_data = array();
        $arr_data['page']= 'applicant';
        $arr_data['contentTitle'] = 'Applicant View';
        $arr_data['titleIcon'] = '<i class="fa fa-user fa-fw"></i>';
        $arr_data["notfound"] = false;
        $userobj = null;
        if(isset($id)){
        $userobj = $rep->find($id);
        }elseif(null !== $request->query->get('email')){
            $userobj = $rep->findByEmail($request->query->get('email'));
        }
        if (null === $userobj || (is_array($userobj) && count($userobj) == 0)) {
            $arr_data["notfound"] = true;
        } else {

            $user = (is_array($userobj) ? ($userobj[0]) : ($userobj));
            $arr_data['candidate'] = $user;
            $trxnrep = $this->getDoctrine()->getRepository(\App\Entity\TransactionLog::class);
            $trxnlog = $trxnrep->findByReference($user->getTrxnRef());
            if (count($trxnlog)) {
                $arr_data['trxnlog'] = $trxnlog[0];
                if($user->getAppId()){
                    $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
                    $arr_data['appId']=$this->generateAppId($user->getAppId(), $session);
                }
            }
        }
        return $this->render('admin/applicantview.html.twig', $arr_data);
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
