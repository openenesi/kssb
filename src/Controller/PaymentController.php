<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\TransactionLog;

class PaymentController extends Controller {

    use \App\Utility\StepUtils;
    use \App\Utility\Utils;

    /**
     * @Route("/apply/form_2", name="form_2")
     */
    public function form_2(Request $request) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        if ($session->getApplicationSessionStatus() == "closed") {
            return $this->render('default/closed.html.twig', array('page' => 'scholarship', 'session' => $session));
        }
        if ($session->getApplicationSessionStatus() == "not-ready") {
            return $this->render('default/notready.html.twig', array('page' => 'scholarship', 'session' => $session));
        }

        $r = $this->ensureStep("form_2");
        if ($r) {
            return $r;
        }

        $user = $this->getUser();
        $ref = "KSSB" . $user->getId();
        $ref = 'xyvdo5errj';

        if ((null !== $request->request->get("pay")) && $request->request->get("pay") == "pay") {

            $curl = curl_init();

            $email = $user->getEmail();
            //$amount = round(($session->getRegistrationCost() / 1.015), 2)* 100;  //the amount in kobo.
            $amount = $session->getRegistrationCost() * 100;  //the amount in kobo.
            //$email_reference = str_replace("@", "", str_replace("_", "=", $email));


            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'amount' => $amount,
                    'email' => $email,
                    /* 'reference' => $ref, */
                    'callback_url' => str_replace("localhost", "10.1.44.65", $this->generateUrl("paid")),
                    'subaccount' => 'ACCT_x949qqo59pog9hd',
                    'metadata' => array(
                        'cart_id' => $user->getEmail(),
                        'custom_fields' => array(
                            array('display_name' => 'Purpose', 'variable_name' => 'Purpose', 'value' => 'KSSB ' . $session->getScholarshipSession() . '/' . ($session->getScholarshipSession() + 1))
                        )
                    ),
                ]),
                CURLOPT_HTTPHEADER => [
                    "authorization: Bearer " . $this->getParameter('paystack_secret_key'), //replace this with your own test key
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                //var_dump($err); exit();
                // there was an error contacting the Paystack API
                return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'errmsg' => $err, 'session' => $session));
            }

            $tranx = json_decode($response, true);
            //var_dump($tranx);
            if (!$tranx['status']) {
                // there was an error from the API
                //var_dump($tranx); exit();
                return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'errmsg' => "Internal server error", 'session' => $session));
            }

            // comment out this line if you want to redirect the user to the payment page
            //print_r($tranx);
            // redirect to page so User can pay
            // uncomment this line to allow the user redirect to the payment page
            header('Location: ' . $tranx['data']['authorization_url']);
            exit();
        }
        //if($user == null) echo "eds"; exit();
        $rep = $this->getDoctrine()->getRepository(\App\Entity\TransactionLog::class);
        $log = $rep->findByReference($ref);
        $log = (isset($log) && is_array($log) && (count($log) >0))?($log[0]):(null);
        if ($log) {
            return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'paymentlog'=>$log, 'session' => $session));
        }
        return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'session' => $session));
    }

    /**
     * @Route("/apply/paid", name="paid")
     */
    public function paid(Request $request, \Swift_Mailer $mailer) {
        $session = $this->getScholarshipSession($this->getDoctrine()->getRepository(\App\Entity\ScholarshipSession::class));
        $user = $this->getUser();
        $ref = $request->query->get('reference');
        $reference = isset($ref) ? $ref : '';
        if (!$reference) {
            return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'session' => $session));
        }

        /*         * *
         * PERFORM DATABASE QUERY TO VERIFY THAT THE REFERENCE HAS NOT BEEN VALUED
         */
        $rep = $this->getDoctrine()->getRepository(\App\Entity\TransactionLog::class);
        $log = $rep->findByReference($ref);
        if ($log) {
            return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'session' => $session));
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer " . $this->getParameter("paystack_secret_key"),
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            // there was an error contacting the Paystack API
            return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'session' => $session));
        }

        if ($response) {
            $result = json_decode($response, true);
            // print_r($result);
            if (isset($result)) {
                if (isset($result['data'])) {
                    //something came in
                    if ($result['data']['status'] == 'success') {
                        // the transaction was successful, you can deliver value
                        /*
                          @ also remember that if this was a card transaction, you can store the
                          @ card authorization to enable you charge the customer subsequently.
                          @ The card authorization is in:
                          @ $result['data']['authorization']['authorization_code'];
                          @ PS: Store the authorization with this email address used for this transaction.
                          @ The authorization will only work with this particular email.
                          @ If the user changes his email on your system, it will be unusable
                         */
                        $trxnlog = new TransactionLog();
                        $trxnlog->setAmount($result['data']['amount']);
                        $trxnlog->setBank($result['data']['authorization']['bank']);
                        $trxnlog->setCardType($result['data']['authorization']['card_type']);
                        $trxnlog->setChannel($result['data']['channel']);
                        $trxnlog->setCountryCode($result['data']['authorization']['country_code']);
                        $trxnlog->setCurrency($result['data']['currency']);
                        $trxnlog->setDomain($result['data']['domain']);
                        $trxnlog->setGatewayResponse($result['data']['gateway_response']);
                        $trxnlog->setIpAddress($result['data']['ip_address']);
                        $trxnlog->setLast4($result['data']['authorization']['last4']);
                        $trxnlog->setMessage($result['data']['message']);
                        $trxnlog->setReference($result['data']['reference']);
                        $trxnlog->setStatus($result['data']['status']);
                        $trxnlog->setTrxnDate($result['data']['transaction_date']);
                        $trxnlog->setAttempts($result['data']['log']['attempts']);

                        try {
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($trxnlog);
                            $user->setPaid(true);
                            $em->persist($user);
                            $em->flush();

                            $message = (new \Swift_Message('Payment Confirmation (KSSB ' . $session->getScholarshipSession() . '/' . ($session->getScholarshipSession() + 1) . ')'))
                                    ->setFrom($session->getEmail())
                                    ->setTo($user->getEmail())
                                    ->setBody(
                                    $this->renderView(
                                            // templates/emails/registration.html.twig
                                            'emails/paymentnotification.html.twig', array('amount' => $trxnlog->getAmount(), 'datePaid' => $trxnlog->getTrxnDate(), 'session' => $session)
                                    ), 'text/html'
                            );
                            $mailer->send($message);
                        } catch (Exception $e) {
                            return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'errmsg' => "Server error", 'session' => $session));
                        }

                        return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'session' => $session));
                        //echo "Transaction was successful";
                    } else {
                        // the transaction was not successful, do not deliver value'
                        // print_r($result);  //uncomment this line to inspect the result, to check why it failed.
                        //echo "Transaction was not successful: Last gateway response was: " . $result['data']['gateway_response'];
                        return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'errmsg' => $result['data']['gateway_response'], 'session' => $session));
                    }
                } else {
                    //echo $result['message'];
                    return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'errmsg' => $result['message'], 'session' => $session));
                }
            } else {
                //print_r($result);
                //die("Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable.");
                return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'session' => $session));
            }
        } else {
            //var_dump($request);
            //die("Something went wrong while executing curl. Uncomment the var_dump line above this line to see what the issue is. Please check your CURL command to make sure everything is ok");
            return $this->render('apply/form_2.html.twig', array('page' => 'scholarship', 'step' => 'pay', 'candidate' => $user, 'error' => true, 'session' => $session));
        }
    }

    /**
     * @Route("/apply/paidwebhook", name="webhookpaid")
     */
    public function paidWebHook(Request $request) {

        // Retrieve the request's body
        $body = @file_get_contents("php://input");
        if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER)) {
            exit();
        }

        $signature = (isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE']) ? $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] : '');

        /* It is a good idea to log all events received. Add code *
         * here to log the signature and body to db or file       */

        if (!$signature) {
            // only a post with paystack signature header gets our attention
            exit();
        }

        define('PAYSTACK_SECRET_KEY', $this->getParameter("paystack_secret_key"));
// confirm the event's signature
        if ($signature !== hash_hmac('sha512', $body, PAYSTACK_SECRET_KEY)) {
            // silently forget this ever happened
            exit();
        }


// parse event (which is json string) as object
// Give value to your customer but don't give any output
// Remember that this is a call from Paystack's servers and 
// Your customer is not seeing the response here at all
        $event = json_decode($body);
        switch ($event['event']) {
            // charge.success
            case 'charge.success':
                // TIP: you may still verify the transaction
                // before giving value.

                $ref = $event['data']['reference'];

                $rep = $this->getDoctrine()->getRepository(\App\Entity\TransactionLog::class);
                $log = $rep->findByReference($ref);
                if ($log) {
                    http_response_code(200);
                    exit();
                }
                //verify transaction ref

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($ref),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        "accept: application/json",
                        "authorization: Bearer " . $this->getParameter("paystack_secret_key"),
                        "cache-control: no-cache"
                    ],
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                if ($err) {
                    // there was an error contacting the Paystack API
                    exit();
                }

                if ($response) {
                    $result = json_decode($response, true);
                    // print_r($result);
                    if (isset($result)) {
                        if (isset($result['data'])) {
                            //something came in
                            if ($result['data']['status'] == 'success') {
                                http_response_code(200);
                                //log transaction
                                $trxnlog = new TransactionLog();
                                $trxnlog->setAmount($event['data']['amount']);
                                $trxnlog->setBank($event['data']['authorization']['bank']);
                                $trxnlog->setCardType($event['data']['authorization']['card_type']);
                                $trxnlog->setChannel($event['data']['channel']);
                                $trxnlog->setCountryCode($event['data']['authorization']['country_code']);
                                $trxnlog->setCurrency($event['data']['currency']);
                                $trxnlog->setDomain($event['data']['domain']);
                                $trxnlog->setGatewayResponse($event['data']['gateway_response']);
                                $trxnlog->setIpAddress($event['data']['ip_address']);
                                $trxnlog->setLast4($event['data']['authorization']['last4']);
                                $trxnlog->setMessage($event['data']['message']);
                                $trxnlog->setReference($event['data']['reference']);
                                $trxnlog->setStatus($event['data']['status']);
                                $trxnlog->setTrxnDate($event['data']['paid_at']);
                                $trxnlog->setAttempts($event['data']['log']['attempts']);

                                try {
                                    $em = $this->getDoctrine()->getManager();
                                    $em->persist($trxnlog);
                                    $em->flush();
                                } catch (Exception $e) {
                                    exit();
                                }
                            }
                        }
                    }
                }
                break;
        }
        exit();
    }

}
