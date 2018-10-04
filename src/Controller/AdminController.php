<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Cache\Simple\MemcachedCache;

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
        $cols = $request->query->get('col');
        $frotooption = $request->query->get('frotochoice');
        $froto = $request->query->get('froto');

        if (!isset($cols) || count($cols) == 0) {
            $arr_data['data'] = array();
        } else {
            $records = $rep->fetchApplicantSummary(
                    array(
                        'lga' => $lga,
                        'ward' => $ward,
                        'instcat' => $instcat,
                        'institution' => $institution,
                        'bank' => $bank,
                        'cols' => $cols,
                        'frotooption' => $frotooption,
                        'froto' => $froto
                    )
            );
        }
        $arr_data['data'] = $records['data'];
        $arr_data['cols'] = $cols;

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
            $sheet->setCellValueExplicit('A' . $i, strtoupper($record['surname']) . " " . ucfirst(strtolower($record['firstName'])) . " " . ucfirst(strtolower($record['otherNames'])), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('B' . $i, "" . $record[(($info == 'email') ? ('email') : (($info == 'gsm') ? ('gsm') : ('regno')))] . "", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $i++;
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

    /**
     * @Route("/kssbadmin/applicant/data/summarysheet/excel", name="summarysheetexcel")
     */
    public function summarySheetExcel(Request $request) {
        $rep = $this->getDoctrine()->getRepository(\App\Entity\User::class);
        $arr_data = array();
        $invalidCharacters = ['*', ':', '/', '\\', '?', '[', ']'];
        $lga = $request->query->get('lga');
        $ward = $request->query->get('ward');
        $instcat = $request->query->get('instcat');
        $institution = $request->query->get('institution');
        $bank = $request->query->get('bank');
        $cols = $request->query->get('col');
        $grouping = $request->query->get('grouping');
        $frotooption = $request->query->get('frotochoice');
        $froto = $request->query->get('froto');
        $draw = 0;
        $queryoptions = array(
            'lga' => $lga,
            'ward' => $ward,
            'instcat' => $instcat,
            'institution' => $institution,
            'bank' => $bank,
            'cols' => $cols,
            'grouping' => $grouping,
            'frotooption' => $frotooption,
            'froto' => $froto,
            'draw' => $draw);
        if (!isset($cols) || count($cols) == 0) {
            $records = array();
        } else {
            $records = $rep->fetchApplicantSummary($queryoptions);
            $records = $records['data'];
        }

        // echo count($records); exit();
        // Create new Spreadsheet object
        //$pool = new \Cache\Adapter\Apcu\ApcuCachePool();
        //$simpleCache = new \Cache\Bridge\SimpleCache\SimpleCacheBridge($pool);
        //\PhpOffice\PhpSpreadsheet\Settings::setCache($simpleCache);
        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()->setCreator('Kogi State Scholarship Board')
                ->setLastModifiedBy('Kogi State Scholarship Board')
                ->setTitle('Summary Sheet of Registered Candidates')
                ->setSubject('Summary Sheet of Registered Candidates')
                ->setDescription('Summary Sheet of Registered Candidates')
                ->setKeywords('kssb 2007 openxml kogi scholarship')
                ->setCategory('Summary Sheet');

// Add some data
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $i = 1;
        $group = '';
        $groupcol = '';
        $row = 2;
        $col = 1;
        $terminal = false;
        while (!$terminal) {
            if(count($records) < 1000){
                $terminal = true;
            }
            foreach ($records as $record) {
                set_time_limit(180);
                if ($group == '' && $grouping != "none") {
                    switch ($grouping) {
                        case 'bank':
                            $groupcol = 'bankName';
                            break;
                        case 'lga':
                            $groupcol = 'lgaName';
                            break;
                        case 'ward':
                            $groupcol = 'wardName';
                            break;
                        case 'inst':
                            $groupcol = 'institutionName';
                            break;
                        case 'inst_cat':
                            $groupcol = 'institutionCategory';
                            break;
                    }
                    $group = $record[$groupcol];
                    $sheet->setTitle(str_replace("_", " ", strtoupper(str_replace($invalidCharacters, '_', $group))));
                }

                if ($grouping != 'none' && $record[$groupcol] != $group) {
                    //reset row coordinates
                    $row = 2;
                    //change group
                    $group = $record[$groupcol];
                    //create new sheet
                    $sheet = $spreadsheet->createSheet($spreadsheet->getSheetCount());

                    //Give it a title
                    $sheet->setTitle(str_replace("_", " ", strtoupper(str_replace($invalidCharacters, '_', $group))));
                }

                if (in_array('appid', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['appId'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "AppId", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('name', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, strtoupper($record['surname']) . " " . ucfirst(strtolower($record['firstName'])) . " " . ucfirst(strtolower($record['otherNames'])), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Name", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('sex', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['gender'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Sex", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('matricno', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['matricNo'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "MatricNo.", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('gsm', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['mobileNo'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Mobile No", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('email', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['email'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Email", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('bank', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['bankName'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Bank", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('accno', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['accountNo'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "AccountNo", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('bvn', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['bvn'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "BVN", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('institution', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['institutionName'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Institution", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('inst_cat', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['institutionCategory'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "InstitutionCategory", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('course', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['courseOfStudy'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Course", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('level', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['level'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Level", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('lga', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['lgaName'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "LGA", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                if (in_array('ward', $cols)) {
                    $sheet->setCellValueExplicitByColumnAndRow($col, $row, $record['wardName'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    if ($row == 2) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $row - 1, "Ward", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                //move to the next row
                $row++;
                //reset column coordinate
                $col = 1;
            }
            $draw++;
            $records = $rep->fetchApplicantSummary($queryoptions);
            $records = $records['data'];
        }
// Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="summarysheet.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

        return new Response("");
    }

}
