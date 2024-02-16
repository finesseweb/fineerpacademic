<?php

class IndexController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    private $_flashMessenger = null;
    private $login_storage = NULL;
    private $roleConfig = NULL;
    private $academic_id = NULL;
    private $term_id = NULL;
    private $start_date = NULL;
    private $end_date = NULL;
    private $_base_url = NULL;
    private $accessConfig = NULL;
    private $admissions_url = null;
    private $cookie_expire = null;

    public function init() {

        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

        require_once APPLICATION_PATH . '/configs/access_level.inc';

        $accessConfig = new accessLevel();

        $config = $zendConfig->mainconfig->toArray();
        $this->cookie_expire = $zendConfig->loginexpiration;
        
        $this->_base_url = $config['erp'];
        $this->admissions_url = $config['admissions'];
        $this->view->mainconfig = $config;

        $config_role = $zendConfig->role_administrator->toArray();

        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $user_log = new Application_Model_UserLog();
        $data = $storage->admin_login;
        $this->view->login_storage = $data;
       
//===[SESSION EXPIRATION TIME SET BY ASHUTOSH]=========================//


        if (isset($data)) {
            $user_log_details = $user_log->getRecordByemplId($storage->admin_login->user_id, $storage->unique_id,!$_COOKIE['PHPSESSID']?0:$_COOKIE['PHPSESSID'],$_SERVER['REMOTE_ADDR']);
       //     echo '<pre>'; print_r($user_log_details ); die;
            if ($user_log_details) {
                
                if (!isset($_COOKIE['admin_login_status'])) {
                    $user_log->update(array("status" => 0), array("empl_id=?" => $storage->admin_login->user_id, "unique_id=?" => $storage->unique_id, "status=?" => 1));
                    $storage->unsetAll();
                    setcookie('admin_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
                    $this->_redirect('index/login');
                } else {
                    $this->view->role_id = $data->role_id;
                    $this->view->login_empl_id = $data->empl_id;
                    setcookie('admin_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
                    setcookie('admin_login_status', md5($storage->unique_id), time() + (60 * $this->cookie_expire), '/; samesite=Lax', "", true, true);
                }
            } else {
                  $user_log->update(array("status" => 0), array("empl_id=?" => $storage->admin_login->user_id, "unique_id=?" => $storage->unique_id, "status=?" => 1));
                $storage->unsetAll();
                setcookie('admin_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
                $this->_redirect('index/login');
            }
        }

        $this->_action = $this->getRequest()->getActionName();
        if ($this->_action == "login" || $this->_action == "forgot-password") {
            $this->_helper->layout->setLayout("layout");
        } else {
            $this->_helper->layout->setLayout("layout");
        }



        $adminaction_model = new Application_Model_Adminactions();

        $this->_act = new Application_Model_Adminactions();

        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $url_arr = explode('/', $_SERVER['REQUEST_URI']);
        
        if (in_array('admission', $url_arr)){
            $this->_redirect($this->admissions_url);
        }
        else if($this->_action != 'captcha'){
            $this->authonticate();
        }
        


        // $this->_db = Zend_Db_Table::getDefaultAdapter();
        
        if ($this->_action != 'forgot-password') {
            $this->view->authontication = $this->_authontication;
        }
        //$this->captcha();
        // echo date(DATE_FORMATE);
        //echo DATE_FORMATE;exit;
    }

    protected function authonticate() {

        $storage = new Zend_Session_Namespace("admin_login");

        $data = $storage->admin_login;

        //echo '<pre>'; print_r($this->_action ); die;


        if ($data->role_id == 0 && $this->_action != 'logout' && $this->_action != 'login')
            $this->_redirect('student-portal/student-dashboard');



        if (!$data && $this->_action != 'login' &&
                $this->_action != 'forgot-password') {
            $this->_redirect('index/login');

            return;
        }
        if ($this->_action != 'forgot-password') {

            $this->_authontication = $data;

            $this->_agentsdata = $storage->agents_data;
        }
    }

    public function indexAction() {
        $toDo_form = new Application_Form_Index();
        $student_portal = new Application_Model_StudentPortal();
       // $result = $student_portal->getDashBoardDetails();
        $this->view->type = $type;
        $this->view->form = $toDo_form;
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;
        // action body 
        //Purchase 
        $ErpIndex_model = new Application_Model_ErpIndex();
        /* $this->view->PurchaseProformaCount = $ErpIndex_model->PurchaseProformaCount();
          $this->view->PurchaseCommercialCount = $ErpIndex_model->PurchaseCommercialCount();
          $this->view->PurchasePackingCount = $ErpIndex_model->PurchasePackingCount();
          $this->view->PurchaseOrderCount = $ErpIndex_model->PurchaseOrderCount();
          $this->view->PurchaseInvoiceCount = $ErpIndex_model->PurchaseInvoiceCount();
          //Sales
          $this->view->SalesProformaCount = $ErpIndex_model->SalesProformaCount();
          $this->view->SalesCommercialCount = $ErpIndex_model->SalesCommercialCount();
          $this->view->SalesPackingCount = $ErpIndex_model->SalesPackingCount();
          $this->view->SalesEnquiryCount = $ErpIndex_model->SalesEnquiryCount();
          $this->view->SalesQuotationCount = $ErpIndex_model->SalesQuotationCount();
          $this->view->SalesOrderCount = $ErpIndex_model->SalesOrderCount();
          $this->view->SalesInvoiceCount = $ErpIndex_model->SalesInvoiceCount(); */
        // Grn	
        //$Sales_order_model = new Application_Model_ErpSalesTyreOrderForm();
        //plant
        //$this->view->PlantMaintenance = $ErpIndex_model->PlantMaintenance();
    }

    public function logoutAction() {

        $user_log = new Application_Model_UserLog();
        $storage = new Zend_Session_Namespace("admin_login");
        $user_log->update(array("status" => 0), array("empl_id=?" => $storage->admin_login->user_id, "unique_id=?" => $storage->unique_id, "status=?" => 1));
        setcookie('admin_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
        $storage->unsetAll();


        $this->_redirect('index/login');
    }

    public function loginAction() {
        $pword = $this->_getparam('key');
        $user = $this->_getparam('user');
        $this->setErpUser();
        $HRMModel_model = new Application_Model_HRMModel();
        $user_log = new Application_Model_UserLog();
        $this->_helper->layout->setLayout("loginlayout");
        //echo '<pre>'; print_r($this->captcha());exit;
       
        
        if (!empty($pword) && !empty($user)) {
            $user_detail = $HRMModel_model->getUserDetail($user);


            if ($user_detail['frgt_status'] == 1) {

                if ($user_detail['password'] === $pword) {

                    $storage = new Zend_Session_Namespace("admin_login");
                    $user_detail['role_set'] = explode(';', $user_detail['areas']);
                    unset($user_detail['sections']);
                    unset($user_detail['areas']);
                    $storage->admin_login = (object) $user_detail;
                    $storage->unique_id = uniqid() . rand() . time(date("Y-m-d H:i:s"));
                    setcookie('admin_login_status', md5($storage->unique_id), time() + (60 * $this->cookie_expire), '/; samesite=Lax', "", true, true);
                    $userdata['empl_id'] = $user_detail['user_id'];
                    $userdata['last_login'] = date("Y-m-d H:i:s");
                    $userdata['ip'] = $_SERVER['REMOTE_ADDR'];
                    $userdata['unique_id'] = $storage->unique_id;
                     $userdata['sess_vlue'] = $_COOKIE['PHPSESSID'];;
                    $user_log->insert($userdata);
                    // $adminroles = new Application_Model_Adminroles();
                    //$stored = $storage->admin_login;
                    $this->_redirect('index/change-password'); //Redirected on Report page, Date - 15-Dec-2017

                }
            } else {
                $this->_redirect('index/logout');
            }

        }

        if ($this->_authontication) {
            $this->_redirect('student/index'); //Redirected on Report page, Date - 15-Dec-2017
        }

       


        $users = new Application_Model_ErpAdmin();


        if ($this->getRequest()->isPost()) {


            $auth = Zend_Auth::getInstance();
            $data = $this->getRequest()->getPost();
            //echo '<PRE>'; print_r($data);exit;
            if (count($data) == 1)
                $this->forgotPassword($data);

            $updateDate = $HRMModel_model->UpdateLoginDate($data['admin_user_name']);

            $user_detail = $HRMModel_model->getUserDetail($data['admin_user_name']);
            //echo'<pre>';print_r($_SESSION);exit;
            if($user_detail['attempts'] >1) { 
               $_SESSION['accountLockedmsg']= "Your Account Is Locked.";
            }
            
            if (is_array($user_detail) && !empty($user_detail)) {
                $stored_password = $user_detail['password'];
                //print_r(md5($data['password']).'===='.$stored_password);exit;
                if (md5($data['password']) === $stored_password && $data['captcha'] == $_SESSION['Captcha'] ) {
                    $auth->clearIdentity();
                    $storage = new Zend_Session_Namespace("admin_login");
                    $user_detail['role_set'] = explode(';', $user_detail['areas']);
                    unset($user_detail['sections']);
                    unset($user_detail['areas']);
                    $storage->admin_login = (object) $user_detail;
                    $storage->unique_id = uniqid() . rand() . time(date("Y-m-d H:i:s"));
                    setcookie('admin_login_status', md5($storage->unique_id), time() + (60 * $this->cookie_expire), '/; samesite=Lax', "", true, true);
                    // $adminroles = new Application_Model_Adminroles();
                    //$stored = $storage->admin_login;
                    $userdata['empl_id'] = $user_detail['user_id'];
                    $userdata['last_login'] = date("Y-m-d H:i:s");
                    $userdata['ip'] = $_SERVER['REMOTE_ADDR'];
                    $userdata['unique_id'] = $storage->unique_id;
                    $userdata['sess_vlue'] = $_COOKIE['PHPSESSID'];
                    $user_log->insert($userdata);
                    $updateAttemptCount = $HRMModel_model->revokeAttempt($data['admin_user_name']);
                    $this->_redirect('index'); //Redirected on Report page, Date - 15-Dec-2017
                } else {
                    $loginAttemptCount = $HRMModel_model->checkLoginAttempt($data['admin_user_name']);
                    // $this->view->errorMessage = "Invalid username or password. Please try again.";
                }
            } else {
                $auth_attempt = FALSE;
            }
            //}
            // die;
            //===================[COMMENTED ON 7-01-2021 FOR STUDENT LOGIN]=====================//

            if (!$auth_attempt) {


                $auth = Zend_Auth::getInstance();
                $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(), 'participants_login');

                $authAdapter->setIdentityColumn('participant_username')->setCredentialColumn('participant_pword');
                $authAdapter->setIdentity($data['admin_user_name'])->setCredential($data['password']);
                $authAdapter->getDbSelect()->where('participant_Active=0');


                $result = $auth->authenticate($authAdapter);
                //echo "<pre>"; print_r($result);echo "</pre>";exit;
                if ($result->isValid()) {

                    $auth->clearIdentity();
                    $storage = new Zend_Session_Namespace("admin_login");
                    $storage->admin_login = $authAdapter->getResultRowObject();
                    $storage->unique_id = uniqid() . rand() . time(date("Y-m-d H:i:s"));

                    setcookie('admin_login_status', '1', time() + (60 * 20), '/',$_SERVER['HTTP_HOST'],true,true,'lax');

                    // $adminroles = new Application_Model_Adminroles();

                    $stored = $storage->admin_login;

                    $this->_redirect('student-portal/student-dashboard'); //Redirected on Report page, Date - 15-Dec-2017
                } else {
                    $_SESSION['flash_message_error']= "Invalid username or password. Please try again.";
                }
            } else {
                $_SESSION['flash_message_error'] = "Invalid username or password. Please try again.";
             }

            }
        

    }



    public function changePasswordAction() {
        $this->view->sub_title_name = 'Change Password';
        $this->view->action_name = 'changePassword';
        $change_pword_form = new Application_Form_ChangePassword();
        $participant = new Application_Model_HRMUserModel();
        $this->view->form = $change_pword_form;
//echo "<pre>";print_r($_SESSION);exit;
        if ($this->getRequest()->isPost()) {
            if ($change_pword_form->isValid($this->getRequest()->getPost())) {
                $data = $change_pword_form->getValues();
                $arr_data['password'] = md5($data['new_password']);
                $arr_data['frgt_status'] = 0;
                $result = $participant->getInfo($_SESSION['admin_login']['admin_login']->email);
                $participant->update($arr_data, array('user_id=?' => $_SESSION['admin_login']['admin_login']->user_id));
                $_SESSION['flash_message'] = 'Password updated successfully.';
                $this->_redirect('index/change-password');
            }
        }
    }

    public function forgotPassword($data) {
        $HRMModel_model = new Application_Model_HRMModel();
        $user_detail = $HRMModel_model->getUserDetail1($data['f_username']);
        //    print_r($user_detail);die;
        if (is_array($user_detail) && !empty($user_detail)) {
            $_SESSION['flash_message'] = 'Password is sent to your email id';
            $this->sendMail($user_detail);
        } else {
            $auth_attempt = FALSE;
            //$this->view->errorMessage = "Invalid username or password. Please try again.";
        }
        if (!$auth_attempt) {
            $participant = new Application_Model_ParticipantsLogin();
            $user_detail = $participant->getInfo($data['f_username']);

            if (is_array($user_detail) && !empty($user_detail)) {
                $_SESSION['flash_message'] = 'Password is sent to your email id';
                $this->sendMail($user_detail);
            } else {
                $_SESSION['flash_message'] = 'Invalid Email Id';
                $this->_redirect('index/login');
                //$this->view->errorMessage = "Invalid username or password. Please try again.";
            }
        }
    }

    public function sendMail($user_detail) {
        if ($user_detail['password'] != '') {
            $pword = $user_detail['password'];
            $name = $user_detail['real_name'];
            $id = $user_detail['user_id'];
        } else {
            $pword = $user_detail['participant_pword'];
            $name = $user_detail['participant_username'];
            $id = $user_detail['user_id'];
        }

        $participant = new Application_Model_HRMUserModel();
        $to = $user_detail['email'];
        $subject = "Forgot password";

        $message = "
<html>
<head>
<title>Forgot PAssword</title>
<style>
table>thead>th,table>thead>td{
padding:5px;
}
</style>
</head>
<body>
<p style='color:red; text-shadow: 1px 1px 3px #000;'>This email contains your forgotten password!</p>
<table border=1>
<tr>
<th>User Id</th>
<th>Name</th>
<th>Link</th>
</tr>
<tr>
<td>" . $id . "</td>
<td>" . $name . "</td>
<td><a href='" . $this->_base_url . "academic/index/login/key/" . $pword . "/user/" . $user_detail['user_id'] . "'>click here</a></td>  
</tr>
</table>
</body>
</html>
";
        $arr_data['frgt_status'] = 1;
        $participant->update($arr_data, array('user_id=?' => $id));

// Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
        $headers .= 'From: <no-reply@dmi.ac.in>' . "\r\n";
        mail($to, $subject, $message, $headers);
        $this->_redirect('index/login');
    }
    
    
    
    
    public function ajaxGetDashboardAction(){
        
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $cmn_terms = $this->_getParam("cmn_terms");
            $session = $this->_getParam("session");
            $department = $this->_getParam("department");  
            $department_model = new Application_Model_Department();
            $tabulation_model = new Application_Model_TabulationReport();
            $academic_model = new Application_Model_Academic();
            $student_model = new Application_Model_StudentPortal();
            $session_model = new Application_Model_Session();
            $dpartment_ids = $department_model->getByDepartmentType($department)['did'];
            $departmentAll = $department_model->getRecordByStream($department);
            $academic_ids  = explode(",",$academic_model->getAcademicId(explode(',',$dpartment_ids),$session)['batch_ids']);
            $total_active_studentin_terms = $tabulation_model->getTotalStudents($cmn_terms,$academic_ids);
            $total_pass_student_in_terms = $tabulation_model->getPassStudentscmnterms($cmn_terms,$academic_ids);
            $total_students               = $student_model->getStudenttotal($academic_ids)['total_students'];
              $newstudents  = $session_model->getTotalEnrollments($department,$session)['total_students'];
              $students =     $student_model->getDashBoardDetails($academic_ids,$cmn_terms);
//=====================================[Reference to chart 2]=======================================//
            $total_students_all          = $student_model->getStudenttotalAll($academic_ids);
            $pass_all    =    $tabulation_model->getPassAllStudentscmnterms($cmn_terms,$academic_ids);
              $fail_all =     $tabulation_model->getFailStudentscmnterms($cmn_terms,$academic_ids);
             $pass_data=array();
             $fail_data = array();
             $not_attended_data = array();
             $total_stu = array();
             $label=array();
            foreach($total_students_all as $key => $value){
                $label[] = $value['short_code'];
                $passzero = true;
                $failzero = true;
                $total_stu[] = $value['total_students'];
                foreach($fail_all as $key1=>$failarr){
                    if($value['academic_id']==$failarr['academic_id']){
                        $fail_data[] = (int)$failarr['fail_students'];
                        $failzero = false;
                    }else{continue;}
                        
                }
                    if($failzero)
                    $fail_data[] =0;
                  foreach($pass_all as $key2=>$passarr){
                    if($value['academic_id']==$passarr['academic_id']){
                        $pass_data[] = (int)$passarr['pass_students'];
                        $passzero =false;
                    }else{continue;}
                        
                }
                
                if($passzero)
                $pass_data[] =0;
            }

            $total_pass_fail = array_map(function () {
    return array_sum(func_get_args());
}, $pass_data, $fail_data);
$subtracted = array_map(function ($x, $y) { return $y-$x; } ,  $total_pass_fail,$total_stu);
$notattendeddata     = array_combine(array_keys($total_pass_fail), $subtracted);
//==========================================[END CHART 2 Refrence]==============================//


//==============================[My chart 1 refrence]==========================================//
$labelenrollment = array();
$enrollmentdata[] = array('Courses',  'Enrolled Students');
$title = "";
$all_new_student = $session_model->getTotalEnrollmentsallcourse($department,$session);
    foreach($departmentAll as $key => $value){
        $labelenrollment[]=$value['short_code'];
        $title = $value['department_type'];
        $enrollzero = true;
        foreach($all_new_student as $key => $new_students){
        if($value['short_code']==$new_students['short_code']){
            $enrollmentdata[] = array($value['short_code'],(int)$new_students['total_students']);
             $enrollzero = false;
        }else{continue;}
        if($enrollzero)
            $enrollmentdata[] = array($value['short_code'],0);
    }
}

//==================================[end chart 1]===============================================//


//==============================[My chart 3 refrence]==========================================//
$labeltopper = array();
$topperdata = array();
$htmlcourselimage = "";
$i =0;
            $resultTop = $tabulation_model->getTop($session,$cmn_terms,$department);
            $top_students = $tabulation_model->getTop3Students($cmn_terms,$academic_ids);
             foreach($top_students as $key => $value){
                 $filename = $value["filename"];
                 $htmlcourselimage.="<div class='item' style='width:33.3%;'><div class='panel panel-default' >";
                    $htmlcourselimage.="<div class='panel-heading' style='min-height:120px' ><span class='toppers'>".$value['short_code']."</span><img class='img-circle' style='width:100px; height:100px;z-index:1;'  src='".$value["filename"]."' alt='Los Angeles'></div>";
                    $htmlcourselimage.="<div class='panel-body' >";
                    $htmlcourselimage.="<b>SGPA</b> :".$value['sgpa'];
                 $htmlcourselimage.="</div></div></div>";
        $labeltopper[]=$value['stu_fname'];
        $topperdata[]=$value['sgpa'];
             }
$limitToplabel = array('Courses');
$limitDataMain = array();
            
foreach($departmentAll as $key => $value){
        $enrollzero = true;
        $limitData = array();
        $limitData[] = $value['short_code'];
        foreach($resultTop as $key => $toppers){
        if($value['short_code']==$toppers['short_code']){
            $limitToplabel[] = $this->addOrdinalNumberSuffix($toppers['row_number']);
            $limitToplabel = array_unique($limitToplabel);
            $limitData[] =(float)$toppers['sgpa'];
             $enrollzero = false;
        }else{continue;}
}
$x= count($limitData);
    for($i=1;$i<=(4-$x);$i++){
    $limitData[]=0;}
    $limitDataMain[]=$limitData;
} 
array_unshift($limitDataMain,$limitToplabel);
$topLimitData = $limitDataMain;
             
             
//==================================[end chart 3]===============================================//


//=========================================[Fees Table]=======================================//

$table = "";
foreach($students as $key => $value){
   
    $table.="<tr>";
    $table.="<td>".$value['stu_fname']."</td>";
    $table.="<td>".$value['department_type']."</td>";
    $table.="<td>".$value['department']."</td>";
    $table.="<td>".$value['batch_code']."</td>";
    $table.="<td>".$value['term_description']."</td>";
    $table.="<td>".$value['grand_term'.substr($value['cmn_terms'],strlen($value['cmn_terms'])-1)."_result"]."</td>";
    $table.="<td>".$value['paid_fee']."</td>";
    if($value['grand_term'.substr($value['cmn_terms'],strlen($value['cmn_terms'])-1)."_result"] == $value['paid_fee']){
        $table.="<td>Paid</td>";
    }
    else
    {
         $table.="<td style='background:red;color:#fff;'>UnPaid</td>";
    }
    $table.="</tr>";
}

//echo $table; die;
//======================================[end Fee Table]==================================//

            $details = array(
                    "card_details"=>array(
                            "total_courses"=>count(explode(',',$dpartment_ids)),
                            "active_students" => $total_active_studentin_terms['active_students'],
                            "inactive_students" => $total_students - $total_active_studentin_terms['active_students'],
                            "pass_students" => $total_active_studentin_terms['active_students'] > 0 ? number_format(($total_pass_student_in_terms['pass_students']/$total_active_studentin_terms['active_students'])*100,2):"0"."%",
                            "total_student" => $total_students,
                            "new_students" => empty($newstudents)?0:$newstudents,
                        ),
                        "student_details"=>array("passdata"=> $pass_data,
                        "faildata" => $fail_data,
                        "notattendeddata"=>$notattendeddata ,
                        "label"=> $label,
                            ),
                            "new_enrollements"=>array(
                                "label"=>$labelenrollment,
                                "data"=>$enrollmentdata,
                                "title"=>empty($title)?"NA":$title,
                                ),
                                "toppersdata"=>array(
                                    "toppers"=>$topLimitData
                                    ),
                                    "table"=>array(
                                        "html" => $table
                                        ),
                                        "toppercoursel"=>array(
                                            "html"=>$htmlcourselimage
                                            )
                );
                echo json_encode($details); die;
        }
        
        
    }

    public function maintainanceDashboardAction() {

        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        // action body 
        //Purchase 
        $ErpIndex_model = new Application_Model_ErpIndex();
        /* $this->view->PurchaseProformaCount = $ErpIndex_model->PurchaseProformaCount();
          $this->view->PurchaseCommercialCount = $ErpIndex_model->PurchaseCommercialCount();
          $this->view->PurchasePackingCount = $ErpIndex_model->PurchasePackingCount();
          $this->view->PurchaseOrderCount = $ErpIndex_model->PurchaseOrderCount();
          $this->view->PurchaseInvoiceCount = $ErpIndex_model->PurchaseInvoiceCount();
          //Sales
          $this->view->SalesProformaCount = $ErpIndex_model->SalesProformaCount();
          $this->view->SalesCommercialCount = $ErpIndex_model->SalesCommercialCount();
          $this->view->SalesPackingCount = $ErpIndex_model->SalesPackingCount();
          $this->view->SalesEnquiryCount = $ErpIndex_model->SalesEnquiryCount();
          $this->view->SalesQuotationCount = $ErpIndex_model->SalesQuotationCount();
          $this->view->SalesOrderCount = $ErpIndex_model->SalesOrderCount();
          $this->view->SalesInvoiceCount = $ErpIndex_model->SalesInvoiceCount(); */
        // Grn	
        //$Sales_order_model = new Application_Model_ErpSalesTyreOrderForm();
        //plant
        $this->view->PlantMaintenance = $ErpIndex_model->PlantMaintenance();
    }

    public function electivesDashboardAction() {
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        //$ErpIndex_model = new Application_Model_ErpIndex();
        $ElectiveSelection_model = new Application_Model_ElectiveSelection();
        $electives = $ElectiveSelection_model->getElectivesDashboard();
        //print_r(count($electives));die;
        $this->view->electives = $electives;
        //$this->view->purchase_quotation = $ErpIndex_model->PurchaseQuotationCount();
        //$this->view->PurchaseOrderCount = $ErpIndex_model->PurchaseOrderCount();
        //$this->view->PurchaseInvoiceCount = $ErpIndex_model->PurchaseInvoiceCount();
    }

    public function financeDashboardAction() {
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;

        $ErpIndex_model = new Application_Model_ErpIndex();
        $this->view->payments = $ErpIndex_model->paymentAmount();
        $this->view->deposits = $ErpIndex_model->depositAmount();
    }

    public function inventoryDashboardAction() {
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;

        $erp_items_master = new Application_Model_ErpItemsMaster();

        $erp_items_category_master = new Application_Model_ErpItemsCategoryMaster();

        $category = $erp_items_category_master->getCateId();

        $result = $erp_items_master->getRecords();

        $page = $this->_getParam('page', 1);

        $paginator_data = array(
            'page' => $page,
            'result' => $result
        );

        $categ_data = array(
            'page' => $page,
            'result' => $result
        );

        $this->view->paginator = $this->_act->pagination($paginator_data);

        $this->view->catdata = $this->_act->pagination($categ_data);
    }

    public function hrmDashboardAction() {
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $ErpIndex_model = new Application_Model_ErpIndex();
        $this->view->professors_count = $ErpIndex_model->Professorcount();
        $this->view->assisprofessors_count = $ErpIndex_model->AssistantProfessorcount();
        $this->view->drivercount = $ErpIndex_model->Drivercount();
        $this->view->officestaffcount = $ErpIndex_model->OfficeStaffcount();
        $this->view->labourcount = $ErpIndex_model->Labourcount();
    }

    public function getViewUrlAction() {
        $this->_helper->layout->disableLayout();
        $start_date = $this->_getParam('start_date');
        $batch = $this->_getParam('batch_id');
        $course_details = new Application_Model_Attendance();
        $max_version = $course_details->getMaxVersionOnDate($start_date, $batch);
        $id = $course_details->getId($start_date, $max_version);
        if (!empty($id['batch_schedule_id']) && !empty($max_version)) {
            echo $this->_base_url . "academic/batch-schedule/index/type/edit/id/" . $id['batch_schedule_id'] . "/version/$max_version";
            die;
        } else {
            echo '#';
            die;
        }
    }

    public function setErpUser() {

        if ($_POST['user']) {

            $_SESSION['admin_login']['admin_login']->id = $this->_getParam('user');
            $_SESSION['admin_login']['admin_login']->user_id = $this->_getParam('username');
            $_SESSION['admin_login']['admin_login']->real_name = !empty($this->_getParam('empl_name')) ? $this->_getParam('empl_name') : $this->_getParam('name');
            $_SESSION['admin_login']['admin_login']->role_id = $this->_getParam('access');
            $_SESSION['admin_login']['admin_login']->email = $this->_getParam('email');
            $_SESSION['admin_login']['admin_login']->empl_id = $this->_getParam('empl_id');
            $_SESSION['admin_login']['admin_login']->last_visit_date = date('Y-m-d h:i:s A', $this->_getParam('last_act'));
            $_SESSION['admin_login']['admin_login']->role_set = $this->_getParam('roles');
            $this->_authontication = $_SESSION['admin_login']['admin_login'];
        }
    }

    /* public function forgotPasswordAction() {
      $this->_helper->layout->setLayout("loginlayout");
      $users = new Application_Model_ErpAdmin();
      $form = new Application_Form_ForgotPassword();
      $this->view->form = $form;
      if ($this->getRequest()->isPost()) {
      if ($form->isValid($_POST)) {
      $data = $form->getValues();
      if ($result->isValid()) {
      //echo ''; die;
      $this->_redirect('index');
      } else {
      $this->view->errorMessage = "Invalid email. Please try again.";
      }
      }
      }
      }
     */

    public function getCourseAction($start_date = '') {
        $start_date = $this->_getParam('start_date');
        $term_id = $this->_getParam('term_id');
        /* $reqModel= new Application_Model_BatchSchedule();
          $section_model= new Application_Model_Section();
          $this->_helper->layout->disableLayout();
          $result = $reqModel->getRecordsByDate($start_date);

          foreach($result as $key => $value){

          if(!empty($value['section']))

          $result[$key]['name'] = $section_model->getRecordById($value['section']);

          }

          $page = $this->_getParam('page', 1);
          //echo '<pre>'; print_r($result);exit;
          $paginator_data = array(

          'page' => $page,

          'result' => $result

          );

          $this->view->paginator = $this->_act->pagination($paginator_data); */







        /* if($term_id==''){
          echo "<table class='table table-striped table-bordered mb30 jambo_table bulk_action' id='dataTable' style='width:100%;'>";

          echo "<thead>";
          //====[HEADING]======//
          echo "<tr>";
          echo "<th>Class I</th>";
          echo "<th> Class II</th>";
          echo "<th>Class III</th>";
          echo "<th>Class IV</th>";
          echo "<th>Class V</th>";
          echo "</tr>";
          echo "</thead>";
          //=====[TABLE BODY]======///
          echo "<tbody>";
          //========[TABLE DATE]===//
          echo "<tr>";
          echo "<th class='text-center' colspan='6'>No Record Found</th>";
          echo "</tr>";
          echo "</tbody>";
          echo "</table>";die;
          }

         */
        //$term_id=15;
        $course_details = new Application_Model_Attendance();
        $terms = new Application_Model_BatchSchedule();
        $section_master = new Application_Model_Section();
        $sections = $section_master->getSectionId($term_id);
        //  echo $start_date.' '.$term_id; exit;
        $allBatch = $terms->getAllBatch($start_date, $term_id);
        //echo '<pre>'; print_r($allBatch);exit;
        foreach ($allBatch as $batch) {
            foreach ($sections as $key => $value) {
                $max_version[$batch['batch']][] = $course_details->getMaxVersionOnDate($start_date, $batch['batch'], $value['id']);
            }
        }

        foreach ($max_version as $key => $value) {
            $result[$key] = $course_details->getCourseDetails($term_id, $key);
        }

        foreach ($result as $Items => $val) {
            foreach ($val as $Item => $value) {
                $course_id[$Items][$Item] = $value['course_id'];
            }
        }
        // echo '<pre>'; print_r($course_id);exit;
        $all_result = $this->getDateAction($course_id, $start_date, $max_version, $term_id, $sections);
        /* $page = $this->_getParam('page', 1);
          echo '<pre>'; print_r($all_result);exit;
          $paginator_data = array(

          'page' => $page,

          'result' => $all_result

          );

          $this->view->paginator = $this->_act->pagination($paginator_data); */die;
    }

    public function getDateAction($courses, $start_date, $max_version, $term_id, $sections) {

        //print_r($courses);exit;
        $date_details = new Application_Model_Attendance();
        $term = new Application_Model_TermMaster();
        $classMaster = new Application_Model_ClassMaster();
        $section = new Application_Model_Section();

        $terms_academic_names = array();
        // $version_id = $term->getMaxVersion($start_date);

        $no_of_classes = 0;

        foreach ($courses as $batch_id => $course_val) {
            $no_of_classes = $classMaster->getRecordByTermIdAndBatch($term_id, $batch_id);
            $new_arr = array_filter($max_version[$batch_id], function($value) {
                return $value != '';
            });
            $result[$batch_id] = $date_details->getAllDateDetails($term_id, $batch_id, $course_val, $start_date, $new_arr, $no_of_classes, $sections);
        }


        foreach ($max_version as $batch_id => $max_value) {
            $terms_academic_names[$batch_id] = $term->getTermOnDat1($term_id, $batch_id);
        }
        $arr = array();
        //echo "<pre>";print_r($result);exit;
        foreach ($result as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $arr[$key] = $value1;
            }
        }



        $class_records = $classMaster->getRecords();
        $class_records = $this->mergData($class_records, array('class_name'), count($class_records));


        echo "<table cellpadding='20' class='table table-striped table-bordered mb30 jambo_table bulk_action' id='dataTable' style='width:100%;'>";

        echo "<thead>";
        //====[HEADING]======//
        echo "<tr>";
        echo "<th>Section</th>";
        for ($i = 1; $i <= $no_of_classes; $i++) {
            echo "<th>" . $class_records[$no_of_classes - $i] . "</th>";
        }
        echo "<th>View More</th>";
        echo "</tr>";
        echo "</thead>";
        //=====[TABLE BODY]======///       
        echo "<tbody>";
        //========[TABLE DATE]===//
        // echo "<pre>";print_r($arr);exit;
        if (count($arr) > 0) {
            $j = 0;
            foreach ($arr as $Items => $val) {

                foreach ($val as $key => $Item_arr) {
                    $res = 0;
                    echo "<tr>";

                    foreach ($Item_arr as $Item_key => $Item) {
                        if ($res == 0) {
                            echo '<th>' . $section->getRecordById($Item['section']) . '</th>';
                            $res++;
                        }
                        $mycourse = explode('-', $Item['class']);

                        if (in_array($mycourse[0], $courses[$Items])) {
                            echo "<th ><a href='#' id='class" . $i . "' style='position:absolute; color:red; '>" . $mycourse[1] . "</a><span style='position:absolute; margin-left:12%; transform: rotate(-40deg)'>" . $Item['time'] . " </span></th>";
                        } else {
                            echo "<th ><a href='#' style = 'text-align:center;' id='class" . $i . "' >--</a></th>";
                        }
                    }
                    echo "<td><a href='" . $this->_base_url . "academic/batch-schedule/index/type/edit/id/" . $Item_arr[1]['batch_schedule_id'] . "/version/" . $Item_arr[1]['version'] . "/section/" . $Item_arr[1]['section'] . "'>Click Here</a></td>";
                    echo"</tr>";
                }
            }
            $j++;
        } else {
            echo "<tr><th class='text-center' colspan='6'>No Record Found</th></tr>";
        }
        echo "</tbody>";
        echo "</table>";
        die;
    }

    function numberToRomanRepresentation($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    //To check Login Attempt of a particular user in a day By: Kedar Date:09 Dec 2019
    public function ajaxCountLoginAttemptAction() {
        $userName = $this->_getParam("userName");
        $userPswd = $this->_getParam("userPswd");
        //echo "<pre>";print_r($userPswd); exit;
        $HRMModel_model = new Application_Model_HRMModel();
        if (!empty($userName)) {
            $updateDate = $HRMModel_model->UpdateLoginDate($userName);

            echo "<pre>";
            print_r($updateDate);
            exit;
        }die;
    }

    //End
    public function ajaxGetAcademicWithEmplAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $degree_id = $this->_getParam("degree_id");
            $empl_id = $this->_getParam("employee_id");
            //print_r($empl_id); die;
            $department_model = new Application_Model_Department();
            $result = $department_model->getRecordByEmplId($empl_id);
            // echo '<pre>'; print_r($result);exit;

            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                //echo '<pre>'; print_r($val);exit;
                echo '<option value="' . $val['id'] . '" >' . $val['department'] . '</option>';
            }
        }die;
    }
    
    
    public function ajaxGetDepartmentAction(){
         $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $cmn_terms = $this->_getParam("cmn_terms");
            $session = $this->_getParam("session");
    $department = new Application_Model_TermMaster();
    $department_details = $department->departmentOnTerm($cmn_terms,$session);
    
    foreach($department_details as $key => $value){
        echo "<option value='".$value["id"]."'>".$value['department_type']."</option>";
    }die;
    
        }
}
    
    //To Generate Captch code :21 August 2020:Kedar
    public function generate_captcha(){
        $random_num = md5(rand(10,100));
        $captcha_code = substr($random_num, 0, 6);
        return $_SESSION['Captcha']=$captcha_code;
    }

    public function captchaAction(){
        // Assign captcha in session
        $captcha_code=$this->generate_captcha();
        $layer = imagecreatetruecolor(168, 37);
        $captcha_bg = imagecolorallocate($layer, 247, 174, 71);
        imagefill($layer, 0, 0, $captcha_bg);
        $captcha_text_color = imagecolorallocate($layer, 0, 0, 0);
        imagestring($layer, 5, 55, 10, $captcha_code, $captcha_text_color);
        header("Content-type: image/jpeg");
        
        imagejpeg($layer);
        
    }
//End
}
