<?php

class EntranceExamFormController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    Private $_unit_id = null;
    private $login_storage = NULL;
    private $roleConfig = NULL;
    private $accessConfig = NULL;
    private $aeccConfig = NULL;
    private $_sms = NULL;
    private $_mail = Null;
    private $cookie_expire = null;

    public function init() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        require_once APPLICATION_PATH . '/configs/access_level.inc';

        $this->accessConfig = new accessLevel();
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $this->_action = $this->getRequest()->getActionName();
        $this->cookie_expire = $zendConfig->loginexpiration;
        //access role id
        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->aeccConfig = $config_role = $zendConfig->aecc_course->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("user_login");
        $this->login_storage = $data = $storage->user_login;
        $this->view->login_storage = $data;
        //echo '<pre>'; print_r($storage->user_login);exit;
        $user_log = new Application_Model_UserLog();
        //echo '<pre>'; print_r($storage->unique_id);exit;
        if (isset($data)) {
            $user_log_details = $user_log->getRecordByemplId($data['app_id'], $storage->unique_id);
            if ($user_log_details) {

                if (!isset($_COOKIE['user_login_status'])) {
                    $user_log->update(array("status" => 0), array("empl_id=?" => $storage->user_login->app_id, "unique_id=?" => $storage->unique_id, "status=?" => 1));
                    $storage->unsetAll();
                    setcookie('user_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
                    $this->_redirect("entrance-exam-form");
                } else {
                    setcookie('user_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
                    setcookie('user_login_status', md5($storage->unique_id), time() + (60 * $this->cookie_expire), "/; samesite=Lax", "", true, true);
                }
            } else {
                $storage->unsetAll();
                setcookie('user_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
                $this->_redirect("entrance-exam-form");
            }
        }

        $this->_helper->layout->setLayout("applicationlayout");


        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();

        //$this->_act = new Application_Model_Adminactions();
        //$this->_db = Zend_Db_Table::getDefaultAdapter();
        //echo '<pre>';print_r($_SESSION);exit;
    }

    protected function authonticate() {

        $storage = new Zend_Session_Namespace("user_login");
        $this->login_storage = $data = $storage->user_login;
        $this->view->login_storage = $data;
        //echo '<pre>'; print_r($data);exit;
        if (!empty($data['app_id'])) {
            $courseModel = new Application_Model_ApplicantCourseDetailModel();
            $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
            $check_applicationStatus = $courseModel->check_form_status($data['app_id']);
            //Check Payment Status
            $check_payment_status = $paymentModel->check_payment_status($data['app_id']);

            //echo '<pre>'; print_r($check_applicationStatus); exit;
            foreach ($check_payment_status as $pay) {
                if ($pay['payment_status'] == 1) {
                    $this->_redirect("entrance-exam-form/document-dashboard/a_id/" . md5($data['app_id']));
                }
            }
            if ($check_applicationStatus['form_status'] === 'okey') {
                $this->_redirect("multi-step-form/index/type/step4/a_id/" . md5($data['app_id']));
            } else {
                $this->_redirect("multi-step-form/index/type/step1/a_id/" . md5($data['app_id']));
                //$this->_redirect("entrance-exam-form/welcome-page/a_id/".md5($data['app_id']));
            }
        }
    }

    public function indexAction() {

        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'entrance_exam_form';
        $this->accessConfig->setAccess('SA_ACAD_ENTRANCE_EXAM_FORM');
        $entrance_form = new Application_Form_EntranceExam();
        $entrance_model = new Application_Model_ApplicantRegisterationModel();
        $courseModel = new Application_Model_ApplicantCourseDetailModel();
        $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
        $user_log = new Application_Model_UserLog();
        $this->view->form = $entrance_form;
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        //echo '<pre>'; print_r($token);exit;
        if ($this->getRequest()->getPost()) {
            $data = $this->getRequest()->getPost();
            //echo '<pre>'; print_r($data);exit;
            if(!empty($data['csrftoken'])) {
                if($data['csrftoken']===$token ){
                  

                $loginData = array(
                    'application_no' => trim(strtoupper($data['applicant_no'])),
                    'password' => trim($data['password'])
                );
                 $check_login = $entrance_model->checkLogin($loginData);


                if (empty($check_login)) {
                    $this->view->invalid = 'Invalid';
                    $_SESSION['flash_message_error'] = "Invalid application number or password. Please try again.";
                } else {

                    $applicatonNumber = trim(strtoupper($data['applicant_no']));

                    //Implement session
                    $storage = new Zend_Session_Namespace("user_login");
                    $user_detail['app_id'] = $applicatonNumber;
                    $storage->user_login = $user_detail;
                    $storage->unique_id = uniqid() . rand() . time(date("Y-m-d H:i:s"));
                    setcookie('user_login_status', md5($storage->unique_id), time() + (60 * $this->cookie_expire), "/; samesite=Lax", "", true, true);
                    $userdata['empl_id'] = $user_detail['app_id'];
                    $userdata['last_login'] = date("Y-m-d H:i:s");
                    $userdata['ip'] = $_SERVER['REMOTE_ADDR'];
                    $userdata['unique_id'] = $storage->unique_id;
                    $user_log->insert($userdata);
                    unset($_SESSION["token"]);
                    $this->_redirect("entrance-exam-form");
                    //End
                    //Check Form Status
                    $check_applicationStatus = $courseModel->check_form_status($applicatonNumber);
                    //Check Payment Status
                    $check_payment_status = $paymentModel->check_payment_status($applicatonNumber);

                    // echo '<pre>'; print_r($check_payment_status); exit;
                    foreach ($check_payment_status as $pay) {
                        if ($pay['payment_status'] == 1) {
                            $this->_redirect("entrance-exam-form/document-dashboard/a_id/" . md5($data['applicant_no']));
                        }
                    }
                    if ($check_applicationStatus['form_status'] === 'okey') {
                        $this->_redirect("multi-step-form/index/type/step4/a_id/" . md5($applicatonNumber));
                    } else {
                        $this->_redirect("multi-step-form/index/type/step1/a_id/" . md5($loginData['application_no']));
                    }
                }
            }else{
                //echo '<pre>';print_r('tokenInvalid');exit;
                $this->view->tokenInvalid = 'tokenInvalid';
                $_SESSION['flash_token_error'] = "Invalid Token";
                
            }
        }
    }
}
    //Registeration Form Action
    public function registerationAction() {
        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'entrance_exam_form';
        $this->accessConfig->setAccess('SA_ACAD_ENTRANCE_EXAM_FORM');
        $entrance_form = new Application_Form_EntranceExam();
        $entrance_model = new Application_Model_ApplicantRegisterationModel();
        $this->view->form = $entrance_form;
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        //echo '<pre>'; print_r($token);exit;
        if ($this->getRequest()->getPost()) {
            $data = $this->getRequest()->getPost();
            //echo '<pre>'; print_r($data);exit;
            if(!empty($data['csrftoken'])) {
                if($data['csrftoken']===$token ){
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                $data['ip'] = $ip;
                $data['status'] = 1;
                unset($data['cnf_password'], $data['check_otp'], $data['csrftoken'],$data['applicant_no']);
                //echo '<pre>';print_r($data); exit;
                 $emailData = array(
                    'email_id' => trim(strtolower($data['email_id']))
                );
                $checkRegisterMail=$entrance_model->checkEmail($emailData);
                if(!empty($checkRegisterMail))
                   $this->_refresh(3,"/academic/entrance-exam-form/registeration",'*Email id is already registered with us....');
                $insert_id = $entrance_model->insert($data);
                unset($_SESSION["token"]);
                //echo"<pre>";print_r($insert);exit; 
                //Insert or update Applicant field on the basis of email id in the database
                if ($insert_id) {

                    //echo '<pre>'; print_r($data['phone']);exit;
                    $to = array(strtolower($data['email_id']));
                    $from = array('noreply@pwcadmissions.in', 'Patna Women\'s College');
                    $first_name = $data['applicant_name'];
                    $subject = "PWC Application Form 2020";
                    $rand_no = $insert_id;
                    $application_no = 'A-2020-' . $rand_no;


                    $message .= "Dear $first_name , <br/><br/> Your login credentials for next step is as follows: <br/> Application No : {$application_no} <br/> Password : {$data['password']} <br/> Please find the link below for login.<br/><br/>";
                    $message .= "<a href='https://pwcadmissions.in/academic/entrance-exam-form/index'>https://pwcadmissions.in/academic/entrance-exam-form/index</a>";




                    $mail = new Application_Model_MailSender($to, $from, $subject, $message);
                    //Login Details Information
                    $loginData = array(
                        'application_no' => $application_no
                    );
                    $upsert = $entrance_model->update($loginData, array('phone_number=?' => $data['phone_number'], 'email_id=?' => $data['email_id']));
                    $applicatonNumber = trim($application_no);
                    //echo '<pre>'; print_r($applicatonNumber); exit;
                    //Implement session

                    if ($mail->mail($this->_mail['user'], $this->_mail['pass'])) {
                        echo "Your login credentials for next step is sent on your email id.";
                        /* $storage = new Zend_Session_Namespace("user_login");
                          $user_detail['app_id'] = $applicatonNumber;
                          $storage->user_login = $user_detail;
                          $storage->unique_id = uniqid() . rand() . time(date("Y-m-d H:i:s"));
                          setcookie('user_login_status', '1', time() + (60 * 4), '/'); */
                        $this->_redirect("entrance-exam-form/welcome-page/a_id/" . md5($loginData['application_no']));
                    } else {
                        /* $storage = new Zend_Session_Namespace("user_login");
                          $user_detail['app_id'] = $applicatonNumber;
                          $storage->user_login = $user_detail;
                          $storage->unique_id = uniqid() . rand() . time(date("Y-m-d H:i:s"));
                          setcookie('user_login_status', '1', time() + (60 * 4), '/');
                          $this->_redirect("entrance-exam-form/welcome-page/a_id/".md5($loginData['application_no'])); */
                        echo 'Sent Failed';
                        $this->_redirect("entrance-exam-form/welcome-page/a_id/" . md5($loginData['application_no']));
                    }
                }
            
                }else{
                    //echo '<pre>';print_r('tokenInvalid');exit;
                    $_SESSION['flash_token_error'] = "Invalid Token";
                }
            }
        }
    }
    //End Registeration Form
    public function instructionAction() {
        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'entrance_exam_form';
    }

    public function usermanualAction() {
        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'entrance_exam_form';
    }

    public function welcomePageAction() {
        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'entrance_exam_form';
        $application_id = $this->_getParam("a_id");
        $this->view->a_id = $application_id;
        $entrance_model = new Application_Model_ApplicantRegisterationModel();
        if (!empty($application_id)) {
            $applicantLoginDetails = $entrance_model->getRecordByAppNo($application_id);
            //$paymentData = $paymentData->getsavedData($application_no);
            //echo"<pre>";print_r($paymentData);
            $this->view->paginator = $applicantLoginDetails;
        }
    }

    public function documentDashboardAction() {
        $allFormData = new Application_Model_ApplicantCourseDetailModel();
        $paymentData = new Application_Model_ApplicantPaymentDetailModel();
        $application_no = $this->_getParam("a_id");
        $this->view->a_id = $application_no;
        $paymentData = $paymentData->check_for_status($application_no);
        //echo '<pre>'; print_r($paymentData); exit;
        $this->view->payment_detail = $paymentData;
    }

    public function admitCardPreviewAction() {
        $allFormData = new Application_Model_ApplicantCourseDetailModel();
        $paymentData = new Application_Model_ApplicantPaymentDetailModel();
        $application_no = $this->_getParam("application_no");
        $this->view->a_id = $application_no;
        if (!empty($application_no)) {
            $formFilledData = $allFormData->getAllFormFilledData($application_no);
            $paymentData = $paymentData->getsavedData($application_no);
            //echo"<pre>";print_r($paymentData);
            $this->view->paginator = $formFilledData;
            $this->view->payment_detail = $paymentData;
        }
    }

    public function applicationFormPreviewAction() {
        $allFormData = new Application_Model_ApplicantCourseDetailModel();
        $paymentData = new Application_Model_ApplicantPaymentDetailModel();
        $application_no = $this->_getParam("application_no");
        $this->view->a_id = $application_no;
        if (!empty($application_no)) {
            $formFilledData = $allFormData->getAllFormFilledData($application_no);
            $paymentData = $paymentData->getsavedData($application_no);

            $this->view->paginator = $formFilledData;
            $this->view->payment_detail = $paymentData;
        }
    }

    public function casteCertifiatePreviewAction() {
        $allFormData = new Application_Model_ApplicantCourseDetailModel();
        $paymentData = new Application_Model_ApplicantPaymentDetailModel();
        $application_no = $this->_getParam("application_no");
        $this->view->a_id = $application_no;
        if ($application_no) {
            $formFilledData = $allFormData->getAllFormFilledData($application_no);
            $paymentData = $paymentData->getsavedData($application_no);
            //echo"<pre>";print_r($formFilledData);exit;
            $this->view->paginator = $formFilledData;
            $this->view->payment_detail = $paymentData;
        }
    }

    public function forgotPasswordAction() {
        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'entrance_exam_form';
        $this->accessConfig->setAccess('SA_ACAD_ENTRANCE_EXAM_FORM');
        $entrance_form = new Application_Form_EntranceExam();
        $entrance_model = new Application_Model_ApplicantRegisterationModel();
        $this->view->form = $entrance_form;
        if ($this->getRequest()->isPost()) {
            if ($entrance_form->isValid($this->getRequest()->getPost())) {
                $data = $entrance_form->getValues();
                $emailData = array(
                    'email_id' => trim(strtolower($data['email_id']))
                );
                //echo "<pre>";print_r($data);exit;
                if ($emailData) {


                    $check_email = $entrance_model->checkEmail($emailData);

                    if (empty($check_email)) {

                        $_SESSION['flash_message_error'] = "Please enter active and valid email-Id, which is registered with us.";
                    } else {
                        $to = array(strtolower(trim($check_email['email_id'])));
                        $from = array('noreply@pwcadmissions.in', 'Patna Women\'s college');
                        $first_name = $check_email['applicant_name'];
                        $subject = "Forgot Password";

                        $message .= "Dear $first_name , <br/><br/> Your login credentials for next step is as follows: <br/> Application No : {$check_email['application_no']} <br/> Password : {$check_email['password']} <br/> Please find the link below for login.<br/><br/>";
                        $message .= "<a href='https://pwcadmissions.in/academic/entrance-exam-form/index'>https://pwcadmissions.in/academic/entrance-exam-form/index</a>";
                        $mail = new Application_Model_MailSender($to, $from, $subject, $message);
// More headers
//$headers .= "From: <$from>" . "\r\n";
                        //echo "<pre>"; print_r($message);exit;
                        //echo"<pre>";print_r($insertLoginData);exit;
                        if ($mail->mail($this->_mail['user'], $this->_mail['pass'])) {
                            $_SESSION['flash_message_error'] = "Mail Sent.";
                        } else {
                            $_SESSION['flash_message_error'] = "Sending Failed.";
                        }
                    }
                }
            }
        }
    }

    //Ajax Area
    //Get Academic on degree id

    public function ajaxVerifyOtpAction() {
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            //$phone = $this->_getParam("phone"); // Recipient mobile number
            $phone_no = $this->_getParam("phone");
            $email_id = $this->_getParam("email_id");
            $inputOtp = $this->_getParam("inputOtp");
            $entrance_model = new Application_Model_ApplicantRegisterationModel();
            $validateOtp = $entrance_model->validateOtp($phone_no, $email_id, $inputOtp);
            if ($validateOtp) {
                echo $otpDisplay = 1;
            } else {
                echo $otpDisplay = 0;
            }
        }die;
    }

    public function ajaxCheckDuplicateEmailAction() {
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            //$phone = $this->_getParam("phone"); // Recipient mobile number
            $email_id = trim($this->_getParam("email_id"));
            $entrance_model = new Application_Model_ApplicantRegisterationModel();
            $checkemail = $entrance_model->checkRowfor_email_exist($email_id);
            if ($checkemail) {
                echo $otpDisplay = 1;
            } else {
                echo $otpDisplay = 0;
            }
        }die;
    }

    public function ajaxSendOtpAction() {
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            //$phone = $this->_getParam("phone"); // Recipient mobile number
            $recipient_no = trim($this->_getParam("phone"));
            $email_id = trim($this->_getParam("email_id"));
            $entrance_model = new Application_Model_ApplicantRegisterationModel();
            $rand_no = rand(10000, 99999);
            //Check previous entry
            $conditions = $recipient_no;
            $checkPrev = $entrance_model->checkRow1($email_id);

            //Insert or update otp in the database
            if ($checkPrev) {
                $otpData = $rand_no;
                //echo $conditions;
                $insert = $entrance_model->updateOtp($otpData, $conditions, $email_id);
                //echo $insert;exit;
            } else {
                $otpData = array(
                    'phone' => $recipient_no,
                    'email_id' => $email_id,
                    'otp' => $rand_no
                );
                //echo "<pre>"; print_r($otpData);exit;
                $insert = $entrance_model->insert($otpData);
            }

            if ($insert) {
                // Send otp to user via SMS

                if ($this->_sms['default'] == 0)
                    $recipient_no = $recipient_no;
                else {
                    $recipient_no = $this->_sms['default'];
                }
                $message = "Dear Applicant,<br/> <br/>OTP for mobile verification is: <br/> OTP No : $rand_no <br/>";
                $phoneMsg = "Dear Applicant,\n \n OTP for mobile verification is: \n OTP No : $rand_no \n";
                $message .= "<br/>Thanks";
                $send = $this->SMSSend($recipient_no, $phoneMsg);
                //Code to Send Otp On mail Date:20 Feb 2020
                $user = "noreply@pwcadmissions.in";
                $password = "Em@l20Pwc";
                $to = array(strtolower($email_id));
                $from = array('noreply@pwcadmissions.in', 'Patna Women\'s college');
                $subject = "Otp for mobile verification";


                $mail = new Application_Model_MailSender($to, $from, $subject, $message);


                if ($mail->mail($this->_mail['user'], $this->_mail['pass'])) {
                    
                } else {
                    
                }
                if ($send) {
                    echo $otpDisplay = 1;
                } else {
                    echo $otpDisplay = 0;
                }
            } else {
                echo $otpDisplay = 0;
            }
        } else {
            echo $otpDisplay = 0;
        }die;
    }

    public function SMSSend($phone, $msg, $debug = false) {
        $user = $this->_sms['user'];

        $password = $this->_sms['password'];

        $senderid = $this->_sms['senderid'];

        $smsurl = $this->_sms['smsurl'];

        $dnd = $this->_sms['dnd'];
        $priority = $this->_sms['priority'];

        $url = 'username=' . $user;
        $url .= '&password=' . $password;
        $url .= '&sender=' . $senderid;
        $url .= '&to=' . urlencode($phone);
        $url .= '&message=' . urlencode($msg);
        $url .= '&priority=' . $priority;
        $url .= '&dnd=' . $dnd;
        $url .= '&unicode=0';


        $urltouse = $smsurl . $url;
        if ($debug) {
            echo "Request: <br>$urltouse<br><br>";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urltouse);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Open the URL to send the message
        //$response = httpRequest($urltouse);
        $response = curl_exec($ch);
        curl_close($ch);
        if ($debug) {
            echo "Response: <br><pre>" .
            str_replace(array("<", ">"), array("&lt;", "&gt;"), $response) .
            "</pre><br>";
        }
        return($response);
    }

    public function developertestAction() {

        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'entrance_exam_form';
        $this->accessConfig->setAccess('SA_ACAD_ENTRANCE_EXAM_FORM');
        $entrance_form = new Application_Form_EntranceExam();
        $entrance_model = new Application_Model_ApplicantRegisterationModel();
        $courseModel = new Application_Model_ApplicantCourseDetailModel();
        $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
        $this->view->form = $entrance_form;

        if ($this->getRequest()->isPost()) {

            if ($this->getRequest()->getPost()) {

                $data = $_POST;

                $loginData = array(
                    'application_no' => trim(strtoupper($data['applicant_no'])),
                    'password' => trim($data['password'])
                );



                $check_login = $entrance_model->checkLogin($loginData);


                if (empty($check_login)) {
                    $this->view->invalid = 'Invalid';
                    $_SESSION['flash_message_error'] = "Invalid application number or password. Please try again.";
                } else {
                    $applicatonNumber = trim(strtoupper($data['applicant_no']));
                    //Check Form Status
                    $check_applicationStatus = $courseModel->check_form_status($applicatonNumber);
                    //Check Payment Status
                    $check_payment_status = $paymentModel->check_payment_status($applicatonNumber);

                    // echo '<pre>'; print_r($check_payment_status); exit;
                    foreach ($check_payment_status as $pay) {
                        if ($pay['payment_status'] == 1) {
                            $this->_redirect("entrance-exam-form/document-dashboard/a_id/" . md5($data['applicant_no']));
                        }
                    }
                    if ($check_applicationStatus['form_status'] === 'okey') {
                        $this->_redirect("multi-step-form/index/type/step5/a_id/" . md5($applicatonNumber));
                    } else {
                        $this->_redirect("multi-step-form/index/type/step1/a_id/" . md5($loginData['application_no']));
                    }
                }
            }
        }
    }

}
