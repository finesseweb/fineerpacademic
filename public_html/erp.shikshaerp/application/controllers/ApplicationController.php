<?php

class ApplicationController extends Zend_Controller_Action {
    
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
    private $accessConfig =NULL;
    private $aeccConfig =NULL;
     private $_base_url = NULL;
    private $_sms = NULL;
    private $_mail = NULL;

    public function init() {

        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        require_once APPLICATION_PATH . '/configs/access_level.inc';
        //require_once APPLICATION_PATH . '/public/Atompay/TransactionRequest.php';k
        //require_once APPLICATION_PATH . '/public/Atompay/TransactionResponse.php';
        require_once APPLICATION_PATH . '/public/atompay/TransactionRequest.php';
        require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
        include_once 'ErrorController.php';               
        $this->accessConfig = new accessLevel();
          $this->_mail = $zendConfig->config_mail->toArray();

        $config = $zendConfig->mainconfig->toArray();
        $this->_sms = $zendConfig->sms->toArray();

        $this->view->mainconfig = $config;
		
        $this->_base_url = $config['host'];
		$this->_main_url = $config['erp'];
        $this->_action = $this->getRequest()->getActionName();

        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
         $this->aeccConfig = $config_role = $zendConfig->aecc_course->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $this->login_storage = $data = $storage->admin_login;
        $this->view->login_storage = $data; 

        if (isset($data)) {
            $this->view->role_id = $data->role_id;
            $this->view->login_empl_id = $data->empl_id;
        }
//        if( $this->_action == "login" || $this->_action == "forgot-password"){
//
//      
//            $this->_helper->layout->setLayout("adminlogin");
//        }
//        
//         else{
//
//            $this->_helper->layout->setLayout("layout");
//        }
            
         $this->_helper->layout->setLayout("applicationlayout");


        $this->_flashMessenger = $this->_helper->FlashMessenger;
//        $this->authonticate();

        $this->_act = new Application_Model_Adminactions();

        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    protected function authonticate() {

        $storage = new Zend_Session_Namespace("admin_login");

        $data = $storage->admin_login;
        if ($data->role_id == 0)
            $this->_redirect('student-portal/assignments');
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
        server_responce_code(404);
         die();
        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'application';
        $this->accessConfig->setAccess('SA_ACAD_APPLICATION_FORM');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $assignment_model = new Application_Model_Application(); 
        $assignment_form = new Application_Form_Application();
        $student_assignment_model = new Application_Model_SubmitAssignment();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $assignment_form;

        switch ($type) {       
            case "add":
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {


                        $courseb = implode(',', $this->getRequest()->getPost('coursebBox'));
                        $course = implode(',', $this->getRequest()->getPost('courseBox'));
                        $date = str_replace('/', '-', $this->getRequest()->getPost('dob_id'));
                       
                        $data = array('stu_id' => $this->getRequest()->getPost('stu_id'),
                            'batch_id' => $this->getRequest()->getPost('acad_id'),
                            'stu_name' => $this->getRequest()->getPost('stu_name_id'),
                            'dob' => date('Y-m-d',strtotime($date)),
                            'course_id' => !empty($course)?','.$course.',':null,
                            'course_id_b' => !empty($courseb)?','.$courseb.',':null,
                            'course_fee' => $this->getRequest()->getPost('course'),
                            'course_fee_b' => $this->getRequest()->getPost('course_b'),
                            'term_id' => $this->getRequest()->getPost('term_id'),
                            'term_id_b' => $this->getRequest()->getPost('term_b_id'),
                            'total_fee' => $this->getRequest()->getPost('total_fee'),
                            'updated_date' => date('Y-m-d')
                        );
                        $result =  $assignment_model->getRecordsByBatch($data['stu_id'],$data['batch_id'], $data['term_id'], $data['term_id_b']);
                        if($result['res'] == 0 && $result['res2'] == 0 ){
                        $last_insert_id = $assignment_model->insert($data);
                        $_SESSION['message_class']='alert-success';
                        $message = 'Application form has successfully submitted';
                        }
                        else {
                                $_SESSION['message_class']='alert-danger';
                                $message ='Application form already exists';
                            }

                        $this->_flashMessenger->addMessage($message);

                        $this->_redirect('application/index');
                    }
                }


                break;
            case 'edit':
                $result = $assignment_model->getRecord($ec_id);
                $result['academic_year_id'] = $result['batch_id'];
                $result['dob'] = date('d/m/Y',strtotime($result['dob']));
                $_SESSION['application']['course_id'] = $result['course_id'];
                $_SESSION['application']['course_id_b'] = $result['course_id_b'];
                $_SESSION['application']['course'] = $result['course_fee'];
                $_SESSION['application']['course_b'] = $result['course_fee_b'];
                $_SESSION['application']['term_id'] = $result['term_id'];
                $_SESSION['application']['term_id_b'] = $result['term_id_b'];

                $assignment_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {
                          $courseb = implode(',', $this->getRequest()->getPost('coursebBox'));
                        $course = implode(',', $this->getRequest()->getPost('courseBox'));
                        $date = str_replace('/', '-', $this->getRequest()->getPost('dob_id'));
                        $data = array('stu_id' => $this->getRequest()->getPost('stu_id'),
                            'batch_id' => $this->getRequest()->getPost('acad_id'),
                            'stu_name' => $this->getRequest()->getPost('stu_name_id'),
                            'dob' => date('Y-m-d',strtotime($date)),
                            'course_id' => !empty($course)?','.$course.',':null,
                            'course_id_b' => !empty($courseb)?','.$courseb.',':null,
                            'course_fee' => $this->getRequest()->getPost('course'),
                            'course_fee_b' => $this->getRequest()->getPost('course_b'),
                            'term_id' => !empty($this->getRequest()->getPost('term_id'))?$this->getRequest()->getPost('term_id'):0,
                            'term_id_b' => !empty($this->getRequest()->getPost('term_b_id'))?$this->getRequest()->getPost('term_b_id'):0,
                            'total_fee' => $this->getRequest()->getPost('total_fee'),
                            'updated_date' => date('Y-m-d')
                        );
                        
                        $assignment_model->update($data, array('application_id=?' => $ec_id));
                    }
                    $_SESSION['message_class']='alert-success';
                    $this->_flashMessenger->addMessage('Application Form has Successfully updated !');
                    $this->_redirect('application/index');
                }



                break;
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('application/index');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result1 = array();
                $result = $assignment_model->getRecords();
                $i = 0;
                foreach ($result as $key){
                    
          
                    $result[$i]['batch_id'] = $assignment_model->getAcademic1($key['batch_id']);
                    $term_id = $key['term_id'];
                    $result[$i]['term_id'] = $assignment_model->getTerm($key['term_id']);
                    $result[$i]['dept'] = explode('-', $result[$i]['batch_id'])[0];
                    $result[$i]['total'] = $assignment_model->getTotal($key['batch_id'],$key['term_id']);
                                  $result1[$i]['result'] =  $assignment_model->getRecordsPdm($key['batch_id'],$term_id);
                                  foreach($result1[$i]['result'] as $key1 => $value1){
                                     $result1[$i]['result'][$key1]['term_id'] = $assignment_model->getTerm($value1['term_id']);
                                     $result1[$i]['result'][$key1]['term_id_b'] = $assignment_model->getTerm($value1['term_id_b']);
                                    
                                  }
                    $i++;
             
                }
                
            // echo "<pre>";print_r($result);exit;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->subtable = $result1 ;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
/**********************************************************************Raushan********************************************************************************************************************************************************************** */  
 
//-----  Collegiate section Add by raushan  -------// 
 public function examformAction() {
       
       
       // http_response_code(404); die;
      
        $this->view->action_name = 'examform';
        $this->view->sub_title_name = 'examform';
        $this->accessConfig->setAccess('SA_ACAD_EXAM_FORM');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();     
       // $assignment_form = new Application_Form_AdmitCardView();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        $attendace_report = new Application_Model_BatachSemesterAttendance();
     ///   $examdate_model = new Application_Model_ExamDateModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $examsubmit = new Application_Model_ExamformSubmissionModel();
        $academic_det = new Application_Model_Academic();
        $term_obj = new Application_Model_TermMaster();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;
         $otpmodel = new Application_Model_OtpModel();
        switch ($type) {   
            case "add":
                //http_response_code(404); die;
            //$assignment_model->populate($result);
             $assignment_form = new Application_Form_AdmitCardView();
             $this->view->form = $assignment_form;

                if ($this->getRequest()->isGet()) {
                    if (isset($_GET)) {
                      // print_r($_GET);
                      
                    $stu_id=trim($_GET['stu_id']);
                    $sem = trim($_GET['sem']);
                    
                      if(strlen($sem)>1 && $sem != 't1'){
                            $term = explode('t',$sem);
                            $prevsem = 't'.((int)$term[1]-1);
                           $prevExamFormDetails =  $examsubmit->isPaymentExist($stu_id,$prevsem);
                           if($prevExamFormDetails['payment_status']==0 || $prevExamFormDetails['payment_activation']==0){
                                 $this->_refresh(3,'/academic/application/examform/type/otpverify','Your Are Not Allowed . Please Consult your college Department..!');
                           }
                        }
                    
                        
                    $payinform= $examsubmit->getPaymentactivefid($stu_id,$sem);
                      //print_r($payinform);
                        //die();
             // $payinform = $examsubmit->isPaymentExist($stu_id,$sem);
                    if($payinform['payment_status']=='1' && $payinform['payment_activation']=='1'){
                     
                         $this->_redirect('application/admitcard/');
                       // $this->redirectExamform('Your form submission and payment are successfull. you can download your admitcard','admitcard');
                        // $this->redirectExamform('After the announcement of End Semester Examination schedule, College Office will provide the Admit Card for the students of Semester-III and V. The examination schedule will be announced after the lockdown.');
                    }
                   if($payinform['payment_activation']=='1'){
                       $this->_redirect('application/payment/?fid='.$_GET['stu_id'].'');
                   }
                        //http_response_code(404); die;
                    $checkk=$attendace_report->checkRecordByUid($stu_id,$sem); 
                  //echo   count($checkk);
                    //  die();
                    
                     if(empty($checkk) || count($checkk)<0)
                   {     
                       $this->_refresh(5,'/academic/application/examform/type/otpverify',"Your Attendance Not prepared , Please Contact Your concerned Deparment !");
                    
                   } 
                  
                   else {
                       
                      // if($checkk['0']['degree_id']>1){
                        //  $this->redirectExamform('Your Academic not prepared Please try  later','pgexam'); 
                      // }
                     //  die();
                    $checkallow=$attendace_report->checkStuAllowedByUid($stu_id,$sem);  
                    $this->view->stu_allow = $checkallow;
                   
                    
            
                    $result = $assignment_model->getStudenFullInfo($stu_id);
                    
                   
                    $this->view->acd_id = $result['academic_id'];
                    $this->view->stu_fname = $result['stu_fname'];
                    $this->view->stu_lname = $result['stu_lname'];
                    $this->view->s_id = $result['session_id'];
                    $this->view->fid = $result['stu_id'];
                    $this->view->st_id = $result['student_id'];
                    
                 
                   $acad_term_arr['batch_id'] = $result['academic_id'];
                   $acad_term_arr['cmn_terms'] = $checkk[0]['cmn_terms'];	
                  // echo "<pre>"; print_r($acad_term_arr);die;
                    //  if(!$checkk || empty($checkk[0]['cmn_terms']) || empty($result['academic_id'])){
                    //     $this->redirectExamform("Attendance not prepared !");
                    //      //server_responce_code(500);
                    //     // die();
                    //     $this->redirectExamform("Attendance not prepared !");
                    // }
                    //==only for this pandamic 
                //   $getterm_details =  $term_obj->getTermRecordsbycmn($acad_term_arr['batch_id'],$acad_term_arr['cmn_terms']); 
                
                 $getterm_details =  $term_obj->getTermRecordsbycmn($acad_term_arr['batch_id'],$sem);
                   $acad_term_arr['term_id'] = $getterm_details;
                   
                   $examdate = $fee_model->getExamdatecolRecord($result['academic_id'],$sem);
                   //print_r($examdate['exam_date']);
                   //die();
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s college';
                    $result['examination'] = date('M-Y',strtotime($examdate['exam_date']));
                     
                     $this->view->exmdate=$result['examination']; 
                    $dept_id = $academic_det->getRecord($acad_term_arr['batch_id']);
                    //=======comented on 16/2019
                   // $dept_id = $dept_model->getRecord($acad_term_arr['batch_id']);
                    $dept_id['id'] = $dept_id['department'];
                    $fee_pay = $fee_model->getRecord($dept_id['id']);
                    
                    
                     //print_r($fee_pay);
                     //die();
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
                    // $fee_pay['feeForm_start_date'];
                     // $fee_pay['feeForm_end_date']; 
                     // $fee_pay['feeForm_extended_date']; 
                     //die();
                     
                     
                     if( $currentdate  >= $fee_pay['feeForm_start_date']  &&  $currentdate <= $fee_pay['feeForm_end_date'])
                     {
                        $this->view->examfeena = 'NA';
                      //echo "NA";
                      //die();
                         
                     }
                     else
                     {
                         $oneday = 24*60*60;
                      
                      $diff = (strtotime($fee_pay['feeForm_extended_date']) - strtotime($fee_pay['feeForm_end_date']));
                      
                      $diff1 = $diff/$oneday;
                      $multi = $fee_pay['fineFee'] * $diff1;
                       $this->view->examfeena = $multi;
                      //echo  round($diff1);
                       //die();
                         
                     }
                      $this->view->totalfee = $multi + $fee_pay['examFee'] ;
                      $this->view->extdate = $fee_pay['feeForm_extended_date'] ;
                    
                      $payctive = $examsubmit->getRecordbyfid($stu_id);
                      $this->view->payactivaion = $payctive[0]['payment_activation'] ;
                      $this->view->termid = $payctive[0]['term_id'] ;
                      //print_r($payctive[payment_activation]);
                      //die();
                   $this->view->course_cat = $core_course_name_arr;
                   $this->view->ge_arr = $ge_arr;
                   $this->view->core_details_arr = $course_details_arr;
                   $this->view->std_name = $result['stu_fname']. ' ' .$result['stu_lname'];
                   $this->view->exam_roll = $result['exam_roll'];
                   $this->view->stu_pic_path = $result['filename'];
                   $this->view->course_val = $selectge;
                   $this->view->ge_id = $this->aeccConfig[0];
                   
                   $this->view->semester = $term_details[0]['term_name'];
                   
                   
                   $result['semester'] = $term_details[0]['term_name'];
                   $result['registration_no'] = $result['reg_no'];
                    //echo "<pre>"; print_r($result['session_id']);exit;
                     //$course_details_arr  = $course_learning->getcourseTypeOn($acad_term_arr['batch_id'],$acad_term_arr['term_id']); 
                  //$course_details_arr = $this->geStudentCourse($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                 
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $exam_schedule;
                   //$this->view->coursestu = $course_details_arr;
                  //echo "<pre>";  print_r($exam_schedule);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                   
                   
                    $assignment_form->populate($result);
                    // print_r($result);
                    // die();

                       // $this->_flashMessenger->addMessage($message);

                      
     
                   }
                    }
                }


                break;
                
            case 'save':
                $examsubmit_model = new Application_Model_ExamformSubmissionModel();
                $examform = new Application_Form_AdmitCardView();
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {
                      //  print_r($_POST);
                       //die();
                        $attendanceInfoData = array(
                           'student_id' => $_POST['st_id'],
                           'year_exam' => $_POST['year'],
                           'session_id' => $_POST['session_id'],
                           'academic_year_id' => $_POST['academic_id'],
                           'term_id' => $_POST['semester'],
                           'stu_name' =>$_POST['stu_name'],
                           'fname' =>$_POST['father_fname'],
                           'date_of_birth' => $_POST['stu_dob'],
                           'reg_no' => $_POST['registration_no'],
                           'examination_id' => $_POST['exam_roll'], 
                           'examination_name' => $_POST['examination'],
                           'college_name' => $_POST['college'],
                           'cc_paper_name' => $_POST['paper_name'][0],
                           'ge_paper_name' => $_POST['paper_name'][1],
                           'aecc_paper_name' => $_POST['paper_name'][2],
                           'created_date' => date('Y-m-d'),
                           'u_id' => $_POST['f_id'],
                           'email' => $_POST['email'],
                           'phone' => $_POST['phone'],
                           'payment_activation' => 1
                          
                        );
                       
                        
                         $last_insert_id = $examsubmit_model->insert($attendanceInfoData);
                         $this->_flashMessenger->addMessage('Attendance Successfully added');
                        $this->_redirect('application/payment/?fid='.md5($_POST['f_id']).'');
                        //print_r($_POST);
                        //die();  
                    }
                    }
                break;
            case 'otpverify':
                 $semdata=  $fee_model->getSemRecord();
                 $this->view->showsem=$semdata; 
                if ($this->getRequest()->isPost()) {
                  if(isset($_POST)){
                    
                      $uid= $_POST['stu_id'];
                      $otp = $_POST['otp'];
                      $semester = $_POST['semester'];
                     
                     $res = $otpmodel->getVerifyotp($uid,$otp,$semester);
                     // print_r($res);die(); 
                      if(!$res || $res['academic_id']<=0){
                      $this->_refresh(5,'/academic/application/examform/type/otpverify',"Invalid Form ID OR You have Choosen Wrong Semester !");
                      }
                      
            $feeitems = new Application_Model_FeeStructureTermItems();
            $sem_fees = new Application_Model_FeesCollection();
                           $feeStructure = new Application_Model_FeeStructure();
                           $acad_id = empty($res['academic_id'])?0:$res['academic_id'];
                          // echo $acad_id; die;
         $examdate=  $fee_model->getEnddate($semester,$res['academic_id']);
         $struct_id = $feeStructure->getStructId($acad_id);
          
           if(!$struct_id){
               
            $this->_refresh(5,'/academic/application/examform/type/otpverify',"Fee not Prepared..or wrong otp !");
     }
         $payrecord =$sem_fees->getPayRecords(trim($uid),$semester);  
        $fee = $feeitems->getFee($struct_id,$semester);
          $fee = $this->mergData($fee,array('totalfee'),count($fee));
        $fee = array_filter($fee,function($value){return ( $value !== null && $value !== false && $value !== '' && $value !=0 );});
        $totfee = 0;
        $paidfee = array();
                  foreach($payrecord as $key => $pay) {
                       $sem2= array_sum($fee);
                      $totfee+=(int)$pay['fee'];
                      $type = $pay['type'];
                      $this->view->ftype = $type;
                      $paidfee[$key] = $pay['fee']."-".$pay['prod']."-".$pay['description'];
                  }
           
                  $due = $sem2-$totfee;
                 
                    $flag = false; 
                   // print_r($examdate);
                    //die();
                    
                   
                    if(!empty($examdate)){
                        
                        
                        if($examdate['feeForm_extended_date']!='0000-00-00'){
                          if(date('Y-m-d')>=$examdate['feeForm_start_date'] && date('Y-m-d')<=$examdate['feeForm_extended_date']) {  
                           $flag = true;
                              
                        }else {
                           $this->_refresh(5,'/academic/application/examform/type/otpverify','Form filling not started for this Academic! OR End Date');
                    
                        }
                        } else {
                            //echo date('Y-m-d');
                           
                        if(strtotime(date('Y-m-d'))>=strtotime($examdate['feeForm_start_date']) && strtotime(date('Y-m-d'))<=strtotime($examdate['feeForm_end_date'])){
                            
                            $flag = true;  
                        }
                        else {
                             $this->_refresh(5,'/academic/application/examform/type/otpverify','Form filling not started for this Academic! OR End Date');
                 
                           
                        }
                            
                        }
                    }
                    else {
                        $this->_refresh(5,'/academic/application/examform/type/otpverify','Form filling not started for this Academic! OR End Date');
                  
                    }
                    //die();
                                preg_match('/^F-2018-\d{1,}/', $uid, $output_array);
                                    
                                    if((!empty($output_array[0]) && $semester =='t6') ){
                                        if($acad_id>=25 && $acad_id <=49){
                                            $flag = true;
                                            
                                        }
                                    }
                                    else{
                                        preg_match('/^F-2019-\d{1,}/', $uid, $output_array); 
                                       
                                          if(!empty($output_array[0]) && $semester =='t4'){
                                              
                                              if(in_array($acad_id,array(50,52,54,56,58,60,62)) ||($acad_id>=1 && $acad_id <=24)){
                                            $flag = true;
                                        }
                                          }
                                        
                                    }
                                    
                                   
                     if($flag == true){
                     if($res && $due<=0){
                         //echo "hii"; die();
                        $this->_redirect('application/examform/type/add?stu_id='.md5($_POST['stu_id']).'&sem='.$semester);
                    
                     }
                      else {
                          
                          if(!$res){
                                      $this->_refresh(5,'/academic/application/examform/type/otpverify','Invalid OTP OR You have Choosen Wrong Semester');
                          }
                          else
                          {
                             
                          $this->_refresh(5,'/academic/application/examform/type/otpverify','You have <span style="color:red">₹'.$due." Fee Due</span>  please Consult your college department !");
                          }
                      }
                  }
                  else{
                      $this->_refresh(5,'/academic/application/examform/type/otpverify','Form filling not started for this Academic!');
                  }
                  }
                }
                break;
                
                
                case 'dashboard':
                 
                if ($this->getRequest()->isGet()) {
                  if(isset($_GET)){
                      $uid= trim($_GET['stu_id']);
                       $this->view->sid=$uid;
                     $payinform= $examsubmit->getPaymentRecordbyfid($uid);                     
                  $this->view->payment_status=$payinform;
                  
                  
                     
                  }
                }
                break;
                
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('application/index');
                }
                break;
            default:
               $this->_redirect('application/examform/type/otpverify');
                 if ($this->getRequest()->isPost()) {
                 if (isset($_POST)) {
                     $rand= rand(999,10000);
                     $stunum = $assignment_model->getStudentM($_POST['stu_id']);
                    // $num =  $stunum['father_mobileno'];
                    $num=$_POST['phone'];
                    $email= $_POST['email'];
                     $insert = array(
                        'u_id' => $_POST['stu_id'],
                        'otp' => $rand,
                        'create_date' => date('Y-m-d')
                     );
                     
                    //===============sms function ======================// 
                    if($this->_sms['default'] == 0){
                    $phonenum = $num;
                    }
                    else{
                        $phonenum = $this->_sms['default'];
                    }
                    $phonenum = $num;
                    $message = 'Dear Student, you need an OTP to access the application form. The OTP is '.$rand;
                    $debug = $this->_sms['debug'];

                    $this->SMSSend($phonenum,$message,$debug);
                    //=====================[END]=====================//
                    
                    
                    //=====================[Email Function]=====================//
                     $to = $email;
                     $subject = "OTP For Form Verification";
                     $txt = $message;
                     $headers = "MIME-Version: 1.0" . "\r\n";
                     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                     $headers = "From: no-reply@pwcadmissions.in" . "\r\n" ;
                     mail($to,$subject,$txt,$headers);
            
                    //=====================[END]=====================//
                    
                      $rereundata= $otpmodel->getRecordsbyuid($_POST['stu_id']);
                       if($rereundata){ 
                           $data['otp']=$rand;
                           $otpmodel->update($data,array('u_id=?'=> $_POST['stu_id']));
                       }
                       else {
                      $last_insert_id = $otpmodel->insert($insert);
                       
                       }
                        $sms = $this->mobverification($rand,$num);
                       
                      $this->_redirect('application/examform/type/otpverify');
                      
                 }
                 }
              $messages = $this->_flashMessenger->getMessages();
              $_SESSION['admin_login']['admin_login']->flash_error = 0;
              $this->view->messages = $messages;
        }
    }

public function admitcardAction() {
    // http_response_code(500);
    // die();
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        $student_exam_form = new Application_Model_ExamformSubmissionModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

        switch ($type) {       
            case "view":
         
             $assignment_form = new Application_Form_AdmitCardView();
             $this->view->form = $assignment_form;
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {
                       // print_r($_POST);
                       // die();
                    $stu_id = md5(trim($_POST['stu_id'])); 
                    
                    //  $getadmitcardstatus = $examactivemodel->getRecordByTerm($_POST['semester']);
                    //print_r($getadmitcardstatus);
                    //die();
                  // if(empty($getadmitcardstatus)){
                  //    $this->_refresh(5,'/academic/application/admitcard/','Admitcard Download Not Active OR Deactive it. Please Consult your college Department..!');
                         
                   //}    
                //   $getvalue = $allow_model->getRecordforadmit($_POST['stu_id'],$_POST['semester']);
                //   if(empty($getvalue)){
                //       echo "<script>
                //           alert('You enter Invalid Form Id OR you Choose Wrong Semester');
                //           window.location.href='application/admitcard';
                //           </script>"; 
                //       die();
                //   ///}
                    $this->view->fid = $_POST['stu_id'];
                   // $result =$student_exam_form->getRecordbyfid($stu_id);
                    $result =$student_exam_form->getPaymentRecordbyfid($stu_id,$_POST['semester']);
                    $checkk=$allow_model->checkRecordByUid($stu_id,$_POST['semester']); 
                    // echo "<pre>"; print_r($result);exit;
                    $this->view->stu_allow = $checkk;
            
                   $stu_image = $assignment_model->getImage($stu_id);
                   if($result=='')
                   {     
                       
                        $this->redirectExamform('You not submit your fill your form or payment Due');
                     
                   } 
                   
                   else {
                    $updatestaus= $student_exam_form->update(array('status'=> 1), array('u_id=?' => $_POST['stu_id'],'payment_status=?'=>1,'status=?'=>0))  ;
                   $sem_id= $allow_model->getTermIdByFid($_POST['stu_id'],$result['academic_year_id']);
                  //echo "<pre>"; print_r($sem_id);exit;
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                    $this->view->semester = $sem_id[0]['term_name'];        
                   //echo $result['payment_status'] ; die();
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                   $term_obj = new Application_Model_TermMaster();
                   $term_details =  $term_obj->getTermRecords($acad_term_arr['batch_id'],$acad_term_arr['term_id']);
                  $examdate = $fee_model->getExamdatecolRecord($result['academic_year_id'],$_POST['semester']);
                 //print_r($examdate);
                   ///  die();
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =  date("M-Y", strtotime($examdate['exam_date'])); 
                     $this->view->exmdate=$result['examination'];
                   // $result['examination'] =   Date('M').'-'.$result['exam_year']  ;
                    if(!$ac_details['department']){
                        echo "Please Fill the form first ";die;
                    }
                    $dept_id = $dept_model->getRecord($ac_details['department']);    
                    $fee_pay = $fee_model->getRecordByDepartment($dept_id['id']);
                     
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
                    
                    
                     
                    /*
                        * stu_id[primary key id] 
                        *   batch_id[id]
                        *   term_id[id]
                        *
                        *
                        */
                     
                   $course_details_arr  = $course_learning->getcourseTypeOn($acad_term_arr['batch_id'],$acad_term_arr['term_id']); 
                  $course_details_arr = $this->geStudentCourse($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                    $core_course_name_arr = $this->mergData($course_details_arr, array('cc_name'), count($course_details_arr));
                    $ge_arr = $this->mergData($course_details_arr, array('ge_id'), count($course_details_arr));
                       
                               
                    
                       $core_course_name_arr = array_values(array_unique($core_course_name_arr));
                       $ge_arr = array_values(array_unique($ge_arr));
                         $selectge = $elective_selection->getCouseDetailByStudentId($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                   $this->view->course_cat = $core_course_name_arr;
                   $this->view->ge_arr = $ge_arr;
                   $this->view->core_details_arr = $course_details_arr;
                   //$this->view->std_name = $result['stu_fname']. ' ' .$result['stu_lname'];
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->course_val = $selectge;
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   
                 
                   $this->view->semester = $term_details[0]['term_name'];
                   if(!$ac_details || !$ac_details['department']){
                       echo "please fill you form first";die;
                   }
                   $this->view->dept_id =$dept_model->getRecord($ac_details['department'])['department'];
                  // echo "<pre>"; print_r($ac_details);exit;
                   $result['exam_roll'] =$result['examination_id'];
                   $result['semester'] = $term_details[0]['term_name'];
                   $result['registration_no'] = $result['reg_no'];
                 
                   //echo "<pre>"; print_r($result['session_id']);exit;
                   
                   //echo $acad_term_arr['batch_id'];
                  // die();
                   
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                   
                  //echo "<pre>";  print_r($course_details_arr);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    $paymentdesc = new Application_Model_ExamfeeSubmitModel();
                    $fetchdetail = $paymentdesc->getRecordbyfid($stu_id,$_POST['semester']);
                    //echo "<pre>"; print_r($_POST['semester']);exit;
                     $this->view->payment_details = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                    
                    
                    $assignment_form->populate($result);
                  
                      
                   }

                    }
                }


                break;
          
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('application/index');
                }
                break;
            
            default:
             $admit_form = new Application_Form_AdmitCard();
             $semdata=  $fee_model->getSemRecord();
             $this->view->showsem=$semdata; 
            //$semdata=  $examactivemodel->getSemcolRecord();
            //     $this->view->showsem=$semdata; 
                break;
        }
    }
   
 //-----   Collegiate section Add by raushan  -------//  
 
 /**********************************************************************Raushan********************************************************************************************************************************************************************** */  
 //-----  Non Collegiate section Add by raushan  -------// 

    
public function noncollegiateAction() {
        
        //  http_response_code(404); die;
      //  echo "server issue";die;
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
       
       // $assignment_form = new Application_Form_AdmitCardView();
        $admit_form = new Application_Form_AdmitCard();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        $otpmodel = new Application_Model_OtpModel();
        $nonmodel =  new Application_Model_NonCollegiateModel(); 
        $backmodel =  new Application_Model_BackSelectionItems(); 
         $fee_model = new Application_Model_Coursefee();
        $term_obj = new Application_Model_TermMaster();
        $acd_obj = new Application_Model_Session();
        $student_model = new Application_Model_StudentPortal();
        $stu_obj = new Application_Model_StudentPortal();
        $examsubmit = new Application_Model_UgNonformSubmissionModel();
        
        switch ($type) {      
            case "dev0a130":
                
               // http_response_code(404);die;
            break;
            case "add":
            //$assignment_model->populate($result);
             $assignment_form = new Application_Form_AdmitCardView();
             $this->view->form = $assignment_form;

                if ($this->getRequest()->isGet()) {
                    if (isset($_GET)) {
                        //print_r($_POST);
                        //die();
                    $stu_id=trim($_GET['stu_id']);
                     $sem=trim($_GET['sem']);
                   
                    
                    $checkk=$backmodel->getStuRecords($stu_id,$sem); 
                   // print_r($checkk);
                     //  die();
                    if(!$checkk){
                       $this->_refresh(5,'/academic/application/noncollegiate/type/otpverify','Wrong Form or Semester');
                    
                    }
                    
                    $details_stu = $student_model->getStudenInfoByU($stu_id);
                    $acad_id = $details_stu['academic_id'];
                    $term_ids = $term_obj->getValidateTermNameById($checkk['0']['terms']);
                    // print_r($term_ids);
                      //  die();
                   $payinform = $examsubmit->checkPaymentRecord($stu_id,$sem);
         //  echo $stu_id;die;
                   if($payinform['payment_status']=='1' && $payinform['payment_activation']=='1'){
                        $this->_redirect('application/admitcardnoncollegiate/');
                   }
                   elseif($payinform['payment_activation']=='1'){
                       $this->_redirect('application/ugnonpayment/?fid='.$_GET['stu_id']);
                   }
                    //http_response_code(404); die;
                   $term_details =  $term_obj->getValidateTermNameById($checkk[0]['terms']);
                   $aca_details = $acd_obj->getRecord($checkk[0]['session']);
                   $stu_details = $stu_obj->getStudenInfoByU($stu_id);
                   $this->view->stu_details =$stu_details;
                   $this->view->checkk =$checkk;
                       // die();
                  
                   $this->view->term_details =  $term_details;
                   $this->view->aca_detail =  $aca_details;
                    $result['year'] = 'Dec '.date('Y'); 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('M').'-'.date('Y'); 
                    $result['college'] = 'Patna Women\'s college';
                     $examdate = $fee_model->getExamdateNoncolRecord($checkk[0]['academic_id'],$sem);
                   
                    $result['examination'] =  date("M-Y", strtotime($examdate['exam_date'])); 
                     $this->view->exmdate=$result['examination'];
                    $result['session'] = $aca_details['session'];
                    $result['registration_no'] =$checkk[0]['reg_no'];
                    $result['exam_roll'] = $checkk[0]['exam_roll'];
                    $result['stu_dob'] = $stu_details['stu_dob'];
                     $this->view->fid= $stu_details['stu_id'];
                    $result['father_fname'] = $stu_details['father_fname'];
                    
                    $this->view->stu_name = $checkk[0]['stu_fname'].' '.$checkk[0]['stu_lname'];
                    
                    $this->view->course_name = $checkk[0]['course_id'];
                    $this->view->s_id = $checkk[0]['session'];
                    $this->view->acd_id = $checkk[0]['academic_id'];
                    $this->view->st_id = $stu_details['student_id'];
                    // print_r($result);
                   //die();
                    //$this->view->st_id = $result['student_id'];
                   
                   // $dept_id = $dept_model->getRecord($acad_term_arr['batch_id']);
                    
                    //$fee_pay = $fee_model->getRecord($dept_id['id']);
                     //print_r($fee_pay);
                     //die();
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
                    // $fee_pay['feeForm_start_date'];
                     // $fee_pay['feeForm_end_date']; 
                     // $fee_pay['feeForm_extended_date']; 
                     //die();
                     
                     
                     if( $currentdate  >= $fee_pay['feeForm_start_date']  &&  $currentdate <= $fee_pay['feeForm_end_date'])
                     {
                        $this->view->examfeena = 'NA';
                      //echo "NA";
                      //die();
                         
                     }
                     else
                     {
                         $oneday = 24*60*60;
                      
                      $diff = (strtotime($fee_pay['feeForm_extended_date']) - strtotime($fee_pay['feeForm_end_date']));
                      
                      $diff1 = $diff/$oneday;
                      $multi = $fee_pay['fineFee'] * $diff1;
                       $this->view->examfeena = $multi;
                      //echo  round($diff1);
                       //die();
                         
                     }
                      $this->view->totalfee = $multi + $fee_pay['examFee'] ;
                      $this->view->extdate = $fee_pay['feeForm_extended_date'] ;
                    
                      //$payctive = $examsubmit->getRecordbyfid($stu_id);
                      $this->view->payactivaion = $payctive[0]['payment_activation'] ;
                      $this->view->termid = $payctive[0]['term_id'] ;
                      //print_r($payctive[payment_activation]);
      
                    $assignment_form->populate($result);
                

                    }
                }


                break;
                
            case 'save':
              
                $examsubmit_model = new Application_Model_UgNonformSubmissionModel();
                $examform = new Application_Form_AdmitCardView();
                if ($this->getRequest()->isPost()) {
                  // print_r($_POST);die();
                    if (isset($_POST)) {
                        
                        $attendanceInfoData = array(
                           'student_id' => $_POST['st_id'],
                           'year_exam' => $_POST['year'],
                           'session_id' => $_POST['session_id'],
                           'academic_year_id' => $_POST['academic_id'],
                           'term_id' => $_POST['semester'],
                           'stu_name' =>$_POST['stu_name'],
                           'fname' =>$_POST['father_fname'],
                           'date_of_birth' => $_POST['stu_dob'],
                           'reg_no' => $_POST['registration_no'],
                           'examination_id' => $_POST['exam_roll'], 
                           'examination_name' => $_POST['examination'],
                           'college_name' => $_POST['college'],
                           'cc_paper_name' => $_POST['paper_name'][0].$_POST['paper_name'][1],
                           'ge_paper_name' => $_POST['ge'],
                           'aecc_paper_name' => $_POST['aecc'],
                           'cc_paper_name' => $_POST['paper_name'][0],
                           'ge_paper_name' => $_POST['paper_name'][1],
                           'aecc_paper_name' => $_POST['paper_name'][2],
                           'created_date' => date('Y-m-d'),
                           'u_id' => $_POST['f_id'],
                           'email' => $_POST['email'],
                           'phone' => $_POST['phone'],
                           'payment_activation' => 1
                        );
                        if(isset($_POST['non_data'])){
                          $attendanceInfoData['non_collegiate_status'] = 1;
                        }
                        
                        
                         $last_insert_id = $examsubmit_model->insert($attendanceInfoData);
                         
                         $this->_flashMessenger->addMessage('Attendance Successfully added');
                        $this->_redirect('application/ugnonpayment/?fid='.md5($_POST['f_id']));
                        //print_r($_POST);
                        //die();  
                    }
                    }
                break;
            case 'otpverify':
                  $semdata=  $fee_model->getNonSemRecord();
                 $this->view->showsem=$semdata; 
                 
                if ($this->getRequest()->isPost()) {
                  if(isset($_POST)){
                      
                     // print_r($_POST);
                      //die();
                      $uid= $_POST['stu_id'];
                      $otp = $_POST['otp'];
                     $semester= $_POST['semester'];
                     
                  
                        $res=$stu_obj->getStudenInfoByU1($uid);
                     //$res = $otpmodel->getVerifyotpnon($uid,$otp);
                        //print_r($res['academic_id']);die();
                      if(empty($res) || $res['academic_id']<=0){
                         
                     $this->_refresh(5,'/academic/application/noncollegiate/type/otpverify','Wrong Form or Semester');
                    
                      }
                  
                      $examdate=  $fee_model->getNonColEnddate($semester,$res['academic_id']); 
                     
                      if(!empty($examdate)){
                        
                        
                        if($examdate['feeForm_extended_date']!='0000-00-00'){
                          if(date('Y-m-d')>=$examdate['feeForm_start_date'] && date('Y-m-d')<=$examdate['feeForm_extended_date']) {  
                           $flag = true;
                              
                        }else {
                           $this->_refresh(5,'/academic/application/noncollegiate/type/otpverify','Form filling not started for this Academic! OR End Date');
                    
                        }
                        } else {
                            //echo date('Y-m-d');
                           
                        if(strtotime(date('Y-m-d'))>=strtotime($examdate['feeForm_start_date']) && strtotime(date('Y-m-d'))<=strtotime($examdate['feeForm_end_date'])){
                            
                            $flag = true;  
                        }
                        else {
                             $this->_refresh(5,'/academic/application/noncollegiate/type/otpverify','Form filling not started for this Academic! OR End Date');
                 
                           
                        }
                            
                        }
                    }
                     if($res){
                         
                      $this->_redirect('application/noncollegiate/type/add?stu_id='.md5($_POST['stu_id']).'&sem='.$semester);
                    
                     }
                      else {    
                            $this->_refresh(5,'/academic/application/noncollegiate/type/otpverify','Invalid OTP OR You have Choosen Wrong Semester');
                      
                      }
		  
                     
                      
                    
                  }
                }
                break;
                
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('application/index');
                }
                break;
            default:
             $this->_redirect('application/noncollegiate/type/otpverify');
                 if ($this->getRequest()->isPost()) {
                 if (isset($_POST)) {
                     $rand= rand(999,10000);
//                     $stunum = $assignment_model->getStudentM($_POST['stu_id']);
//                     $num =  $stunum['father_mobileno'];
                     
                     $num=$_POST['phone'];
                    $email= $_POST['email'];
                     $insert = array(
                        'u_id' => $_POST['stu_id'],
                        'otp' => $rand,
                        'create_date' => date('Y-m-d')
                     );
                     
                     
                       //===============sms function ======================// 
                    if($this->_sms['default'] == 0){
                    $phonenum = $num;
                    }
                    else{
                        $phonenum = $this->_sms['default'];
                    }
                    $phonenum = $num;
                    $message = 'Dear Student, you need an OTP to access the application form. The OTP is '.$rand;
                    $debug = $this->_sms['debug'];

                    $this->SMSSend($phonenum,$message,$debug);
                    //=====================[END]=====================//
                    
                    
                    //=====================[Email Function]=====================//
                     $to = $email;
                     $subject = "OTP For Form Verification";
                     $txt = $message;
                     $headers = "MIME-Version: 1.0" . "\r\n";
                     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                     $headers = "From: no-reply@pwcadmissions.in" . "\r\n" ;
                     mail($to,$subject,$txt,$headers);
            
                    //=====================[END]=====================//
                    
                       $rereundata= $otpmodel->getRecordsbyuid($_POST['stu_id']);
                       if($rereundata){ 
                           $data['otp']=$rand;
                           $otpmodel->update($data,array('u_id=?'=> $_POST['stu_id']));
                       }
                       else {
                      $last_insert_id = $otpmodel->insert($insert);
                       
                       }
                        $sms = $this->mobverification($rand,$num);
                      $this->_redirect('application/noncollegiate/type/otpverify');
                      
                 }
                 }
              $messages = $this->_flashMessenger->getMessages();
             $_SESSION['admin_login']['admin_login']->flash_error = 0;
              $this->view->messages = $messages;
                break;
        }
    }
    
public function pgnoncollegiateAction() {
	    
	     // http_response_code(404); die;
  
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
       
       // $assignment_form = new Application_Form_AdmitCardView();
        $admit_form = new Application_Form_AdmitCard();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        $otpmodel = new Application_Model_OtpModel();
        $nonmodel =  new Application_Model_NonPgCollegiateModel(); 
        $term_obj = new Application_Model_TermMaster();
        $acd_obj = new Application_Model_Session();
        $stu_obj = new Application_Model_StudentPortal();
        $examsubmit = new Application_Model_NonPgDataModel();
        
        switch ($type) {       
            case "add":
            //$assignment_model->populate($result);
             $assignment_form = new Application_Form_AdmitCardView();
             $this->view->form = $assignment_form;

                if ($this->getRequest()->isGet()) {
                    if (isset($_GET)) {
                       // print_r($_POST);
                        //die();
                    $stu_id=trim($_GET['stu_id']);
                   
                   // echo $stu_id;
                    //die();
                    $checkk=$nonmodel->getStuRecords($stu_id); 
                     // print_r($checkk);
                       ///die();
                    if(!$checkk){
                        $this->redirectExamform("Your Form Id does not match OR The form is not yet filled",'pg');
                    }
                    
                   $payinform= $examsubmit->getPayRecordbyfid($stu_id);
                   $payactivation = $payinform['payment_activation'];
                   $paystatus = $payinform['payment_status'];
                   if($paystatus =='1' && $payactivation=='1'){
                       $this->_redirect('application/admitcardpgnoncollegiate/');
                   }
                  // echo $payinform ; die();
                   elseif($payactivation=='1'){
                      $this->_redirect('application/pgnonpayment/?fid='.$_GET['stu_id'].'');
                   }
                    //http_response_code(404); die;
                   $term_details =  $term_obj->getRecordbycmm($checkk[0]['term_id']);
                   $aca_details = $acd_obj->getRecord($checkk[0]['session_id']);
                   $stu_details = $stu_obj->getStudenInfoByU($stu_id);
                   $this->view->stu_details =$stu_details;
                   $this->view->checkk =$checkk;
                       // die();
                   
                   $this->view->term_details =  $term_details;
                   $this->view->aca_detail =  $aca_details;
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s college';
                    $result['examination'] = date('M').'-'.date('Y');;
                    $result['session'] = $aca_details['session'];
                    $result['registration_no'] =$checkk[0]['reg_no'];
                    $result['exam_roll'] = $checkk[0]['exam_roll'];
                    $result['stu_dob'] = $stu_details['stu_dob'];
                    $result['father_fname'] = $stu_details['father_fname'];
                    
                    $this->view->stu_name = $stu_details['stu_fname'].$stu_details['stu_lname'];
                    
                    $this->view->course_name = $checkk[0]['course_id'];
                    $this->view->s_id = $checkk[0]['session_id'];
                    $this->view->acd_id = $checkk[0]['academic_id'];
                    $this->view->st_id = $stu_details['student_id'];
                    $this->view->fid= $stu_details['stu_id'];
                    $this->view->totalfee = $multi + $fee_pay['examFee'] ;
                    $this->view->extdate = $fee_pay['feeForm_extended_date'] ;
                    
                      //$payctive = $examsubmit->getRecordbyfid($stu_id);
                      $this->view->payactivaion = $payctive[0]['payment_activation'] ;
                      $this->view->termid = $payctive[0]['term_id'] ;
                      //print_r($payctive[payment_activation]);
      
                    $assignment_form->populate($result);
                

                    }
                }


                break;
                
            case 'save':
                $examform = new Application_Form_AdmitCardView();
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {
                        
                        $attendanceInfoData = array(
                           'student_id' => $_POST['st_id'],
                           'year_exam' => $_POST['year'],
                           'session_id' => $_POST['session_id'],
                           'academic_year_id' => $_POST['academic_id'],
                           'term_id' => $_POST['semester'],
                           'stu_name' =>$_POST['stu_name'],
                           'fname' =>$_POST['father_fname'],
                           'date_of_birth' => $_POST['stu_dob'],
                           'reg_no' => $_POST['registration_no'],
                           'examination_id' => $_POST['exam_roll'], 
                           'examination_name' => $_POST['examination'],
                           'college_name' => $_POST['college'],
                            'cc_1'=>$_POST['course_code'][0],
                            'cc_2'=>$_POST['course_code'][1],
                            'cc_3'=>$_POST['course_code'][2],
                           'cc_paper_name' => $_POST['course_name'][0],
                           'ge_paper_name' => $_POST['course_name'][1],
                           'aecc_paper_name' => $_POST['course_name'][2],
                           'created_date' => date('Y-m-d'),
                           'u_id' => $_POST['f_id'],
                           'email' => $_POST['email'],
                           'phone' => $_POST['phone'],
                           'payment_activation' => 1
                        );
                        if(isset($_POST['non_data'])){
                          $attendanceInfoData['non_collegiate_status'] = 1;
                        }
                         $last_insert_id = $examsubmit->insert($attendanceInfoData);
                         
                         $this->_flashMessenger->addMessage('Attendance Successfully added');
                        $this->_redirect('application/pgnonpayment/?fid='.md5($_POST['f_id']));
                        //print_r($_POST);
                        //die();  
                    }
                    }
                break;
            case 'otpverify':
                 
                if ($this->getRequest()->isPost()) {
                  if(isset($_POST)){
                      $uid= $_POST['stu_id'];
                      $otp = $_POST['otp'];
                     
                      
                
                     $res = $otpmodel->getVerifyotpnon($uid,$otp);
                     if($res){
                      $this->_redirect('application/pgnoncollegiate/type/add?stu_id='.md5($_POST['stu_id']));
                    
                     }
                      else {
                          echo "<script>
                          alert('Invalid OTP');
                          </script>"; 
                      }
                  }
                }
                break;
                
                
              case 'admitcard':
                 
                if ($this->getRequest()->isPost()) {
                  if(isset($_POST)){
                      $uid= $_POST['stu_id'];
                      $otp = $_POST['otp'];
                     
                      
                  
                     $res = $otpmodel->getVerifyotp($uid,$otp);
                     if($res){
                      $this->_redirect('application/pgnoncollegiate/type/add?stu_id='.$_POST['stu_id']);
                    
                     }
                      else {
                          echo "<script>
                          alert('Invalid OTP');
                          </script>"; 
                      }
                  }
                }
                break;
                
           
            default:
             
                 if ($this->getRequest()->isPost()) {
                 if (isset($_POST)) {
                     $rand= rand(999,10000);
//                     $stunum = $assignment_model->getStudentM($_POST['stu_id']);
//                     $num =  $stunum['father_mobileno'];
                     $num=$_POST['phone'];
                     $email= $_POST['email']; 
                     $insert = array(
                        'u_id' => $_POST['stu_id'],
                        'otp' => $rand,
                        'create_date' => date('Y-m-d')
                     );
                     
                     
                     //===============sms function ======================// 
                    if($this->_sms['default'] == 0){
                    $phonenum = $num;
                    }
                    else{
                        $phonenum = $this->_sms['default'];
                    }
                    $message = 'Dear Student, you need an OTP to access the application form. The OTP is '.$rand;
                    $debug = $this->_sms['debug'];

                    $this->SMSSend($phonenum,$message,$debug);
                    //=====================[END]=====================//
            
                     //=====================[Email Function]=====================//
                     $to = $email;
                     $subject = "OTP From Verificaion";
                     $txt = $message;
                     $headers = "MIME-Version: 1.0" . "\r\n";
                     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                     $headers = "From: infp@pwcadmissions.in" . "\r\n" ;
                     mail($to,$subject,$txt,$headers);
            
                    //=====================[END]=====================//
                     
                       $rereundata= $otpmodel->getRecordsbyuid($_POST['stu_id']);
                       if($rereundata){ 
                           $data['otp']=$rand;
                           $otpmodel->update($data,array('u_id=?'=> $_POST['stu_id']));
                       }
                       else {
                      $last_insert_id = $otpmodel->insert($insert);
                       
                       }
                        $sms = $this->mobverification($rand,$num);
                      $this->_redirect('application/pgnoncollegiate/type/otpverify');
                      
                 }
                 }
              $messages = $this->_flashMessenger->getMessages();
             $_SESSION['admin_login']['admin_login']->flash_error = 0;
              $this->view->messages = $messages;
                break;
        }
    }
    
public function admitcardnoncollegiateAction() {
          //http_response_code(500);
    //die();
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
      // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
       $student_exam_form = new Application_Model_UgNonformSubmissionModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

        switch ($type) {       
		case "admitcard":
		     ///die();
                       $assignment_form = new Application_Form_AdmitCardView();
                       $this->view->form = $assignment_form;
                      if ($this->getRequest()->isPost()) {
                      if (isset($_POST)) {
                       // print_r($_POST);
                       // die();
               switch ($_POST['semester']) {
                case "t1":
                    $semester ="Semester 1";
                  break;
                case "t2":
                    $semester ="Semester 2";
                  break;
                case "t3":
                     $semester ="Semester 3";
                  break;
                case "t4":
                    $semester ="Semester 4";
                  break;
                case "t5":
                    $semester ="Semester 5";
                  break;
                case "t6":
                    $semester ="Semester 6";
                  break;
                default:
                  echo "n/a";
            }
                    $stu_id=md5(trim($_POST['stu_id'])); 
                    $this->view->fid = $_POST['stu_id'];
                    $result =$student_exam_form->getNonRecordbyfid($_POST['stu_id'],$_POST['semester']);
                    
                   $stu_image = $assignment_model->getImage($stu_id);
                   if($result=='')
                   {     
                     
                       
                       $this->redirectExamform('You not submit your fill your form or payment Due',1);
                   } 
                   else {
                   $updatestaus= $student_exam_form->update(array('status'=> 1), array('u_id=?' => $_POST['stu_id'],'payment_status=?'=>1,'status=?'=>0))  ;
                  // $sem_id= $allow_model->getTermIdByFid($stu_id,$result['academic_year_id']);
                   // print_r($sem_id);
                   // die();
                  // echo $result['academic_year_id']."server down !"; exit;
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                    $acad_term_arr['term_id'] = $result['term_id'];
                    $examdate = $fee_model->getExamdateNoncolRecord($result['academic_year_id'],$_POST['semester']);
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =  date("M-Y", strtotime($examdate['exam_date'])); 
                    $this->view->exmdate=$result['examination'];
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
             //print_r($result);
              //     die();
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   $this->view->semester = $semester;
                 if(!$ac_details || !$ac_details['department']){
                       echo 'You have to fill your form first !' ;
                   }
                   $this->view->dept_id = $dept_model->getRecord($ac_details['department'])['department'];
                  
                   $result['exam_roll'] =$result['examination_id'];
                   $result['registration_no'] = $result['reg_no'];
                 
//echo "<pre>"; print_r($result['session_id']);exit;
                    $paymentdesc = new Application_Model_NonColpayment();
                    // echo $stu_id; 
                    $fetchdetail = $paymentdesc->getRecordbyfid($stu_id);
                   
                   //echo "<pre>"; print_r($fetchdetail);exit;
                     $this->view->transaction_detail = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                   $nonsubject = new Application_Model_BackSelectionItems();
                     $getsub= $nonsubject->getStuSubRecords($_POST['stu_id'],$_POST['semester']);
                      // echo "<pre>";  print_r($getsub); die();
                     $this->view->subjectdetail = $getsub;
                   
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    
                    
                    $assignment_form->populate($result);
                  
                      
                   }

                    }
                }


                break;

            default:
             $admit_form = new Application_Form_AdmitCard();
                $semdata=  $fee_model->getNonSemRecord();
                 $this->view->showsem=$semdata; 
           // $semdata=  $examactivemodel->getSemnonclRecord();
           // $this->view->showsem=$semdata; 
                break;
        }
    }  
    
public function admitcardpgnoncollegiateAction() {
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        $student_exam_form = new Application_Model_NonPgDataModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

        switch ($type) {       
		case "admitcard":
                       $assignment_form = new Application_Form_AdmitCardView();
                       $this->view->form = $assignment_form;
                      if ($this->getRequest()->isPost()) {
                      if (isset($_POST)) {
                        //print_r($_POST);
                        //die();
                    $stu_id=md5(trim($_POST['stu_id'])); 
                   
                    $result =$student_exam_form->getNonRecordbyfid($_POST['stu_id']);
                   // print_r($result);
                      // die();
                  
                   if(empty($result))
                   {     
                       //echo "hii";
                       //die();
                     echo "<script>
                     alert('Application form Not submitted');
                     </script>";
                       $this->redirectExamform("Your Exam Form does not Submit Or Payment Not Completed",'pg');
                   } 
                   else {
                    $this->view->fid = $_POST['stu_id'];
                    $stu_image = $assignment_model->getImage($stu_id);
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                 
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                  
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =   date('M').'-'.$result['exam_year']  ;
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
            
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   $this->view->semester = 'Semester 3';
                   //echo "<pre>";print_r($ac_details);die;
                   $deprt = $dept_model->getRecord($ac_details['department']);
                    //print_r($deprt[department]);
                      // die();
                   $this->view->dept_id = $deprt[department];
                  
                   $result['exam_roll'] =$result['examination_id'];
                   $result['registration_no'] = $result['reg_no'];
                 
//echo "<pre>"; print_r($result['session_id']);exit;
                    $paymentdesc = new Application_Model_NonpgPaymentModel();
                    $fetchdetail = $paymentdesc->getpayRecordbyfid($stu_id);
                    //echo "<pre>"; print_r($fetchdetail);exit;
                     $this->view->transaction_detail = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                  //echo "<pre>";  print_r($exam_schedule);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    $nonsubject = new Application_Model_NonPgCollegiateModel();
                     $getsub= $nonsubject->getStuRecords($stu_id);
                     $this->view->subjectdetail = $getsub;
                     //echo "<pre>";  print_r($getsub);
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    
                    
                    $assignment_form->populate($result);
                  
                      
                   }

                    }
                }


                break;

            default:
             $admit_form = new Application_Form_AdmitCard();
           
                break;
        }
    }   
    
   
 //-----  Non Collegiate section Add by raushan  -------//
    
 /**********************************************************************Raushan********************************************************************************************************************************************************************** */  
    
    public function reportcardAction() {
        
      //  http_response_code(404); die;
      
        $this->view->action_name = 'examform';
        $this->view->sub_title_name = 'examform';
        $this->accessConfig->setAccess('SA_ACAD_EXAM_FORM');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();     
       // $assignment_form = new Application_Form_AdmitCardView();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        $attendace_report = new Application_Model_BatachSemesterAttendance();
        $student_assignment_model = new Application_Model_SubmitAssignment();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $examsubmit = new Application_Model_ExamformSubmissionModel();
        $academic_det = new Application_Model_Academic();
        $term_obj = new Application_Model_TermMaster();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;
         $otpmodel = new Application_Model_OtpModel();
        switch ($type) {   
            
         
            case "reportform":
                    $uid= $_GET['stu_id'];
                      $otp = $_GET['o'];
                
                     $res = $otpmodel->getVerifyotp1($uid,$otp);
                    
                        if($res){
                            $this->studentTermReportAction($uid,$otp);
                            }
                      else {
                                echo "<script>
                                alert('Invalid User');
                                </script>"; 
                            }
            break;
            case 'otpverify':     
                if ($this->getRequest()->isPost()){
                  if(isset($_POST)){
                      $uid= $_POST['stu_id'];
                      $otp = $_POST['otp'];
                      
                     $res = $otpmodel->getVerifyotp1($uid,md5($otp));
                     if($res){
                      $this->_redirect('application/reportcard/type/reportform?o='.md5($otp).'&& stu_id='.$uid);
                     }
                      else {
                          echo "<script>
                          alert('Invalid OTP');
                          </script>"; 
                      }
                  }
                }
                break;
            case 'noncolotpverify':     
                if ($this->getRequest()->isPost()){
                  if(isset($_POST)){
                      $uid= $_POST['stu_id'];
                      $otp = $_POST['otp'];
                     
                     $res = $otpmodel->getVerifyotp($uid,$otp);
                     if($res){
                      $this->_redirect('application/noncolreportcard/type/backreportform?o='.md5($otp).'&& stu_id='.$uid);
                     }
                      else {
                          echo "<script>
                          alert('Invalid OTP');
                          </script>"; 
                      }
                  }
                }
                break;
            default:
             
                 if ($this->getRequest()->isPost()) {
                 if (isset($_POST)) {
                     $rand= rand(999,10000);
                     $stunum = $assignment_model->getStudentM($_POST['stu_id']);
                    // $num =  $stunum['father_mobileno'];
                    $num=$_POST['phone'];
                    $email= $_POST['email'];
                     $insert = array(
                        'u_id' => $_POST['stu_id'],
                        'otp' => $rand,
                        'create_date' => date('Y-m-d')
                     );
                     
                    //===============sms function ======================// 
                    if($this->_sms['default'] == 0){
                    $phonenum = $num;
                    }
                    else{
                        $phonenum = $this->_sms['default'];
                    }
                   
                    $phonenum = $num;
                    $message = 'Dear Student, you need an OTP to access the application form. The OTP is '.$rand;
                    $debug = $this->_sms['debug'];

                    $this->SMSSend($phonenum,$message,$debug);
                    //=====================[END]=====================//
                    
                    
                    //=====================[Email Function]=====================//
                     $to = $email;
                     $subject = "OTP From Verificaion";
                     $txt = $message;
                     $headers = "MIME-Version: 1.0" . "\r\n";
                     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                     $headers = "From: infp@pwcadmissions.in" . "\r\n" ;
                     mail($to,$subject,$txt,$headers);
            
                    //=====================[END]=====================//
                    
                      $rereundata= $otpmodel->getRecordsbyuid($_POST['stu_id']);
                       if($rereundata){ 
                           $data['otp']=$rand;
                           $otpmodel->update($data,array('u_id=?'=> $_POST['stu_id']));
                       }
                       else {
                      $last_insert_id = $otpmodel->insert($insert);
                       
                       }
                        $sms = $this->mobverification($rand,$num);
                       
                      $this->_redirect('application/reportcard/type/otpverify');
                      
                 }
                 }
              $messages = $this->_flashMessenger->getMessages();
              $_SESSION['admin_login']['admin_login']->flash_error = 0;
              $this->view->messages = $messages;
        }
    }
    public function noncolreportcardAction() {
        
      //  http_response_code(404); die;
      
        $this->view->action_name = 'examform';
        $this->view->sub_title_name = 'examform';
        $this->accessConfig->setAccess('SA_ACAD_EXAM_FORM');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();     
       // $assignment_form = new Application_Form_AdmitCardView();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        $attendace_report = new Application_Model_BatachSemesterAttendance();
        $student_assignment_model = new Application_Model_SubmitAssignment();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $examsubmit = new Application_Model_ExamformSubmissionModel();
        $academic_det = new Application_Model_Academic();
        $term_obj = new Application_Model_TermMaster();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;
         $otpmodel = new Application_Model_OtpModel();
        switch ($type) {   
            
         
           
            case "backreportform":
                    $uid= $_GET['stu_id'];
                      $otp = $_GET['o'];
                
                     $res = $otpmodel->getVerifyotp1($uid,$otp);
                    
                        if($res){
                            $this->studentBackTermReportAction($uid,$otp);
                            }
                      else {
                                echo "<script>
                                alert('Invalid User');
                                </script>"; 
                            }
            break;
            case 'noncolotpverify':     
                if ($this->getRequest()->isPost()){
                  if(isset($_POST)){
                      $uid= $_POST['stu_id'];
                      $otp = $_POST['otp'];
                   
                     $res = $otpmodel->getVerifyotp($uid,$otp);
                     if($res){
                      $this->_redirect('application/noncolreportcard/type/backreportform?o='.md5($otp).'&& stu_id='.$uid);
                     }
                      else {
                          echo "<script>
                          alert('Invalid OTP');
                          </script>"; 
                      }
                  }
                }
                break;
            default:
             
                 if ($this->getRequest()->isPost()) {
                 if (isset($_POST)) {
                     $rand= rand(999,10000);
                     $stunum = $assignment_model->getStudentM($_POST['stu_id']);
                    // $num =  $stunum['father_mobileno'];
                    $num=$_POST['phone'];
                    $email= $_POST['email'];
                     $insert = array(
                        'u_id' => $_POST['stu_id'],
                        'otp' => $rand,
                        'create_date' => date('Y-m-d')
                     );
                     
                    //===============sms function ======================// 
                    if($this->_sms['default'] == 0){
                    $phonenum = $num;
                    }
                    else{
                        $phonenum = $this->_sms['default'];
                    }
                   
                    $phonenum = $num;
                    $message = 'Dear Student, you need an OTP to access the application form. The OTP is '.$rand;
                    $debug = $this->_sms['debug'];

                    $this->SMSSend($phonenum,$message,$debug);
                    //=====================[END]=====================//
                    
                    
                    //=====================[Email Function]=====================//
                     $to = $email;
                     $subject = "OTP From Verificaion";
                     $txt = $message;
                     $headers = "MIME-Version: 1.0" . "\r\n";
                     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                     $headers = "From: infp@pwcadmissions.in" . "\r\n" ;
                     mail($to,$subject,$txt,$headers);
            
                    //=====================[END]=====================//
                    
                      $rereundata= $otpmodel->getRecordsbyuid($_POST['stu_id']);
                       if($rereundata){ 
                           $data['otp']=$rand;
                           $otpmodel->update($data,array('u_id=?'=> $_POST['stu_id']));
                       }
                       else {
                      $last_insert_id = $otpmodel->insert($insert);
                       
                       }
                        $sms = $this->mobverification($rand,$num);
                       
                      $this->_redirect('application/noncolreportcard/type/noncolotpverify');
                      
                 }
                 }
              $messages = $this->_flashMessenger->getMessages();
              $_SESSION['admin_login']['admin_login']->flash_error = 0;
              $this->view->messages = $messages;
        }
    }
    
     public function studentTermReportAction($stu_id = '',$otp='') {
      
          $uid = $_GET['stuid'];
          $term = $_GET['term'];
        //$uid = $this->_getParam("stu_id");
       //  $term = $this->_getParam("term");
       
          // echo '<pre>'; print_r($_GET);die();                
        $this->view->action_name = 'student-term-report';
        $this->view->sub_title_name = 'student-term-report';
        $this->accessConfig->setAccess('SA_ACAD_TERM_GRADE_SHEET');
        $session = 0;
        $student_report_form = new Application_Form_StudentReport();
        $student_model = new Application_Model_StudentPortal();
       
        $student_details = $student_model->getStudenInfoByU2($uid);
     
        if(!$student_details ){
           // echo "<b>Error Code 403 : </b>Unauthorized access $uid";exit; 
            $this->_redirect('error/error_404');
        }
        //===================new code start=======================///
          
                                    $term_model= new Application_Model_TermMaster();

                                    $term_det = $term_model->getTermRecordsbycmn1($student_details['academic_id'],$term);
                                    $cmn_terms=$term;
                                    
                                    $term_id = "";
                                   //echo '<pre>'; print_r($term_det);exit;
                                    
                              
                                
                                
                                
                                
                                
        $check = $_POST['username'];
        $stu_id = $student_details['student_id'];
        $academic_id = $student_details['academic_id'];
        $year_id = $term_det['year_id'];
        $term_id = $term_det['term_id'];
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        
       
        $term_master = new Application_Model_TermMaster();
        $academic_master = new Application_Model_Academic();
        $term_result = $term_master->getRecordByAcademicId($academic_id);
          $core_course_master = new Application_Model_Corecourselearning();
 
        $filename = 'Grade Report';  
        $stuname = 'no student';
        $prom_text = '';
        $dateterm_id = '';
        $term_iterator = array();
        $single = false;
        $pge_height = 350;
        //============QR COde Generator url link for studenr=====================//
         $url = $this->_base_url."application/secondyeargradesheetreport/id/$stu_id/acd_id/$academic_id/year/$year_id/mode/view";
      //------------------[bulk term_id]---------------//  
        if(empty($term_id)){
            
            $pge_height = 0;
            foreach($term_result as $key => $value){
            
                    $result = $this->getReports($stu_id,$academic_id,$year_id,$value['term_id']);
                     
                    if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                            $tabl_date = $result['get_student_sgpa']['added_date'];
                              $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $term_iterator[] = $value['term_id'];
                             $tbl_id =  $result['tabulation_id'];
                            $dateterm_id = !isset($result['dateTerm_id'])?$dateterm_id:$result['dateTerm_id'];
                            $stuname = $result['student_details']['stu_fname'];
                            $course_ids = $this->mergData($student_course_marks[$value['term_id']], array('course_id'), count($student_course_marks[$value['term_id']]));                 
            $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $value['term_id'], $course_ids));
                            $pge_height+=300;
                    }
                }
        }
        else if(!empty($term_id))
        {
            $value['term_id'] = $term_id;
            $term_iterator[] = $term_id;
      
       
            $result = $this->getReports($stu_id,$academic_id,$year_id,$value['term_id']);
            
            if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                            $tabl_date = $result['get_student_sgpa']['added_date'];
                            $student_details = $result['student_details'];
                            $prom_text = $result['get_student_sgpa']['promotion_text']=='--'?'Not Promoted':$result['get_student_sgpa']['promotion_text'];
                            
                            // if($prom_text !='--')
                            // {
                            //     $prom_text = "Pass";
                            // }
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                             $tbl_id =  $result['tabulation_id'];
                            $dateterm_id = !isset($result['dateTerm_id'])?$dateterm_id:$result['dateTerm_id'];
                            $stuname = $result['student_details']['stu_fname'];
                            $filename = $academic_details[$value['term_id']] . '-'.$term_details[$value['term_id']]['term_name'].'-Grade-Report';
                            
                            $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));         $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
       
                           
            }
             $single = true;
        }
       //---------------------[END]-------------------------// 
        
        
        
        $degree_id = $academic_master->getAcademicDegree($academic_id);
       
        if(!empty($degree_id))
        $passpercent = $this->getPasspercent($degree_id,$session);
        $this->view->passpercent = $passpercent;
        $this->view->student_marks_details = $student_course_marks;
        $this->view->stu_sgpa = $get_student_sgpa;
        $this->view->stu_fail_in = $get_student_fail_in;
        $this->view->stu_details = $student_details;
        $this->view->stu_term_details = $term_details;
        $this->view->stu_academic_details = $academic_details;
        $this->view->stu_course = $formated_result;
        $this->view->header_component = $max;
        $this->view->corespan = $core_course_span;
        $this->view->term_marks = $term_iterator;
        $this->view->single = $single;
        $this->view->prom = $prom_text;
        $this->view->sgpaspan = $course_credit_info;
        $this->view->cmn_terms = $cmn_terms;
      $this->view->tabldetails = array('tabl_id'=>$tbl_id,'term_id'=>$dateterm_id);
        //echo $prom_text; die;
        if($publishDate){
            $this->view->publish_dates = $publishDate;
        }
        else{
         $this->view->publish_dates = $tabl_date;
        }
        
       
        
        if($term_id)
        $this->view->term_id = $term_id;
        else
            $this->view->term_id = 0;
       
        $this->view->url = $url;

        //echo '<pre>'; print_r('Sm');exit;  
        //===================[GRADE SHEET NUMBER]===============================//
                   if($term_id){
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $stu_id);
                $this->view->gradesheet_number = $gradesheet_number;
                $htmlcontent = $this->view->render('report/ajax-get-grade-view.phtml');
                
                
        }
        else
        {
            if($year_id){
                 $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $stu_id);
            }
            else{
                $GradeSheet_model = new Application_Model_FinalGradeNo();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $stu_id,0);
              
            }
                $this->view->gradesheet_number = $gradesheet_number;
        }
                                        
                                       
                                        
              
            if($gradesheet_number !=0 && count($student_course_marks)>0){
            $htmlcontent = $this->view->render('application/ajax-get-grade-view.phtml');
            echo $htmlcontent; die;
           // $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $stuname . '-' .$filename,'L',$pge_height,$backcontent );
            }
            else
            {
                echo "Grade not generated yet.";die;
            }                    
                                        
                                        
                                        

    }
    
    
    public function getStudentReportAction() {
        $this->_helper->layout->disableLayout();
        $studentreport_model = new Application_Model_StudentReport();
        $result = $studentreport_model->getRecords();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            $year_id = $this->_getParam("year_id");
            $term_id = $this->_getParam("term_id");
            $this->view->term_id = $term_id;
            $this->view->year_id = $year_id;
            $stu_id = $this->_getParam("stu_id");
            
            

            if ($academic_id) {
                $Studentreport_model = new Application_Model_StudentPortal();
                $result = $Studentreport_model->getStudentPCRecord($academic_id, $stu_id);
                $this->view->corecourseresult = $result;
               
            }
        }
    }
    
    
    
    
    
     
//=====================================[Start BAck paper Student]===========================================// 
     public function studentBackTermReportAction($stu_id = '',$otp='') {
                  
            $uid = $_GET['stuid'];
          $term = $_GET['term'];           
                    
        $this->view->action_name = 'student-term-report';
        $this->view->sub_title_name = 'student-term-report';
        $this->accessConfig->setAccess('SA_ACAD_TERM_GRADE_SHEET');
        $student_report_form = new Application_Form_StudentReport();
        $student_model = new Application_Model_StudentPortal();
       // $uid = $this->_getParam("stu_id");
       // $term = $this->_getParam("term");
        $student_details = $student_model->getStudenInfoByU2($uid);
        if(!$student_details){
            echo "<b>Error Code 403 : </b>Unauthorized access $uid";exit; 
        }
            
        $this->view->academic_id = $student_details['academic_id'];
        $this->view->u_id = $uid;
        $this->view->id = $student_details['student_id'];
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->term = $term;
        $this->view->form = $student_report_form;
 }
 
     public function getBackStudentReportAction() {
        $this->_helper->layout->disableLayout();
        $studentreport_model = new Application_Model_StudentReport();
        $result = $studentreport_model->getRecords();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            $year_id = $this->_getParam("year_id");
            $term_id = $this->_getParam("term_id");
            $this->view->term_id = $term_id;
            $this->view->year_id = $year_id;
            $stu_id = $this->_getParam("stu_id");

            if ($academic_id) {
                $Studentreport_model = new Application_Model_StudentPortal();
                $result = $Studentreport_model->getStudentPCRecord($academic_id, $stu_id);
                $this->view->corecourseresult = $result;
            }
        }
  }
    
 public function getBackStudentMarks($academic_id, $tabulation_id, $stu_id, $term_id) {

        $GradeAllocationReport = new Application_Model_BackGradeAllocationReportItems();
        $GradeMaster = new Application_Model_GradeAllocation();
        $getStudent_course_details = $GradeAllocationReport->getMarksDetailsWithCourse($tabulation_id, $stu_id, $term_id);
        
        foreach ($getStudent_course_details as $key => $value) {
            $getStudent_course_details[$key]['number_value'] = $GradeMaster->getGradeRecordsOnBack($academic_id, $term_id, $value['course_id'], $stu_id)['number_value'];
            $getStudent_course_details[$key]['grade_value'] = $GradeMaster->getGradeRecordsOnBack($academic_id, $term_id, $value['course_id'], $stu_id)['grade_value'];
        }

        return $getStudent_course_details;
    }
    public function geStudentBackPaperCourse($academic_id,$term_id,$stu_id){
        $Corecourselearning_model = new Application_Model_Corecourselearning();
          //$core_courses = $Corecourselearning_model->getcorecourse($academic_id, $term_id);
          $student_ge = $Corecourselearning_model->getBackStudentGE($academic_id, $term_id,$stu_id);
          $course_type_result = $student_ge;
      // echo "<pre>"; print_r($course_type_result);exit;
          return $course_type_result;
    }
	
	  //==================get fail pass student details===========================//  
   public function getBackReports($stu_id,$academic_id,$year_id,$term_id){
       
       $result = array();
               $student_info = new Application_Model_StudentPortal();
        $tabulation_report = new Application_Model_TabulationReport();
        $eval_component = new Application_Model_EvaluationComponentsItems();
        $term_details = new Application_Model_TermMaster();
        $academic_details = new Application_Model_Academic();
        $Corecourselearning_model = new Application_Model_Corecourselearning();
        
        $result['core_course_span'] = 0;
        
        $cc_id = $Corecourselearning_model->getOnCC($academic_id, $term_id);
        
        
        $course_type_result = $this->geStudentBackPaperCourse($academic_id, $term_id,$stu_id);
       
        $course_type_result = array_values($course_type_result);
     
        $course_ids = $this->selectData($course_type_result, array('course_id'),count($course_type_result));
          
        $compo_count = array();
        foreach($course_ids as $key => $value){
            $component_res = $eval_component->getRecordsByCourse($value['course_id']);
            $compo_count[] = count($component_res);
            $key_val[count($component_res)] =  $component_res;
        }
       
      
        $result['max_num'] = $key_val[max($compo_count)];
        $result['formated_result'] =  $this->stackData($cc_id,$course_type_result);
      // echo "<pre>";print_r($result);exit;
        $result['get_student_sgpa'] = $tabulation_report->fetchBackStudentSgpa($academic_id, $term_id, $stu_id);

        $result['tabulation_id'] = $result['get_student_sgpa']['tabl_id'];
        if(!$result['tabulation_id']){
            return false;
       }
        $result['student_course_marks'] = $this->getBackStudentMarks($academic_id, $result['tabulation_id'], $stu_id, $term_id);
      // echo "<pre>";print_r($result['student_course_marks']);exit;
     //   echo $academic_id;die;
        $result['student_details'] = $student_info->getRecord($stu_id);
        $result['term_details'] = $term_details->getRecord($term_id);
        $result['academic_details'] = $academic_details->getRecord($academic_id);
      //echo "<pre>";print_r($result);exit;
        return $result;
       
   }
   
    public function backPaperReportAction(){
      //  die;

        $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("id");
        $academic_id = $this->_getParam("acd_id");
        $year_id = $this->_getParam("year");
        $mode = $this->_getParam("mode");
        $session = 0;
        $term_id = $this->_getParam("term");
        $cmn_terms=$term_id;
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        
        $term_master = new Application_Model_TermMaster();
        $academic_master = new Application_Model_Academic();
        $term_result = $term_master->getTermRecordsByYear($academic_id, $year_id);
         $core_course_master = new Application_Model_Corecourselearning();
        $filename = 'Grade Report';  
        $stuname = 'no student';
        $term_iterator = array();
        $single = false;
        $pge_height = 400;
        //============QR COde Generator url link for studenr=====================//
         $url = $this->_base_url."application/back-paper-report/id/$stu_id/acd_id/$academic_id/year/$year_id/mode/view";
      //------------------[bulk term_id]---------------//  
        if(empty($term_id)){
            
          //  echo "<pre>"; print_r($item_result);exit;
            $pge_height = 0;
            foreach($term_result as $key => $value){
            
                    $result = $this->getBackReports($stu_id,$academic_id,$year_id,$value['term_id']);
                    if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $tabl_date[$value['term_id']] = $result['get_student_sgpa']['added_date'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                              $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $term_iterator[] = $value['term_id'];
                            $stuname = $result['student_details']['stu_fname'];
                            $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));         $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
                            $pge_height+=300;
                    }
                }
        }
        else if(!empty($term_id))
        {
            $value['term_id'] = $term_id;
            $term_iterator[] = $term_id;
            $result = $this->getBackReports($stu_id,$academic_id,$year_id,$value['term_id']);
            if($result){
                $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                            $tabl_date[$value['term_id']] = $result['get_student_sgpa']['added_date'];
                            $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $stuname = $result['student_details']['stu_fname'];
                            $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));         $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
                            $filename = $academic_details[$value['term_id']] . '-'.$term_details[$value['term_id']]['term_name'].'-Grade-Report';
                           
            }
             $single = true;
        }
       //---------------------[END]-------------------------// 
        
        
        
        $degree_id = $academic_master->getAcademicDegree($academic_id);
        if(!empty($degree_id))
        $passpercent = $this->getPasspercent($degree_id,$session);
        
        $this->view->passpercent = $passpercent;
        $this->view->student_marks_details = $student_course_marks;
        $this->view->stu_sgpa = $get_student_sgpa;
        $this->view->stu_fail_in = $get_student_fail_in;
        $this->view->stu_details = $student_details;
        $this->view->stu_term_details = $term_details;
        $this->view->stu_academic_details = $academic_details;
        $this->view->stu_course = $formated_result;
        $this->view->header_component = $max;
        $this->view->corespan = $core_course_span;
        $this->view->term_marks = $term_iterator;
        $this->view->single = $single;
        $this->view->sgpaspan = $course_credit_info;
        $this->view->cmn_terms = $cmn_terms;
     
        $this->view->publish_dates = $tabl_date;
        
       
        $this->view->url = $url;
                

        
       
        
        //===================[GRADE SHEET NUMBER]===============================//
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $stu_id);
                $this->view->gradesheet_number = $gradesheet_number;
        //------------------------------[Print Option According to Mode]-------------------//
         $htmlcontent = $this->view->render('application/back-paper-report.phtml');
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $stuname . '-' .$filename,'L',$pge_height );
       //-----------------------[END]--------------------//     
    }
    public function ajaxGetBackGradeViewAction(){
      //  die;

        $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("stu_id");
        $academic_id = $this->_getParam("academic_id");
        $year_id = $this->_getParam("year_id");
        $mode = $this->_getParam("mode");
        $term_id = $this->_getParam("term_id");
        $term = $this->_getParam("cmn_term");
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        $term_master = new Application_Model_TermMaster();
        $academic_master = new Application_Model_Academic();
        $term_result = $term_master->getRecordByAcademicId($academic_id);
      
      //  $term_result = $term_master->getTermRecordsByYear($academic_id, $year_id);
         $core_course_master = new Application_Model_Corecourselearning();
        $filename = 'Grade Report';  
        $stuname = 'no student';
        $term_iterator = array();
        $single = false;
        $pge_height = 400;
        //============QR COde Generator url link for studenr=====================//
         $url = $this->_base_url."application/back-paper-report/id/$stu_id/acd_id/$academic_id/year/$year_id/mode/view";
      //------------------[bulk term_id]---------------//  
        if(empty($term_id)){
            
          //  echo "<pre>"; print_r($item_result);exit;
            $pge_height = 0;
            foreach($term_result as $key => $value){
            
                    $result = $this->getBackReports($stu_id,$academic_id,$year_id,$value['term_id']);
                    if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $tabl_date[$value['term_id']] = $result['get_student_sgpa']['added_date'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                              $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $term_iterator[] = $value['term_id'];
                            $stuname = $result['student_details']['stu_fname'];
                            $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));         $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
                            $pge_height+=300;
                    }
                }
        }
        else if(!empty($term_id))
        {
            $value['term_id'] = $term_id;
            $term_iterator[] = $term_id;
            $result = $this->getBackReports($stu_id,$academic_id,$year_id,$value['term_id']);
            if($result){
                $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $tabl_date[$value['term_id']] = $result['get_student_sgpa']['added_date'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                            $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $stuname = $result['student_details']['stu_fname'];
                  $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));         $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
                            $filename = $academic_details[$value['term_id']] . '-'.$term_details[$value['term_id']]['term_name'].'-Grade-Report';
                           
            }
             $single = true;
        }
       //---------------------[END]-------------------------// 
        
        
        
        $degree_id = $academic_master->getAcademicDegree($academic_id);
        if(!empty($degree_id))
        $passpercent = $this->getPasspercent($degree_id,$session);
        $this->view->passpercent = $passpercent;
        $this->view->student_marks_details = $student_course_marks;
        $this->view->stu_sgpa = $get_student_sgpa;
        $this->view->stu_fail_in = $get_student_fail_in;
        $this->view->stu_details = $student_details;
        $this->view->stu_term_details = $term_details;
        $this->view->stu_academic_details = $academic_details;
        $this->view->stu_course = $formated_result;
        $this->view->header_component = $max;
        $this->view->corespan = $core_course_span;
        $this->view->term_marks = $term_iterator;
        $this->view->single = $single;
        $this->view->sgpaspan = $course_credit_info;
        $this->view->publish_dates = $tabl_date;
         $this->view->cmn_terms = $term;
        $this->view->url = $url;
        
        //===================[GRADE SHEET NUMBER]===============================//
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $stu_id);
                $this->view->gradesheet_number = $gradesheet_number;
    }
    //=========================[END BACK PAPER]==========================================//
    
    
    
      //==================get fail pass student details===========================//  
   public function getReports($stu_id,$academic_id,$year_id,$term_id,$arc_status=false){
      
       $result = array();
               $student_info = new Application_Model_StudentPortal();
        $tabulation_report = new Application_Model_TabulationReport();
        $eval_component = new Application_Model_EvaluationComponentsItems();
        $term_details = new Application_Model_TermMaster();
        $academic_details = new Application_Model_Academic();
        $Corecourselearning_model = new Application_Model_Corecourselearning();
        
        
//        $term_id = $term_details->getTermRecordsbycmn($academic_id,$term_id)['term_id'];
        
        $result['core_course_span'] = $Corecourselearning_model->getcorecourse($academic_id, $term_id);
      
      
        
        $cc_id = $Corecourselearning_model->getOnCC($academic_id, $term_id);
        
    
        
       $course_type_result = $this->geStudentCourse($academic_id, $term_id,$stu_id);
       $course_type_result = array_values($course_type_result);
        
        $course_ids = $this->selectData($course_type_result, array('course_id'),count($course_type_result));
          
        $compo_count = array();
        $key_val = array();
         foreach($course_ids as $key => $value){
            $component_res = $eval_component->getRecordsByCourse($value['course_id']);
            $compo_count[] = count($component_res);
            $key_val[count($component_res)] =  $component_res;
        }
        
      
        $result['max_num'] = $key_val[max($compo_count)];
       
        $result['formated_result'] =  $this->stackData($cc_id,$course_type_result);
  
        $result['get_student_sgpa'] = $tabulation_report->fetchStudentSgpa($academic_id, $term_id, $stu_id);
      

        
        if($result['get_student_sgpa']['btbl_id']){
           $result['btabulation_id'] = $result['get_student_sgpa']['tabl_id'];
           $result['dateTerm_id'] = $term_id; 
        }
        else
        {
           
            $result['tabulation_id'] = $result['get_student_sgpa']['tabl_id'];
        }
        
        $result['tabulation_id'] =$result['btabulation_id']?$result['btabulation_id']:$result['get_student_sgpa']['tabl_id'];
        if(!$result['tabulation_id']){
            return false;
       }
        
        $result['student_course_marks'] = $this->getStudentMarks($academic_id, $result['tabulation_id'], $stu_id, $term_id,$arc_status);
        
  
        $result['student_details'] = $student_info->getRecord($stu_id);
        $result['term_details'] = $term_details->getRecord($term_id);
        $result['academic_details'] = $academic_details->getRecord($academic_id);
        return $result;
       
       
       
   }
    
    
 public function secondyeargradesheetreportAction(){
      $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("id");
        $academic_id = $this->_getParam("acd_id");
        $year_id = $this->_getParam("year");
        $mode = $this->_getParam("mode");
        $session = 0;
        $term_id = $this->_getParam("term");
        $cmn_terms=$term_id;
        $term_master = new Application_Model_TermMaster();
        if($term_id)
         $term_id = $term_master->getTermRecordsbycmn($academic_id,$term_id)['term_id'];
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        $academic_master = new Application_Model_Academic();
        if($year_id)
        {
          $term_result = $term_master->getTermRecordsByYear($academic_id,$year_id);
        }else{
                $term_result = $term_master->getRecordByAcademicId($academic_id);
            }
    //    $term_result = $term_master->getTermRecordsByYear($academic_id, $year_id);
          $core_course_master = new Application_Model_Corecourselearning();

        $filename = 'Grade Report';  
        $stuname = 'no student';
        $dateterm_id = '';
        $prom_text = array();
        $term_iterator = array();
        $single = false;
        $pge_height = 350;
        //============QR COde Generator url link for studenr=====================//
         $url = $this->_base_url."application/secondyeargradesheetreport/id/$stu_id/acd_id/$academic_id/year/$year_id/mode/view";
      //------------------[bulk term_id]---------------//  
        if(empty($term_id)){
            
          //  echo "<pre>"; print_r($item_result);exit;
            $pge_height = 0;
            foreach($term_result as $key => $value){
            
                    $result = $this->getReports($stu_id,$academic_id,$year_id,$value['term_id']);
                    if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                            $tabl_date = $result['get_student_sgpa']['added_date'];
                             $prom_text[$value['term_id']] = $result['get_student_sgpa']['promotion_text']=='--'?'Not Promoted':$result['get_student_sgpa']['promotion_text'];
                              $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $term_iterator[] = $value['term_id'];
                            $stuname = $result['student_details']['stu_fname'];
                             $tbl_id =  $result['tabulation_id'];
                            $dateterm_id = !isset($result['dateTerm_id'])?$dateterm_id:$result['dateTerm_id'];
          $course_ids = $this->mergData($student_course_marks[$value['term_id']], array('course_id'), count($student_course_marks[$value['term_id']]));                 $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $value['term_id'], $course_ids));
                            $pge_height+=100;
                    }
                }
        }
        else if(!empty($term_id))
        {
            $value['term_id'] = $term_id;
            $term_iterator[] = $term_id;
            $result = $this->getReports($stu_id,$academic_id,$year_id,$value['term_id']);
          
            if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $tabl_date = $result['get_student_sgpa']['added_date'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                            $student_details = $result['student_details'];
                            $prom_text[$value['term_id']] = $result['get_student_sgpa']['promotion_text']=='--'?'Not Promoted':$result['get_student_sgpa']['promotion_text'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $stuname = $result['student_details']['stu_fname'];
                            $filename =  '-'.$term_details[$value['term_id']]['term_name'].'-Grade-Report';
                         //$pge_height+=count($result['core_course_span'])*20;
                             $tbl_id =  $result['tabulation_id'];
                            $dateterm_id = !isset($result['dateTerm_id'])?$dateterm_id:$result['dateTerm_id'];
                            $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));         
                            $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
       
                           
            }
             $single = true;
        }
       //---------------------[END]-------------------------// 
        
        $pge_height+=140;
        
        $degree_id = $academic_master->getAcademicDegree($academic_id);
        
        if(!empty($degree_id))
        $passpercent = $this->getPasspercent($degree_id,$session);
    
        $this->view->passpercent = $passpercent;
        $this->view->student_marks_details = $student_course_marks;
        $this->view->stu_sgpa = $get_student_sgpa;
        $this->view->stu_fail_in = $get_student_fail_in;
        $this->view->stu_details = $student_details;
        $this->view->stu_term_details = $term_details;
        $this->view->stu_academic_details = $academic_details;
        $this->view->stu_course = $formated_result;
        $this->view->header_component = $max;
        $this->view->corespan = $core_course_span;
        $this->view->term_marks = $term_iterator;
        $this->view->single = $single;
        $this->view->prom = $prom_text;
        $this->view->sgpaspan = $course_credit_info;
    //   echo "<pre>";print_r($formated_result);exit;
        $this->view->publish_dates = $tabl_date;
        $this->view->cmn_terms = $cmn_terms;
        $this->view->tabldetails = array('tabl_id'=>$tbl_id,'term_id'=>$dateterm_id);
        if($term_id)
        $this->view->term_id = $term_id;
        else
            $this->view->term_id = 0;
        
        $this->view->url = $url;
        
        //===================[GRADE SHEET NUMBER]===============================//
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, 1, $stu_id,0);
                $this->view->gradesheet_number = $gradesheet_number;
        //------------------------------[Print Option According to Mode]-------------------//
         $htmlcontent = $this->view->render('application/secondyeargradesheetreport.phtml');
         if(empty($term_id)){
         $backcontent = $this->view->render('report/backpage.phtml');
         }
         else
         {
             $backcontent="";
         }
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $stuname . '-' .$filename,'L',$pge_height,$backcontent );
       //-----------------------[END]--------------------//      
    }
    
  public function ajaxGetGradeViewAction(){
           http_response_code(500);
   $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
        $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("stu_id");
        $academic_id = $this->_getParam("academic_id");
        $year_id = $this->_getParam("year_id");
        $mode = $this->_getParam("mode");
        $session = 0;
        $term_id = $this->_getParam("term_id");
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        
       
        $term_master = new Application_Model_TermMaster();
        $academic_master = new Application_Model_Academic();
        $term_result = $term_master->getTermRecordsByYear($academic_id, $year_id);
          $core_course_master = new Application_Model_Corecourselearning();
 
        $filename = 'Grade Report';  
        $stuname = 'no student';
        $prom_text = '';
        $dateterm_id = '';
        $term_iterator = array();
        $single = false;
        $pge_height = 350;
        //============QR COde Generator url link for studenr=====================//
         $url = $this->_base_url."application/secondyeargradesheetreport/id/$stu_id/acd_id/$academic_id/year/$year_id/mode/view";
      //------------------[bulk term_id]---------------//  
        if(empty($term_id)){
            
          //  echo "<pre>"; print_r($item_result);exit;
            $pge_height = 0;
            foreach($term_result as $key => $value){
            
                    $result = $this->getReports($stu_id,$academic_id,$year_id,$value['term_id']);
                    if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                            $tabl_date[$value['term_id']] = $result['get_student_sgpa']['added_date'];
                              $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                             $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $term_iterator[] = $value['term_id'];
                             $tbl_id =  $result['tabulation_id'];
                            $dateterm_id = !isset($result['dateTerm_id'])?$dateterm_id:$result['dateTerm_id'];
                            $stuname = $result['student_details']['stu_fname'];
          $course_ids = $this->mergData($student_course_marks[$value['term_id']], array('course_id'), count($student_course_marks[$value['term_id']]));                 $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $value['term_id'], $course_ids));
                            $pge_height+=300;
                    }
                }
        }
        else if(!empty($term_id))
        {
            $value['term_id'] = $term_id;
            $term_iterator[] = $term_id;
            
       
            $result = $this->getReports($stu_id,$academic_id,$year_id,$value['term_id']);
            if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                            $tabl_date[$value['term_id']] = $result['get_student_sgpa']['added_date'];
                            $student_details = $result['student_details'];
                        $prom_text = $result['get_student_sgpa']['promotion_text']=='--'?'Not Promoted':$result['get_student_sgpa']['promotion_text'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                             $session = $result['academic_details']['session'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                             $tbl_id =  $result['tabulation_id'];
                            $dateterm_id = !isset($result['dateTerm_id'])?$dateterm_id:$result['dateTerm_id'];
                            $stuname = $result['student_details']['stu_fname'];
                            $filename = $academic_details[$value['term_id']] . '-'.$term_details[$value['term_id']]['term_name'].'-Grade-Report';
                            
                            $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));         $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
       
                           
            }
             $single = true;
        }
       //---------------------[END]-------------------------// 
        
        
        
        $degree_id = $academic_master->getAcademicDegree($academic_id);
       
        if(!empty($degree_id))
        $passpercent = $this->getPasspercent($degree_id,$session);
        
        $this->view->passpercent = $passpercent;
        $this->view->student_marks_details = $student_course_marks;
        $this->view->stu_sgpa = $get_student_sgpa;
        $this->view->stu_fail_in = $get_student_fail_in;
        $this->view->stu_details = $student_details;
        $this->view->stu_term_details = $term_details;
        $this->view->stu_academic_details = $academic_details;
        $this->view->stu_course = $formated_result;
        $this->view->header_component = $max;
        $this->view->corespan = $core_course_span;
        $this->view->term_marks = $term_iterator;
        $this->view->single = $single;
        $this->view->prom = $prom_text;
        $this->view->sgpaspan = $course_credit_info;
         $this->view->publish_dates = $tabl_date;
         $this->view->tabldetails = array('tabl_id'=>$tbl_id,'term_id'=>$dateterm_id);
        if($term_id)
        $this->view->term_id = $term_id;
        else
            $this->view->term_id = 0;
       
        $this->view->url = $url;
                

        
       
        
        //===================[GRADE SHEET NUMBER]===============================//
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $stu_id,0);
                $this->view->gradesheet_number = $gradesheet_number;
                
        }
       //-----------------------[END]--------------------//      
    }
    
 
  
public function getPasspercent($degree_id='',$session=''){
            try{  
                if(!empty($degree_id)){
            $ReferenceGradeMasterItems_model = new Application_Model_ReferenceGradeMasterItems();
            $ref_grades = $ReferenceGradeMasterItems_model->getRecordsByAcademicId(0, $degree_id,$session);

            $arr_ref_number_grade_all = $this->mergData($ref_grades, array('number_grade'), count($ref_grades));
            $arr_ref_number_grade_all = array_filter($arr_ref_number_grade_all, function($value) {
                return $value > 0;
            });
            $min_pass_percent = min($arr_ref_number_grade_all);

            $min_pass_percent = count($ref_grades) == 0 ? 0 : $min_pass_percent;

            $range = $ReferenceGradeMasterItems_model->getRecordsByNumgrade($min_pass_percent,$degree_id,$session);



            $min_pass_percent = count($ref_grades) == 0 ? 0 : min($range);
            
            return $min_pass_percent;
                }
                throw new Exception('$degree_id should not be empty !');
            }
            catch(Exception $e){
                echo $e->getMessage();die;
            }
        
        
    }
    
public function geStudentCourseA($academic_id,$term_id,$stu_id){
        $Corecourselearning_model = new Application_Model_Corecourselearning();
          $core_courses = $Corecourselearning_model->getcorecourse($academic_id, $term_id);
          $student_ge = $Corecourselearning_model->getStudentGE($academic_id, $term_id,$stu_id);
          $course_type_result = array_merge($core_courses,$student_ge);
          return $course_type_result;
    }
    
public function getStudentMarks($academic_id, $tabulation_id, $stu_id, $term_id) {

        $GradeAllocationReport = new Application_Model_GradeAllocationReportItems();
        $GradeMaster = new Application_Model_GradeAllocation();
        $getStudent_course_details = $GradeAllocationReport->getMarksDetailsWithCourse($tabulation_id, $stu_id, $term_id);
        
        foreach ($getStudent_course_details as $key => $value) {
            $getStudent_course_details[$key]['number_value'] = $GradeMaster->getGradeRecordsOn($academic_id, $term_id, $value['course_id'], $stu_id)['number_value'];
            $getStudent_course_details[$key]['grade_value'] = $GradeMaster->getGradeRecordsOn($academic_id, $term_id, $value['course_id'], $stu_id)['grade_value'];
        }

        return $getStudent_course_details;
    }
 
    ////-----------------//////19////////----------
    
   
    
    public function redirectExamform($message,$col = false){
        $this->_flashMessenger->addMessage($message);
        if(!$col)
        $this->_redirect('application/examform');
        elseif($col=='pg')
           $this->_redirect('application/pgnoncollegiate'); 
         elseif($col=='admitcard')
           $this->_redirect('application/admitcard/'); 
         elseif($col=='pgexam')
           $this->_redirect('application/examform/'); 

        else
           $this->_redirect('application/noncollegiate');  
    }

//// payment method add by ruashan////
    
public function paymentAction() {
        
        //====only for two students 11-08-2020
       
  //  http_response_code(404); die;
        require_once APPLICATION_PATH . '/public/atompay/TransactionRequest.php';
        
        
         $pay_form = new Application_Form_PaymentForm();
          $this->view->form = $pay_form;
          $semattend = new Application_Model_BatachSemesterAttendance();
          $exammodel = new Application_Model_ExamformSubmissionModel();
          $examfee = new Application_Model_ExamfeeSubmitModel();
          $studetails = new Application_Model_StudentPortal();
          
            $term_obj = new Application_Model_TermMaster();
          $dept_model = new Application_Model_Department();
          $academic_model = new Application_Model_Academic();
          $fee_model = new Application_Model_Coursefee();
          $transactionRequest = new Application_Model_TransactionRequest();
          //$transactionRequest = new Application_Model_TransactionRequest();exa
          
           $type = $this->_getParam("type");
           
           $this->view->type = $type;
           
           $fids = array(md5("F-2018-4966"),md5("F-2018-3780"));
           
           $fid = $_GET['fid'];
          
            
                   
          $transactionRequest  = new TransactionRequest();
           switch ($type) {   
               case "add":    
                //   print_r($_POST);
                //   die();
                   // http_response_code(404); die;
                   $details_stu = $studetails->getStudenInfoByU($_POST['registraion_num']);
                    $acad_id = $details_stu['academic_id'];
                    $term_ids = 0;
                
                if($_GET['col']){
                    
                 //   $term_ids = $term_obj->getTermRecordsbycmn($acad_id,'t1');
                }
                else
                {
                 
                    //$term_ids = $term_obj->getTermRecordsbycmn($acad_id,'t1');
                }
          
               
                        $insertdata = array (
                       'u_id' => $_POST['registraion_num'],
                        'exam_id' => $_POST['exmination_id'],
                        'stu_name' => $_POST['stu_name'],
                        'total_fee' => $_POST['total_fee'],
                        'exam_fee' => $_POST['exam_fee'],
                        'late_fine' => $_POST['late_fine'],    
                        'late_fine' => $_POST['late_fine'],
                        'term_id' => $term_ids,
                        'acad_id'=>$acad_id,
                        'payment_id'=>$_POST['last_id'],
                        'submit_date' => date('Y-m-d')
                        );
                        
                        
                             $stu_details = new Application_Model_StudentPortal();
                             $details = $stu_details->getStudentM($_POST['registraion_num']);
                             $acad_id  = $details['academic_id'];
                             $acad_details = new Application_Model_Academic();
                             $ac_details = $acad_details->getRecord($acad_id);
                             $dep_id = $ac_details['department'];
                             $cf = new Application_Model_Coursefee();
                             $fe_details = $cf->getRecordByDepartment($dep_id);
                           // echo "<pre>";print_r($fe_details) ; exit;
                             
                            $last_id =$_POST['last_id'];
                        //echo $sfid;
                        //die();
                            $last_insert_id = $examfee->insert($insertdata);
                           $mail_data =  $exammodel->getRecordbyfid3($_POST['registraion_num']);
                           
                      //$exammodel->update(array('payment_status'=> 1), array('u_id=?' => $sfid))  ;
                      $this->_flashMessenger->addMessage('Payment Successfully now You can Download your Admit Card');
                      date_default_timezone_set('Asia/Calcutta');
                        $datenow = date("d/m/Y h:m:s");
                        $transactionDate = str_replace(" ", "%20", $datenow);

                        $transactionId = rand(1,1000000);

                        //Setting all values here
                        $transactionRequest->setMode("live");
                        $transactionRequest->setLogin(53243);
                        $transactionRequest->setPassword("Patna@1234");
                        $transactionRequest->setProductId($fe_details['product_id']);
                        $transactionRequest->setAmount($_POST['total_fee']);
                        $transactionRequest->setTransactionCurrency("INR");
                        $transactionRequest->setTransactionAmount($_POST['total_fee']);
                        $transactionRequest->setReturnUrl($this->_base_url  . 'application/response/?u_id='.$_POST['registraion_num']."_$last_id"."_$last_insert_id");
                        $transactionRequest->setClientCode(53243);
                        $transactionRequest->setTransactionId($transactionId);
                        $transactionRequest->setTransactionDate($transactionDate);
                        $transactionRequest->setCustomerName($_POST['stu_name']);
                        $transactionRequest->setCustomerEmailId($mail_data['email']);
                        $transactionRequest->setCustomerMobile($mail_data['phone']);
                        $transactionRequest->setCustomerBillingAddress($_POST['registraion_num']);
                        $transactionRequest->setCustomerId($_POST['exmination_id']."|".$details['roll_no']);
                        $transactionRequest->setCustomerAccount("639827");
                       $transactionRequest->setReqHashKey("b338c08a991313f12c");
                       
                       // echo "<pre>";print_r($transactionRequest);exit;
                       
                        $url = $transactionRequest->getPGUrl();
                        $this->_redirect($url);
                      
                      //$this->_redirect('application/admitcard');
                  
                    break;
                default:
              $entranceExamModel = new Application_Model_EntranceExamScheduleModel();  
        //   if(!in_array($fid,$fids)){
        //       $this->_refresh(3,'https://www.pwcadmissions.in/index.html','Date is already closed for Exammination form !');
        //   }
              // $data = $exammodel->getRecordbyfid($fid);
               $data = $exammodel->getRecordbyfid1($fid);
               
            //echo "<pre>";print_r($data);
          // die();
             $stu = $studetails->getStudenInfoByU($fid);
              //echo "<pre>";print_r($stu);
          /// die();
         //  echo "<pre>";print_r($data);
          // die();
             
             $this->view->fid = $data['u_id'];
             $this->view->eid = $data['examination_id'];
             $this->view->stdname = $data['stu_name'];
             $this->view->email = $data['email'];
             $this->view->number = $data['phone'];
             $this->view->lastid = $data['id'];
             
            
           
                    
                     
                      if($data['academic_year_id']==0 || empty($data['academic_year_id'])){
                          
                            $this->redirectExamform($fid.' not present in academic!',$_GET['col']);
                        } else {
                            $dept_id = $academic_model->getRecord($data['academic_year_id']);
                            if(!$dept_id['department']){
                                $this->redirectExamform('Access denied by Administrator',$_GET['col']);
                            }else{
                                $fee_pay = $fee_model->getColByDepartment($dept_id['department']);
                              
                                 if($fee_pay['feeForm_start_date']<=date('Y-m-d') || $fee_pay['feeForm_extended_date'] <=date('Y-m-d')){
                                 // print_r($fee_pay);die();
                                     if(!$fee_pay['examFee']){
                                $this->redirectExamform('No Exam Fee Applicable',$_GET['col']);}
                                 else{
                                     
                                     $oneday = 24*60*60;
                                        $diff = (strtotime(date('Y-m-d')) - strtotime($fee_pay['feeForm_end_date']));
                                        $diff1 = $diff/$oneday;
                                    // $fee_pay['examFee']
                                      $this->view->feeamount = $fee_pay['examFee'];}
                            }
                               elseif($fee_pay['feeForm_end_date']<=date('Y-m-d') && $fee_pay['feeForm_extended_date'] >= date('Y-m-d')){
                                 if(!$fee_pay['examFee']){
                                $this->redirectExamform('No Exam Fee Applicable',$_GET['col']);}
                                 else{
                                     
                                     $oneday = 24*60*60;
                                        $diff = (strtotime(date('Y-m-d')) - strtotime($fee_pay['feeForm_end_date']));
                                        $diff1 = $diff/$oneday;
                                    // $fee_pay['examFee']
                                      $this->view->feeamount = $fee_pay['examFee'];}
                            }
                               else if($fee_pay['feeForm_start_date']<=date('Y-m-d') && $fee_pay['feeForm_end_date'] >= date('Y-m-d')){
                                 if(!$fee_pay['examFee']){
                                $this->redirectExamform('No Exam Fee Applicable',$_GET['col']);}
                                 else{
                                    // $fee_pay['examFee']
                                      $this->view->feeamount = $fee_pay['examFee'];}
                            }
                            else
                            {
                                 $this->_refresh(3,'https://www.pwcadmissions.in/index.html','Date is already closed for Exammination form OR Exam Form Not Start Yet!');
                            }
                            }
                        }
                  
                   

                      $currentdate= date('Y-m-d');
                      
                      //$currentdate= $data['created_date'];
                    //  echo $currentdate; exit;
                    // $fee_pay['feeForm_start_date'];
                     // $fee_pay['feeForm_end_date']; 
                     // $fee_pay['feeForm_extended_date']; 
                     //die();
                     
                     
                     if($currentdate <= $fee_pay['feeForm_end_date'])
                     {
                        $this->view->examfeena = 'NA';
                      //echo "NA";
                      //die();
                         
                     }
                     else
                     {
                      if ($currentdate <= $fee_pay['feeForm_extended_date'])  {
                         $oneday = 24*60*60;
                      $datetime1 = new DateTime($currentdate);//====greater date
                    $datetime2 = new DateTime($fee_pay['feeForm_end_date']);//====less date
                    $difference = $datetime1->diff($datetime2);
                 //   print_r($difference->d); die();
                    
                    $finefee = $fee_pay['fineFee'];
                    $perDayinc = $fee_pay['perday'];
                   // echo $perDayinc; die();
                  // echo $fee_pay['fineFee']; die();
                        if($difference->d > $perDayinc){
                            $addfineTimes = ($difference->d / $perDayinc);
                           // echo $addfineTimes; die();
                            $finefee=0;
                                for($i = 0.5 ;$i<=$addfineTimes;$i++ ){
                                   $multi+= $fee_pay['fineFee'];
                                  
                                }
                            $this->view->examfeena = $multi;     
                        }
                        else {
                             $this->view->examfeena = $fee_pay['fineFee'];
                            $multi= $fee_pay['fineFee']; 
                        }
                       // echo  $multi; die();
                         
//                      $diff = ($fee_pay['feeForm_extended_date'] - $fee_pay['feeForm_end_date']);
//                       echo $diff; die();
//                      $diff1 = $diff/$fee_pay['perday'];
//                     
//                      if($fee_pay['perday']>=$diff1){
//                      $multi = $fee_pay['fineFee'] * $diff1;
//                      // $this->view->examfeena = $multi;
//                      } else {
//                       $multi = $fee_pay['fineFee'] * $diff1;
//                       $this->view->examfeena = $multi; 
//                      }
                       
                      }
                      else {
                         // echo "hii";
                          //die();
                        $this->_refresh(3,'https://www.pwcadmissions.in/index.html','Extended Date is already closed for Exammination form !');
                             
                      }
                      //echo  round($diff1);
                      // die();
                         
                     }
                      $this->view->totalfee = $multi + $fee_pay['examFee'] ;
                      $this->view->extdate = $fee_pay['feeForm_extended_date'] ;
             //print_r($_POST);
              //$pay_form->populate($_POST); 
             // die();
        
                    break;   
           }
    }
    
public function ugnonpaymentAction() {
       
    //http_response_code(404); die;
        //require_once APPLICATION_PATH . '/public/atompay/TransactionRequest.php';
        
        
         $pay_form = new Application_Form_PaymentForm();
          $this->view->form = $pay_form;
          $semattend = new Application_Model_BatachSemesterAttendance();
          $exammodel = new Application_Model_UgNonformSubmissionModel();
          $examfee = new Application_Model_NonColpayment();
          $studetails = new Application_Model_StudentPortal();
          
            $term_obj = new Application_Model_TermMaster();
          $dept_model = new Application_Model_Department();
          $academic_model = new Application_Model_Academic();
          $fee_model = new Application_Model_Coursefee();
          $transactionRequest = new Application_Model_TransactionRequest();
          //$transactionRequest = new Application_Model_TransactionRequest();
          
           $type = $this->_getParam("type");
           
           $this->view->type = $type;
           
           $fid = $_GET['fid'];
           
                   
          $transactionRequest  = new TransactionRequest();
           switch ($type) {   
               case "add":    
                  // print_r($_POST);
               //    die();
                   // http_response_code(404); die;
                   $details_stu = $studetails->getStudenInfoByU($_POST['registraion_num']);
                    $acad_id = $details_stu['academic_id'];
                    $term_ids = 0;
                
                if($_GET['col']){
                    
                 //   $term_ids = $term_obj->getTermRecordsbycmn($acad_id,'t1');
                }
                else
                {
                 
                    //$term_ids = $term_obj->getTermRecordsbycmn($acad_id,'t1');
                }
          
               
                        $insertdata = array (
                       'u_id' => $_POST['registraion_num'],
                        'exam_id' => $_POST['exmination_id'],
                        'stu_name' => $_POST['stu_name'],
                        'total_fee' => $_POST['total_fee'],
                        'exam_fee' => $_POST['exam_fee'],
                        'late_fine' => $_POST['late_fine'],               
                        'term_id' => $term_ids,
                        'acad_id'=>$acad_id,
                        'payment_id'=>$_POST['last_id'],
                        'submit_date' => date('Y-m-d')
                        );
                        
                        
                             $stu_details = new Application_Model_StudentPortal();
                             $details = $stu_details->getStudentM($_POST['registraion_num']);
                             $acad_id  = $details['academic_id'];
                             $acad_details = new Application_Model_Academic();
                             $ac_details = $acad_details->getRecord($acad_id);
                             $dep_id = $ac_details['department'];
                             $cf = new Application_Model_Coursefee();
                             $fe_details = $cf->getRecordByDepartment($dep_id);
                           // echo "<pre>";print_r($fe_details) ; exit;
                             
                            $last_id =$_POST['last_id'];
                        //echo $sfid;
                        //die();
                            $last_insert_id = $examfee->insert($insertdata);
                           $mail_data =  $exammodel->getRecordbyfid3($_POST['registraion_num']);
                           
                      //$exammodel->update(array('payment_status'=> 1), array('u_id=?' => $sfid))  ;
                      $this->_flashMessenger->addMessage('Payment Successfully now You can Download your Admit Card');
                      date_default_timezone_set('Asia/Calcutta');
                        $datenow = date("d/m/Y h:m:s");
                        $transactionDate = str_replace(" ", "%20", $datenow);

                        $transactionId = rand(1,1000000);
  //echo "<pre>";print_r($transactionRequest);exit;
                        //Setting all values here
                        $transactionRequest->setMode("live");
                        $transactionRequest->setLogin(53243);
                        $transactionRequest->setPassword("Patna@1234");
                        $transactionRequest->setProductId($fe_details['product_id']);
                        $transactionRequest->setAmount($_POST['total_fee']);
                        $transactionRequest->setTransactionCurrency("INR");
                        $transactionRequest->setTransactionAmount($_POST['total_fee']);
                        $transactionRequest->setReturnUrl($this->_base_url  . 'application/ugnonresponse/?u_id='.$_POST['registraion_num']."_$last_id"."_$last_insert_id");
                        $transactionRequest->setClientCode(53243);
                        $transactionRequest->setTransactionId($transactionId);
                        $transactionRequest->setTransactionDate($transactionDate);
                        $transactionRequest->setCustomerName($_POST['stu_name']);
                        $transactionRequest->setCustomerEmailId($mail_data['email']);
                        $transactionRequest->setCustomerMobile($mail_data['phone']);
                        $transactionRequest->setCustomerBillingAddress($_POST['registraion_num']."|".'Non-Collegiate');
                        $transactionRequest->setCustomerId($_POST['exmination_id']."|".$details['roll_no']);
                        $transactionRequest->setCustomerAccount("639827");
                       $transactionRequest->setReqHashKey("b338c08a991313f12c");
                   
                        $url = $transactionRequest->getPGUrl();
                        $this->_redirect($url);
                      
                      //$this->_redirect('application/admitcard');
                  
                    break;
                default:
                   /////http_response_code(404); die; 
              // $data = $exammodel->getRecordbyfid($fid);
               $data = $exammodel->getRecordbyfid1($fid);
               
               // echo "<pre>";print_r($data);
               ////die();
             $stu = $studetails->getStudenInfoByU($fid);
             //echo "<pre>";print_r($stu);
          // die();
         //  echo "<pre>";print_r($data);
          // die();
             $this->view->term = $data['term_id'];
             $this->view->fid = $data['u_id'];
             $this->view->eid = $data['examination_id'];
             $this->view->stdname = $data['stu_name'];
             $this->view->email = $data['email'];
             $this->view->number = $data['phone'];
             $this->view->lastid = $data['id'];
             
            
           
                    
                     
                      if($data['academic_year_id']==0 || empty($data['academic_year_id'])){
                          
                            $this->redirectExamform('Your Form Id not present in academic!',$_GET['col']);
                        } else {
                            $dept_id = $academic_model->getRecord($data['academic_year_id']);
                            if(!$dept_id['department']){
                                $this->redirectExamform('Access denied by Administrator',$_GET['col']);
                            }else{
                                
                                 $fee_pay = $fee_model->getNonColByDepartment($dept_id['department']);
                      
                                 
                                  if($fee_pay['feeForm_start_date']<=date('Y-m-d') || $fee_pay['feeForm_extended_date'] <=date('Y-m-d')){
                                 // print_r($fee_pay);die();
                                     if(!$fee_pay['examFee']){
                                $this->redirectExamform('No Exam Fee Applicable',$_GET['col']);}
                                 else{
                                     
                                     $oneday = 24*60*60;
                                        $diff = (strtotime(date('Y-m-d')) - strtotime($fee_pay['feeForm_end_date']));
                                        $diff1 = $diff/$oneday;
                                    // $fee_pay['examFee']
                                      $this->view->feeamount = $fee_pay['examFee'];}
                            }
                                elseif($fee_pay['feeForm_end_date']<=date('Y-m-d') && $fee_pay['feeForm_extended_date'] >= date('Y-m-d')){
                                 
                                 if(!$fee_pay['examFee']){
                                $this->redirectExamform('No Exam Fee Applicable',$_GET['col']);}
                                 else{
                                      $this->view->feeamount = $fee_pay['examFee'];}
                                  
                      }     else if($fee_pay['feeForm_start_date']<=date('Y-m-d') && $fee_pay['feeForm_end_date'] >= date('Y-m-d')){
                                     
                                if(!$fee_pay['examFee']){
                                $this->redirectExamform('No Exam Fee Applicable',$_GET['col']);}
                                 else{
                                    // $fee_pay['examFee']
                                      $this->view->feeamount = $fee_pay['examFee'];}       
                                 }
                                  
                                  else {
                                    $this->_refresh(3,'https://www.pwcadmissions.in/index.html','Extended Date is already closed OR Not Start for Exammination form !');
                             
                       
                                  }
                            }
                        }
                  
                   

                      $currentdate= date('Y-m-d');
                      
                      //$currentdate= $data['created_date'];
                    //  echo $currentdate; exit;
                    // $fee_pay['feeForm_start_date'];
                     // $fee_pay['feeForm_end_date']; 
                     // $fee_pay['feeForm_extended_date']; 
                     //die();
                     
                     
                     if($currentdate <= $fee_pay['feeForm_end_date'])
                     {
                        $this->view->examfeena = 'NA';
                      //echo "NA";
                      //die();
                         
                     }
                     else
                     {
                        if ($currentdate <= $fee_pay['feeForm_extended_date'])  {
                         $oneday = 24*60*60;
                      $datetime1 = new DateTime($currentdate);//====greater date
                    $datetime2 = new DateTime($fee_pay['feeForm_end_date']);//====less date
                    $difference = $datetime1->diff($datetime2);
                 //   print_r($difference->d); die();
                    
                    $finefee = $fee_pay['fineFee'];
                    $perDayinc = $fee_pay['perday'];
                   // echo $perDayinc; die();
                  // echo $fee_pay['fineFee']; die();
                        if($difference->d > $perDayinc){
                            $addfineTimes = ($difference->d / $perDayinc);
                           // echo $addfineTimes; die();
                            $finefee=0;
                                for($i = 0.5 ;$i<=$addfineTimes;$i++ ){
                                   $multi+= $fee_pay['fineFee'];
                                  
                                }
                            $this->view->examfeena = $multi;     
                        }
                        else {
                             $this->view->examfeena = $fee_pay['fineFee']; 
                             $multi=$fee_pay['fineFee']; 
                        }
                       // echo  $multi; die();
                         
//                      $diff = ($fee_pay['feeForm_extended_date'] - $fee_pay['feeForm_end_date']);
//                       echo $diff; die();
//                      $diff1 = $diff/$fee_pay['perday'];
//                     
//                      if($fee_pay['perday']>=$diff1){
//                      $multi = $fee_pay['fineFee'] * $diff1;
//                      // $this->view->examfeena = $multi;
//                      } else {
//                       $multi = $fee_pay['fineFee'] * $diff1;
//                       $this->view->examfeena = $multi; 
//                      }
                       
                      }
                      else {
                         // echo "hii";
                          //die();
                        $this->_refresh(3,'https://www.pwcadmissions.in/index.html','Extended Date is already closed for Exammination form !');
                             
                      }
                     }
                      $this->view->totalfee = $multi + $fee_pay['examFee'] ;
                      $this->view->extdate = $fee_pay['feeForm_extended_date'] ;
             //print_r($_POST);
              //$pay_form->populate($_POST); 
             // die();
        
                    break;   
           }
    }
     
public function responseAction(){
    require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
            $exammodel = new Application_Model_ExamformSubmissionModel();
            $payment = new Application_Model_ExamfeeSubmitModel();
            $transactionResponse = new Application_Model_TransactionResponse();
          //  $transactionResponse = new Application_Model_TransactionResponse();
          
         $transactionResponse = new TransactionResponse();
            $transactionResponse->setRespHashKey("8b013258b7a4b89428");
            $this->view->vaild_response = $transactionResponse->validateResponse($_POST);
           if($_GET['u_id']=='')
              $this->_redirect ($this->_main_url);
            $id= $_GET['u_id'];
            $id_arr = explode('_',$id);
            $id = $id_arr[0];
            $last_insert_id = $id_arr[1];
            $last_id = $id_arr[2];
            $updatedata = array(
                'mmp_txn'=>$_POST['mmp_txn'],
                'mer_txn'=>$_POST['mer_txn'],
                'bank_txn'=>$_POST['bank_txn'],
                'bank_name'=>$_POST['bank_name'],
                'prod'=>$_POST['prod'],
                'date'=>$_POST['date'],
                'f_code'=>$_POST['f_code'],
                'clientcode'=>$_POST['clientcode'],
                'merchant_id'=>$_POST['merchant_id'],
                'status'=>1
            );
                
                    if($_POST['f_code'] == 'Ok'){
                    
                    $payment->update($updatedata, array('u_id=?' => $id , 'payment_id=?'=>$last_insert_id,'id=?'=>$last_id))  ;
                   
                    $exammodel->update(array('payment_status'=> 1), array('u_id=?' => $id,'id=?'=>$last_insert_id))  ;
                    
                    $this->view->response_data = $_POST;
             }
             
           
         }
         
public function ugnonresponseAction(){
    require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
            $exammodel = new Application_Model_UgNonformSubmissionModel();
            $payment = new Application_Model_NonColpayment();
            $transactionResponse = new Application_Model_TransactionResponse();
          //  $transactionResponse = new Application_Model_TransactionResponse();
          
         $transactionResponse = new TransactionResponse();
            $transactionResponse->setRespHashKey("8b013258b7a4b89428");
            $this->view->vaild_response = $transactionResponse->validateResponse($_POST);
            if($_GET['u_id']=='')
               $this->_redirect ($this->_main_url);
            $id= $_GET['u_id'];
            $id_arr = explode('_',$id);
            $id = $id_arr[0];
            $last_insert_id = $id_arr[1];
            $last_id = $id_arr[2];
            $updatedata = array(
                'mmp_txn'=>$_POST['mmp_txn'],
                'mer_txn'=>$_POST['mer_txn'],
                'bank_txn'=>$_POST['bank_txn'],
                'bank_name'=>$_POST['bank_name'],
                'prod'=>$_POST['prod'],
                'date'=>$_POST['date'],
                'f_code'=>$_POST['f_code'],
                'clientcode'=>$_POST['clientcode'],
                'merchant_id'=>$_POST['merchant_id'],
                'status'=>1
            );
                
                    if($_POST['f_code'] == 'Ok'){
                    
                    $payment->update($updatedata, array('u_id=?' => $id , 'payment_id=?'=>$last_insert_id,'id=?'=>$last_id))  ;
                   
                    $exammodel->update(array('payment_status'=> 1), array('u_id=?' => $id,'id=?'=>$last_insert_id))  ;
                    
                    $this->view->response_data = $_POST;
             }
             
           
         } 
       
       
public function pgnonpaymentAction() {
   //http_response_code(404); die;
    
  require_once APPLICATION_PATH . '/public/atompay/TransactionRequest.php';
        
        
         $pay_form = new Application_Form_PaymentForm();
          $this->view->form = $pay_form;
          $semattend = new Application_Model_BatachSemesterAttendance();
          $exammodel = new Application_Model_NonPgDataModel();
          $examfee = new Application_Model_NonpgPaymentModel();
          $studetails = new Application_Model_StudentPortal();
          $dept_model = new Application_Model_Department();
          $academic_model = new Application_Model_Academic();
          $fee_model = new Application_Model_Coursefee();
          $transactionRequest = new Application_Model_TransactionRequest();
          //$transactionRequest = new Application_Model_TransactionRequest();
           $type = $this->_getParam("type");
           $this->view->type = $type;
           $fid = $_GET['fid'];
          
          $transactionRequest  = new TransactionRequest();
           switch ($type) {   
               case "add":    
                  // print_r($_POST);
                  // die();
                   //http_response_code(404); die;
                        $insertdata = array (
                       'u_id' => $_POST['registraion_num'],
                        'exam_id' => $_POST['exmination_id'],
                        'stu_name' => $_POST['stu_name'],
                        'total_fee' => $_POST['total_fee'],
                        'exam_fee' => $_POST['exam_fee'],
                        'late_fine' => $_POST['late_fine'],    
                        'late_fine' => $_POST['late_fine'],
                        'payment_id'=>$_POST['last_id'],
                        'submit_date' => date('Y-m-d')
                        );
                        
                        
                             $stu_details = new Application_Model_StudentPortal();
                             $details = $stu_details->getStudentM($_POST['registraion_num']);
                             $acad_id  = $details['academic_id'];
                             $acad_details = new Application_Model_Academic();
                             $ac_details = $acad_details->getRecord($acad_id);
                             $dep_id = $ac_details['department'];
                             $cf = new Application_Model_Coursefee();
                             $fe_details = $cf->getRecordByDepartment($dep_id);
                             
                             
                        //echo $sfid;
                        //die();
                           $last_id =  $_POST['last_id'];
                           $last_insert_id = $examfee->insert($insertdata);
                           $mail_data =  $exammodel->getNonRecordbyfid($_POST['registraion_num']);
                           
                      //$exammodel->update(array('payment_status'=> 1), array('u_id=?' => $sfid))  ;
                      $this->_flashMessenger->addMessage('Payment Successfully now You can Download your Admit Card');
                      date_default_timezone_set('Asia/Calcutta');
                        $datenow = date("d/m/Y h:m:s");
                        $transactionDate = str_replace(" ", "%20", $datenow);

                        $transactionId = rand(1,1000000);

                        //Setting all values here
                         $transactionRequest->setMode("live");
                        $transactionRequest->setLogin(53243);
                        $transactionRequest->setPassword("Patna@1234");
                        $transactionRequest->setProductId($fe_details['product_id']);
                        $transactionRequest->setAmount($_POST['total_fee']);
                        $transactionRequest->setTransactionCurrency("INR");
                        $transactionRequest->setTransactionAmount($_POST['total_fee']);
                        $transactionRequest->setReturnUrl($this->_base_url  . 'application/responsepg/?u_id='.$_POST['registraion_num']."_$last_id"."_$last_insert_id");
                        $transactionRequest->setClientCode(53243);
                        $transactionRequest->setTransactionId($transactionId);
                        $transactionRequest->setTransactionDate($transactionDate);
                        $transactionRequest->setCustomerName($_POST['stu_name']);
                        $transactionRequest->setCustomerEmailId($mail_data['email']);
                        $transactionRequest->setCustomerMobile($mail_data['phone']);
                        $transactionRequest->setCustomerBillingAddress($_POST['registraion_num']."|".'Non-Collegiate');
                        $transactionRequest->setCustomerId($_POST['exmination_id']."|".$details['roll_no']);
                        $transactionRequest->setCustomerAccount("639827");
                       $transactionRequest->setReqHashKey("b338c08a991313f12c");

               //print_r($transactionRequest);
              // die();


                        $url = $transactionRequest->getPGUrl();
                        $this->_redirect($url);
                      
                      //$this->_redirect('application/admitcard');
                  
                    break;
                default:
                    
               $data = $exammodel->getRecordbyfid($fid);
            //echo "<pre>";print_r($data);
           //die();
             $stu = $studetails->getStudenInfoByU($fid);
              //echo "<pre>";print_r($stu);
          /// die();
//            echo "<pre>";print_r($data);
//        die();
             
             $this->view->fid = $data['u_id'];
             $this->view->eid = $data['examination_id'];
             $this->view->stdname = $data['stu_name'];
             $this->view->email = $data['email'];
             $this->view->phone = $data['phone'];
             $this->view->last_id = $data['id'];
             
            
           
                    
                     
                      if($data['academic_year_id']==0 || empty($data['academic_year_id'])){
                            $this->redirectExamform($fid.' not present in academic!','pg');
                        } else {
                            $dept_id = $academic_model->getRecord($data['academic_year_id']);
                            if(!$dept_id['department']){
                                $this->redirectExamform('Access denied by Administrator','pg');
                            }else{
                                 $fee_pay = $fee_model->getRecordByDepartment($dept_id['department']);
                                 if(!$fee_pay['examFee']){
                                $this->redirectExamform('No Exam Fee Applicable','pg');}
                                 else{
                                      $this->view->feeamount = $fee_pay['examFee'];}
                            }
                        }
                  
                   

                      $currentdate= $data['created_date'];
                    // $fee_pay['feeForm_start_date'];
                     // $fee_pay['feeForm_end_date']; 
                     // $fee_pay['feeForm_extended_date']; 
                     //die();
                     
                     
                     if( $currentdate <= $fee_pay['feeForm_end_date'])
                     {
                        $this->view->examfeena = 'NA';
                      //echo "NA";
                      //die();
                         
                     }
                     else
                     {
                         $oneday = 24*60*60;
                      
                      $diff = (strtotime($fee_pay['feeForm_extended_date']) - strtotime($fee_pay['feeForm_end_date']));
                      
                      $diff1 = $diff/$oneday;
                      $multi = $fee_pay['fineFee'] * $diff1;
                       $this->view->examfeena = $multi;
                      //echo  round($diff1);
                       //die();
                         
                     }
                      $this->view->totalfee = $multi + $fee_pay['examFee'] ;
                      $this->view->extdate = $fee_pay['feeForm_extended_date'] ;
             //print_r($_POST);
              //$pay_form->populate($_POST); 
             // die();
        
                    break;   
           }
    }
               
public function responsepgAction(){
    require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
            $exammodel = new Application_Model_NonPgDataModel();
            $payment = new Application_Model_NonpgPaymentModel();
            $transactionResponse = new Application_Model_TransactionResponse();
          //  $transactionResponse = new Application_Model_TransactionResponse();
          
         $transactionResponse = new TransactionResponse();
            $transactionResponse->setRespHashKey("8b013258b7a4b89428");
            $this->view->vaild_response = $transactionResponse->validateResponse($_POST);
            if($_GET['u_id']=='')
              $this->_redirect ($this->_main_url);
            $id= $_GET['u_id'];
            $id_arr = explode('_',$id);
            $id = $id_arr[0];
            $last_insert_id = $id_arr[1];
            $last_id = $id_arr[2];
            $updatedata = array(
                'mmp_txn'=>$_POST['mmp_txn'],
                'mer_txn'=>$_POST['mer_txn'],
                'bank_txn'=>$_POST['bank_txn'],
                'bank_name'=>$_POST['bank_name'],
                'prod'=>$_POST['prod'],
                'date'=>$_POST['date'],
                'f_code'=>$_POST['f_code'],
                'clientcode'=>$_POST['clientcode'],
                'merchant_id'=>$_POST['merchant_id'],
                'status'=>1
            );
           
            
            
            
               
                   if(($_POST['f_code']) == 'Ok'){
                   
                    $exammodel->update(array('payment_status'=> 1), array('u_id=?' => $id,'id=?'=>$last_insert_id))  ;
                   
                    $payment->update($updatedata, array('id=?' => $last_insert_id , 'payment_id=?'=>$last_insert_id,'id=?'=>$last_id));
                    
                    $this->view->response_data = $_POST;
             }
          
              
         }
         
 //// payment method add by ruashan////    
         
public function responsereportAction(){
            $htmlcontent = $this->view->render('application/response.phtml');
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "transaction-report");
             
         }
         
            
            
    public function GetRecentTermAndBatch($date = '') {
        $date  = !empty($date)?$date:date('d-m-Y');
        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $my_date = $date;
        $date = date_create($my_date);
        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnDate($my_date);

        foreach ($result as $key) {
            $term_start = explode("/", $key['start_date']);
            $term_end = explode("/", $key['end_date']);
            $start = date_create($term_start[2] . "-" . $term_start[1] . "-" . $term_start[0]);
            $end = date_create($term_end[2] . "-" . $term_end[1] . "-" . $term_end[0]);


            if (strtotime(date_format($date, "Y-m-d")) >= strtotime(date_format($start, "Y-m-d")) && strtotime(date_format($date, "Y-m-d")) <= strtotime(date_format($end, "Y-m-d"))) {
                $term_id = $key['term_id'];
                $academic_year_id = $key['academic_year_id'];
                $start_date = date_format($start, "Y-m-d");
                $end_date = date_format($end, "Y-m-d");
                $term_name = $key['term_name'];
                $academic_name = $key['academic_name'];
                break;
            }
        }
        $result['recent']['batch_id'] = $academic_year_id;
        $result['recent']['term_id'] = $term_id;


       return $result['recent'];
        
    }
    public function GetRecentTerm($date = '',$academic_id = '') {
        $date  = !empty($date)?$date:date('d-m-Y');
        $academic_id = !empty($academic_id)?$academic_id:0;
        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $my_date = $date;
        $date = date_create($my_date);
        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnAcademicId($academic_id);

        foreach ($result as $key) {
            $term_start = explode("/", $key['start_date']);
            $term_end = explode("/", $key['end_date']);
            $start = date_create($term_start[2] . "-" . $term_start[1] . "-" . $term_start[0]);
            $end = date_create($term_end[2] . "-" . $term_end[1] . "-" . $term_end[0]);


            if (strtotime(date_format($date, "Y-m-d")) >= strtotime(date_format($start, "Y-m-d")) && strtotime(date_format($date, "Y-m-d")) <= strtotime(date_format($end, "Y-m-d"))) {
                $term_id = $key['term_id'];
                $academic_year_id = $key['academic_year_id'];
                $start_date = date_format($start, "Y-m-d");
                $end_date = date_format($end, "Y-m-d");
                $term_name = $key['term_name'];
                $academic_name = $key['academic_name'];
                break;
            }
        }
        return $term_id;
    }

    public function ajaxGetStudentDetailsAction() {

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $id = $this->_getParam("stu_id");
            $application = new Application_Model_Application();
            $result = $application->getRecordsByPdmId($id);
            echo json_encode($result);
            die;
        }
    }
    public function ajaxGetCourseFeeAction() {

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam("batch_id");
            $application = new Application_Model_Coursefee();
            $result = $application->getFee($term_id,$batch_id,1);
            echo $result;
            die;
        }
    }
    public function ajaxGetBackCourseFeeAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam("batch_id");
            $application = new Application_Model_Coursefee();
            $result = $application->getFee($term_id,$batch_id,2);
            echo $result;
            die;
        }
    }
    
     public function ajaxGetStudentCourseAction() {
         $elective_selection =  new Application_Model_ElectiveSelectionItems();
         /// $course_learning = new  Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam("batch_id");
            $sid = $this->_getParam("sid");
            $fid = $this->_getParam("fid");
            $this->view->fid=$fid;
            
            $application = new Application_Model_Corecourselearning();
            $course_details_arr = $application->getcourseTypeOn($batch_id,$term_id);
            
           $course_details_arr = $this->geStudentCourse($batch_id, $term_id, $sid);
            $core_course_name_arr = $this->mergData($course_details_arr, array('cc_name'), count($course_details_arr));
            
            //echo "<pre>"; print_r($core_course_name_arr);exit;
            $ge_arr = $this->mergData($course_details_arr, array('ge_id'), count($course_details_arr));
            
            
            $core_course_name_arr = array_values(array_unique($core_course_name_arr));
             $ge_arr = array_values(array_unique($ge_arr));
             
            
             $selectge = $elective_selection->getCouseDetailByStudentId($batch_id,$term_id,$sid);
              //$course_details_arr1  = $application->getcourseTypeOn($batch_id,$term_id); 
                //  $course_details_arr1 = $this->geStudentCourse($batch_id,$term_id,$sid);
                 
             
             //echo "<pre>"; print_r($selectge);exit;
            //$this->view->course_cat = $core_course_name_arr;
           // $this->view->core_details_arr = $course_details_arr;
            $this->view->exam_sch = $course_details_arr;
            $this->view->course_val = $selectge;
            $this->view->ge_arr = $ge_arr;
            $this->view->ge_id = $this->aeccConfig[0];
            //print_r($selectge);
            //echo $result;
           // die;
        }
    }
    
      public function ajaxGetStudentCourse1Action() {
         $elective_selection =  new Application_Model_ElectiveSelectionItems();
         /// $course_learning = new  Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam("batch_id");
            $sid = $this->_getParam("sid");
            $fid = $this->_getParam("fid");
            $this->view->fid=$fid;
           
            $application = new Application_Model_Corecourselearning();
            $course_details_arr = $application->getcourseTypeOn($batch_id,$term_id);
            
           $course_details_arr = $this->geStudentCourse($batch_id, $term_id, $sid);
            $core_course_name_arr = $this->mergData($course_details_arr, array('cc_name'), count($course_details_arr));
            
            // echo "<pre>"; print_r($core_course_name_arr);exit;
            $ge_arr = $this->mergData($course_details_arr, array('ge_id'), count($course_details_arr));
            
            
            $core_course_name_arr = array_values(array_unique($core_course_name_arr));
             $ge_arr = array_values(array_unique($ge_arr));
             
            
             $selectge = $elective_selection->getCouseDetailByStudentId($batch_id,$term_id,$sid);
              //$course_details_arr1  = $application->getcourseTypeOn($batch_id,$term_id); 
                //  $course_details_arr1 = $this->geStudentCourse($batch_id,$term_id,$sid);
                 
             
             //echo "<pre>"; print_r($selectge);exit;
            //$this->view->course_cat = $core_course_name_arr;
           // $this->view->core_details_arr = $course_details_arr;
            $this->view->exam_sch = $course_details_arr;
            $this->view->course_val = $selectge;
            $this->view->ge_arr = $ge_arr;
            $this->view->ge_id = $this->aeccConfig[0];
            //print_r($selectge);
            //echo $result;
           // die;
        }
    }
    
    public function geStudentCourse($academic_id,$term_id,$stu_id){
        $Corecourselearning_model = new Application_Model_Corecourselearning();
          $core_courses = $Corecourselearning_model->getcorecourse($academic_id, $term_id);
          $student_ge = $Corecourselearning_model->getStudentGE($academic_id, $term_id,$stu_id);
          
         
          foreach($student_ge as $key => $value){
              $checkarr = false;
              foreach($core_courses as $keycore => $valuecore){
                  if($valuecore['ge_id'] != 0){
                  if($valuecore['course_id']==$value['course_id']){
                      $checkarr = TRUE;
                  }
                  else
                  {
                   $checkarr  =  $keycore;
                  }
                   }
              }
             if($checkarr !== TRUE and $checkarr !== FALSE)
              unset($core_courses[$checkarr]);
          }
         
          $course_type_result = array_merge($core_courses,$student_ge);
          return $course_type_result;
    }
    
    
    
     public function ajaxGetSubjectDetailsAction(){
          $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $exam_id = $this->_getParam("exam_date");
            //echo $exam_id;
            //die();
              $exam_sch = new Application_Model_ExamScheduleModel();
            $subject_name = $exam_sch->getcourseById($exam_id);
           $this->view->subject_name =  $subject_name;
        }
    }

    
     public function mobverification($otp,$number) {
        $username = "DMIPATNA";
        $password = "e-2!j6HS1";
        $sender = "DMIADM"; // This is who the message appears to be from.
        $numbers = $number; // A single number or a comma-seperated list of numbers
        // print_r($_SESSION['public']['userefrence']['ca_pri_mobile']);
        //echo $number ;exit;
        $pin =$otp;
        $message = "This is a verification auto generated pin from DMI." . $pin;
        $message = urlencode($message);
        //information storing data variable 
        $data = "username=" . $username . "&pass=" . $password . "&senderid=" . $sender . "&dest_mobileno=" . $numbers . "&message=" . $message . '&response=Y';
       $url = 'https://www.smsjust.com/sms/user/urlsms.php?' . $data;
       //echo $url ;exit;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //$ch = curl_init('https://www.smsjust.com/sms/user/urlsms.php?' . $data);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    


########################################################
# Functions used to send the SMS message
########################################################
public function httpRequest($url){
    $pattern = "/http...([0-9a-zA-Z-.]*).([0-9]*).(.*)/";
    preg_match($pattern,$url,$args);
    $in = "";
    $fp = fsockopen($args[1],80, $errno, $errstr, 30);
    if (!$fp) {
       return("$errstr ($errno)");
    } else {
  $args[3] = "C".$args[3];
        $out = "GET /$args[3] HTTP/1.1\r\n";
        $out .= "Host: $args[1]:$args[2]\r\n";
        $out .= "User-agent: PATNA WOMEN COLLEGE\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Connection: Close\r\n\r\n";

        fwrite($fp, $out);
        while (!feof($fp)) {
           $in.=fgets($fp, 128);
        }
    }
    fclose($fp);
    return($in);
}

public function SMSSend($phone, $msg, $debug=false){
     
       $user = $this->_sms['user'];
       
       $password = $this->_sms['password'];
       
       $senderid = $this->_sms['senderid'];
       
       $smsurl = $this->_sms['smsurl'];
       
       $dnd =  $this->_sms['dnd'];
       $priority = $this->_sms['priority'];

      $url = 'username='.$user;
      $url.= '&password='.$password;
      $url.= '&sender='.$senderid;
     $url.= '&to='.urlencode($phone);
      $url.= '&message='.urlencode($msg);
      $url.= '&priority='.$priority;
      $url.= '&dnd='.$dnd;
      $url.= '&unicode=0';
      $urltouse =  $smsurl.$url;
      if ($debug) { echo "Request: <br>$urltouse<br><br>"; }
      $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $urltouse);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      //Open the URL to send the message
      //$response = httpRequest($urltouse);
	  $response = curl_exec($ch);
      curl_close($ch);
      if ($debug) {
           echo "Response: <br><pre>".
           str_replace(array("<",">"),array("&lt;","&gt;"),$response).
           "</pre><br>"; }

      return($response);
}




########################################################
# GET data from sendsms.html
########################################################   


###########################################
##### tuition Fee
####################################


public function tuitionfeesAction() {
       
    //http_response_code(404); die;
        require_once APPLICATION_PATH . '/public/atompay/TransactionRequest.php';
        
        
         
          $semattend = new Application_Model_BatachSemesterAttendance();
          
          $studetails = new Application_Model_StudentPortal();
          $feeitems = new Application_Model_FeeStructureTermItems();
          //$term_obj = new Application_Model_TermMaster();
           $tut_fees = new Application_Model_Tuitionfees();
            $latefine = new Application_Model_Latefine();
          $sem_fees = new Application_Model_FeesCollection();
          $dept_model = new Application_Model_Department();
          $academic_model = new Application_Model_Academic();
          $sanction = new Application_Model_SanctionSeatModel();
          $eazy_php = new Application_Model_Eazy();
          $dept_type_details = new Application_Model_DepartmentType();
              $feeStructure = new Application_Model_FeeStructure();
          //$fee_model = new Application_Model_Coursefee();
          //$transactionRequest = new Application_Model_TransactionRequest();
          //$transactionRequest = new Application_Model_TransactionRequest();
          
           $type = $this->_getParam("type");
           
           $this->view->type = $type;
           
         
                   
          $transactionRequest  = new TransactionRequest();
           switch ($type) { 
              
               case "view": 
                                       
     
               $payrecord =$sem_fees->getPayTotRecords(trim($_POST['stu_id']),$_POST['semester']);
                    $acad_stu = $studetails->getStudenacademicDetails(trim($_POST['stu_id']));
              // echo "<pre>"; print_r($acad_stu['academic_id']);
               // die();
                   $struct_id = $feeStructure->getStructId($acad_stu['academic_id']);
                   $fee = $feeitems->getFee($struct_id,$_POST['semester']);  
          $fee = $this->mergData($fee,array('totalfee'),count($fee));
        $fee = array_filter($fee,function($value){return ($value !== null && $value !== false && $value !== '' && $value !=0);});
        $totfee = 0;
        $paidfee = array();
                  foreach($payrecord as $key => $pay) {
                       $sem2= array_sum($fee);
                      $totfee+=(int)$pay['fee'];
                      $type = $pay['type'];
                      $this->view->ftype = $type;
                      $paidfee[$key] = $pay['fee']."-".$pay['prod']."-".$pay['description'];
                  }
                  $due = $sem2-$totfee;
                  $this->view->due=$due;
                  $this->view->paidfee = json_encode($paidfee);
                 
                 
                         if(!empty($payrecord)){
                              
                   
                            if($due <= 0){
                                
                              $this->_refresh(5,'/academic/application/tuitionfees?v2','You have already paid for this term..');
                            }
                            else {
                                 $this->view->msg ="You Have due amount :".$due; 
                            }
                         }
                   //   print_r($payrecord);
                   //echo $due; die() ;
                $semester = $_POST['semester'];
               if($_POST['semester'] != 't1'){
                   $details_stu = $studetails->getStudenacademicDetails(trim($_POST['stu_id']));
               
                     if(empty($details_stu['academic_id'])){
                        $this->_refresh(3,'/academic/application/tuitionfees?v2','Wrong Form Id ..');
                }
                 $currentdate= date('Y-m-d');
                   $getperiod = $tut_fees->getRecordByTerm($_POST['semester'],$_POST['department']);
                   //=====checking for fine dates 
                  if(($getperiod['feeForm_end_date'] > $getperiod['feeForm_extended_date']) || $getperiod['fineFee']==0 || strtotime($currentdate) < strtotime($getperiod['feeForm_end_date'])  ) {
                     
                   //======cheking for end dates
               if( strtotime($currentdate)  < strtotime($getperiod['feeForm_start_date'])  ||  strtotime($currentdate) > strtotime($getperiod['feeForm_end_date']))
                     {
                        $this->_refresh(5,'/academic/application/tuitionfees?v2','Fee Submission  not started OR End date for this Academic !');


                     }
                     else {
                   $details_stu = $studetails->getPermotedStudenacademicDetails(trim($_POST['stu_id']),$details_stu['academic_id'],$_POST['semester']);
                  // echo "<pre>";print_r($details_stu);exit;
                     }
                  }
                  else
                  {//==calc for fine date numbers 
                  
                  if(strtotime($currentdate) > strtotime($getperiod['feeForm_extended_date']))
                  {
                      $this->_refresh(5,'/academic/application/tuitionfees?v2','Fee Submission  is closed !');
                  }
                      $fineExists = $latefine->isFine($_POST['stu_id'], $details_stu['academic_id'], $_POST['semester']);
                     
                     
                      if($currentdate > $getperiod['feeForm_end_date']){
                      $datetime1 = new DateTime($currentdate);//====greater date
                    $datetime2 = new DateTime($getperiod['feeForm_end_date']);//====less date
                    $difference = $datetime1->diff($datetime2);
                    $finefee = $getperiod['fineFee'];
                    $perDayinc = $getperiod['perday_number'];
                        if($difference->d > $perDayinc){
                            $addfineTimes = floor($difference->d / $perDayinc);
                            $finefee=0;
                                for($i = 1 ;$i<=$addfineTimes;$i++ ){
                                   $finefee+= $getperiod['fineFee'];
                                }
                        }
                        $details_stu = $studetails->getPermotedStudenacademicDetails(trim($_POST['stu_id']),$details_stu['academic_id'],$_POST['semester']);
                      if($details_stu){
                          $details_stu['cmn_terms'] = $_POST['semester'];
                        
                        $details_stu['product_id'] = $getperiod['product_id'];
                       
                         if($fineExists['amount'] <> $finefee){
                             
                             $finefee = isset($fineExists['amount'])?$finefee-(int)$fineExists['amount']:$finefee;
                           $details_stu['finefee'] = $finefee;  
                            $_SESSION['stu_details'] = $details_stu;
                        $this->_redirect('/application/late-fine-dasboard'); 
                         }
                      }
                      else
                      {
                          $this->_refresh(5,'/academic/application/tuitionfees?v2','You are Not Promoted Please Counsult college Department..!'); 
                      }
    }
                  }
                 }
               else{
                    $stu_details = new Application_Model_ApplicantCourseDetailModel();
                       $details_stu = $stu_details->getApplicationNumber(trim($_POST['stu_id']));
                       $details_stu['stu_fname'] = $details_stu['applicant_name'];
                        
                        
                        if($details_stu['core_course1']){
                        $details_stu['academic_id'] = $details_stu['core_course1'];
                        }
                        else{
                         $dept_type = $details_stu['course'];
                         
                         
                   if(!$dept_type)
                   {
                      
                       $this->redirecttuitionform("Your Form Does Not Present in academic");
                       
                   }
                         
             $dept_details = $dept_type_details->getRecord($dept_type);
            
             $dept_id_only = $dept_model->getByDepartmentType($dept_type)['did'];
           
            $details_stu['academic_id'] = $academic_model->getAcademicsBySD($dept_details['session_id'],$dept_id_only)['academic_year_id'];
          //  echo $details_stu['academic_id']; die;
                        }
               // echo "<pre>"; print_R(  $details_stu['academic_id']);exit;
                        
                        $details_stu['father_mobileno'] = $details_stu['phone'];
                        $details_stu['roll_no'] = 0;
                        $details_stu['exam_roll'] = 0;
                        $details_stu['stu_id'] = $details_stu['form_id'] ;
                        $details_stu['filename'] = $details_stu['photo'];
                       
               }
           
                   if(count($details_stu) == 0)
                   {
                      
                       $this->redirecttuitionform("Your Form Does Not Present in academic");
                       
                   }
                   
                     if(!$details_stu['academic_id']){
                        $this->_refresh(5,'/academic/application/tuitionfees?v2','you are not allowed for this semester ..');
                }
                  $this->view->batch= $academic_model->getRecord($details_stu['academic_id'])['short_code'];
                   
                   $dept= $academic_model->getRecord($details_stu['academic_id'])['department'];
                   $this->view->department=$dept_model->getRecord($dept)['department'];
                 // echo "<pre>"; print_r($details_stu);exit;
                   $this->view->studetais = $details_stu;
                   $this->view->departmentId = $dept;
                   $this->view->sem = $_POST['semester'];
                 
                    $acad_id = $details_stu['academic_id'];
                    $term_ids = 0;
                   $payrecord =$sem_fees->getPayRecords(trim($_POST['stu_id']),$_POST['semester']);
                
                            
         $struct_id = $feeStructure->getStructId($acad_id);
           if(!$struct_id){
            $this->_refresh(5,'/academic/application/tuitionfees?v2','Fee not prepared...');
     }
          
          
        $fee = $feeitems->getFee($struct_id,$semester);
         
        if($semester == 't1'){
            
            
                        $fund_type_arr = $sanction->checkFeeSlpGenerated($details_stu['stu_id'],$semester);
                         $feeacc =$feeitems->getFeeByaccname($struct_id,$semester,explode(',',$fund_type_arr['fund_type']));
                        // echo "<pre>";print_r($fund_type_arr);exit;
                         $totalFee = $feeitems->getTotFee($struct_id,$semester);
                       //  echo "<pre>"; print_r($details_stu['stu_id']);die;
                         if($totalFee != $feeacc || !$fund_type_arr['fund_type'] ){
                              $this->_refresh(5,'/academic/application/tuitionfees?v2','Please Consult the college Department For fee slip ..!');
                         }
        }
                         
        $fee = $this->mergData($fee,array('totalfee'),count($fee));
        $fee = array_filter($fee,function($value){return ($value !== null && $value !== false && $value !== '' && $value !=0);});
        $totfee = 0;
        $paidfee = array();
                  foreach($payrecord as $key => $pay) {
                       $sem2= array_sum($fee);
                      $totfee+=(int)$pay['fee'];
                      $type = $pay['type'];
                      $this->view->ftype = $type;
                      $paidfee[$key] = $pay['fee']."-".$pay['prod']."-".$pay['description'];
                  }
                  $due = $sem2-$totfee;
                  $this->view->due=$due;
                  $this->view->paidfee = json_encode($paidfee);
                 
                         if(!empty($payrecord)){
                            if($due <= 0){
                              $this->_refresh(5,'/academic/application/tuitionfees?v2','You have already paid for this term..');
                            }
                            else {
                                 $this->view->msg ="You Have due amount :".$due; 
                            }
                         }
                       break;
               case "save";
               $department = $_POST['department'];
               $semester = $_POST['semester'];
               //unset($_POST['department']);
                   $feeStructure = new Application_Model_FeeStructure();
                    if($_POST['semester'] != 't1'){
                   $details_stu = $studetails->getStudenacademicDetails(trim($_POST['stu_id']));
               }
               else{
                    $stu_details = new Application_Model_ApplicantCourseDetailModel();
                       $details_stu = $stu_details->getApplicationNumber(trim($_POST['stu_id']));
                       $details_stu['stu_fname'] = $details_stu['applicant_name'];
                        if($details_stu['core_course1']){
                        $details_stu['academic_id'] = $details_stu['core_course1'];
                        }
                        else{
                         $dept_type = $details_stu['course'];
             $dept_details = $dept_type_details->getRecord($dept_type);
            
             $dept_id_only = $dept_model->getByDepartmentType($dept_type)['did'];
           
            $details_stu['academic_id'] = $academic_model->getAcademicsBySD($dept_details['session_id'],$dept_id_only)['academic_year_id'];
          //  echo $details_stu['academic_id']; die;
                        }
                        $details_stu['father_mobileno'] = $details_stu['phone'];
                        $details_stu['roll_no'] = 0;
                        $details_stu['exam_roll'] = 0;
                        $details_stu['stu_id'] = $details_stu['form_id'] ;
                        $details_stu['filename'] = $details_stu['photo'];
               }
         $struct_id = $feeStructure->getStructId($details_stu['academic_id']);
        // echo $details_stu['academic_id']; die;
          $feeitems = new Application_Model_FeeStructureTermItems();
         if(!$struct_id){
            $this->_refresh(5,'/academic/application/tuitionfees/type/view','Fee not prepared...');
     }
        $fee = $feeitems->getFee($struct_id,$semester);
        
       
    
        $accname = $fee;
        $fee = $this->mergData($fee,array('totalfee'),count($fee));
        $fee = array_filter($fee,function($value){return ($value !== null && $value !== false && $value !== '' && $value !=0);});
      
                      $sem2= array_sum($fee);
                      $feeamount = $fee;
                      
                      
                     if (in_array($_POST['fee'], $feeamount) && $_POST['type'] == 'R')
                     {
                         $amt= $_POST['fee'];
                         $keyindex =  array_search($amt,$feeamount);
                         $product_id= $accname[$keyindex]['acc_name'];
                         $des = "Regular";
                   
                     }
                     else
                     {
                            
                    $installment = new Application_Model_FeeInstallment();
                    $feeamount =  $installment->getRecordbyacadId($details_stu['academic_id'],$semester);
                    $feeamount = $this->mergData($feeamount,array('amount'),count($feeamount));

                        if( in_array($_POST['fee'], $feeamount) && $_POST['type'] == 'I'){
                              $amt = $_POST['fee'];
                            $product_id = explode('-',$_POST['fees'])[1];
                            $des = explode('-',$_POST['fees'])[2];
                         }
                         else{
                               echo "Amount Mismatch";
                               die();
                         }
                      
                     }
                   // echo "Oops Site has been disable due to technical issue will resume soon ..";exit;
                    $dueamount = $sem2-$_POST['fee'];
                    $insertdata = array (
                        'batch' => $_POST['batch'],
                        'name' => $_POST['name'],
                        'claas_roll' => $_POST['claas_roll'],
                        'exam_id' => $_POST['exam_id'],
                        'department' => $_POST['department'],    
                        'phone' => $_POST['phone'],
                        'email' => $_POST['email'],
                        'semester' => $_POST['semester'],
                        'fee' => $amt, 
                        'stu_id' => $_POST['stu_id'], 
                        'due_amount' => $dueamount, 
                        'submit_date' => date('Y-m-d'),
                        'type' => $_POST['type'],
                        'description' => $des
                    );
                        
                        //echo"<pre>";print_r($product_id);exit;
                       $last_insert_id = $sem_fees->insert($insertdata);
                         
                           
                      //$exammodel->update(array('payment_status'=> 1), array('u_id=?' => $sfid))  ;
                      $this->_flashMessenger->addMessage('Payment Successfully now You can Download your Admit Card');
                      date_default_timezone_set('Asia/Calcutta');
                        $datenow = date("d/m/Y h:m:s");
                        $transactionDate = str_replace(" ", "%20", $datenow);

                        $transactionId = rand(1,1000000);
                        //=====[Ashu change]=====//
                    //   $_POST['fee'] = 1;
 if($_POST['semester'] != 't1'){
                        $transactionRequest->setMode("live");
                        $transactionRequest->setLogin(53243);
                        $transactionRequest->setPassword("Patna@1234");
                        $transactionRequest->setProductId($product_id);
                        $transactionRequest->setAmount($_POST['fee']);
                        $transactionRequest->setTransactionCurrency("INR");
                        $transactionRequest->setTransactionAmount($_POST['fee']);
                        $transactionRequest->setReturnUrl($this->_base_url  . 'application/semfeeresponse/?id='.$last_insert_id);
                        $transactionRequest->setClientCode(53243);
                        $transactionRequest->setTransactionId($transactionId);
                        $transactionRequest->setTransactionDate($transactionDate);
                        $transactionRequest->setCustomerName($_POST['name']);
                        $transactionRequest->setCustomerEmailId($_POST['email']);
                        $transactionRequest->setCustomerMobile($_POST['phone']);
                        $transactionRequest->setCustomerBillingAddress($_POST['stu_id']."|".$des);
                        $transactionRequest->setCustomerId($_POST['exam_id']."|".$_POST['claas_roll']);
                        $transactionRequest->setCustomerAccount("639827");
                      $transactionRequest->setReqHashKey("b338c08a991313f12c");
                  //   echo "<pre>";print_r($transactionRequest);exit;
                        $url = $transactionRequest->getPGUrl();
                          $this->_redirect($url);
                        
 }else{
                        //==============[payment Primary Details]======================//
                        //== amount ==//
                        //==random refrence no===//
                        //==optional Fields==//
                        //=============[End]======================//
                        
                        $amount = $_POST['fee'];
                        $reference_no = rand();
                        $eazy_php->name = $_POST['name'];
                        $eazy_php->phone_no = $_POST['phone'];
                        $eazy_php->email = $_POST['email'];
                        $eazy_php->form_no = $_POST['stu_id'];
                        $eazy_php->department = $dept_model->getRecord($_POST['department'])['department'];
                        $eazy_php->idr = 'E';
                  //   echo "<pre>";print_r($eazy_php);die;
                        
                        $optionalField=$_POST['class_roll']."|".$_POST['exam_id']."|upivpa|".$des."|".$last_insert_id."|misc4";
                        $url=$eazy_php->getPaymentUrl($amount, $reference_no,$optionalField );
                        $this->_redirect($url);
                        
 }
                  
                    break;
                default:
                     $degree_model = new Application_Model_Degree();
                     $deegreedata = $degree_model->getDropDownList();
                
                $data = $tut_fees->getRecords();
                $Department_model = new Application_Model_Department();
                $depdata = $Department_model->getFeesActiveDropDownList();
                $this->view->tuidata  =  $data;
                $this->view->deptdata  =  $depdata;
                $this->view->degreedata  =  $deegreedata;
               


        
                    break;   
           }
    }
    
 public function ajaxGetFeesDeptAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $degree_id = $this->_getParam("degree_id");
            //print_r($short_code); die;
            $department_model = new Application_Model_Department();
            $result = $department_model->getFeesActiveDegreeId($degree_id);


            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['id'] . '" >' . $val['department'] . '</option>';
            }
        }die;
        
    }    
    
      public function latefineresponseAction(){
          $payment = new Application_Model_Latefine();
          if($_GET['id']=='')
              $this->_redirect ($this->_main_url);
            $id = $_GET['id']; 
            
             $updatedata = array(
                'mmp_txn'=>$_POST['mmp_txn'],
                'mer_txn'=>$_POST['mer_txn'],
                'bank_txn'=>$_POST['bank_txn'],
                'bank_name'=>$_POST['bank_name'],
                'prod'=>$_POST['prod'],
                'date'=>$_POST['date'],
                'f_code'=>$_POST['f_code'],
                'clientcode'=>$_POST['clientcode'],
                'merchant_id'=>$_POST['merchant_id']
               // 'status'=>1
            );
            
           if($_POST['f_code'] == 'Ok'){
               
            $updatedata['status'] = 1; 
            
           }
           $record= $payment->getRecord($id);
           if(empty($record['f_code'])){
             $payment->update($updatedata, array('id=?' => $id)) ;
           }
    //   $record= $payment->getRecord($record);
      
        if(strtoupper($record['f_code']) == 'OK'){
                    $this->view->response_data = $record;
                     $record= $payment->getRecord($id);
                     $this->view->details = $record;
        }
        else
        {
             $this->view->response_data = $record;
                     $record= $payment->getRecord($id);
                     $this->view->details = $record;
        }
          
          
      }
    
    
     public function semfeeresponseAction(){
         
        
            require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
           
            $payment = new Application_Model_FeesCollection();
             $Academic_model = new Application_Model_Academic();
             $session_model = new Application_Model_Session();
            $FeeCategory_model = new Application_Model_FeeCategory();
            $FeeHeads_model = new Application_Model_FeeHeads();
            $account_model = new Application_Model_Account();
            $studetails = new Application_Model_StudentPortal();
            $FeeStructure_model = new Application_Model_FeeStructure();
            $dept = new Application_Model_Department();

            $StructureItems_model = new Application_Model_FeeStructureItems();

            $term_model = new Application_Model_TermMaster();

            $TermItems_model = new Application_Model_FeeStructureTermItems();
            $transactionResponse = new Application_Model_TransactionResponse();
          //  $transactionResponse = new Application_Model_TransactionResponse();
          
            $transactionResponse = new TransactionResponse();
            $transactionResponse->setRespHashKey("8b013258b7a4b89428");
            $this->view->vaild_response = $transactionResponse->validateResponse($_POST);
           if($_GET['id']=='')
              $this->_redirect ($this->_main_url);
            $id = $_GET['id'];
            
            
           
            $updatedata = array(
                'mmp_txn'=>$_POST['mmp_txn'],
                'mer_txn'=>$_POST['mer_txn'],
                'bank_txn'=>$_POST['bank_txn'],
                'bank_name'=>$_POST['bank_name'],
                'prod'=>$_POST['prod'],
                'date'=>$_POST['date'],
                'f_code'=>$_POST['f_code'],
                'clientcode'=>$_POST['clientcode'],
                'merchant_id'=>$_POST['merchant_id']
               // 'status'=>1
            );
           if($_POST['f_code'] == 'Ok'){
               
            $updatedata['status'] = 1; 
            
           }
           $record= $payment->getRecord($id);
           if(empty($record['f_code'])){
             $payment->update($updatedata, array('id=?' => $id)) ;
           }
       $record= $payment->getRecord($id);
       
      // echo "<pre>"; print_r($record);exit;
                              
                    if($record['f_code'] == 'Ok'){
                    $this->view->response_data = $record;
                     $record= $payment->getRecord($id);
                    $record['acc_number'] = $account_model->getName($record["prod"])['acc_number'];
                    $this->view->details = $record;
                    
                     //======[mail]==== 
                       $to = array(strtolower($record['email'])); 
                        $from = array('noreply@pwcadmissions.in','Patna Women\'s College');
                        $subject = "PWC Semester Fee Reciept 2020";
                        $message .= "Dear {$record['name']} , <br/><br/>Please find the link below :-.<br/><br/>";
                        $message.= "<a class='btn btn-primary text-center'  href = '{$this->_base_url}application/semfeeresponse-print/?id={$id}&v3'>Download Reciept</a>";
                        
                        
                        
                        
                        $mail = new Application_Model_MailSender($to, $from,$subject,$message);
                         if($mail->mail($this->_mail['user'],$this->_mail['pass'])){
                            echo "Your login credentials for next step is sent on your email id.";
                           
                        }else{
                            echo 'Sent Failed';
                            
                        }
                    
                    
                        $details_stu = $studetails->getStudenacademicDetails(trim($record['stu_id']));
                      
         $struct_id = $FeeStructure_model->getStructId($details_stu['academic_id']);
         if(!$struct_id){
            $this->_refresh(5,'/academic/application/tuitionfees?v2','Fee not prepared...');
     }
     
    $structure_id =  $struct_id;  
            if($structure_id){
                
                 $result = $TermItems_model->getItemRecordsByTerm($structure_id,$record['semester']);
                
                 $deptname = $dept->getRecordbyAcademic($details_stu['academic_id']);
            $this->view->result = $result;
            $this->view->department = $deptname['dpt_name'];
            $result1 = $StructureItems_model->getStructureRecords($structure_id);
            
            $this->view->session = $session_model->getRecord($deptname['session']);
            $this->view->result1 = $result1;
            $academic_id  = $TermItems_model->getAcademicId($structure_id);
            $terms_data = $term_model->getRecordByAcademicId($academic_id['academic_id']);
            $degree_id = $Academic_model->getAcademicDegree($academic_id['academic_id']);
            $this->view->term_data = $terms_data; 
            $this->view->structure_id = $structure_id;
            $Category_data = $FeeCategory_model->getFeeCategory($degree_id,$record['prod']);
            $this->view->Category_data = $Category_data;
            
            $Feeheads_data = $FeeHeads_model->getFeeheads($degree_id);
             $this->view->heads_data = $Feeheads_data;
              
            }
                    
                  
                    
                   
             }
             else{
                  $record= $payment->getRecord($id);
                 $this->view->response_data = $record;
             }
             
           
         }
         
         
          
     public function semfeeresponsePrintAction(){
         

         
           
                  require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
           
            $payment = new Application_Model_FeesCollection();
             $Academic_model = new Application_Model_Academic();
             $session_model = new Application_Model_Session();
            $FeeCategory_model = new Application_Model_FeeCategory();
            $FeeHeads_model = new Application_Model_FeeHeads();
            $account_model = new Application_Model_Account();
            $studetails = new Application_Model_StudentPortal();
            $FeeStructure_model = new Application_Model_FeeStructure();
            $dept = new Application_Model_Department();

            $StructureItems_model = new Application_Model_FeeStructureItems();

            $term_model = new Application_Model_TermMaster();

            $TermItems_model = new Application_Model_FeeStructureTermItems();
            $transactionResponse = new Application_Model_TransactionResponse();
          //  $transactionResponse = new Application_Model_TransactionResponse();
          
            $transactionResponse = new TransactionResponse();
        
            $id = $this->_getParam("id");
            
                if($_POST['ID'] == 302278){
               $this->view->result = $_POST;
               
                    $mandatory_fields = explode('|',$_POST["mandatory_fields"]);
                    $optional_fields = explode('|',$_POST['optional_fields']);
            if($_POST['Response_Code'] == "E000"){
                
$url = "https://eazypay.icicibank.com/EazyPGVerify?ezpaytranid={$_POST['Unique_Ref_Number']}&amount=&paymentmode=&merchantid={$_POST['ID']}&trandate=&pgreferenceno=";
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERAGENT => 'abc'
]);
// Send the request & save response to $resp
$resp = curl_exec($curl);
// Close request to clear up some resources
curl_close($curl);
          $val = explode('&',$resp);
                
                       $updatedata = array(
                'mmp_txn'=>$_POST['Unique_Ref_Number'],
                'mer_txn'=>$_POST['ReferenceNo'],
                'bank_txn'=>$_POST['bank_txn'],
                'bank_name'=>0000,
                'date'=>$_POST['Transaction_Date'],
                'f_code'=>$_POST['Response_Code'],
                'clientcode'=>"000",
                'merchant_id'=>$_POST['ID'],
                'status'=>1,
                'live_status' => explode('=',$val['0'])[1],
                'pay_mode'=>$_POST['Payment_Mode'],
                'pf'=>$_POST['Processing_Fee_Amount'],
                'stx'=>$_POST['Service_Tax_Amount'],
                'total_amount'=>$_POST['Total_Amount'],
            );
                    $payment->update($updatedata, array('id=?' => $optional_fields[4])) ;
                    
                      $to = array(strtolower($mandatory_fields[5])); 
                        $from = array('noreply@pwcadmissions.in','Patna Women\'s College');
                        $subject = "PWC Semester Fee Reciept 2020";
                        $message .= "Dear {$mandatory_fields[3]} , <br/><br/>Please find the link below :-.<br/><br/>";
                        $message.= "<a class='btn btn-primary text-center'  href = '{$this->_base_url}application/semfeeresponse-print/?id={$optional_fields[4]}&v3'>Download Reciept</a>";
                        
                        
                        
                        
                        $mail = new Application_Model_MailSender($to, $from,$subject,$message);
                         if($mail->mail($this->_mail['user'],$this->_mail['pass'])){
                            echo "Your login credentials for next step is sent on your email id.";
                           
                        }else{
                            echo 'Sent Failed';
                            
                        }
                    
        
            }
            
            
           }
      
           
      $record= $payment->getRecord($id);
           
      $this->view->response_data = $record;
            
                    if($record['f_code'] == 'Ok' || $record['f_code']=="E000"){
                    $this->view->response_data = $record;
                    
                    $record['acc_number'] = $account_model->getName($record["prod"])['acc_number'];
                    $this->view->details = $record;
                // echo "<prE>";print_r($record);exit;
                    
                    if( $record['f_code']!="E000"){
                $details_stu = $studetails->getStudenacademicDetails(trim($record['stu_id']));
                    }
                    else
                    {
                         $stu_details = new Application_Model_ApplicantCourseDetailModel();
                       $details_stu = $stu_details->getApplicationNumber(trim($record['stu_id']));
                       $details_stu['stu_fname'] = $details_stu['applicant_name'];
                        $details_stu['academic_id'] = $details_stu['core_course1'];
                        $details_stu['father_mobileno'] = $details_stu['phone'];
                        $details_stu['roll_no'] = 0;
                        $details_stu['exam_roll'] = 0;
                        $details_stu['stu_id'] = $details_stu['form_id'] ;
                        $details_stu['filename'] = $details_stu['photo'];
                    }
                      
         $struct_id = $FeeStructure_model->getStructId($details_stu['academic_id']);
         if(!$struct_id){
            $this->_refresh(5,'/academic/application/tuitionfees?v2','Fee not prepared...');
     }
     
    $structure_id =  $struct_id;  
         if($structure_id){
                 $result = $TermItems_model->getItemRecordsByTerm($structure_id,$record['semester']);
                
                 $deptname = $dept->getRecordbyAcademic($details_stu['academic_id']);
            $this->view->result = $result;
            $this->view->department = $deptname['dpt_name'];
            $result1 = $StructureItems_model->getStructureRecords($structure_id);
            
            $this->view->session = $session_model->getRecord($deptname['session']);
             
                                 $htmlcontent = $this->view->render('/application/semfeeresponse-print.phtml');
            $this->_act->generateadmitcardPdf($pdfheader, $pdffooter, $htmlcontent,"feeslip",'P',100 );
             
             
                }
             }
             
           
}
         
         
            public function transctionreportAction(){
           
            $payment = new Application_Model_FeesCollection();
             $Academic_model = new Application_Model_Academic();
             $session_model = new Application_Model_Session();
            $FeeCategory_model = new Application_Model_FeeCategory();
            $FeeHeads_model = new Application_Model_FeeHeads();
            $account_model = new Application_Model_Account();
            $studetails = new Application_Model_StudentPortal();
            $FeeStructure_model = new Application_Model_FeeStructure();
            $dept = new Application_Model_Department();

            $StructureItems_model = new Application_Model_FeeStructureItems();

            $term_model = new Application_Model_TermMaster();

            $TermItems_model = new Application_Model_FeeStructureTermItems();

        
            $id = $this->_getParam("id");
           
      $record= $payment->getRecord($id);
      $stu_id = $record['stu_id'];
      $records = $payment->getTRecordb($stu_id);
    //   $semesters = $this->mergData($record,array('semester'), count($record));
      $this->view->response_data = $records;
                    $this->view->response_data = $records;
                   
                 //   $record['acc_number'] = $account_model->getName($record["prod"])['acc_number'];
                    $this->view->details = $record;
               //   echo "<prE>";print_r($record);exit;
                    
                    
        $details_stu = $studetails->getStudenacademicDetails(trim($stu_id));
        $this->view->details = $details_stu;
                     // echo "<pre>";print_r($details_stu);exit;
         $struct_id = $FeeStructure_model->getStructId($details_stu['academic_id']);
         if(!$struct_id){
            $this->_refresh(5,'/academic/application/tuitionfees?v2','Fee not prepared...');
     }
     
    $structure_id =  $struct_id;  
         if($structure_id){
             //===[starts]
                
                 $result = $TermItems_model->gettotalFee($structure_id,$record['semester']);
                
                
                 $deptname = $dept->getRecordbyAcademic($details_stu['academic_id']);
            $this->view->result = $result;
            $this->view->department = $deptname['dpt_name'];
            $result1 = $StructureItems_model->getStructureRecords($structure_id);
            
            $this->view->session = $session_model->getRecord($deptname['session']);
            $this->view->result1 = $result1;
           
            $academic_id  = $TermItems_model->getAcademicId($structure_id);
            $terms_data = $term_model->getRecordByAcademicId($academic_id['academic_id']);
            $degree_id = $Academic_model->getAcademicDegree($academic_id['academic_id']);
            $this->view->term_data = $terms_data; 
            $this->view->structure_id = $structure_id;
            $Category_data = $FeeCategory_model->getCategory($degree_id);
            $this->view->Category_data = $Category_data;
            
            $Feeheads_data = $FeeHeads_model->getFeeheads($degree_id);
             $this->view->heads_data = $Feeheads_data; //======END
            $this->view->result = $this->stackData($Category_data,$result,count($Category_data));
             
                                 $htmlcontent = $this->view->render('/application/transction_report.phtml');
                                 echo $htmlcontent; die;
        $this->_act->generateadmitcardPdf($pdfheader, $pdffooter, $htmlcontent,"feeslip",'P',100 );
             
              
            }
   
           
         }
         
         
         
   public function redirecttuitionform($message){
        $this->_flashMessenger->addMessage($message);
       
           $this->_redirect('application/tuitionfees');  
          
    }
    
    
    public function previewfromAction() {
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        
        $student_exam_form = new Application_Model_ExamformSubmissionModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

        switch ($type) {       
            case "view":
         
             $assignment_form = new Application_Form_AdmitCardView();
             $this->view->form = $assignment_form;
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {
                       // print_r($_POST);
                       // die();
                    $stu_id=md5(trim($_POST['stu_id'])); 
                    
                   $getvalue =$allow_model-> getformpreview($_POST['stu_id']);
                   if(empty($getvalue)){
                       echo "<script>
                          alert('You enter Invalid Form Id OR you Choose Wrong Semester');
                          window.location.href='application/admitcard';
                          </script>"; 
                       die();
                   }
                    $this->view->fid = $_POST['stu_id'];
                    $result =$student_exam_form->getRecordbyfid($stu_id);
                    $checkk=$allow_model->checkRecordByUid($stu_id); 
                    $this->view->stu_allow = $checkk;
               // echo "<pre>"; print_r($result);exit;
                   $stu_image = $assignment_model->getImage($stu_id);
                   if($result=='')
                   {     
                       
                        $this->redirectExamform('You not submit your fill your form or payment Due');
                     
                   } 
                   
                   else {
                   $sem_id= $allow_model->getTermIdByFid($_POST['stu_id'],$result['academic_year_id']);
                   // echo "<pre>"; print_r($sem_id);exit;
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                    $this->view->semester = $sem_id[0]['term_name'];        
                   
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                   $term_obj = new Application_Model_TermMaster();
                   $term_details =  $term_obj->getTermRecords($acad_term_arr['batch_id'],$acad_term_arr['term_id']);
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =  date('M') .'-'.$result['exam_year']  ;
                    if(!$ac_details['department']){
                        echo "Please Fill the form first ";die;
                    }
                    $dept_id = $dept_model->getRecord($ac_details['department']);    
                    $fee_pay = $fee_model->getRecordByDepartment($dept_id['id']);
                     //print_r($fee_pay);
                     //die();
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
                    
                    
                     
                    /*
                        * stu_id[primary key id] 
                        *   batch_id[id]
                        *   term_id[id]
                        *
                        *
                        */
                     
                   $course_details_arr  = $course_learning->getcourseTypeOn($acad_term_arr['batch_id'],$acad_term_arr['term_id']); 
                  $course_details_arr = $this->geStudentCourse($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                    $core_course_name_arr = $this->mergData($course_details_arr, array('cc_name'), count($course_details_arr));
                    $ge_arr = $this->mergData($course_details_arr, array('ge_id'), count($course_details_arr));
                       
                               
                    
                       $core_course_name_arr = array_values(array_unique($core_course_name_arr));
                       $ge_arr = array_values(array_unique($ge_arr));
                         $selectge = $elective_selection->getCouseDetailByStudentId($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                   $this->view->course_cat = $core_course_name_arr;
                   $this->view->ge_arr = $ge_arr;
                   $this->view->core_details_arr = $course_details_arr;
                   //$this->view->std_name = $result['stu_fname']. ' ' .$result['stu_lname'];
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->course_val = $selectge;
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   
                 
                   $this->view->semester = $term_details[0]['term_name'];
                   if(!$ac_details || !$ac_details['department']){
                       echo "please fill you form first";die;
                   }
                   $this->view->dept_id =$dept_model->getRecord($ac_details['department'])['department'];
                  // echo "<pre>"; print_r($ac_details);exit;
                   $result['exam_roll'] =$result['examination_id'];
                   $result['semester'] = $term_details[0]['term_name'];
                   $result['registration_no'] = $result['reg_no'];
                 
                   //echo "<pre>"; print_r($result['session_id']);exit;
                   
                   //echo $acad_term_arr['batch_id'];
                  // die();
                   
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                   
                  //echo "<pre>";  print_r($course_details_arr);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    $paymentdesc = new Application_Model_ExamfeeSubmitModel();
                    $fetchdetail = $paymentdesc->getRecordbyfid($stu_id);
                    //echo "<pre>"; print_r($fetchdetail);exit;
                     $this->view->payment_details = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                    
                    
                    $assignment_form->populate($result);
                  
                      
                   }

                    }
                }


                break;
          
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('application/index');
                }
                break;
            
            default:
             $admit_form = new Application_Form_AdmitCard();
           
                break;
        }
    }
    
    
        public function previewfrom1Action() {
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        
        $student_exam_form = new Application_Model_ExamformSubmissionModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

        switch ($type) {       
            case "view":
         
             $assignment_form = new Application_Form_AdmitCardView();
             $this->view->form = $assignment_form;
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {
                       // print_r($_POST);
                       // die();
                    $stu_id=md5(trim($_POST['stu_id'])); 
                    
                   $getvalue =$allow_model-> getformpreview($_POST['stu_id']);
                   if(empty($getvalue)){
                       echo "<script>
                          alert('You enter Invalid Form Id OR you Choose Wrong Semester');
                          window.location.href='application/admitcard';
                          </script>"; 
                       die();
                   }
                    $this->view->fid = $_POST['stu_id'];
                    $result =$student_exam_form->getRecordbyfid($stu_id);
                    $checkk=$allow_model->checkRecordByUid($stu_id); 
                    $this->view->stu_allow = $checkk;
                  // echo "<pre>"; print_r($checkk);exit;
                   $stu_image = $assignment_model->getImage($stu_id);
                   if($result=='')
                   {     
                       
                        $this->redirectExamform('You not submit your fill your form or payment Due');
                     
                   } 
                   
                   else {
                   $sem_id= $allow_model->getTermIdByFid($_POST['stu_id'],$result['academic_year_id']);
                   // echo "<pre>"; print_r($sem_id);exit;
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                    $this->view->semester = $sem_id[0]['term_name'];        
                   
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                   $term_obj = new Application_Model_TermMaster();
                   $term_details =  $term_obj->getTermRecords($acad_term_arr['batch_id'],$acad_term_arr['term_id']);
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =   date('M').'-'.$result['exam_year']  ;
                    if(!$ac_details['department']){
                        echo "Please Fill the form first ";die;
                    }
                    $dept_id = $dept_model->getRecord($ac_details['department']);    
                    $fee_pay = $fee_model->getRecordByDepartment($dept_id['id']);
                     //print_r($fee_pay);
                     //die();
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
                    
                    
                     
                    /*
                        * stu_id[primary key id] 
                        *   batch_id[id]
                        *   term_id[id]
                        *
                        *
                        */
                     
                   $course_details_arr  = $course_learning->getcourseTypeOn($acad_term_arr['batch_id'],$acad_term_arr['term_id']); 
                  $course_details_arr = $this->geStudentCourse($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                    $core_course_name_arr = $this->mergData($course_details_arr, array('cc_name'), count($course_details_arr));
                    $ge_arr = $this->mergData($course_details_arr, array('ge_id'), count($course_details_arr));
                       
                               
                    
                       $core_course_name_arr = array_values(array_unique($core_course_name_arr));
                       $ge_arr = array_values(array_unique($ge_arr));
                         $selectge = $elective_selection->getCouseDetailByStudentId($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                   $this->view->course_cat = $core_course_name_arr;
                   $this->view->ge_arr = $ge_arr;
                   $this->view->core_details_arr = $course_details_arr;
                   //$this->view->std_name = $result['stu_fname']. ' ' .$result['stu_lname'];
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->course_val = $selectge;
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   
                 
                   $this->view->semester = $term_details[0]['term_name'];
                   if(!$ac_details || !$ac_details['department']){
                       echo "please fill you form first";die;
                   }
                   $this->view->dept_id =$dept_model->getRecord($ac_details['department'])['department'];
                  // echo "<pre>"; print_r($ac_details);exit;
                   $result['exam_roll'] =$result['examination_id'];
                   $result['semester'] = $term_details[0]['term_name'];
                   $result['registration_no'] = $result['reg_no'];
                 
                   //echo "<pre>"; print_r($result['session_id']);exit;
                   
                   //echo $acad_term_arr['batch_id'];
                  // die();
                   
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                   
                  //echo "<pre>";  print_r($course_details_arr);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    //$paymentdesc = new Application_Model_ExamfeeSubmitModel();
                    //$fetchdetail = $paymentdesc->getRecordbyfid($stu_id);
                    //echo "<pre>"; print_r($fetchdetail);exit;
                    // $this->view->payment_details = $fetchdetail;
                    //$this->view->transaction = $fetchdetail['mmp_txn'];
                    //$this->view->bankname = $fetchdetail['bank_name'];
                    
                    
                    $assignment_form->populate($result);
                  
                      
                   }

                    }
                }


                break;
          
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('application/index');
                }
                break;
            
            default:
             $admit_form = new Application_Form_AdmitCard();
           
                break;
        }
    }
    
    
    public function previewnonformAction() {
         
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        
        $student_exam_form = new Application_Model_UgNonformSubmissionModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

        switch ($type) {       
		case "view":
		     ///die();
                       $assignment_form = new Application_Form_AdmitCardView();
                       $this->view->form = $assignment_form;
                      if ($this->getRequest()->isPost()) {
                      if (isset($_POST)) {
                        //print_r($_POST);
                       // die();
                    $stu_id=md5(trim($_POST['stu_id'])); 
                    $this->view->fid = $_POST['stu_id'];
                    $result =$student_exam_form->getNonPreviewRecord($_POST['stu_id'],$_POST['semester']);
                   // print_r($result);
                    //die();
                   $stu_image = $assignment_model->getImage($stu_id);
                   if($result=='')
                   {     
                     
                       
                       $this->redirectExamform('You not submit your fill your form or payment Due',1);
                   } 
                   else {
                   $sem_id= $allow_model->getTermIdByFid($stu_id,$result['academic_year_id']);
                  // echo $result['academic_year_id']."server down !"; exit;
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                  
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =  date('M') .'-'.$result['exam_year']  ;
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
            
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   $this->view->semester = $result['term_description'];
                 if(!$ac_details || !$ac_details['department']){
                       echo 'You have to fill your form first !' ;
                   }
                   $this->view->dept_id = $dept_model->getRecord($ac_details['department'])['department'];
                  
                   $result['exam_roll'] =$result['examination_id'];
                   $result['registration_no'] = $result['reg_no'];
                 
//echo "<pre>"; print_r($result['session_id']);exit;
                    $paymentdesc = new Application_Model_NonColpayment();
                    $fetchdetail = $paymentdesc->getRecordbyfid($stu_id);
                    //echo "<pre>"; print_r($fetchdetail);exit;
                     $this->view->transaction_detail = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                  //echo "<pre>";  print_r($exam_schedule);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    // print_r($_POST);
                    //die();
                   $nonsubject = new Application_Model_BackSelectionItems();
                     $getsub= $nonsubject->getStuPreviewRecords($_POST['stu_id'],$_POST['semester']);
                     $this->view->subjectdetail = $getsub;
                    // echo "<pre>";  print_r($getsub);die();
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    
                    
                    $assignment_form->populate($result);
                  
                      
                   }

                    }
                }


                break;

            default:
             $admit_form = new Application_Form_AdmitCard();
           
                break;
        }
    }
    
     public function previewpgformAction() {
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        $student_exam_form = new Application_Model_NonPgDataModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

        switch ($type) {       
		case "view":
                       $assignment_form = new Application_Form_AdmitCardView();
                       $this->view->form = $assignment_form;
                      if ($this->getRequest()->isPost()) {
                      if (isset($_POST)) {
                        //print_r($_POST);
                        //die();
                    $stu_id=md5(trim($_POST['stu_id'])); 
                   
                    $result =$student_exam_form->getNonRecordbyfid($_POST['stu_id']);
                    ///print_r($result);
                    //  die();
                  
                   if(empty($result))
                   {     
                       //echo "hii";
                       //die();
                     echo "<script>
                     alert('Application form Not submitted');
                     </script>";
                       $this->redirectExamform("Your Exam Form does not Submit Or Payment Not Completed",'pg');
                   } 
                   else {
                    $this->view->fid = $_POST['stu_id'];
                    $stu_image = $assignment_model->getImage($stu_id);
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                 
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                  
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =   date('M').'-'.$result['exam_year']  ;
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
            
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   $this->view->semester = 'Semester 3';
                   //echo "<pre>";print_r($ac_details);die;
                   $deprt = $dept_model->getRecord($ac_details['department']);
                    //print_r($deprt[department]);
                      // die();
                   $this->view->dept_id = $deprt[department];
                  
                   $result['exam_roll'] =$result['examination_id'];
                   $result['registration_no'] = $result['reg_no'];
                 
//echo "<pre>"; print_r($result['session_id']);exit;
                    $paymentdesc = new Application_Model_NonpgPaymentModel();
                    $fetchdetail = $paymentdesc->getpayRecordbyfid($stu_id);
                    //echo "<pre>"; print_r($fetchdetail);exit;
                     $this->view->transaction_detail = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                  //echo "<pre>";  print_r($exam_schedule);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    $nonsubject = new Application_Model_NonPgCollegiateModel();
                     $getsub= $nonsubject->getStuRecords($stu_id);
                     $this->view->subjectdetail = $getsub;
                     //echo "<pre>";  print_r($getsub);
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    
                    
                    $assignment_form->populate($result);
                  
                      
                   }

                    }
                }


                break;

            default:
             $admit_form = new Application_Form_AdmitCard();
           
                break;
        }
    }
	
	
	public function ajaxGetFeeAction(){
	    	$this->_helper->layout->disableLayout();

		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
	     $stu_id = $this->_getParam("academic_id");
        $semester = $this->_getParam("term_id");
        $studetails = new Application_Model_StudentPortal();
        $feeStructure = new Application_Model_FeeStructure();
         $dept_model = new Application_Model_Department();
          $academic_model = new Application_Model_Academic();
           $dept_type_details = new Application_Model_DepartmentType();
        if($semester != 't1'){
        $details_stu = $studetails->getStudenacademicDetails($stu_id);
        }
        else
        {
                $stu_details = new Application_Model_ApplicantCourseDetailModel();
                       $details_stu = $stu_details->getApplicationNumber($stu_id);
                       $details_stu['stu_fname'] = $details_stu['applicant_name'];
                         if($details_stu['core_course1']){
                        $details_stu['academic_id'] = $details_stu['core_course1'];
                        }
                        else{
                         $dept_type = $details_stu['course'];
             $dept_details = $dept_type_details->getRecord($dept_type);
            
             $dept_id_only = $dept_model->getByDepartmentType($dept_type)['did'];
           
            $details_stu['academic_id'] = $academic_model->getAcademicsBySD($dept_details['session_id'],$dept_id_only)['academic_year_id'];
          //  echo $details_stu['academic_id']; die;
                        }
                        $details_stu['father_mobileno'] = $details_stu['phone'];
                        $details_stu['roll_no'] = 0;
                        $details_stu['exam_roll'] = 0;
                        $details_stu['stu_id'] = $details_stu['form_id'] ;
                        $details_stu['filename'] = $details_stu['photo'];
        }
         $struct_id = $feeStructure->getStructId($details_stu['academic_id']);
         //echo $struct_id;die;
        $feeitems = new Application_Model_FeeStructureTermItems();
        
       
        $fee = $feeitems->getFee($struct_id,$semester);
        echo "<option value=''>Select Fees</option>";  
        foreach($fee as $key => $value){
            echo "<option value={$value['totalfee']}>{$value['totalfee']}</option>";    
            }die;
		}
	}
	
	
	
	
		public function ajaxGetInstalmentAction(){
	    	$this->_helper->layout->disableLayout();

		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
	     $stu_id = $this->_getParam("academic_id");
        $semester = $this->_getParam("term_id");
        $studetails = new Application_Model_StudentPortal();
        $feeStructure = new Application_Model_FeeStructure();
        if($semester != 't1'){
        $details_stu = $studetails->getStudenacademicDetails($stu_id);
        }
        else
        {
                $stu_details = new Application_Model_ApplicantCourseDetailModel();
                       $details_stu = $stu_details->getApplicationNumber($stu_id);
                       $details_stu['stu_fname'] = $details_stu['applicant_name'];
                        $details_stu['academic_id'] = $details_stu['core_course1'];
                        $details_stu['father_mobileno'] = $details_stu['phone'];
                        $details_stu['roll_no'] = 0;
                        $details_stu['exam_roll'] = 0;
                        $details_stu['stu_id'] = $details_stu['form_id'] ;
                        $details_stu['filename'] = $details_stu['photo'];
        }
         $struct_id = $feeStructure->getStructId($details_stu['academic_id']);
         //echo $struct_id;die;
        $feeitems = new Application_Model_FeeStructureTermItems();
        
        $installment = new Application_Model_FeeInstallment();
        
      $fee =  $installment->getRecordbyacadId($details_stu['academic_id'],$semester);
        
      // echo "<Pre>"; print_r($fee);exit;
    //    $fee_acc = $feeitems->getFee($struct_id,$semester);
        
     //  echo "<prE>"; print_r($fee);exit;
        echo "<option value=''>Select Fees</option>";  
        foreach($fee as $key => $value){
            $accn = explode('-',$value['fees'])[1];
            $des = $value['description'];
            
            echo "<option value='{$value['amount']}-$accn-{$value['description']}' >{$value['amount']}-{$value['description']}</option>";    
            }die;
		}
	}
	
	
			public function ajaxGetInstalmentDateAction(){
	    	$this->_helper->layout->disableLayout();

		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
	     $stu_id = $this->_getParam("academic_id");
        $semester = $this->_getParam("term_id");
        $studetails = new Application_Model_StudentPortal();
        $feeStructure = new Application_Model_FeeStructure();
        if($semester != 't1'){
        $details_stu = $studetails->getStudenacademicDetails($stu_id);
        }
        else
        {
                $stu_details = new Application_Model_ApplicantCourseDetailModel();
                       $details_stu = $stu_details->getApplicationNumber($stu_id);
                       $details_stu['stu_fname'] = $details_stu['applicant_name'];
                        $details_stu['academic_id'] = $details_stu['core_course1'];
                        $details_stu['father_mobileno'] = $details_stu['phone'];
                        $details_stu['roll_no'] = 0;
                        $details_stu['exam_roll'] = 0;
                        $details_stu['stu_id'] = $details_stu['form_id'] ;
                        $details_stu['filename'] = $details_stu['photo'];
        }
         $struct_id = $feeStructure->getStructId($details_stu['academic_id']);
         //echo $struct_id;die;
        $feeitems = new Application_Model_FeeStructureTermItems();
        
        $installment = new Application_Model_FeeInstallment();
        
      $fee =  $installment->getRecordbyacadId($details_stu['academic_id'],$semester);
        
      // echo "<Pre>"; print_r($fee);exit;
    //    $fee_acc = $feeitems->getFee($struct_id,$semester);
        
     //   echo "<prE>"; print_r($fee_acc);exit;
        $arr = array();
        foreach($fee as $key => $value){
           $arr[$key] = "{$value['endDate']}-{$value['description']}" ;
            }echo json_encode($arr);die;
		}
	}
	
	
	
	public function marksheetAction(){
             $this->view->mainurl=  $this->_base_url;
             if($_POST) {
                 $stu_id = md5($_POST['app_id']);
                 $term = md5($_POST['term']);
             if($_POST['stu_type']=='2') {
                 
                 $this->_redirect('application/student-back-term-report/?stuid='.$stu_id.'&term='.$_POST['term'].'');
                }
             else {
                
                  $this->_redirect('application/student-term-report/?stuid='.$stu_id.'&term='.$_POST['term'].'');
              
             }
             
             //print_r($_POST); die();
             }
	    
	}
	
	   public function hallticketAction(){
                    if (isset($_POST['app_id'])) {
                        $application_id = md5($_POST['app_id']);
                       //header( "refresh:5;url=/academic/entrance-exam-form/document-dashboard/a_id/{$application_id}" );die;                    
                       // $this->_redirect("entrance-exam-form/document-dashboard/a_id/{$application_id}");
                    }
         
        
        
    }
    
   public function admitcardprintAction() {
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        
        $student_exam_form = new Application_Model_ExamformSubmissionModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $st_id = $this->_getParam("uid");
        $semester = $this->_getParam("term_id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

       
         
             $assignment_form = new Application_Form_AdmitCardView();
             $this->view->form = $assignment_form;
                //if ($this->getRequest()->isPost()) {
                 //   if (isset($_POST)) {
                       // print_r($_POST);
                      
             //echo $semester;
              //die();
                    $stu_id=md5(trim($st_id)); 
                    

//                   $getvalue =$allow_model-> getRecordforadmit($st_id,$semester);

//                   if(empty($getvalue)){
//                       echo "<script>
//                          alert('You enter Invalid Form Id OR you Choose Wrong Semester');
//                          window.location.href='application/admitcard';
//                          </script>"; 
//                       die();
//                   }
                    $this->view->fid = $st_id;
                    $result =$student_exam_form->getRecordbyfid($stu_id);
                    $exam_roll = $result['examination_id'];
                 //  echo "<pre>"; print_r($result);exit;
                    $checkk=$allow_model->checkRecordByUid($stu_id,$semester); 
                    $this->view->stu_allow = $checkk;
               // echo "<pre>"; print_r($checkk);exit;
                   $stu_image = $assignment_model->getImage($stu_id);
                   if($result=='')
                   {     
                       
                        $this->redirectExamform('You not submit your fill your form or payment Due');
                     
                   } 
                   
                   else {
                   $sem_id= $allow_model->getTermIdByFid($st_id,$result['academic_year_id']);
                   // echo "<pre>"; print_r($sem_id);exit;
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                    $this->view->semester = $sem_id[0]['term_name'];        
                   
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                   $term_obj = new Application_Model_TermMaster();
                   $term_details =  $term_obj->getTermRecords($acad_term_arr['batch_id'],$acad_term_arr['term_id']);
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =   date('M').'-'.$result['exam_year']  ;
                    if(!$ac_details['department']){
                        echo "Please Fill the form first ";die;
                    }
                    $dept_id = $dept_model->getRecord($ac_details['department']);    
                    $fee_pay = $fee_model->getRecordByDepartment($dept_id['id']);
                     //print_r($fee_pay);
                     //die();
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
                    
                    
                     
                    /*
                        * stu_id[primary key id] 
                        *   batch_id[id]
                        *   term_id[id]
                        *
                        *
                        */
                     
                   $course_details_arr  = $course_learning->getcourseTypeOn($acad_term_arr['batch_id'],$acad_term_arr['term_id']); 
                  $course_details_arr = $this->geStudentCourse($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                    $core_course_name_arr = $this->mergData($course_details_arr, array('cc_name'), count($course_details_arr));
                    $ge_arr = $this->mergData($course_details_arr, array('ge_id'), count($course_details_arr));
                       
                               
                    
                       $core_course_name_arr = array_values(array_unique($core_course_name_arr));
                       $ge_arr = array_values(array_unique($ge_arr));
                         $selectge = $elective_selection->getCouseDetailByStudentId($acad_term_arr['batch_id'],$acad_term_arr['term_id'],$result['student_id']);
                   $this->view->course_cat = $core_course_name_arr;
                   $this->view->ge_arr = $ge_arr;
                   $this->view->core_details_arr = $course_details_arr;
                   //$this->view->std_name = $result['stu_fname']. ' ' .$result['stu_lname'];
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->course_val = $selectge;
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   
                 
                   $this->view->semester = $term_details[0]['term_name'];
                   if(!$ac_details || !$ac_details['department']){
                       echo "please fill you form first";die;
                   }
                   $this->view->dept_id =$dept_model->getRecord($ac_details['department'])['department'];
                  // echo "<pre>"; print_r($ac_details);exit;
                   $result['exam_roll'] =$result['examination_id'];
                   $result['semester'] = $term_details[0]['term_name'];
                   $result['registration_no'] = $result['reg_no'];
                 
                   //echo "<pre>"; print_r($result['session_id']);exit;
                   
                   //echo $acad_term_arr['batch_id'];
                  // die();
                   
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                   
                  //echo "<pre>";  print_r($course_details_arr);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    $paymentdesc = new Application_Model_ExamfeeSubmitModel();
                    $fetchdetail = $paymentdesc->getRecordbyfid($stu_id);
                  //  echo "<pre>"; print_r($fetchdetail);exit;
                     $this->view->payment_details = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                    
                    
                    $assignment_form->populate($result);
                    $htmlcontent = $this->view->render('application/admitcardprint.phtml');
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generateadmitcardPdf($pdfheader, $pdffooter, $htmlcontent, $exam_roll .$filename,'P',100 );
                  
                      
                   }

                    //}
                //}


               
        }
        
        
        public function admitcardnoncollegiateprintAction() {
         
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        
        $student_exam_form = new Application_Model_UgNonformSubmissionModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $s_id = $this->_getParam("uid");
        $term = $this->_getParam("term_id");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

       
		     ///die();
                       $assignment_form = new Application_Form_AdmitCardView();
                       $this->view->form = $assignment_form;
                      //if ($this->getRequest()->isPost()) {
                      //if (isset($_POST)) {
                        //print_r($_POST);
                        //die();
                    $stu_id=md5(trim($s_id)); 
                    $this->view->fid = $s_id;
                    $result =$student_exam_form->getNonRecordbyfid($s_id);
                    $exam_roll = $result['examination_id'];
                     //print_r($result);
                    //die();
                   $stu_image = $assignment_model->getImage($stu_id);
                   if($result=='')
                   {     
                     
                       
                       $this->redirectExamform('You not submit your fill your form or payment Due',1);
                   } 
                   else {
                   $sem_id= $allow_model->getTermIdByFid($stu_id,$result['academic_year_id']);
                  // echo $result['academic_year_id']."server down !"; exit;
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                  
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =   date('M').'-'.$result['exam_year']  ;
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
            
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   $this->view->semester = 'Semester 3';
                 if(!$ac_details || !$ac_details['department']){
                       echo 'You have to fill your form first !' ;
                   }
                   $this->view->dept_id = $dept_model->getRecord($ac_details['department'])['department'];
                  
                   $result['exam_roll'] =$result['examination_id'];
                   $result['registration_no'] = $result['reg_no'];
                 
//echo "<pre>"; print_r($result['session_id']);exit;
                    $paymentdesc = new Application_Model_NonColpayment();
                    $fetchdetail = $paymentdesc->getRecordbyfid($stu_id);
                    //echo "<pre>"; print_r($fetchdetail);exit;
                     $this->view->transaction_detail = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                  //echo "<pre>";  print_r($exam_schedule);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    $nonsubject = new Application_Model_NonCollegiateModel();
                     $getsub= $nonsubject->getStuRecords($stu_id);
                     $this->view->subjectdetail = $getsub;
                     //echo "<pre>";  print_r($getsub);
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    
                    
                    $assignment_form->populate($result);
                  
                    $htmlcontent = $this->view->render('application/admitcardnoncollegiateprint.phtml');
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generateadmitcardPdf($pdfheader, $pdffooter, $htmlcontent, $exam_roll .$filename,'P',100 );  
                   }

//                    }
//                }
      
        
    }
    
      public function admitcardpgnoncollegiateprintAction() {
        $this->view->action_name = 'admitcard';
        $this->view->sub_title_name = 'admitcard';
        $this->accessConfig->setAccess('SA_ACAD_ADMIT_CARD');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $elective_selection =  new Application_Model_ElectiveSelectionItems();
        $course_learning = new  Application_Model_Corecourselearning();
        $assignment_model = new Application_Model_StudentPortal();
        $allow_model = new Application_Model_BatachSemesterAttendance();
       // $assignment_form = new Application_Form_AdmitCardView();
        $academic_details = new Application_Model_Academic();
        $admit_form = new Application_Form_AdmitCard();
        $dept_model = new Application_Model_Department();
        $fee_model = new Application_Model_Coursefee();
        $student_exam_form = new Application_Model_NonPgDataModel();
        $examschedule_model = new Application_Model_ExamScheduleModel();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
         $st_id = $this->_getParam("uid");
        $this->view->type = $type;
        $this->view->form = $admit_form;
        //$this->view->form = $assignment_form;

       
                       $assignment_form = new Application_Form_AdmitCardView();
                       $this->view->form = $assignment_form;
                      //if ($this->getRequest()->isPost()) {
                      //if (isset($_POST)) {
                        //print_r($_POST);
                        //die();
                    $stu_id=md5(trim($st_id)); 
                   
                    $result =$student_exam_form->getNonRecordbyfid($st_id);
                    $exam_roll = $result['examination_id'];
                   // print_r($result);
                      // die();
                  
                   if(empty($result))
                   {     
                       //echo "hii";
                       //die();
                     echo "<script>
                     alert('Application form Not submitted');
                     </script>";
                       $this->redirectExamform("Your Exam Form does not Submit Or Payment Not Completed",'pg');
                   } 
                   else {
                    $this->view->fid = $st_id;
                     $updatestaus= $student_exam_form->update(array('status'=> 1), array('u_id=?' => $_POST['stu_id'],'payment_status=?'=>1,'status=?'=>0))  ;
                    $stu_image = $assignment_model->getImage($stu_id);
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                 
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                  
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =   date('M').'-'.$result['exam_year']  ;
                    $this->view->feeamount = $fee_pay['examFee'];
                    $currentdate= date('Y-m-d');
            
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   $this->view->semester = 'Semester 1';
                   //echo "<pre>";print_r($ac_details);die;
                   $deprt = $dept_model->getRecord($ac_details['department']);
                    //print_r($deprt[department]);
                      // die();
                   $this->view->dept_id = $deprt[department];
                  
                   $result['exam_roll'] =$result['examination_id'];
                   $result['registration_no'] = $result['reg_no'];
                 
//echo "<pre>"; print_r($result['session_id']);exit;
                    $paymentdesc = new Application_Model_NonpgPaymentModel();
                    $fetchdetail = $paymentdesc->getpayRecordbyfid($stu_id);
                    //echo "<pre>"; print_r($fetchdetail);exit;
                     $this->view->transaction_detail = $fetchdetail;
                    $this->view->transaction = $fetchdetail['mmp_txn'];
                    $this->view->bankname = $fetchdetail['bank_name'];
                   $exam_schedule = $examschedule_model->getRecordBysession($result['session_id'],$acad_term_arr['batch_id']);
                   $this->view->exam_sch = $course_details_arr;
                  //echo "<pre>";  print_r($exam_schedule);
                  
                  //echo $exam_schedule[0]['course_id'];
                  //die();
                    $nonsubject = new Application_Model_NonPgCollegiateModel();
                     $getsub= $nonsubject->getStuRecords($stu_id);
                     $this->view->subjectdetail = $getsub;
                     //echo "<pre>";  print_r($getsub);
                    $result['comm_exam'] =date('d-m-Y',strtotime($exam_schedule[0]['exam_date']));
                    
                    
                    $assignment_form->populate($result);
                    $htmlcontent = $this->view->render('application/admitcardpgnoncollegiateprint.phtml');
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generateadmitcardPdf($pdfheader, $pdffooter, $htmlcontent, $exam_roll .$filename,'P',100 ); 
                  
                      
                   }

                    //}
                


              
       // }
    }
     //promotion certificate module date: 20-07-2019
    public function promotionStudentAction(){
       $this->view->action_name = 'studentinfo';
        $this->view->sub_title_name = 'student_detail';
        $this->accessConfig->setAccess('SA_ACAD_STUDETAIL');
        //$this->_helper->layout->disableLayout();
        $this->_helper->layout->setLayout("applicationlayout");
        $student_form = new Application_Form_StudentPortal($this->aeccConfig[0]);
        $student_report_form = new Application_Form_StudentsAdmitcard();
        //$academic_id = $this->_getParam("id");
        $this->view->form = $student_report_form;   
    }
    public function ajaxGetStudentInfoForPromotionAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $erpStudent_model = new Application_Model_StudentPortal();
            $form_id = $this->_getParam("form_id");
            $term_id = $this->_getParam("term_id");
            $checkPromotionDocument= $erpStudent_model->checkPromotionDocument($form_id);
            if(!empty($checkPromotionDocument)){
                $result='docs exists';
                $this->view->resultData='';
            }else{
                $result = $erpStudent_model->getStudenInfoByFormIdpromoted($form_id,$term_id);
                if(!empty($result['batch'])){
                $this->view->resultData=$result;
                }else{
                    $result='not exists';
                    $this->view->resultData=$result;
                }
            }
            
        }
    }
    
     public function promotionprintAction(){
        $this->_helper->layout->disableLayout();
         $this->_helper->layout->setLayout("applicationlayout");
        $stu_id = $this->_getParam("stu_id");
        $erpStudent_model = new Application_Model_StudentPortal();
        $applicantDetails=$erpStudent_model->getStudenInfoByFormId($stu_id);
        
        $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $applicantDetails
                );
               
               //echo"<pre>";print_r($applicantDetails);exit;
                $this->view->paginator = $applicantDetails;
                 $htmlcontent = $this->view->render('application/promotionprint.phtml');
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generateadmitcardPdf($pdfheader, $pdffooter, $htmlcontent, $applicantDetails['stu_fname'] .$applicantDetails['stu_id'],'P',150 );     
    }
    
     public function followUpPromotionCertStudentAction(){
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
        $erpStudent_model = new Application_Model_StudentPortal();
        $form_id = $this->_getParam("form_id");
        $checkPromotionDocument= $erpStudent_model->checkPromotionDocument($form_id);
        if(!empty($checkPromotionDocument)){
            $result = $erpStudent_model->updatePromotionStudents($form_id);
            echo 'update';
        }else{
            $date = date('d/m/Y');
            $data=array(
                'form_id'=>$form_id,
                'status'=>1,
                'date' =>$date
            );
            $result = $erpStudent_model->insertPromotionStudents($data);
            echo 'insert';
        }
        }die;
        
    }


    //For upload documents
    public function loginToUploadAction(){
        $this->_helper->layout->setLayout("applicationlayout");
        $student_form = new Application_Form_StudentPortal($this->aeccConfig[0]);
        $reqModel=new Application_Model_AnnounceResultModel();
        $student_report_form = new Application_Form_StudentsAdmitcard();
        //$academic_id = $this->_getParam("id");
        $this->view->form = $student_report_form;
          if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                //echo '<pre>'; print_r($data);exit;
                if(!empty($data['app_no'])){
                    $stu_id=trim($data['app_no']);
                    $checkAlreadyUploaded=$reqModel->checkUploadedId($stu_id);
                   
                    if(empty($checkAlreadyUploaded)){
                        $checkAppNo=$reqModel->validateStuId($stu_id) ;
                        $a_id= trim($checkAppNo['stu_id']);
                        $encryptedId=md5($a_id);
                        if(!empty($checkAppNo)){
                            $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                        }else{
                            echo "<b style='color:red'>Please Enter a valid application number.</b>";
                            echo '<br>';
                            echo "<b style='color:blue'>Please try again with valid application number.</b>";
                            header( "refresh:5;url=login-to-upload" );
                            die;
                        }
                    }else{
                          echo '<b>You have already uploaded your documents.</b>';
                            echo '<br>';
                            echo '<b>If you have any queries kindly email on following mail id.</b>';
                             echo '<br>'; 
                              echo "<b style='color:red'>support@finessewebtech.com</b>";
                            header( "refresh:30;url=login-to-upload" );
                            die;
                        
                    }
                  
                    
                }
          }
    }
    //Upload Documents for New Passed Students Controller
    public function uploadAdmDocAction(){
        $this->_helper->layout->setLayout("applicationlayout");
        $student_form = new Application_Form_StudentPortal($this->aeccConfig[0]);
        $student_report_form = new Application_Form_StudentsAdmitcard();
        $reqModel=new Application_Model_AnnounceResultModel();
        //$academic_id = $this->_getParam("id");
        $this->view->form = $student_report_form;  
        $app_id = $this->_getParam("a_id");
        
        
        $getStudentDetails=$reqModel->studentDetails($app_id);
        $getDocumentsDetails=$reqModel->documentDetails($app_id);
        $this->view->documentData=$getDocumentsDetails;
        $this->view->studentData=$getStudentDetails;
        if ($this->getRequest()->isPost()) {
           $data = $this->getRequest()->getPost();
        
            $app_number= $getStudentDetails['application_no'];
            //echo '<pre>'; print_r($getStudentDetails);exit;
                        
                        
                        $dirPath1 = realpath(APPLICATION_PATH . '/../public/required_document') .'/marks_sheet/';
                        $dirPath2 = realpath(APPLICATION_PATH . '/../public/required_document') .'/slc_certificate/';
                        $dirPath3 = realpath(APPLICATION_PATH . '/../public/required_document') .'/char_certificate/';
                        $dirPath4 = realpath(APPLICATION_PATH . '/../public/required_document') .'/caste_certificate/';
                        $dirPath5 = realpath(APPLICATION_PATH . '/../public/required_document') .'/baptism_certificate/'; 
                        $dirPath6 = realpath(APPLICATION_PATH . '/../public/required_document') .'/undertaking_cert/';
                        
                        //For Pg Documents
                        $dirPath7 = realpath(APPLICATION_PATH . '/../public/required_document') .'/first_year/';
                        $dirPath8 = realpath(APPLICATION_PATH . '/../public/required_document') .'/second_year/';
                        $dirPath9 = realpath(APPLICATION_PATH . '/../public/required_document') .'/third_year/';


                            if (!file_exists($dirPath1)) {
                                mkdir($dirPath1, 755, true);
                            }
                            if (!file_exists($dirPath2)) {
                                mkdir($dirPath2, 755, true);
                            }
                            if (!file_exists($dirPath3)) {
                                mkdir($dirPath3, 755, true);
                            }
                            if (!file_exists($dirPath4)) {
                                mkdir($dirPath4, 755, true);
                            }
                            if (!file_exists($dirPath5)) {
                                mkdir($dirPath5, 755, true);
                            }
                            if (!file_exists($dirPath6)) {
                                mkdir($dirPath6, 755, true);
                            }
                            if (!file_exists($dirPath7)) {
                                mkdir($dirPath7, 755, true);
                            }
                            if (!file_exists($dirPath8)) {
                                mkdir($dirPath8, 755, true);
                            }
                            if (!file_exists($dirPath9)) {
                                mkdir($dirPath9, 755, true);
                            }
                            
                            if(!empty($_FILES['interMark']['name'])){
                                $inter_temp = explode(".", $_FILES["interMark"]["name"]);
                                $intercertificate = $app_number . '.' . end($inter_temp);
                                $inter_temp = $_FILES['interMark']['tmp_name'];
                                //echo $_FILES["casteCertificate"]["name"];exit;
                            }
                            
                            if(!empty($_FILES['slcCert']['name'])){
                                $slc_temp = explode(".", $_FILES["slcCert"]["name"]);
                                $slc_cert = $app_number . '.' . end($slc_temp);
                                $slc_temp = $_FILES['slcCert']['tmp_name'];
                            }
                            
                            if(!empty($_FILES['casteCert']['name'])){
                                $caste_temp = explode(".", $_FILES["casteCert"]["name"]);
                                $caste_cert = $app_number . '.' . end($caste_temp);
                                $caste_temp = $_FILES['casteCert']['tmp_name'];
                            }
                            if(!empty($_FILES['charCert']['name'])){
                                $char_temp = explode(".", $_FILES["charCert"]["name"]);
                                $char_cert = $app_number . '.' . end($char_temp);
                                $char_temp = $_FILES['charCert']['tmp_name'];
                            }
                            if(!empty($_FILES['baptCert']['name'])){
                                $bapt_temp = explode(".", $_FILES["baptCert"]["name"]);
                                $bapt_cert = $app_number . '.' . end($bapt_temp);
                                $bapt_temp = $_FILES['baptCert']['tmp_name'];
                            }
                            if(!empty($_FILES['undertakingCert']['name'])){
                                $undertaking_temp = explode(".", $_FILES["undertakingCert"]["name"]);
                                $undertaking_cert = $app_number . '.' . end($undertaking_temp);
                                $undertaking_temp = $_FILES['undertakingCert']['tmp_name'];
                            }
                            if(!empty($_FILES['1styr']['name'])){
                                $firstyr_temp = explode(".", $_FILES["1styr"]["name"]);
                                $firstYr_cert = $app_number . '.' . end($firstyr_temp);
                                $firstyr_temp = $_FILES['1styr']['tmp_name'];
                            }
                            if(!empty($_FILES['2ndyr']['name'])){
                                $secndYr_temp = explode(".", $_FILES["2ndyr"]["name"]);
                                $secndYr_cert = $app_number . '.' . end($secndYr_temp);
                                $secndYr_temp = $_FILES['2ndyr']['tmp_name'];
                            }
                            if(!empty($_FILES['3rdyr']['name'])){
                                $thirdYr_temp = explode(".", $_FILES["3rdyr"]["name"]);
                                $thirdYr_cert = $app_number . '.' . end($thirdYr_temp);
                                $thirdYr_temp = $_FILES['3rdyr']['tmp_name'];
                            }
                            
                        
                        
                //File Combinations        
                $file_edu['marksheet'] = "public/required_document/marks_sheet/" .$intercertificate;
                $file_slc['slc_cert'] = "public/required_document/slc_certificate/" .$slc_cert;
                $file_char['char_cert'] = "public/required_document/char_certificate/" .$char_cert;
                $file_caste['caste_cert'] = "public/required_document/caste_certificate/" .$caste_cert;
                $file_bapt['bapt_cert'] = "public/required_document/baptism_certificate/" .$bapt_cert;
                $file_undertake['undertaking_cert'] = "public/required_document/undertaking_cert/" .$undertaking_cert;
                $file_fYear['f_year'] = "public/required_document/first_year/" .$firstYr_cert;
                $file_SYear['s_year'] = "public/required_document/second_year/" .$secndYr_cert;
                $file_tYear['t_year'] = "public/required_document/third_year/" .$thirdYr_cert;
                
                
                //To insert in database:
                $data['marksheet'] = $file_edu['marksheet'];
                $data['slc_cert'] =$file_slc['slc_cert'];
                $data['char_cert'] = $file_char['char_cert'];
                $data['caste_cert'] = $file_caste['caste_cert'];
                $data['bapt_cert'] = $file_bapt['bapt_cert'];
                $data['undertaking_cert'] = $file_undertake['undertaking_cert'];
                $data['f_year'] = $file_fYear['f_year'];
                $data['s_year'] = $file_SYear['s_year'];
                $data['t_year'] = $file_tYear['t_year'];
                
                if(empty($_FILES['interMark']['name'])){
                    unset($data['marksheet']); 
                }
                if(empty($_FILES['slcCert']['name'])){
                    unset($data['slc_cert']); 
                }
                if(empty($_FILES['charCert']['name'])){
                    unset($data['char_cert']); 
                }
                if(empty($_FILES['casteCert']['name'])){
                    unset($data['caste_cert']); 
                }
                if(empty($_FILES['baptCert']['name'])){
                    unset($data['bapt_cert']); 
                }
                if(empty($_FILES['undertakingCert']['name'])){
                    unset($data['undertaking_cert']); 
                }
                if(empty($_FILES['1styr']['name'])){
                    unset($data['f_year']); 
                }
                if(empty($_FILES['2ndyr']['name'])){
                    unset($data['s_year']); 
                }
                if(empty($_FILES['3rdyr']['name'])){
                    unset($data['t_year']); 
                }
                $data['application_no']= $getStudentDetails['application_no'];
                $data['degree_id']= $getStudentDetails['degree_id'];
                $data['department']= $getStudentDetails['dept_id'];
                
                //Insert Documents
                //echo '<pre>'; print_r($data);exit;
               $checkDocuments=$reqModel->checkExistedEntry($data['application_no']);
               $encryptedId=md5($data['application_no']);
               if(empty($checkDocuments)){
                   
                        if(file_exists($_FILES['3rdyr']['tmp_name'])){
                            
                            if (move_uploaded_file($thirdYr_temp, $dirPath9 . $thirdYr_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                            
                        }
                        else{
                            if (move_uploaded_file($thirdYr_temp, $dirPath9 . $thirdYr_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        if(file_exists($_FILES['2ndyr']['tmp_name'])){
                            if (move_uploaded_file($secndYr_temp, $dirPath8 . $secndYr_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        else{
                            if (move_uploaded_file($secndYr_temp, $dirPath8 . $secndYr_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        if(file_exists($_FILES['1styr']['tmp_name'])){
                            
                            if (move_uploaded_file($firstyr_temp, $dirPath7 . $firstYr_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 

                        }
                        else{
                            if (move_uploaded_file($firstyr_temp, $dirPath7 . $firstYr_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }
                        }
                        if(file_exists($_FILES['interMark']['tmp_name'])){
                            if (move_uploaded_file($inter_temp, $dirPath1 . $intercertificate)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }                            

                        }
                        else{
                            if (move_uploaded_file($inter_temp, $dirPath1 . $intercertificate)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }
                        }

                        if(file_exists($_FILES['slcCert']['tmp_name'])){
                            if (move_uploaded_file($slc_temp, $dirPath2 . $slc_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }  
                        }
                        else{
                            if (move_uploaded_file($slc_temp, $dirPath2 . $slc_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }  
                        }
                        
                        if(file_exists($_FILES['charCert']['tmp_name'])){
                            if (move_uploaded_file($char_temp, $dirPath3 . $char_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }      
                        }
                        else{
                            if (move_uploaded_file($char_temp, $dirPath3 . $char_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        if(file_exists($_FILES['casteCert']['tmp_name'])){
                            if (move_uploaded_file($caste_temp, $dirPath4 . $caste_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }   
                        }
                        else{
                            if (move_uploaded_file($caste_temp, $dirPath4 . $caste_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        if(file_exists($_FILES['baptCert']['tmp_name'])){
                            if (move_uploaded_file($bapt_temp, $dirPath5 . $bapt_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }   
                        }
                        else{
                            if (move_uploaded_file($bapt_temp, $dirPath5 . $bapt_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }  
                        }
                        if(file_exists($_FILES['undertakingCert']['tmp_name'])){
                            if (move_uploaded_file($undertaking_temp, $dirPath6 . $undertaking_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }    
                        }
                        else{
                            if (move_uploaded_file($undertaking_temp, $dirPath6 . $undertaking_cert)) {
                                $insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                   
               }else{
                   
                   if(file_exists($_FILES['3rdyr']['tmp_name'])){
                            
                            if (move_uploaded_file($thirdYr_temp, $dirPath9 . $thirdYr_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                            
                        }
                        else{
                            if (move_uploaded_file($thirdYr_temp, $dirPath9 . $thirdYr_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        if(file_exists($_FILES['2ndyr']['tmp_name'])){
                            if (move_uploaded_file($secndYr_temp, $dirPath8 . $secndYr_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        else{
                            if (move_uploaded_file($secndYr_temp, $dirPath8 . $secndYr_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        if(file_exists($_FILES['1styr']['tmp_name'])){
                            
                            if (move_uploaded_file($firstyr_temp, $dirPath7 . $firstYr_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                //$insertDoc=$reqModel->insertDocuments($data);
                    
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 

                        }
                        else{
                            if (move_uploaded_file($firstyr_temp, $dirPath7 . $firstYr_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }
                        }
                        if(file_exists($_FILES['interMark']['tmp_name'])){
                            if (move_uploaded_file($inter_temp, $dirPath1 . $intercertificate)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }                            

                        }
                        else{
                            if (move_uploaded_file($inter_temp, $dirPath1 . $intercertificate)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}");  
                            }
                        }

                        if(file_exists($_FILES['slcCert']['tmp_name'])){
                            if (move_uploaded_file($slc_temp, $dirPath2 . $slc_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }  
                        }
                        else{
                            if (move_uploaded_file($slc_temp, $dirPath2 . $slc_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }  
                        }
                        
                        if(file_exists($_FILES['charCert']['tmp_name'])){
                            if (move_uploaded_file($char_temp, $dirPath3 . $char_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }      
                        }
                        else{
                            if (move_uploaded_file($char_temp, $dirPath3 . $char_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        if(file_exists($_FILES['casteCert']['tmp_name'])){
                            if (move_uploaded_file($caste_temp, $dirPath4 . $caste_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }   
                        }
                        else{
                            if (move_uploaded_file($caste_temp, $dirPath4 . $caste_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                        if(file_exists($_FILES['baptCert']['tmp_name'])){
                            if (move_uploaded_file($bapt_temp, $dirPath5 . $bapt_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }   
                        }
                        else{
                            if (move_uploaded_file($bapt_temp, $dirPath5 . $bapt_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }  
                        }
                        if(file_exists($_FILES['undertakingCert']['tmp_name'])){
                            if (move_uploaded_file($undertaking_temp, $dirPath6 . $undertaking_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            }    
                        }
                        else{
                            if (move_uploaded_file($undertaking_temp, $dirPath6 . $undertaking_cert)) {
                                $updateDoc=$reqModel->updateDocuments($data);
                                $this->_redirect("application/upload-adm-doc/a_id/{$encryptedId}"); 
                            } 
                        }
                   
               }
               
        }   
        
    }
    public function confirmFileUploadAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $req_model = new Application_Model_AnnounceResultModel();
            $a_id = $this->_getParam("a_id");
            $confirmUpload= $req_model->confirmFileUpload($a_id);
           
                echo 'confirmed';
            
            
        }die;
        
    }
    
    //Payment Gateway Integration for TC And Migration Card: Kedar : 04 - Nov.-2020
    public function transferCertFeesAction() {
       
    //http_response_code(404); die;
        require_once APPLICATION_PATH . '/public/atompay/TransactionRequest.php';
        $studetails = new Application_Model_StudentPortal();
        $payment = new Application_Model_TcFeeCollection();

        $type = $this->_getParam("type");

        $this->view->type = $type;
        $transactionRequest  = new TransactionRequest();
        
            switch ($type) { 
              
                case "view": 
                    
                    $feeType = $_POST['type'];
                    $_SESSION['feeType'] = $_POST['type'];
                    //echo '<pre>'; print_r($_SESSION['feeType']); exit;
                    
                    $checkFormId = $studetails->getStudenInfoByFormId(trim($_POST['stu_id']));
                    $details_stu = $studetails->getStudentacademicDetailsForTc(trim($_POST['stu_id']),$feeType);
                    
                    $this->view->stud_info=$checkFormId;
                    $this->view->fee_type=$feeType;
                    
                    //echo '<pre>'; print_r($checkFormId); exit;
                    
                    
                    if(!empty($details_stu['pay_status'])){
                        $this->_refresh(5,'/academic/application/transfer-cert-fees?v2','You had already paid the fees for selected Certificate. ..');
                    }
                    if(empty($checkFormId['academic_id'])){
                        $this->_refresh(5,'/academic/application/transfer-cert-fees?v2','Wrong Form Id ..');
                    }
                      
                break;
                case "save":
                    
                     $updateData = array (
                        'stu_aadhar' => $_POST['stu_aadhar'],
                        'stu_email_id' => $_POST['stu_email_id'],
                        'stu_doA' => $_POST['stu_doA']
                        
                    );
                    
                    $update_id = $studetails->update($updateData,array('stu_id=?' => trim($_POST['stu_id'])));
                    $details_stu = $studetails->getStudenacademicDetails(trim($_POST['stu_id']));
                    //echo '<pre>'; print_r($details_stu);exit;
                    $feeType = $_POST['type'];
                    //echo '<pre>'; print_r($details_stu); exit;
                    $checkPayment = $studetails->getStudentacademicDetailsForTc(trim($_POST['stu_id']),$feeType);
                    
                    if(!empty($checkPayment['pay_status'])){
                        $this->_refresh(5,'/academic/application/transfer-cert-fees?v2','You had already paid the fees for selected Fee Type. ..');
                    }
                        
                        
                        $insertData= array(
                            'stu_id'=> $details_stu['stu_id'],
                            'name'=> $details_stu['stu_fname'],
                            'email_id'=> $details_stu['stu_email_id'],
                            'tc_number'=> $details_stu['tc_number'],
                            'acad_id'=> $details_stu['academic_id'],
                            'fee_type'=> $_POST['type'],
                            
                        );
                        
                        $last_insert_id= $payment->insert($insertData);
                        
                        if($_POST['type'] == 'migration'){
                        $payable_amount= 200;
                        }else{
                            $payable_amount= 500;
                        }
                        
                        if($_POST['type'] == 'migration'){
                            $product_id = 'PWC_REMITTANCE';
                        }else{
                             $product_id = 'PRINCIPAL_PWC';
                        }
                        
                       
                        $this->_flashMessenger->addMessage('Payment Successfully now You can Download your Recipt Card');
                        date_default_timezone_set('Asia/Calcutta');
                        $datenow = date("d/m/Y h:m:s");
                        $transactionDate = str_replace(" ", "%20", $datenow);

                        $transactionId = rand(1,1000000);

                        $transactionRequest->setMode("live");
                        $transactionRequest->setLogin(53243);
                        $transactionRequest->setPassword("Patna@1234");
                        $transactionRequest->setProductId($product_id);
                        $transactionRequest->setAmount($payable_amount);
                        $transactionRequest->setTransactionCurrency("INR");
                        $transactionRequest->setTransactionAmount($payable_amount);
                        $transactionRequest->setReturnUrl($this->_base_url  . 'application/tcfeeresponse/?id='.$last_insert_id);
                        $transactionRequest->setClientCode(53243);
                        $transactionRequest->setTransactionId($transactionId);
                        $transactionRequest->setTransactionDate($transactionDate);
                        $transactionRequest->setCustomerName($details_stu['stu_fname']);
                        $transactionRequest->setCustomerEmailId($_POST['stu_email_id']);
                        $transactionRequest->setCustomerMobile($_POST['phone']);
                        $transactionRequest->setCustomerBillingAddress($_POST['stu_id']);
                        $transactionRequest->setCustomerId($details_stu['exam_roll']."|".$details_stu['roll_no']);
                        $transactionRequest->setCustomerAccount("639827");
                      $transactionRequest->setReqHashKey("b338c08a991313f12c");
                       //echo "<pre>";print_r($transactionRequest);exit;
                        $url = $transactionRequest->getPGUrl();
                        //echo "<pre>";print_r($url);exit;
                          $this->_redirect($url); 
                break;
                default:


                break;
            }
                
    }
    
    public function tcfeeresponseAction(){
        require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
           
        $payment = new Application_Model_TcFeeCollection();
        $Academic_model = new Application_Model_Academic();
        $studetails = new Application_Model_StudentPortal();
        $account_model = new Application_Model_Account();

        $transactionResponse = new Application_Model_TransactionResponse();

        $transactionResponse = new TransactionResponse();
        $transactionResponse->setRespHashKey("8b013258b7a4b89428");
        $this->view->vaild_response = $transactionResponse->validateResponse($_POST);
            if($_GET['id']=='')
              $this->_redirect ($this->_main_url);
            $id = $_GET['id'];
           
            $updatedata = array(
                'mmp_txn'=>$_POST['mmp_txn'],
                'mer_txn'=>$_POST['mer_txn'],
                'bank_txn'=>$_POST['bank_txn'],
                'bank_name'=>$_POST['bank_name'],
                'prod'=>$_POST['prod'],
                'date'=>$_POST['date'],
                'f_code'=>$_POST['f_code'],
                'clientcode'=>$_POST['clientcode'],
                'merchant_id'=>$_POST['merchant_id'],
                'amount'=>$_POST['amt']
               // 'status'=>1
            );
           
           if($_POST['f_code'] == 'Ok'){
               
            $updatedata['status'] = 1; 
            
           }
           $record= $payment->getRecord($id);
            //echo '<pre>'; print_r($record); exit;
           if(empty($record['f_code'])){
                $payment->update($updatedata, array('id=?' => $id)) ;
           }
            $record= $payment->getRecord($id);
       
            // echo "<pre>"; print_r($record);exit;
            if($record['fee_type'] == 'migration'){
                
                
            }
                              
        if($record['f_code'] == 'Ok'){
            //========[To generate migration number] =========
                $get_mig_no= $payment->getMigNo();
                $mig_no= $get_mig_no['migNumber'] + 1;
                
                $update_mig= array(
                        'mig_no'=>$mig_no
                );
                $update_id = $payment->update($update_mig, array('id=?' => $id));
            //=================End=======================
            $this->view->response_data = $record;
            $record= $payment->getRecord($id);
            $record['acc_number'] = $account_model->getName($record["prod"])['acc_number'];
            $this->view->details = $record;

             //======[mail]==== 
               $to = array(strtolower($record['email_id'])); 
                $from = array('noreply@pwcadmissions.in','Patna Women\'s College');
                $subject = "PWC Semester Fee Reciept 2020";
                $message .= "Dear {$record['name']} , <br/><br/>Please find the link below :-.<br/><br/>";
                $message.= "<a class='btn btn-primary text-center'  href = '{$this->_base_url}application/semfeeresponse-print/?id={$id}&v3'>Download Reciept</a>";

                $mail = new Application_Model_MailSender($to, $from,$subject,$message);
                if($mail->mail($this->_mail['user'],$this->_mail['pass'])){
                    echo "Your login credentials for next step is sent on your email id.";

                }else{
                    echo 'Sent Failed';

                }
                $details_stu = $studetails->getStudenacademicDetails(trim($record['stu_id']));
        }
        else{
            $record= $payment->getRecord($id);
            $this->view->response_data = $record;
        }
             
           
    }
    //Tc Fee Print
    public function tcfeeresponsePrintAction(){
        require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
           
            $payment = new Application_Model_TcFeeCollection();
            $account_model = new Application_Model_Account();
            $studetails = new Application_Model_StudentPortal();
            $transactionResponse = new Application_Model_TransactionResponse();
          
            $transactionResponse = new TransactionResponse();
        
            $id = $this->_getParam("id");
            
            if($_POST['ID'] == 302278){
               $this->view->result = $_POST;
               
                $mandatory_fields = explode('|',$_POST["mandatory_fields"]);
                $optional_fields = explode('|',$_POST['optional_fields']);
            }
      
           
      $record= $payment->getRecord($id);
           
      $this->view->response_data = $record;
            
        if($record['f_code'] == 'Ok' || $record['f_code']=="E000"){
            $this->view->response_data = $record;

            $record['acc_number'] = $account_model->getName($record["prod"])['acc_number'];
            $this->view->details = $record;
            // echo "<prE>";print_r($record);exit;
            $details_stu = $studetails->getStudenacademicDetails(trim($record['stu_id']));

        }
             
           
    }
    //End : Kedar Date: 28 Jan 2021
    public function ajaxGetActiveBatchAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $type = $this->_getParam("type");
            //print_r($session_id); die;
            $req_model = new Application_Model_ExamDateModel();
            $result = $req_model->getRecordByType($type);

            //echo '<pre>'; print_r($result);
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['academic_year_id'] . '" >' . $val['short_code'] . '</option>';
            }
        }die;
        
    }
    public function ajaxGetActiveTermAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            $type = $this->_getParam("exam_type");
          
            //print_r($type); die;
            $req_model = new Application_Model_ExamDateModel();
            $result = $req_model->getRecordByBatch($batch_id,$type);

           echo '<pre>'; print_r($result);
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['cmn_terms'] . '" >' . $val['term_name'] . '</option>';
            }
        }die;
        
    }
    public function ajaxGetBatchInfoAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $uid = $this->_getParam("formId");
            
            
            $student_model = new Application_Model_StudentPortal();
            $student_details = $student_model->getStudenInfoByStuIdForSemFee($uid);
            //echo '<pre>';print_r($student_details);die;
            if(!$student_details ){
               echo "<b style=color:red>Error Code 403 : Invalid Form Id:". $uid."</b>"; 
                //$this->_redirect('error/error_404');
            }

            echo "<b style=color:red>".$student_details['short_code']."</b>";
            
        }die;
        
    }
    
    
    public function lateFineDasboardAction(){
        $type = $this->_getParam("type");
        $email = $this->_getParam("email");
        $phone = $this->_getParam("phone");
        require_once APPLICATION_PATH . '/public/atompay/TransactionRequest.php';
        $transactionRequest  = new TransactionRequest();
       $latefine = new Application_Model_Latefine();
       $academic = new Application_Model_Academic();
        $this->view->mainurl=  $this->_base_url;
        if($type==="save"){
            if(!empty($email) && !empty($phone)){
                        date_default_timezone_set('Asia/Calcutta');
                        $datenow = date("d/m/Y h:m:s");
                        $_POST = $_SESSION['stu_details'];
                       // print_r($_SESSION['stu_details']);die();
                        $_POST['email'] = $email;
                        $_POST['phone']  = $phone;
                        $product_id = $_POST['product_id'];
                      
                        $academic_details = $academic->getRecord($_POST['academic_id']);
                         $insertdata = array (
                        'batch' => $academic_details['batch_code'],    
                        'phone' => $_POST['phone'],
                        'academic_id'=>$_POST['academic_id'],
                        'email' => $_POST['email'],
                        'term' => $_POST['cmn_terms'],
                        'amount' => $_POST['finefee'], 
                        'stu_id' => $_POST['stu_id'],  
                        'submit_date' => date('Y-m-d'));
                        
                   
                       $last_insert_id = $latefine->insert($insertdata);
                        $transactionDate = str_replace(" ", "%20", $datenow);
                        $transactionId = rand(1,1000000);
                        //=====[Ashu change]=====//
                        $_POST['fee'] = $_POST['finefee'];
                        $transactionRequest->setMode("live");
                        $transactionRequest->setLogin(53243);
                        $transactionRequest->setPassword("Patna@1234");
                        $transactionRequest->setProductId($product_id);
                        $transactionRequest->setAmount($_POST['fee']);
                        $transactionRequest->setTransactionCurrency("INR");
                        $transactionRequest->setTransactionAmount($_POST['finefee']);
                        $transactionRequest->setReturnUrl($this->_base_url  . 'application/latefineresponse/?id='.$last_insert_id);
                        $transactionRequest->setClientCode(53243);
                        $transactionRequest->setTransactionId($transactionId);
                        $transactionRequest->setTransactionDate($transactionDate);
                        $transactionRequest->setCustomerName($_POST['stu_name']);
                        $transactionRequest->setCustomerEmailId($_POST['email']);
                        $transactionRequest->setCustomerMobile($_POST['phone']);
                        $transactionRequest->setCustomerBillingAddress($_POST['stu_id']."|Late Fine");
                        $transactionRequest->setCustomerId($_POST['examination_id']."|".$_POST['roll_no']);
                        $transactionRequest->setCustomerAccount("639827");
                      $transactionRequest->setReqHashKey("b338c08a991313f12c");
                 //     echo "<pre>"; print_r($transactionRequest);exit;
                        $url = $transactionRequest->getPGUrl();
                          $this->_redirect($url);
            }
        }
              


    }

 }
    
