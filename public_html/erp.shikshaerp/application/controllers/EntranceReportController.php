<?php
//ini_set('display_errors', '1');
/* 
    Author: Kedar Kumar
    Summary: This controller is used to handle The Entrance Report
    Date: 21 May 2019
*/

class EntranceReportController extends Zend_Controller_Action {

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
    private $accessConfig =NULL;
    private $aeccConfig =NULL;
    

    public function init() {
        $zendConfig = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $this->_action = $this->getRequest()->getActionName();
        //access role id
        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->aeccConfig = $config_role = $zendConfig->aecc_course->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $this->login_storage = $data = $storage->admin_login;
        $this->view->login_storage = $data;
        //print_r($data);exit;
        if (isset($data)) {
            $this->view->role_id = $data->role_id;
            $this->view->login_empl_id = $data->empl_id;
        }
        if ($this->_action == "login" || $this->_action == "forgot-password") {
            $this->_helper->layout->setLayout("adminlogin");
        } else {
            $this->_helper->layout->setLayout("layout");
        }
        $this->_act = new Application_Model_Adminactions();
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
          if($data->role_id==0)
        $this->_redirect("student-portal/student-dashboard");
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
        $this->view->action_name = 'Admission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_ENTRANCEREPORT');
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $this->view->form = $multi_step_entrance_form;
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $this->view->type = $type;
        switch ($type) {
           
            case "edit":
                $academic_model = new Application_Model_Department();
                $department_model = new Application_Model_DepartmentType();
                $applicantCourseData = new Application_Model_ApplicantCourseDetailModel();
                //$Aeccge_course = new Application_Model_Aeccge();
                $session_id = $this->_getParam("s_id");
               
                $result = $academic_model->getCoreCourseByCourseId($edit_id,$session_id);
                $departmentName=$department_model->getIndividualDepartmentType($edit_id,$session_id);
                
                    foreach ($result  as $key => $value){ 
                        if($session_id > 7){
                            
                            $pgCourseCount=$applicantCourseData->getRecordByindividualPgCourse($edit_id);
                            //echo '<pre>';print_r($pgCourseCount);exit;
                            $result[$key]['applied']=$pgCourseCount['total'];
                        }else{
                           
                            if($edit_id == 10){
                                //echo 'kk';exit;
                                $pgCourseCount=$applicantCourseData->getRecordByindividualPgCourse($edit_id);
                                $result[$key]['applied'] = $pgCourseCount['total'];
                            }else{
                                $coreCourseCount=$applicantCourseData->getRecordByindividualCourse($value['academic_year_id']); 
                                //echo '<pre>';print_r($coreCourseCount);exit;
                                $result[$key]['caste'] = array(
                                    'General'=>$coreCourseCount['General'],
                                    'BC-1(EBC)'=>$coreCourseCount['BC-1(EBC)'],
                                    'BC-2(OBC)'=>$coreCourseCount['BC-2(OBC)'],
                                    'EWS'=>$coreCourseCount['EWS'],
                                    'SC'=>$coreCourseCount['SC'],
                                    'ST'=>$coreCourseCount['ST'],
                                ); 
                                
                                
                                $result[$key]['applied'] = $coreCourseCount['total'];
                                $result[$key]['max_seat'] = $coreCourseCount['max_seat'];
                                $result[$key]['Hindi'] = $coreCourseCount['Hindi'];
                                $result[$key]['English'] = $coreCourseCount['English'];
                            }
                            
                        }//exit;
                    }
                
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                
                $this->view->department = $departmentName;
                
                break;
       
                default:
                $applicantCourseData = new Application_Model_SanctionSeatModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId(); 
                $ugCourseCountData=$applicantCourseData->getAllFinalUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllFinalPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $ugCourseCountData
                    );
                    $pg_data = array(
                        'result' => $pgCourseCountData
                    );
                    //echo"<pre>";print_r($paginator_data);exit;
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                    $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
    public function ajaxGetEntranceReportByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
            $applicantCourseData = new Application_Model_SanctionSeatModel();
            $ugCourseCountData=$applicantCourseData->getAllFinalUgCourseCount($yearId);
            $pgCourseCountData=$applicantCourseData->getAllFinalPgCourseCount($yearId);
            //$this->view->result = $courseCountData;
            $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $ugCourseCountData
                );
                $pg_data = array(
                    'result' => $pgCourseCountData
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                $this->view->pgData = $this->_act->pagination($pg_data);
            
        }
    }
    //For Document verification Interface
    public function scrutinyAction(){
        
        $this->view->action_name = 'Admission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_SCRUTINY');
        
        $academic_year_form= new Application_Form_AcademicYear();
        
        $this->view->form=$academic_year_form;
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $this->view->type = $type;
         switch ($type) {
           
            case "edit":
                
                
                break;
                
                case "getStudents":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getRecordByCouse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                
                default:
                $applicantCourseData = new Application_Model_ApplicantPaymentDetailModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId();     
                $ugCourseCountData=$applicantCourseData->getAllUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
    public function ajaxGetScrutinyApplicantsByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
                $applicantCourseData = new Application_Model_ApplicantPaymentDetailModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $ugCourseCountData=$applicantCourseData->getAllUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllPgCourseCount($yearId);
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
        }
    }
    //End
    
    //For principal interface
    public function verifiedStudentAction(){
        $this->view->action_name = 'Admission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_PRINCIPAL');
        
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $this->view->form = $multi_step_entrance_form;
        $sanction_seat_form = new Application_Form_SanctionedSeatMaster();
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("dept_id");
        $this->view->type = $type;
        $this->view->form=$sanction_seat_form;
        $this->view->deptId= $edit_id;
        $this->view->type = $type;
         switch ($type) {
           
                
                case "getDocumentVerifiedStudents":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getdocumentVerifiedRecordByCouse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                
                default:
                $applicantCourseData = new Application_Model_SanctionSeatModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId(); 
                $ugCourseCountData=$applicantCourseData->getAllScrutinizedUgCourseCount($yearId);
                  //echo"<pre>";print_r($ugCourseCountData);exit;
                $pgCourseCountData=$applicantCourseData->getAllScrutinizedPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
    public function ajaxGetVerifiedApplicantsByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
                $applicantCourseData = new Application_Model_ApplicantPaymentDetailModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $ugCourseCountData=$applicantCourseData->getAllUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllPgCourseCount($yearId);
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
        }
    }
    //For Admission card
    public function admissionCardAction(){
       
        $app_id = $this->_getParam("a_id");
        $studentDetails= new Application_Model_SanctionSeatModel();
        $applicantDetails=$studentDetails->getStudentDetails($app_id);
        
        $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $applicantDetails
                );
               
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $applicantDetails;
         
    }
    //For Download I-Card
    public function icardprintAction(){
        $this->_helper->layout->disableLayout();
         $this->_helper->layout->setLayout("applicationlayout");
        $app_id = $this->_getParam("a_id");
        $studentDetails= new Application_Model_SanctionSeatModel();
        $applicantDetails=$studentDetails->getStudentDetails($app_id);
        
        $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $applicantDetails
                );
               
                //echo"<pre>";print_r($applicantDetails);exit;
                $this->view->paginator = $applicantDetails;
                 $htmlcontent = $this->view->render('entrance-report/icardprint.phtml');
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generateadmitcardPdf($pdfheader, $pdffooter, $htmlcontent, $applicantDetails['applicant_name'] .$applicantDetails['form_id'],'P',150 );
                  
                      
                   
         
    }
    public function appformprintAction(){
        $this->_helper->layout->disableLayout();
         $this->_helper->layout->setLayout("applicationlayout");
        $application_no = md5($this->_getParam("a_id"));
        
        $allFormData = new Application_Model_ApplicantCourseDetailModel();
        $paymentData = new Application_Model_ApplicantPaymentDetailModel();
        $formFilledData = $allFormData->getAllFormFilledData($application_no);
        //$paymentData = $paymentData->getsavedData($application_no);
        //echo '<pre>';print_r($formFilledData);exit;
            $this->view->paginator = $formFilledData;
            //$this->view->payment_detail = $paymentData;
            
        
    }
    //End
   
    
    public function generateSlipAction(){
        // $this->_helper->layout->disableLayout();
        //  if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $stu_id = $this->_getParam("form_id");
          //  echo $stu_id; die;
            $term_id = $this->_getParam("term_id");
            $prod = $this->_getParam("prod_id");
            $payment = new Application_Model_FeesCollection();
             $Academic_model = new Application_Model_Academic();
            $FeeCategory_model = new Application_Model_FeeCategory();
            $dept_type_details = new Application_Model_DepartmentType();
            $FeeHeads_model = new Application_Model_FeeHeads();
          //  $studetails = new Application_Model_StudentPortal();
          $dept_id_only = 0;
          
          $stu_details = new Application_Model_ApplicantCourseDetailModel();
            $FeeStructure_model = new Application_Model_FeeStructure();
            $dept = new Application_Model_Department();

            $StructureItems_model = new Application_Model_FeeStructureItems();

            $term_model = new Application_Model_TermMaster();

            $TermItems_model = new Application_Model_FeeStructureTermItems();
            
        
         // $details_stu = $studetails->getStudenacademicDetails($stu_id);
         $session_det = 0;
         $details_stu = $stu_details->getApplicationNumber($stu_id);
         if($details_stu['core_course1'])
         $details_stu['academic_id'] = $details_stu['core_course1'];
         else{
             $dept_type = $details_stu['course'];
             $dept_details = $dept_type_details->getRecord($dept_type);
            
             $dept_id_only = $dept->getByDepartmentType($dept_type)['did'];
             
           
            $details_stu['academic_id'] = $Academic_model->getAcademicsBySD($dept_details['session_id'],$dept_id_only)['academic_year_id'];
               // echo "<pre>"; print_R(  $details_stu['academic_id']);exit;
         }
         
         $details_stu['semester'] = 't1';
         
         $acad_details = $Academic_model->getRecord($details_stu['academic_id']);
        //  echo "<pre>" ;print_r($acad_details);die;
         $deptname = $dept->getRecordbyAcademic($details_stu['academic_id']);
            //  echo "<pre>" ;print_r($details_stu)       ;die;
         $struct_id = $FeeStructure_model->getStructId($details_stu['academic_id']);
         if(!$struct_id){
                echo "<div style='text-align:center;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);'>
     <img src='/academic/public//images/loader.gif' width='100px' class='loder_img1'> <br/>
     <b><span style='color:red;'>{$deptname['dpt_name']}</span> Fee is yet to be prepared..</b><br/><span style='color:green;'>This dialogue box will close in  5 sec...</span><div>";
     
        echo "<script type=\"text/javascript\" charset=\"utf-8\">setTimeout(function(){ window.self.close(); }, 7000);</script>";die;
     }
     
    $structure_id =  $struct_id;  
   // echo $structure_id; die;
        if($structure_id){
                
            $result = $TermItems_model->getItemRecordsByTerm($structure_id,$term_id);
                
         //    echo "<prE>";print_r($result);exit;
              
            $this->view->result = $result;
            $this->view->department = $deptname['dpt_name'];
            $result1 = $StructureItems_model->getStructureRecords($structure_id);
            $dept_id_only = $dept->getRecord($acad_details['department'])['department_type'];
            $this->view->result1 = $result1;
            $academic_id  = $TermItems_model->getAcademicId($structure_id);
            $terms_data = $term_model->getRecordByAcademicId($academic_id['academic_id']);
            $degree_id = $Academic_model->getAcademicDegree($academic_id['academic_id']);
            $this->view->term_data = $terms_data; 
            $this->view->structure_id = $structure_id;
           $this->view->details = $details_stu;
            $Category_data = $FeeCategory_model->getFeeCategory($degree_id,$prod,$acad_details['session'],$dept_id_only);
            $this->view->Category_data = $Category_data;
            $Feeheads_data = $FeeHeads_model->getFeeheads($degree_id,$acad_details['session'],$dept_id_only);
             $this->view->heads_data = $Feeheads_data;
             $this->view->prod = $prod;
             $this->view->session = $acad_details['session'];
             $htmlcontent = $this->view->render('entrance-report/generate-slip.phtml');
                echo $htmlcontent;
                exit;
            
           }
        //  }
    }
    
    //For Account interface
    public function approvedStudentsAction(){
        
        $this->view->action_name = 'Admission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_ACCOUNT');
        
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $this->view->form = $multi_step_entrance_form;
        $sanction_seat_form = new Application_Form_SanctionedSeatMaster();
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $this->view->type = $type;
        $this->view->form=$sanction_seat_form;
         switch ($type) {
           
            case "edit":
                
                
                break;
                
                case "getPrincipalApprovedStudents":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getapprovedRecordByCourse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                
                default:
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId();  
                $applicantCourseData = new Application_Model_SanctionSeatModel();
                $ugCourseCountData=$applicantCourseData->getAllApprovedUgCourseCount($yearId);
                //echo"<pre>";print_r($ugCourseCountData);exit;
                $pgCourseCountData=$applicantCourseData->getAllApprovedPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
    public function ajaxGetApprovedApplicantsByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
                $applicantCourseData = new Application_Model_SanctionSeatModel();
                $ugCourseCountData=$applicantCourseData->getAllApprovedUgCourseCount($yearId);
                //echo"<pre>";print_r($ugCourseCountData);exit;
                $pgCourseCountData=$applicantCourseData->getAllApprovedPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
        }
    }
    //End
    //For Payment Collection interface(Fee-Slip)
    public function paySlipGeneratedStudentsAction(){
        
        $this->view->action_name = 'Admission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_PAYMENT');
        
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $this->view->form = $multi_step_entrance_form;
        $sanction_seat_form = new Application_Form_SanctionedSeatMaster();
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $this->view->type = $type;
        $this->view->form=$sanction_seat_form;
         switch ($type) {
           
            case "edit":
                
                
                break;
                
                case "getStudents":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getSlipGeneratedRecordByCourse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                
                default:
                $applicantCourseData = new Application_Model_SanctionSeatModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId(); 
                $ugCourseCountData=$applicantCourseData->getSlipGeneratedUgCourseCount($yearId);
                //echo"<pre>";print_r($ugCourseCountData);exit;
                $pgCourseCountData=$applicantCourseData->getSlipGeneratedPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
    public function ajaxGetSlipGeneratedApplicantsByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
            $applicantCourseData = new Application_Model_SanctionSeatModel();
                $ugCourseCountData=$applicantCourseData->getSlipGeneratedUgCourseCount($yearId);
                //echo"<pre>";print_r($ugCourseCountData);exit;
                $pgCourseCountData=$applicantCourseData->getSlipGeneratedPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $ugCourseCountData
                    );
                    $pg_data = array(
                        'result' => $pgCourseCountData
                    );
                    //echo"<pre>";print_r($paginator_data);exit;
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                    $this->view->pgData = $this->_act->pagination($pg_data);
        }
    }
    //End
    //For paid applicant details
    public function applicantDetailsAction(){
        
        $this->view->action_name = 'Admission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_APPLICANTDETAILS');
        
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $this->view->form = $multi_step_entrance_form;
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $this->view->type = $type;
         switch ($type) {
           
            case "edit":
                
                
                break;
                
                case "getStudents":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getPaidRecordByCourse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    //echo '<pre>';print_r($result);die;
                    $this->view->degree_id=$result[0]['degree_id'];
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                
                default:
                $applicantCourseData = new Application_Model_SanctionSeatModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId();
                $ugCourseCountData=$applicantCourseData->getAllFinalUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllFinalPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
    public function ajaxGetApplicantsDetailByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
            
            $applicantCourseData = new Application_Model_SanctionSeatModel();  
            $ugCourseCountData=$applicantCourseData->getAllFinalUgCourseCount($yearId);
            $pgCourseCountData=$applicantCourseData->getAllFinalPgCourseCount($yearId);
            //$this->view->result = $courseCountData;
            $page = $this->_getParam('page', 1);
            $paginator_data = array(
                'page' => $page,
                'result' => $ugCourseCountData
            );
            $pg_data = array(
                'result' => $pgCourseCountData
            );
            //echo"<pre>";print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
            $this->view->pgData = $this->_act->pagination($pg_data);
            
        }
        
    }
    //End
    //For applied applicant details
    public function appliedApplicantDetailsAction(){
        $this->view->action_name = 'Admission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_APPLIEDAPPLICANTS');
        
        $academic_year_form= new Application_Form_AcademicYear();
        
        $this->view->form=$academic_year_form;
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $this->view->type = $type;
         switch ($type) {
           
                
                case "getStudents":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getRecordByCouse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    //echo '<pre>';print_r($result);die;
                    $this->view->degree_id=$result[0]['degree_id'];
                    $this->view->course_id=$result[0]['course'];
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                    
                break;

                default:
                $applicantCourseData = new Application_Model_ApplicantPaymentDetailModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId();    
                $ugCourseCountData=$applicantCourseData->getAllUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
     public function ajaxGetAppliedApplicantsByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
            
            $applicantCourseData = new Application_Model_ApplicantPaymentDetailModel();    
            $ugCourseCountData=$applicantCourseData->getAllUgCourseCount($yearId);
            $pgCourseCountData=$applicantCourseData->getAllPgCourseCount($yearId);
            //$this->view->result = $courseCountData;
            $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $ugCourseCountData
                    );
                    $pg_data = array(
                        'result' => $pgCourseCountData
                    );
                    //echo"<pre>";print_r($paginator_data);exit;
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                    $this->view->pgData = $this->_act->pagination($pg_data);
        }
        
    }
    //End 8 Oct 2020
    //Declaration of Result data
    public function declareResultAction(){
        $this->view->action_name = 'Addmission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_DECLARERESULT');
        $academic_year_form= new Application_Form_AcademicYear();
        $acadYearData= new Application_Model_AcademicYear();
        
        $this->view->form=$academic_year_form;
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $dept_id = $this->_getParam("dept_id");
        $this->view->dept_id=$dept_id;
        $this->view->type = $type;
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        switch ($type) {
           
               
            case "announceResult":
                
                $resultModel= new Application_Model_AnnounceResultModel();
                
                if ($this->getRequest()->getPost()) {
                if(!empty($_POST['csrftoken'])) {
                    if($_POST['csrftoken']===$token ){
                $studentList= explode(",",trim(preg_replace('/\s\s+/', ' ', $_POST['student_lists'])));
                //echo '<pre>'; print_r($studentList);exit;
                if(!empty($_SERVER['HTTP_CLIENT_IP'])){
                $ip = $_SERVER['HTTP_CLIENT_IP'];
                }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }else{
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                $insertMasterResult= array(
                    'acad_year_id' =>$_POST['academic_year_list'],
                    'cutoff_list'=>$_POST['cutoff_list'],
                    'submit_date'=>date('Y-m-d'),
                    'ip_address'=>$ip,
                    'published_by'=>$this->login_storage->empl_id,
                    'department_type'=>$dept_id

                );
               //echo '<pre>'; print_r($studentList);exit;
                $last_insert_id=$resultModel->insert($insertMasterResult);

                        if($last_insert_id){

                            foreach ($studentList as $key => $stu_id) {

                                $decalredStudentData= array(
                                'master_id' =>  $last_insert_id, 
                                'stu_id' => $studentList[$key] 

                                );
                               // echo '<pre>'; print_r($decalredStudentData); exit;
                                $insert_id=$resultModel->insertResultItem($decalredStudentData);

                            }


                        }
                       // echo '<pre>'; print_r($insert_id);exit;
                        $checkInsertedDAta=$resultModel->checkInsertData($last_insert_id);
                        //echo '<pre>'; print_r(count($checkInsertedDAta));exit;
                    if(count($checkInsertedDAta)>=1){
                        $_SESSION['message_class'] = 'alert-success';
                        $this->_flashMessenger->addMessage('Result decalred ready for review.');
                        $this->_redirect('entrance-report/decalared-list');
                    }else{
                        $deleteMasterData=$dailyAttendanceMaster->dumpMasterData($last_insert_id);
                         $_SESSION['message_class'] = 'alert-danger';
                        $this->_flashMessenger->addMessage('Result not Saved! Please try again.');
                        $this->_redirect('entrance-report/applied-applicant-details');
                    }
                }else{
                    $this->_refresh(3,"/academic/entrance-report/declare-result/type/announceResult/dept_id/{$dept_id}",'Invalid Token ..');
                }
            }
        }
            break;
            default:
            $applicantCourseData = new Application_Model_ApplicantPaymentDetailModel();
            $academicYearModel= new Application_Model_AcademicYear();
            $yearId=$academicYearModel->getAcadYearId();
            $ugCourseCountData=$applicantCourseData->getAllUgCourseCount($yearId);
            $pgCourseCountData=$applicantCourseData->getAllPgCourseCount($yearId);
            //$this->view->result = $courseCountData;
            $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $ugCourseCountData
                    );
                    $pg_data = array(
                        'result' => $pgCourseCountData
                    );
                    //echo"<pre>";print_r($paginator_data);exit;
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                    $this->view->pgData = $this->_act->pagination($pg_data);

         break;
        }
    }
    //Added By Kedar: 07 Oct 2020
    public function ajaxGetDeclareRecordByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
            
            $applicantCourseData = new Application_Model_ApplicantPaymentDetailModel();
            $ugCourseCountData=$applicantCourseData->getAllUgCourseCount($yearId);
            $pgCourseCountData=$applicantCourseData->getAllPgCourseCount($yearId);
            //$this->view->result = $courseCountData;
            $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $ugCourseCountData
                    );
                    $pg_data = array(
                        'result' => $pgCourseCountData
                    );
                    //echo"<pre>";print_r($paginator_data);exit;
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                    $this->view->pgData = $this->_act->pagination($pg_data);
        }
        
    }
    
    public function decalaredListAction(){
        $resultModel= new Application_Model_AnnounceResultModel();
        $academicYearModel= new Application_Model_AcademicYear();
        $academic_year_form= new Application_Form_AcademicYear();
        
        $this->view->form=$academic_year_form;
        $yearId=$academicYearModel->getAcadYearId();
        $DeclaredList=$resultModel->getAllDeclaredList($yearId);
        $this->view->result = $DeclaredList;
        $page = $this->_getParam('page', 1);
        $paginator_data = array(
            'page' => $page,
            'result' => $DeclaredList
        );

        //echo"<pre>";print_r($paginator_data);exit;
        $this->view->paginator = $this->_act->pagination($paginator_data);
    }
    public function dailyAdmCountAction(){
        $resultModel= new Application_Model_AnnounceResultModel();
        $academicYearModel= new Application_Model_AcademicYear();
        $academic_year_form= new Application_Form_AcademicYear();
        
        $this->view->form=$academic_year_form;
        $yearId=$academicYearModel->getAcadYearId();
        $DeclaredList=$resultModel->getAllDeclaredList($yearId);
        $this->view->result = $DeclaredList;
        $page = $this->_getParam('page', 1);
        $paginator_data = array(
            'page' => $page,
            'result' => $DeclaredList
        );

        //echo"<pre>";print_r($paginator_data);exit;
        $this->view->paginator = $this->_act->pagination($paginator_data);
    }
    public function ajaxGetRecordByDateAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $resultModel= new Application_Model_SanctionSeatModel();
            $date = $this->_getParam("date");
            
            $recordList=$resultModel->getDailyAdmCount($date);
            $this->view->effective_date = $date;
            $page = $this->_getParam('page', 1);
            $paginator_data = array(
                'page' => $page,
                'effective_date'=>$date,
                'result' => $recordList
            );

           //echo"<pre>";print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
        }
        
    }
    public function ajaxGetDeclareListByYearIdAction(){
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $resultModel= new Application_Model_AnnounceResultModel();
            $academicYearModel= new Application_Model_AcademicYear();
            $academic_year_form= new Application_Form_AcademicYear();
            $yearId = $this->_getParam("year_id");
            $this->view->form=$academic_year_form;
            $DeclaredList=$resultModel->getAllDeclaredList($yearId);
            $this->view->result = $DeclaredList;
            $page = $this->_getParam('page', 1);
            $paginator_data = array(
                'page' => $page,
                'result' => $DeclaredList
            );

            //echo"<pre>";print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
        }
        
    }
    //End 07 Oct
    public function editDeclaredResultAction(){
      $edit_id = $this->_getParam("e_id");  
      $resultModel= new Application_Model_AnnounceResultModel();
      $DeclaredListItem=$resultModel->getAllDeclaredListItem($edit_id);
      $this->view->result = $DeclaredListItem;
        $page = $this->_getParam('page', 1);
        $paginator_data = array(
            'page' => $page,
            'result' => $DeclaredListItem
        );

        //echo"<pre>";print_r($paginator_data);exit;
        $this->view->paginator = $this->_act->pagination($paginator_data); 
    }
    public function ajaxCheckCutofflistEntryAction(){
        $dept_id = $this->_getParam("dept_id");  
        $year_id = $this->_getParam("year_id");  
        $cutoff_list = $this->_getParam("cutoff_list");  
        $resultModel= new Application_Model_AnnounceResultModel();
        $DeclaredListItem=$resultModel->checkCutofflistEntry($dept_id,$year_id,$cutoff_list);
        if(!empty($DeclaredListItem)){
            echo 'exists';
        }else{
            echo 'not exists';
        }die;
    }
    public function ajaxGetDeleteMasterIdAction(){
        $delete_id = $this->_getParam("delete_id");  
        $resultModel= new Application_Model_AnnounceResultModel();
        $DeclaredListItem=$resultModel->getDeleteMasterId($delete_id);
        if(!empty($DeclaredListItem)){
            echo $DeclaredListItem['id'];
        }die; 
    }
    public function ajaxGetDeleteIdAction(){
        $delete_id = $this->_getParam("delete_id");  
        $resultModel= new Application_Model_AnnounceResultModel();
        $DeclaredListItem=$resultModel->getDeleteId($delete_id);
        if(!empty($DeclaredListItem)){
            echo $DeclaredListItem['id'];
        }die; 
    }
    public function ajaxDeleteAnnouncedResultAction(){
        $delete_id = $this->_getParam("delete_id");  
        $resultModel= new Application_Model_AnnounceResultModel();
        $DeletedItem=$resultModel->deleteAnnouncedResults($delete_id);
        if(!empty($DeletedItem)){
            echo 'deleted';
        }die; 
    }
    public function ajaxDeleteAnnounceItemResultAction(){
        $delete_id = $this->_getParam("delete_id");  
        $resultModel= new Application_Model_AnnounceResultModel();
        $DeletedItem=$resultModel->deleteAnnounceItemResults($delete_id);
        if(!empty($DeletedItem)){
            echo 'deleted';
        }die; 
    }
    //Applicant Documents Details
    public function admDocAction(){
        $this->view->action_name = 'batchAttendance';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_ADMDOC');
        
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $this->view->form = $multi_step_entrance_form;
    }
    public function ajaxGetApplicantDocumentAction(){
        $this->_helper->layout->disableLayout();
        $degree_id = $this->_getParam("degree_id"); 
        $dept_id = $this->_getParam("dept_id"); 
        $year_id = $this->_getParam("yearId"); 
        $resultModel= new Application_Model_AnnounceResultModel();
        $DeclaredList=$resultModel->getAllApplicantDocumentList($degree_id,$dept_id,$year_id);
        $this->view->result = $DeclaredList;
        $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $DeclaredList
                );
                
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->degree_id=$degree_id;
                $this->view->paginator = $this->_act->pagination($paginator_data);   
    }
    //For I-card applicant details
    public function icardDetailsAction(){
        $this->view->action_name = 'Addmission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_ICARD');
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $this->view->form = $multi_step_entrance_form;
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $this->view->type = $type;
         switch ($type) {
           
            case "edit":
                
                
                break;
                
                case "getStudents":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getPaidRecordByCourse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                case "download-icard":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getPaidRecordByCourse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                
                default:
                $applicantCourseData = new Application_Model_SanctionSeatModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId();
                $ugCourseCountData=$applicantCourseData->getAllFinalUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllFinalPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
    public function applicationFormAction(){
        $this->view->action_name = 'Addmission Report';
        $this->view->sub_title_name = 'AdmissionReport';
        $this->accessConfig->setAccess('SA_ACAD_AR_APPFORM');
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $this->view->form = $multi_step_entrance_form;
        $type = $this->_getParam("type");
        $edit_id = $this->_getParam("id");
        $this->view->type = $type;
         switch ($type) {
           
            case "edit":
                
                
                break;
                
               
                case "download-icard":
                    $paymentModel = new Application_Model_ApplicantPaymentDetailModel();
                    $dept_id = $this->_getParam("dept_id");
                    $result = $paymentModel->getPaidRecordByCourse($dept_id);
                    //echo "<pre>";print_r($result);die;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                
                default:
                $applicantCourseData = new Application_Model_SanctionSeatModel();
                $academicYearModel= new Application_Model_AcademicYear();
                $yearId=$academicYearModel->getAcadYearId();
                $ugCourseCountData=$applicantCourseData->getAllFinalUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllFinalPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                        $paginator_data = array(
                            'page' => $page,
                            'result' => $ugCourseCountData
                        );
                        $pg_data = array(
                            'result' => $pgCourseCountData
                        );
                        //echo"<pre>";print_r($paginator_data);exit;
                        $this->view->paginator = $this->_act->pagination($paginator_data);
                        $this->view->pgData = $this->_act->pagination($pg_data);
            
             break;
         }
    }
    public function ajaxGetIcardApplicantDetailByYearIdAction(){
        
        $this->_helper->layout->disableLayout();
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $yearId = $this->_getParam("year_id");
            $applicantCourseData = new Application_Model_SanctionSeatModel();
                $ugCourseCountData=$applicantCourseData->getAllFinalUgCourseCount($yearId);
                $pgCourseCountData=$applicantCourseData->getAllFinalPgCourseCount($yearId);
                //$this->view->result = $courseCountData;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $ugCourseCountData
                );
                $pg_data = array(
                    'result' => $pgCourseCountData
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                $this->view->pgData = $this->_act->pagination($pg_data);
            
        }
    }
    //End
    //Seating capacity controller
    public function sanctionSeatAction(){
        $this->view->action_name = 'seatingcapacity';
        $this->view->sub_title_name = 'sanctionedSeat';
        $this->accessConfig->setAccess('SS_ACAD_SANSEAT');
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $sanction_seat_form = new Application_Form_SanctionedSeatMaster();
      
        $type = $this->_getParam("type");
        $update_id=$this->_getParam("id");
        $this->view->type = $type;
        $this->view->form = $sanction_seat_form;
 if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        switch ($type) {
            case "add":
                $messages = $this->_flashMessenger->getMessages();
                
               
                if ($this->getRequest()->isPost()) {
                    //if ($sanction_seat_form->isValid($this->getRequest()->getPost())) {
                    
                        $data=$_POST;
                       echo '<pre>';print_r($data);exit;
                        $insertData = array(

                            'degree_id' =>$data['degree_id'],
                            'course' =>$data['course'],
                            'session' => $data['session'],
                            'core_course' => $data['core_course'],
                            'generic_elective' => $data['generic_elective'],
                            'max_seat'=>$data['max_seat']
               
                        );
                         if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){  
                        $sanction_seat_model->insert($insertData);
                          unset($_SESSION["token"]);
                           $_SESSION['message_class'] = 'alert-success';
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('entrance-report/sanction-seat'); 
                         }else {
                     $message="Invalid Token";
		   $_SESSION['message_class'] = 'alert-danger';
                     $this->_flashMessenger->addMessage($message);
                    $this->_redirect('entrance-report/sanction-seat');
                }
                    }
                                
                    //}
                }
                break;
            case 'edit':
               
                $result = $sanction_seat_model->getRecordById($update_id);
                 //echo '<pre>'; print_r($result);
                $this->view->id = $update_id;
                $sanction_seat_form->populate($result);
               
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    
                        $data = $_POST;
                        
                        //echo '<pre>'; print_r($data); exit;
                         $updateData = array(

                            'degree_id' =>$data['degree_id'],
                            'course' =>$data['course'],
                            'session' => $data['session'],
                            'core_course' => $data['core_course'],
                            'generic_elective' => $data['generic_elective'],
                            'max_seat'=>$data['max_seat']
               
                        );
                          if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){  
                              $_SESSION['message_class'] = 'alert-success';
                        $sanction_seat_model->update($updateData, array('id =?' => $update_id ));
                          unset($_SESSION["token"]);
                     
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('entrance-report/sanction-seat/');
                           }else {
                     $message="Invalid Token";
		   $_SESSION['message_class'] = 'alert-danger';
                     $this->_flashMessenger->addMessage($message);
                    $this->_redirect('entrance-report/sanction-seat');
                }
                    }
                   
                }
                break;
        
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $sanction_seat_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                //echo '<pre>';print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
    
    
    //Ajax Area
    //To get ge Course
    public function ajaxGetGeAction(){
        $Aeccge_course = new Application_Model_Aeccge();
        $ge_course= new Application_Model_Department();
        $sanctionSeats= new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        $applicantCourseData = new Application_Model_ApplicantCourseDetailModel();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $department_id = $this->_getParam("department_id");
            $course_id = $this->_getParam("course");
            //echo '<pre>'; print_r($department_id);
            //echo '<pre>'; print_r($academic_year_id);die;
            $result = $Aeccge_course->getRecordByDepartment3($department_id,$academic_year_id);
            $geName=$ge_course->getRecord($department_id);
            
            $academicIds= implode(",",$allAcademicIds);
                foreach ($result  as $key => $value){ 
                    //echo"<pre>";print_r($course_id);
                    $geSeatCount=$sanctionSeats->getRecordByindividualGenericElectiveSeatCount($course_id,$value['ge_id']); 
                    $geCount=$applicantCourseData->getRecordByindividualGenericElective($academic_year_id,$value['ge_id']); 
                    $result[$key]['applied'] = $geCount['total'];
                    $result[$key]['max_seat'] = $geSeatCount['max_seat'];
                }
            $paginator_data = array(
                                    'result' => $result
                                );
               $this->view->geDepartment=$geName['department'];
               $this->view->paginator = $this->_act->pagination($paginator_data);
            }
    }
    //end
    public function ajaxGetCoreCourseAction(){
        $this->_helper->layout->disableLayout();
        $applicantCourseDetailModel = new Application_Model_ApplicantCourseDetailModel();
        //$application_id= $this->_getParam("a_id");
        //echo '<pre>'; print_r($application_id);
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $c_id = $this->_getParam("c_id");
            $session_id = $this->_getParam("session_id");
            //print_r($short_code); die;
            $academic_model = new Application_Model_Department();
            $result = $academic_model->getCoreCourseByCourseId($c_id,$session_id);
            echo "<pre>";print_r($result);
            echo '<option value="0">Select</option>';
            echo '<option value="0">Get Department GE</option>';
            foreach ($result as $k => $val) {
                //secho "<pre>";print_r($val);exit;
                echo '<option value="' . $val['academic_year_id'] . '" >' .$val['department'] . '</option>';
            }   
        }die;
    }
    public function ajaxGetGeForSeatAction(){
        $Aeccge_course = new Application_Model_Aeccge();
        $batch = new Application_Model_Academic();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id_for_dept = $this->_getParam("academic_id");
           // $academic_year_id = $this->_getParam("academic_id");
            $ids= implode(',',$academic_year_id_for_dept);
            $result = $batch->getRecordByIds($ids);
            //echo '<pre>';print_r($result);exit;
            foreach ($result as $key => $value) {
             
                $result[$key]= $value['department'];
               
            }
            $department_id = implode(',',$result);
            $academic_year_id=implode(',',$this->_getParam("academic_id"));
            
            $result = $Aeccge_course->getRecordByDepartment3($department_id,$academic_year_id);
              //echo "<pre>";print_r($result);
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                //echo "<pre>";print_r($val);
                echo '<option value="' . $val['ge_id'] . '" >' .$val['general_elective_name'] . '</option>';
            }   
        }die;
    }
    public function ajaxCheckExistedEntryAction(){
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $dept = $this->_getParam("dept");
            $core_course = $this->_getParam("core_course");
            $ge_id = $this->_getParam("ge_id");
            
            $result = $sanction_seat_model->checkExistedEntry($dept,$core_course,$ge_id);
            if($result['course']){
                echo 'course';
            }elseif ($result['core_course']) {
                echo 'core';
            }elseif($result['generic_elective']){
                echo 'ge';
            }else{
                echo 'go';
            }
           
          
        }die;
    }
    //For Document verification
    public function ajaxGetApplicantInfoAction(){
        $courseDetailmodel = new Application_Model_ApplicantCourseDetailModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $application_no = $this->_getParam("application_no");
           
            $applicant_data = $courseDetailmodel->getRecordByAppID($application_no);
            //echo '<pre>';print_r($result);exit;
             
            $this->view->paginator =$applicant_data;
        }
    }
    //For applicant Info for pay-slip
    public function ajaxApplicantInfoForPaySlipAction(){
        $courseDetailmodel = new Application_Model_ApplicantCourseDetailModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $application_no = $this->_getParam("application_no");
            $pay_mode = $this->_getParam("pay_mode");
            //echo '<pre>'; print_r($application_no);exit;
            if(!empty($pay_mode)){
                $this->view->pay_method =$pay_mode;
                $this->view->paginator =$applicant_data;
            }
            $applicant_data = $courseDetailmodel->getRecordByAppIDPaySlip($application_no);
           
            $OnlineData= array(
                'Account_Name1'=>!empty($applicant_data[0]['account1'])?$applicant_data[0]['account1']:$applicant_data[1]['account1'],
                'Account_Name2'=>!empty($applicant_data[0]['account2'])?$applicant_data[0]['account2']:$applicant_data[1]['account2'],
                'Amount1'=>!empty($applicant_data[0]['total_fee1'])?$applicant_data[0]['total_fee1']:$applicant_data[1]['total_fee1'],
                'Amount2'=>!empty($applicant_data[0]['total_fee2'])?$applicant_data[0]['total_fee2']:$applicant_data[1]['total_fee2']
                
            );
            //echo '<pre>';print_r($applicant_data[0]);exit;
            $this->view->online_details=$OnlineData;
            $this->view->paginator =$applicant_data[0];
            
        }
    }
    public function ajaxGetPaymodeAction(){
        
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $pay_mode = $this->_getParam("pay_mode");
            //echo '<pre>'; print_r($pay_mode);exit;
            if(!empty($pay_mode)){
                $this->view->pay_method =$pay_mode;
            }
            
        }
    }
    public function ajaxUpsertDocumentsAction(){
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $docArray = $this->_getParam("docArray");
            $form_id = $this->_getParam("form_id");
            $app_id = $this->_getParam("app_id");
            $course_id = $this->_getParam("course_id");
            //echo '<pre>';print_r($docArray);exit;
            if($form_id){
                $checkData=$sanction_seat_model->checkExistedData($form_id);
                if($checkData['form_id']){
                    $updateDocument_data = $sanction_seat_model->updateDocuments($docArray,$form_id,$app_id);
                    echo 'Records updated successfully';
                }else{
                    $document_data = $sanction_seat_model->insertDocuments($docArray,$form_id,$app_id,$course_id);
                    echo 'Records inserted successfuly';
                }   
            }
            //$this->view->paginator =$applicant_data;
        }die;
    }
    public function ajaxUpdatePrincipalStatusAction(){
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $buttonValue = $this->_getParam("buttonValue");
            $form_id = $this->_getParam("form_id");
           
            //echo '<pre>';print_r($docArray);exit;
           
            $updatePrincipalStatus = $sanction_seat_model->updatePrincipalStatus($form_id,$buttonValue);
            echo 'ok';
                
            
            //$this->view->paginator =$applicant_data;
        }die;
    }
    public function ajaxGetRecordByPstatusAction(){
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $status_filter = $this->_getParam("status_filter");
            $course = $this->_getParam("course");
           
            $filterData = $sanction_seat_model->getRecordByPrincipalStatus($status_filter,$course);
            $paginator_data = array(
                        'page' => $page,
                        'result' => $filterData
                    );
            //echo '<pre>'; print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
            
            //$this->view->paginator =$filterData;
        }
    }
    public function ajaxGetRecordByAstatusAction(){
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $status_filter = $this->_getParam("status_filter");
            $course = $this->_getParam("course");
           
            $filterData = $sanction_seat_model->getRecordByAccountStatus($status_filter,$course);
            $paginator_data = array(
                        'page' => $page,
                        'result' => $filterData
                    );
            //echo '<pre>'; print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
            
            //$this->view->paginator =$filterData;
        }
    }
    public function ajaxUpdateForFeeSlipAction(){
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $buttonValue = $this->_getParam("buttonValue");
            $form_id = $this->_getParam("form_id");
           
            //echo '<pre>';print_r($buttonValue);exit;
           
            $updatePrincipalStatus = $sanction_seat_model->updateFeeSlipStatus($form_id,$buttonValue);
            echo 'ok';
        }die;
    }
    public function ajaxUpsertFeeSlipAction(){
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $stu_details = new Application_Model_ApplicantCourseDetailModel();
            $fund_type = $this->_getParam("fund_type");
            $form_id = $this->_getParam("form_id");
            $details_stu = $stu_details->getApplicationNumber($form_id);
            
            if($details_stu['degree_id'] == 1){
                $acad_id = $details_stu['core_course1'];
            }else{
                $department= new Application_Model_DepartmentType();
                $academic_details= $department->getAcademicDdetails($details_stu['course']);
               $acad_id = $academic_details['academic_id'];
            }
             $semester= 't1';
            //echo '<pre>';print_r($acad_id);exit;
            $feeitems = new Application_Model_FeeStructureTermItems(); 
            $feeStructure = new Application_Model_FeeStructure();
            $struct_id = $feeStructure->getStructId($acad_id); 
            $fee = $feeitems->getFee($struct_id,$semester);
            
            $feeData= array(
                'totalfee1'=>$fee[0]['totalfee'],
                'account_name1' =>$fee[0]['acc_name'],
                'totalfee2'=>$fee[1]['totalfee'],
                'account_name2' =>$fee[1]['acc_name']
            );
            //echo '<pre>';print_r($feeData);exit;
            $checkExistedData = $sanction_seat_model->checkData($form_id,$fund_type);
            //echo '<pre>';print_r($checkExistedData);exit;
            if(!empty($checkExistedData)){
                $updatePaymentDetail = $sanction_seat_model->updateFeeSlip($fund_type,$checkExistedData['id'],$feeData);
            }else{
                $insertPaymentDetail = $sanction_seat_model->insertFeeSlip($form_id,$fund_type,$feeData);
            }
            
            echo 'ok';
        }die;
    }
    //To upsert pay details
    public function ajaxUpsertPayDetailsAction(){
        $sanction_seat_model = new Application_Model_SanctionSeatModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $docArray = $this->_getParam("docArray");
            $form_id = trim($this->_getParam("form_id"));
            $app_id = trim($this->_getParam("app_id"));
            $course_id = $this->_getParam("course_id");
         //  echo '<pre>';print_r($docArray);exit;
            if($form_id){
                $checkData=$sanction_seat_model->checkExistedPayModeData($form_id);
                if($checkData['form_id']){
                    $updateDocument_data = $sanction_seat_model->updatePayDetails($docArray,$form_id,$app_id);
                    echo 'Records updated successfully';
                }else{
                    
                    $generateRoll=$sanction_seat_model->checkCourseForRoll($course_id);
                        //echo '<pre>';print_r($generateRoll) ;exit;
                        if($generateRoll){
                            $classRoll = 1+$generateRoll['roll_no'];
                        }else{
                            $classRoll = 1;
                        }   
                        
                    $document_data = $sanction_seat_model->inserttPayDetails($docArray,$form_id,$course_id,$classRoll);
                    $updateScrutinyStatus= $sanction_seat_model->updateScrutinyStatus($form_id);
                    echo 'Records inserted successfuly';
                    
                }   
            }
        }die;
    }
}
?>