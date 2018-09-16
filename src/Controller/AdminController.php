<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $lgas = $replga->findBy(array(), array("lgaName" => "ASC"));
        $repinstitution = $this->getDoctrine()->getRepository(\App\Entity\Institution::class);
        $institutions = $repinstitution->findBy(array(), array("institutionName" => "ASC"));
        $repbank = $this->getDoctrine()->getRepository(\App\Entity\Bank::class);
        $banks = $repbank->findBy(array(), array("bankName" => "ASC"));
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
     * @Route("/kssbadmin/applicant/data/generatesummarysheet", name="generatesummarysheet")
     */
    public function generateSummarySheet(Request $request) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $replga = $this->getDoctrine()->getRepository(\App\Entity\Lga::class);
        $lgas = $replga->findBy(array(), array("lgaName" => "ASC"));
        $repbank = $this->getDoctrine()->getRepository(\App\Entity\Bank::class);
        $banks = $repbank->findBy(array(), array("bankName" => "ASC"));
        $pg = ($request->query->get('pg')) ? ($request->query->get('pg')) : ("");

        return $this->render('admin/generatesummarysheet.html.twig', [
                    'session' => $session,
                    'lgas' => $lgas,
                    'banks' => $banks,
                    'page' => 'summarysheet',
                    'instcats' => $this->getUtilInstitutionCategories(),
                    'pg' => $pg,
                    'contentTitle' => 'Summary Sheet',
                    'titleIcon' => '<i class="fa fa-pie-chart fa-fw"></i>',
        ]);
    }

    /**
     * @Route("/kssbadmin/applicant/data/summarysheet", name="summarysheet")
     */
    public function summarySheet(Request $request) {
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $arr_data = array();

        $lga = $request->query->get('lga');
        $ward = $request->query->get('ward');
        $instcat = $request->query->get('instcat');
        $institution = $request->query->get('institution');
        $bank = $request->query->get('bank');

        $records = $rep->fetchApplicantSummary(
                array(
                    'lga' => $lga,
                    'ward' => $ward,
                    'instcat' => $instcat,
                    'institution' => $institution,
                    'bank' => $bank)
        );

        $arr_data['data'] = $records['data'];

        return $this->render('admin/summarysheet.html.twig', $arr_data);
    }

    /**
     * @Route("/kssbadmin/applicant/data/view/{id}", name="viewApplicantData")
     */
    public function viewApplicantData(Request $request, $id = null) {
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $arr_data = array();
        $arr_data['page'] = 'applicant';
        $arr_data['contentTitle'] = 'Applicant View';
        $arr_data['titleIcon'] = '<i class="fa fa-user fa-fw"></i>';
        $arr_data["notfound"] = false;
        $userobj = null;
        if (isset($id)) {
            $userobj = $rep->find($id);
        } elseif (null !== $request->query->get('email')) {
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
                if ($user->getAppId()) {
                    $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
                    $arr_data['appId'] = $this->generateAppId($user->getAppId(), $session);
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

    /**
     * @Route("/kssbadmin/distribution/distributions", name="fetchDistributions")
     */
    public function fetchDistributionByDistrict(Request $request) {
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $distribution = $rep->fetchDistributionByDistrict();
        $arr_data = array();
        $arr_data['page'] = 'district';
        $arr_data['contentTitle'] = 'Distribution by Senatorial District';
        $arr_data['titleIcon'] = '<i class="fa fa-user fa-fw"></i>';

        $arr_data['distribution'] = $distribution;
        return $this->render('admin/distribution/distributions.html.twig', $arr_data);
    }

    /**
     * @Route("/kssbadmin/applicant/{info}/{format}", name="fetchApplicantInfo")
     */
    public function fetchApplicantInfo(Request $request, $info, $format = 'csv') {
        if (!in_array($info, ['email', 'gsm', 'regno'])) {
            return new Response("<h3 style='text-align:center'>Invalid information requested.</h3>");
        }
        if (!in_array($format, ['csv', 'xlsx', 'html'])) {
            return new Response("<h3 style='text-align:center'>Invalid format specified.</h3>");
        }
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);

        $status = $request->query->get('status');
        $lga = $request->query->get('lga');
        $institution = $request->query->get('institution');
        $bank = $request->query->get('bank');

        $records = $rep->fetchApplicantInfo(
                array(
                    'status' => $status,
                    'lga' => $lga,
                    'institution' => $institution,
                    'bank' => $bank,
                    'info' => $info)
        );

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

// Set document properties
        $spreadsheet->getProperties()->setCreator('Kogi State Scholarship Board')
                ->setLastModifiedBy('Kogi State Scholarship Board')
                ->setTitle('Comma Separated ' . (($info == 'email') ? ('Emails') : (($info == 'gsm') ? ('Mobile Numbers') : ('Registration Numbers'))) . ' of Candidates')
                ->setSubject('Office 2007 XLSX Test Document')
                ->setDescription((($info == 'email') ? ('Emails') : (($info == 'gsm') ? ('Mobile Numbers') : ('Registration Numbers'))) . ' of candidates')
                ->setKeywords('office 2007 openxml php')
                ->setCategory((($info == 'email') ? ('Emails') : (($info == 'gsm') ? ('Mobile Numbers') : ('Registration Numbers'))));

// Add some data
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $i = 1;
        foreach ($records as $record) {
            $sheet->setCellValueExplicit('A' . $i++, $record[(($info == 'email') ? ('email') : (($info == 'gsm') ? ('gsm') : ('regno')))], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }

// Rename worksheet
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle((($info == 'email') ? ('Emails') : (($info == 'gsm') ? ('Mobile Numbers') : ('Registration Numbers'))));


// Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . (($info == 'email') ? ('emails') : (($info == 'gsm') ? ('gsm') : ('regno'))) . '.' . $format . '"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        //$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        if ($format == 'csv') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        } else if ($format == 'xlxs') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlxs($spreadsheet);
        } else if ($format == 'html') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
        }
        $writer->setDelimiter(',');
        $writer->setEnclosure('');
        $writer->setLineEnding("\r\n");
        $writer->save('php://output');
        exit;

        return new Response("");
    }

}
