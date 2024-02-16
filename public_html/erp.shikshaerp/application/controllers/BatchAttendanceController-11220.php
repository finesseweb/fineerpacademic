<?php
   //ini_set('display_errors', '1');
/* 
    Author: Kedar Kumar
    Summary: This controller is used to handle The Batch Attendance
    Date: 03 Oct. 2019
*/
class BatchAttendanceController extends Zend_Controller_Action {

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
        $this->view->action_name = 'batchAttendance';
        $this->view->sub_title_name = 'batchAttendance';
        $this->accessConfig->setAccess('SA_ACAD_M_ATTENDANCE');
        $batchAttendance_form = new Application_Form_BatchAttendance();
        $promotionRuleModel = new Application_Model_SemesterRule();
        $batchAttendanceModel = new Application_Model_BatchAttendance();
        $monthlyAttendanceMaster = new Application_Model_MonthlyAttendanceLineModel();
        $component_master = new Application_Model_Component();
        $update_id = $this->_getParam("id");
        //print_r($update_id);exit;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $batchAttendance_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    //echo '<pre>'; print_r($_POST);exit;
                    if ($batchAttendance_form->isValid($this->getRequest()->getPost())) {
                        $data = $batchAttendance_form->getValues();
                        $data['course_id'] = $this->getRequest()->getPost('course_id');
                        $student_ids = $this->getRequest()->getPost('stu_id');
                        $data['u_id'] = $this->getRequest()->getPost('u_id');
                        $data['batch_code'] = $this->getRequest()->getPost('batch_code');
                        $data['attended_class'] =$this->getRequest()->getPost('attended_class');
                        // echo "<pre>";print_r($data);exit;
                        $date = explode('/',$data['effective_date']);
                        $effective_date= $date[2]."/".$date[1]."/".$date[0];
                        $data['effective_date']=$effective_date;
                        if(empty($this->getRequest()->getPost('department'))){   
                        $data['department'] ='0';
                        }
                        if(empty($this->getRequest()->getPost('ge_id'))){    
                        $data['ge_id'] ='0';
                        }
                        //echo "<pre>";print_r($data);exit;
                        //$last_insert_id = $FeeStructure_model->insert($data);
                        $attendanceMasterData = array(
                            'session' => $data['session'],
                            'cmn_terms' => $data['cmn_terms'],
                            'degree_id' => $data['degree_id'],
                            'cc_id' =>$data['cc_id'],
                            'ge_id' =>$data['ge_id'],
                            'department_id' => $data['department_id'],
                            'employee_id' => $data['employee_id'],
                            'conducted_class' => $data['conducted_class'],
                            'course_id' => $data['course_id'], 
                            'effective_date' => $effective_date,
                            'theory' => $_POST['theory'] ,
                            'practical' => $_POST['practical'],
                            'department' => $data['department']
                           
                        );
                        
                            //echo "<pre>";print_r($attendanceMasterData);exit;
                            unset($data['session_filter'],$data['course_group']);
                            //echo "<pre>";print_r($data);exit;
                            $last_insert_id=$monthlyAttendanceMaster->insert($attendanceMasterData);
                            if($last_insert_id){
                                    
                                //
                                foreach ($student_ids as $key => $stu_id) {
                                   
                                    $attendanceInfoData= array(
                                    'attendance_master_id' =>  $last_insert_id,   
                                    
                                    'batch' => $data['batch_code'][$key],
                                    'u_id' => $data['u_id'][$key],
                                    'attended_class'=> $data['attended_class'][$key],
                                    'percent' => $_POST['percent_sum'][$key] 
                                        
                                );
                                    //echo '<pre>'; print_r($attendanceInfoData);
                                    foreach($component_master->getActiveRecords() as $comp_keys => $comp_val){
                                        $attendanceInfoData["component_{$comp_val['id']}"] = $_POST["component_{$comp_val['id']}"][$key];
                                        $attendanceInfoData["component_{$comp_val['id']}_%"] = $_POST["component_{$comp_val['id']}_%"][$key];
                                    }
                                    //echo '<pre>'; print_r($attendanceInfoData); exit;
                                    $insert_id=$batchAttendanceModel->insert($attendanceInfoData);
                                }
                                
                                
                            }
                       
                        $this->_flashMessenger->addMessage('Attendance Successfully added');
                        $this->_redirect('batch-attendance/index');
                        
                    }
                }
                break;
            case 'edit':
                
                $result = $batchAttendanceModel->getRecordById($update_id);
                $start_date = date_create($result[0]['effective_date']);
                $result[0]['effective_date'] = date_format($start_date,"d/m/Y"); 
                $this->view->course_id = $result[0]['course_id'];
                $this->view->attended_class = $result[0]['attended_class'];
                $this->view->id = $update_id;
                $batchAttendance_form->populate($result[0]);
               
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if($batchAttendance_form->isValid($this->getRequest()->getPost())){
                       
                        $student_ids = $this->getRequest()->getPost('u_id');
                        $data['attended_class'] =$this->getRequest()->getPost('attended_class');
                        $date = explode('/',$_POST['effective_date']);
                        $effective_date= $date[2]."/".$date[1]."/".$date[0];
                        $data['effective_date']=$effective_date;
                        $update_master_attend= array(
                            'effective_date'=>$data['effective_date'],
                            'theory'=>$_POST['theory'],
                            'practical' =>$_POST['practical'],
                            'conducted_class'=>$_POST['conducted_class']
                        );
                        //echo "<pre>"; print_r($update_master_attend);exit;
                         $monthlyAttendanceMaster->update($update_master_attend, array("md5(id)=?" => $update_id));

                        foreach ($student_ids as $key => $stu_id) {
                            $data = $batchAttendance_form->getValues();
                            $dataPost= $_POST;
                            
                            $uId= $this->getRequest()->getPost('u_id');
                            $attendStatus=$this->getRequest()->getPost('attended_class');
                            $batch_code = $this->getRequest()->getPost('batch_code');
                            $data['u_id']=$uId[$key];
                            $data['batch'] = $batch_code[$key];
                            $data['attended_class']=$attendStatus[$key];
                            $data['percent'] = $_POST['percent_sum'][$key];   
                             foreach($component_master->getActiveRecords() as $comp_keys => $comp_val){
                            $data["component_{$comp_val['id']}"] = $_POST["component_{$comp_val['id']}"][$key];
                            $data["component_{$comp_val['id']}_%"] = $_POST["component_{$comp_val['id']}_%"][$key];
                        }
                            unset($data['component_paper'],$data['session'],$data['degree_id'],$data['effective_date'],$data['conducted_class'],$data['required_percentage'],$data['department'],$data['ge_id'],$data['cmn_terms'],$data['cc_id'],$data['department_id'],$data['employee_id']);
                            
                            unset($data['session_filter'],$data['course_group']);
                            
                            //echo '<pre>'; print_r($data); exit;
                            $batchAttendanceModel->update($data, array('u_id=?' => $data['u_id'],
                                "md5(attendance_master_id) =?" => $update_id
                               ));  
                        }
                       
                       
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('batch-attendance/index');
                    } else {
                        //$this->_redirect('fee-heads/index');						
                    }
                }
               
                break;
            case 'delete':
                $data['status'] = 2;
                if ($update_id) {
                    $promotionRuleModel->update($data, array('id=?' => $update_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('batchAttendance/index');
                }
                break;
            default:
                
                if($this->login_storage->role_id != 2 ){
                    $empl_id=$this->login_storage->empl_id;
                    $messages = $this->_flashMessenger->getMessages();
                    $this->view->messages = $messages;
                    $result = $batchAttendanceModel->getRecordsByEmplId($empl_id);
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );

                    //echo"<pre>";print_r($paginator_data);exit;
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                      break;
                } else{
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $batchAttendanceModel->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                }
                break;
                       
        }
    }
    //To calculate attendance by Semesetr Date:10 Oct. 2019
    public function semesterAction(){
        $this->view->action_name = 'batchAttendance';
        $this->view->sub_title_name = 'semsterAttendance';
        $this->accessConfig->setAccess('SA_ACAD_P_ATTENDANCE');
        $semesterAttend_form =  new Application_Form_BatchAttendance();
        $this->view->form = $semesterAttend_form;
        $batchSemesterAttendanceModel = new Application_Model_BatachSemesterAttendance();
        $update_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $semesterAttend_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($semesterAttend_form->isValid($this->getRequest()->getPost())) {
                        $data = $semesterAttend_form->getValues();
                       
                        $data['course_id'] = $this->getRequest()->getPost('course_id');
                        $student_ids = $this->getRequest()->getPost('u_id');
                        $data['u_id'] = $this->getRequest()->getPost('u_id');
                        $data['attended_class'] =$this->getRequest()->getPost('attended_class');
                        $data['overall_percent'] =$this->getRequest()->getPost('overall_percent');
                        $data['attendance_val'] =$this->getRequest()->getPost('attendance_val');
                        $data['attend_remarks'] =$this->getRequest()->getPost('attend_remarks');
                        if(empty($this->getRequest()->getPost('department'))){   
                            $data['department'] ='0';
                        }
                        if(empty($this->getRequest()->getPost('ge_id'))){    
                            $data['ge_id'] ='0';
                        }
                        if(empty($this->getRequest()->getPost('course_id'))){   
                            $data['course_id'] ='0';
                        }
                        foreach ($student_ids as $key => $stu_id) {
                        $semesterAttendanceData = array(
                           'session' => $data['session'],
                           'cmn_terms' => $data['cmn_terms'],
                           'degree_id' => $data['degree_id'],
                           'cc_id' =>$data['cc_id'],
                           'ge_id' =>$data['ge_id'],
                           'department' => $data['department'],
                           'course_id' => $data['course_id'],
                           'conducted_class' => $data['conducted_class'],
                           'component_paper' => implode(',',$data['component_paper']),
                           'u_id' =>$data['u_id'][$key],
                           'attended_class'=> $data['attended_class'][$key],
                           'required_percentage'=> $data['required_percentage'],
                           'attend_status'=> $data['attendance_val'][$key],
                           'attend_remarks'=> $data['attend_remarks'][$key],
                            'overall_percent'=>$data['overall_percent'][$key]
 
                        );
                            unset($data['session_filter'],$data['course_group']);
                           //echo "<pre>";print_r($semesterAttendanceData);exit;
                            $batchSemesterAttendanceModel->insert($semesterAttendanceData);
                       }
                       
                        $this->_flashMessenger->addMessage('Semester Attendance Successfully added');
                        $this->_redirect('batch-attendance/semester');
                    }
                }


                break;
            case 'edit':
                
                $results = $batchSemesterAttendanceModel->getRecordById($update_id);
                $results['component_paper'] = explode(',',$results['component_paper']);
                //echo "<pre>"; print_r($results);exit;
                $this->view->course_id = $results['course_id'];
                $this->view->attended_class = $results['attend_class'];
                $this->view->attendance_val = $results['attend_status'];
                $this->view->attend_remarks = $results['remarks'];
                $this->view->overall_percent = $results['overall_percent'];
                $this->view->id = $update_id;
                $semesterAttend_form->populate($results);
               
                $this->view->result = $results;
                   //  echo "<pre>";print_r($this->view->result);exit;
                if ($this->getRequest()->isPost()) {
                    if ($this->getRequest()->getPost()) {
                        $student_ids = $this->getRequest()->getPost('u_id');
                        $data['attended_class'] =$this->getRequest()->getPost('attended_class');
                        
                        //echo "<pre>"; print_r($_POST);exit();
                        foreach ($student_ids as $key => $stu_id) {
                            $data = $semesterAttend_form->getValues();
                            if(empty($this->getRequest()->getPost('department'))){   
                            $data['department'] ='0';
                            }
                            if(empty($this->getRequest()->getPost('ge_id'))){    
                                $data['ge_id'] = '0';
                            }
                            if(empty($this->getRequest()->getPost('course_id'))){   
								$data['course_id'] ='0';
							}
                            $uId= $this->getRequest()->getPost('u_id');
                            $attendclass=$this->getRequest()->getPost('attended_class');
                            $attend_status =$this->getRequest()->getPost('attendance_val');
                            $overall_percent =$this->getRequest()->getPost('overall_percent');
                            $attend_remarks =$this->getRequest()->getPost('attend_remarks');
                            $data['u_id']=$uId[$key];
                            
                            $data['overall_percent']= $overall_percent[$key];
                            $data['attended_class']=$attendclass[$key];
                            $data['attend_status']=$attend_status[$key];
                            $data['attended_class']=$attendclass[$key];
                            $data['attend_remarks']=$attend_remarks[$key];
                            $data['component_paper']=implode(',',$_POST['component_paper']);
                            
                            unset($data['session_filter'],$data['course_group'],$data['department_id'],$data['employee_id']);
                            
                            //echo '<pre>'; print_r($data);exit;
                            $batchSemesterAttendanceModel->update($data, array('id=?' => $update_id));  
                        }
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('batch-attendance/semester');
                    } else {
                        //$this->_redirect('fee-heads/index');						
                    }
                }
               
                break;
            case 'delete':
                $data['status'] = 2;
                if ($update_id) {
                    $batchSemesterAttendanceModel->update($data, array('id=?' => $update_id));
                    //$FeeHeadItems_model->update($data, array('feehead_id=?' => $feehead_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('promotion/semester');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                /*$result = $batchSemesterAttendanceModel->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);*/
                break;
        }
    }
    //End
    //Calculate Monthly Attendance on behalf of daily attendance: 23 Sept. 2020 : Kedar
    public function calculateMonthlyAttendanceAction(){
        $this->view->action_name = 'batchAttendance';
        $this->view->sub_title_name = 'batchAttendance';
        $this->accessConfig->setAccess('SA_ACAD_M_ATTENDANCE');
        $batchAttendance_form = new Application_Form_BatchAttendance();
        $promotionRuleModel = new Application_Model_SemesterRule();
        $batchAttendanceModel = new Application_Model_BatchAttendance();
        $monthlyAttendanceMaster = new Application_Model_MonthlyAttendanceLineModel();
        $component_master = new Application_Model_Component();
        $update_id = $this->_getParam("id");
        //print_r($update_id);exit;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $batchAttendance_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    //echo '<pre>'; print_r($_POST);exit;
                    if ($batchAttendance_form->isValid($this->getRequest()->getPost())) {
                        $data = $batchAttendance_form->getValues();
                        $data['course_id'] = $this->getRequest()->getPost('course_id');
                        $student_ids = $this->getRequest()->getPost('u_id');
                        $data['u_id'] = $this->getRequest()->getPost('u_id');
                        $data['batch_code'] = $this->getRequest()->getPost('batch_code');
                        $data['attended_class'] =$this->getRequest()->getPost('attended_class');
                        // echo "<pre>";print_r($data);exit;
                        $date = explode('-',$data['effective_date']);
                        $defaultDay='01';
                        $effective_date= $date[1]."/".$date[0]."/".$defaultDay;
                        $data['effective_date']=$effective_date;
                        if(empty($this->getRequest()->getPost('department'))){   
                        $data['department'] ='0';
                        }
                        if(empty($this->getRequest()->getPost('ge_id'))){    
                        $data['ge_id'] ='0';
                        }
                        //echo "<pre>";print_r($data);exit;
                        //$last_insert_id = $FeeStructure_model->insert($data);
                        $attendanceMasterData = array(
                            'session' => $data['session'],
                            'cmn_terms' => $data['cmn_terms'],
                            'degree_id' => $data['degree_id'],
                            'cc_id' =>$data['cc_id'],
                            'ge_id' =>$data['ge_id'],
                            'department_id' => $data['department_id'],
                            'employee_id' => $this->login_storage->empl_id,
                            'conducted_class' => $data['conducted_class'][0],
                            'course_id' => $data['course_id'], 
                            'effective_date' => $effective_date,
                            'theory' =>  $data['conducted_class'][0] ,
                            'practical' => $_POST['practical'],
                            'department' => $data['department']
                           
                        );
                        
                            //echo "<pre>";print_r($attendanceMasterData);exit;
                            unset($data['session_filter'],$data['course_group']);
                            //echo "<pre>";print_r($data);exit;
                            $last_insert_id=$monthlyAttendanceMaster->insert($attendanceMasterData);
                            if($last_insert_id){
                                 $data['pract_class']= '0';   
                               // echo '<pre>'; print_r($student_ids);exit;
                                foreach ($student_ids as $key => $stu_id) {
                                   
                                    $attendanceInfoData= array(
                                    'attendance_master_id' =>  $last_insert_id,   
                                    
                                    'batch' => $data['batch_code'][$key],
                                    'u_id' => $data['u_id'][$key],
                                    'attended_class'=> $data['attended_class'][$key],
                                    'component_2'=> $data['attended_class'][$key],
                                    'component_3'=> $data['pract_class'][$key],
                                    'percent' => round($_POST['attended_class'][$key] /$data['conducted_class'][0]*100)
                                        
                                );
                                    
                                    $insert_id=$batchAttendanceModel->insert($attendanceInfoData);
                                }
                                
                                
                            }
                       
                        $this->_flashMessenger->addMessage('Attendance Successfully added');
                        $this->_redirect('batch-attendance/index');
                        
                    }
                }
                
            break;
            case 'edit':
                break;
            case 'delete':
                break;
            default:
                break;
        }
    }
    
    public function ajaxCalculateMonthlyAttendanceAction(){
        $this->_helper->layout->disableLayout();
        $batchAttendanceModel = new Application_Model_BatchAttendance();
     
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $session = $this->_getParam("session");
            $term_id = $this->_getParam("term_id");
            $degree_id = $this->_getParam("degree_id");
            $cc_id = $this->_getParam("cc_id");
            $ge_id=$this->_getParam("ge_id");
            $hons_id = $this->_getParam("department");
            $course_id = $this->_getParam("course_id");
            $effective_date= $this->_getParam("effective_date");
            $checkExistedData= $batchAttendanceModel->checkMonthlyAttendance($session,$term_id,$degree_id,$effective_date,$cc_id,$ge_id,$hons_id,$course_id);
            if(!empty($checkExistedData)){
                 echo '<pre>'; print_r('<h2 style="color:red">Sorrry this month attendace is already marked. </h2>');exit;
            }else{
                $result = $batchAttendanceModel->calculateMonthlyAttendance($session,$term_id,$degree_id,$effective_date,$cc_id,$ge_id,$hons_id,$course_id);
            $paginator_data = array(
                'page' => $page,
                'result' => $result
            );
            $totalClass=$result[0]['total_class'];
            //echo"<pre>";print_r($result);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
            }
       }
           
            
    }
    public function ajaxCalculateTotalConductedAction(){
        $this->_helper->layout->disableLayout();
        $batchAttendanceModel = new Application_Model_BatchAttendance();
     
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $session = $this->_getParam("session");
            $term_id = $this->_getParam("term_id");
            $degree_id = $this->_getParam("degree_id");
            $cc_id = $this->_getParam("cc_id");
            $ge_id=$this->_getParam("ge_id");
            $hons_id = $this->_getParam("department");
            $course_id = $this->_getParam("course_id");
            $effective_date= $this->_getParam("effective_date");
           
            $result = $batchAttendanceModel->calculateMonthlyAttendance($session,$term_id,$degree_id,$effective_date,$cc_id,$ge_id,$hons_id,$course_id);
            
            $totalClass=$result[0]['total_class'];
            echo $totalClass;
        }die;
    }
    //End
    //Daily Basis Attendanc
    public function dailyAttendanceAction(){
        $this->view->action_name = 'batchAttendance';
        $this->view->sub_title_name = 'batchAttendance';
        $this->accessConfig->setAccess('SA_ACAD_D_ATTENDANCE');
        $batchAttendance_form = new Application_Form_BatchAttendance();
        $promotionRuleModel = new Application_Model_SemesterRule();
        $dailyAttendanceMaster = new Application_Model_DailyAttendanceModel();
        $component_master = new Application_Model_Component();
        $update_id = $this->_getParam("id");
        //print_r($update_id);exit;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $batchAttendance_form;
        $empl_id=$this->login_storage->empl_id;
        $empl_dept = $dailyAttendanceMaster->getEmplDeptById($empl_id);
        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    //echo '<pre>'; print_r($_POST);exit;
                    if ($batchAttendance_form->isValid($this->getRequest()->getPost())) {
                        $data = $batchAttendance_form->getValues();
                        $data['course_id'] = $this->getRequest()->getPost('course_id');
                        $student_ids = $this->getRequest()->getPost('stu_id');
                        $data['u_id'] = $this->getRequest()->getPost('u_id');
                        $data['attend_status'] = array_values($this->getRequest()->getPost('attendStatus'));
                        $data['batch_code'] = $this->getRequest()->getPost('batch_code');
                        $data['period'] =$this->getRequest()->getPost('period');
                        $data['teacher_dept'] =$this->getRequest()->getPost('teacher_dept');
                        
                        if(empty($this->getRequest()->getPost('department'))){   
                        $data['department'] ='0';
                        }
                        if(empty($this->getRequest()->getPost('ge_id'))){    
                        $data['ge_id'] ='0';
                        }
                        
                        $date = explode('/',$_POST['effective_date']);
                        $effective_date= $date[2]."/".$date[1]."/".$date[0];
                        $data['effective_date']=$effective_date;
                        
                        $attendanceMasterData = array(
                            'session' => $data['session'],
                            'cmn_terms' => $data['cmn_terms'],
                            'degree_id' => $data['degree_id'],
                            'cc_id' =>$data['cc_id'],
                            'ge_id' =>$data['ge_id'],
                            'department_id' => $data['department_id'],
                            'employee_id' => $this->login_storage->empl_id,
                            'course_id' => $data['course_id'], 
                            'effective_date' => $data['effective_date'],
                            'submit_date' => date("Y-m-d"),
                            'period'=>$data['period'],
                            'teacher_dept'=>$data['teacher_dept'],
                            'department' => $data['department']
                           
                        );
                        
                            //echo "<pre>";print_r($attendanceMasterData);exit;
                            unset($data['session_filter'],$data['course_group']);
                            //echo "<pre>";print_r($attendanceMasterData);exit;
                            $last_insert_id=$dailyAttendanceMaster->insert($attendanceMasterData);
                            
                            if($last_insert_id){
                                   
                                foreach ($student_ids as $key => $stu_id) {
                                   
                                    $attendanceInfoData= array(
                                    'master_id' =>  $last_insert_id,   
                                    
                                    'batch' => $data['batch_code'][$key],
                                    'attend_status'=>$data['attend_status'][$key],
                                    'f_id' => $data['u_id'][$key] 
                                        
                                    );
                                    //echo '<pre>'; print_r($masterId); exit;
                                    $insert_id=$dailyAttendanceMaster->insertDailyAttendance($attendanceInfoData);
                                    
                                }
                                
                                
                            }
                            //echo '<pre>'; print_r($insert_id);exit;
                            $checkInsertedDAta=$dailyAttendanceMaster->checkInsertData($last_insert_id);
                            //echo '<pre>'; print_r(count($checkInsertedDAta));exit;
                        if(count($checkInsertedDAta)>=1){
                            $_SESSION['message_class'] = 'alert-success';
                            $this->_flashMessenger->addMessage('Attendance Successfully added');
                            $this->_redirect('batch-attendance/daily-attendance');
                        }else{
                            $deleteMasterData=$dailyAttendanceMaster->dumpMasterData($last_insert_id);
                             $_SESSION['message_class'] = 'alert-danger';
                            $this->_flashMessenger->addMessage('Attendance not Saved! Please try again.');
                            $this->_redirect('batch-attendance/daily-attendance');
                        }
                        
                    }
                }
                break;
            case 'edit':
                
                $result = $dailyAttendanceMaster->getRecordById($update_id);
                $this->view->course_id = $result[0]['course_id'];
                $start_date = date_create($result[0]['effective_date']);
                $result[0]['effective_date'] = date_format($start_date,"d/m/Y"); 
                $this->view->id = $update_id;
                $batchAttendance_form->populate($result[0]);
               
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if($batchAttendance_form->isValid($this->getRequest()->getPost())){
                       
                        $student_ids = $_POST['u_id'];
                        $date = explode('/',$_POST['effective_date']);
                        $effective_date= $date[2]."/".$date[1]."/".$date[0];
                        $data['effective_date']=$effective_date;
                        
                        if(empty($this->getRequest()->getPost('department'))){   
                            $data['department'] ='0';
                        }else{
                            $data['department']= $_POST['department'];
                        }
                        if(empty($this->getRequest()->getPost('ge_id'))){    
                            $data['ge_id'] ='0';
                        }else{
                            $data['ge_id']= $_POST['ge_id'];
                        }
                        
                        $update_master_attend= array(
                            'session' => $_POST['session'],
                            'cmn_terms' => $_POST['cmn_terms'],
                            'degree_id' => $_POST['degree_id'],
                            'cc_id' =>$_POST['cc_id'],
                            'ge_id' =>$data['ge_id'],
                            'department' => $data['department'],
                            'course_id' => $_POST['course_id'], 
                            'effective_date'=> $data['effective_date'],
                            'period'=>$_POST['period'],
                            'teacher_dept'=>$_POST['teacher_dept']
                        );
                        //echo "<pre>"; print_r($update_master_attend);exit;
                        $dailyAttendanceMaster->update($update_master_attend, array("md5(id)=?" => $update_id));
                        //echo '<pre>'; print_r($update);exit;
                        foreach ($student_ids as $key => $stu_id) {
                            $data = $batchAttendance_form->getValues();
                            $dataPost= array_values($_POST['attendStatus']);
                           
                            $uId= $this->getRequest()->getPost('u_id');
                            $batch_code = $this->getRequest()->getPost('batch_code');
                            $attend_id= $this->getRequest()->getPost('attendStatus');
                            
                            $updateDailyAttend= array(
                                'f_id'=>$uId[$key],
                                'batch'=>$batch_code[$key],
                                'attend_status'=>$dataPost[$key]
                                );
                           
                            //echo '<pre>'; print_r($updateDailyAttend); exit;
                            
                            $dailyAttendanceMaster->updateDailyAttendInfo($updateDailyAttend,$updateDailyAttend['f_id'],$update_id);  
                        }
                       
                       
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('batch-attendance/daily-attendance');
                    } else {
                        //$this->_redirect('fee-heads/index');						
                    }
                }
               
                break;
            case 'delete':
                $data['status'] = 2;
                if ($update_id) {
                    $promotionRuleModel->update($data, array('id=?' => $update_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('batchAttendance/index');
                }
                break;
            default:
                 

                if($this->login_storage->role_id != 2 ){
                    $empl_id=$this->login_storage->empl_id;
                    $messages = $this->_flashMessenger->getMessages();
                    $this->view->messages = $messages;
                    $result = $dailyAttendanceMaster->getRecordsByEmplId($empl_id);
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );

                    //echo"<pre>";print_r($paginator_data);exit;
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                      break;
                } else{
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $dailyAttendanceMaster->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );

                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
                }             
                
        }
    }
        
    //End
    //get student by session in Semester Attendance
    public function ajaxGetStudentBySessionAction(){
        
        $this->_helper->layout->disableLayout();
        $batchSemesterAttendanceModel = new Application_Model_BatachSemesterAttendance();
     
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $session_id = $this->_getParam("session_id");
            $course_group = $this->_getParam("course_group");
            $degree_id = $this->_getParam("degree_id");
            
            
            if ($session_id) {
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                
                $result = $batchSemesterAttendanceModel->getRecordsBySession($session_id,$course_group,$degree_id);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
            }
        }
    }
	//Get Monthly Batch Attendace after filter Date:02 Apr 2020
	public function ajaxGetMonthlyWiseStudentAction(){
		$this->_helper->layout->disableLayout();
        $batchAttendanceModel = new Application_Model_BatchAttendance();
     
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $session = $this->_getParam("session");
            $term_id = $this->_getParam("term_id");
            $degree_id = $this->_getParam("degree_id");
            $cc_id = $this->_getParam("cc_id");
            $ge_id=$this->_getParam("ge_id");
            $hons_id = $this->_getParam("hons_id");
            $course_id = $this->_getParam("course_id");
            $empl_id = $this->_getParam("employee_id");
            $effective_date= $this->_getParam("effective_date");
            $result = $batchAttendanceModel->getMonthlyBatchRecords($session,$term_id,$degree_id,$effective_date,$cc_id,$ge_id,$hons_id,$course_id,$empl_id);
            $paginator_data = array(
                'page' => $page,
                'result' => $result
            );
            //echo"<pre>";print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
        }
	}
	//End
    //Get daily batch attendance after filter Date:22 July 2020
    public function ajaxGetDailyWiseStudentAction(){
		$this->_helper->layout->disableLayout();
        $batchAttendanceModel = new Application_Model_BatchAttendance();
     
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $session = $this->_getParam("session");
            $term_id = $this->_getParam("term_id");
            $degree_id = $this->_getParam("degree_id");
            $effective_date= $this->_getParam("effective_date");
            $empl_id=$this->_getParam("employee_id");
            $result = $batchAttendanceModel->getDailyBatchRecords($session,$term_id,$degree_id,$effective_date,$empl_id);
            $paginator_data = array(
                'page' => $page,
                'result' => $result
            );
            //echo"<pre>";print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
        }
	}
    //Get Semester End Student after filter : Date:26 March 2020
    public function ajaxGetSemesterEndStudentAction(){
        $this->_helper->layout->disableLayout();
        $batchSemesterAttendanceModel = new Application_Model_BatachSemesterAttendance();
     
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $session = $this->_getParam("session");
            $term_id = $this->_getParam("term_id");
            $degree_id = $this->_getParam("degree_id");
            $cc_id = $this->_getParam("cc_id");
            $ge_id=$this->_getParam("ge_id");
            $hons_id = $this->_getParam("hons_id");
            $course_id = $this->_getParam("course_id");
            
            $result = $batchSemesterAttendanceModel->getEndSemesterRecords($session,$term_id,$degree_id,$cc_id,$ge_id,$hons_id,$course_id);
            $paginator_data = array(
                'page' => $page,
                'result' => $result
            );
            //echo"<pre>";print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
        }
            
        
    }
    //End
    //get student by session in Semester Attendance
    public function ajaxGetStudentByCourseGroupAction(){
        
        $this->_helper->layout->disableLayout();
        $batchAttendanceModel = new Application_Model_BatchAttendance();
     
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $session_id = $this->_getParam("session_id");
            $course_group = $this->_getParam("course_group");
            $degree_id = $this->_getParam("degree_id");
            $search_date = $this->_getParam("effective_date");
            $department = $this->_getParam("department");
            $cmn_terms = $this->_getParam("cmn_terms");
            //echo"<pre>";print_r($department);exit;
            if ($session_id) {
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                
                $result = $batchAttendanceModel->getRecordsByCourseGroup($session_id,$course_group,$degree_id,$search_date, $department, $cmn_terms);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
            }
        }
    }
    //Date 23 Oct. 2019
    public function ajaxGetExistedBatchAttendanceAction(){
        $monthlyAttendanceModel = new Application_Model_BatchAttendance();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()){
            $effective_date = $this->_getParam("effective_date"); 
            $course_id = $this->_getParam("course_id");
            $term_id = $this->_getParam("term_id");
            $hons_id = $this->_getParam("hons_id");
            $ge_id = $this->_getParam("ge_id");
           //echo $ge_id; exit;
            $existedData=$monthlyAttendanceModel->get_existed_attendance_data($term_id,$course_id,$effective_date,$hons_id,$ge_id);
           
            echo $existedData['cmn_terms'];
            
        }die;
    }
    public function ajaxGetTotalClassConductedAction(){
        $semesterAttendanceModel = new Application_Model_BatchAttendance();
         if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()){
            $session = $this->_getParam("session"); 
            $course_id = $this->_getParam("course_id");
            $coreCourse = $this->_getParam("coreCourse");
            $hons_id = $this->_getParam("hons_id");
            $term_id = $this->_getParam("term_id");
            $u_id = $this->_getParam("u_id");
            
            $conductedClassData=$semesterAttendanceModel->get_conducted_class_data($session,$course_id,$term_id,$coreCourse,$hons_id);
            
            echo $conductedClassData['conducted_class'];
        }die;          
    }
    public function ajaxGetTotalClassAttendedAction(){
        $id = $this->_getParam("id");
            //echo $id;
        $semesterAttendanceModel = new Application_Model_BatchAttendance();
        $semester_details = new Application_Model_BatachSemesterAttendance();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()){
        if(!empty($id)){
          
            $term = $this->_getParam("term_id");
            $totalClass = $this->_getParam("tot_class");
            $semesterAttendance = $semester_details->getRecordById($id);
            
            //$course_id = $this->_getParam("course_id");
            $attendedClassData=$semester_details->get_attended_class_count($id);
             //echo "<pre>"; print_r($attendedClassData);die;
            $Category_data[] = $attendedClassData;
            $this->view->attend_status=$semesterAttendance['attend_status'];
            $this->view->tot_class = $totalClass;
            $this->view->category_data = $Category_data;
        }else{
            
                $term = $this->_getParam("term_id");
                $coreCourse = $this->_getParam("coreCourse");
                $course_id = $this->_getParam("course_id");
                $totalClass = $this->_getParam("tot_class");
                $hons_id = $this->_getParam("hons_id");
                $batchData=$semesterAttendanceModel->getMasterIds($course_id,$hons_id,$term,$coreCourse);
               
                $batchData = $this->mergData($batchData, array('id'), count($batchData));
                $masterIds =implode(',',$batchData);
                
                $uniqueStudents = $semesterAttendanceModel->getUniqueStudents($masterIds);
                //echo '<pre>'; print_r($uniqueStudents);exit;
                foreach ($uniqueStudents as $key => $values ){
                   //echo '<pre>';print_r($values);exit;
                   $attendedClassData=$semesterAttendanceModel->get_attended_class_count($values['u_id'],$course_id,$coreCourse,$hons_id,$masterIds);
                   
                   $Category_data[] = $attendedClassData;
                }   
            }
            $Category_data = array_filter($Category_data,function($val){ return $val!='';} );
            $this->view->tot_class = $totalClass;
            $this->view->category_data = $Category_data;  
        }         
    }
    //To update attend Status :Allowd/Not Allowed   
    public function ajaxUpdateAttendStatusAction(){
        $endSemesterModel = new Application_Model_BatachSemesterAttendance();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $buttonValue = $this->_getParam("buttonValue");
            $remarks = $this->_getParam("remarks");
            $update_id = $this->_getParam("update_id");
            $data=array(
                'attend_status'=>$buttonValue,
                'attend_remarks'=>$remarks
                
            );
            //echo '<pre>';print_r($docArray);exit;
           
            $updateAttendStatus = $endSemesterModel->update($data, array('id=?' =>$update_id));
            //$promotionRuleModel->update($data, array('id=?' => $update_id));
            echo 'ok';
                
            
            //$this->view->paginator =$applicant_data;
        }die;
    }
    //Student details by session course
            
        public function ajaxGetStudentsDetailsAction(){
        $term_model = new Application_Model_TermMaster();
        $course_learning = new Application_Model_ElectiveSelection();
        $core_course_learning = new Application_Model_Corecourselearning();
        $batch_attendance = new Application_Model_BatchAttendance();
        $academic_model = new Application_Model_Academic();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $ct_id = $this->_getParam("ct_id");
            $employee_id = $this->_getParam("employee_id");
            $department_id = $this->_getParam("department_id");
            $course_id = $this->_getParam("course_id");
            $term_id = $this->_getParam("term_id");
            $grade_allocate_id = $this->_getParam("grade_allocate_id");
            $honors_id= $this->_getParam("honors_id");
            $session_id= $this->_getParam("session_id");
            $degree_id= $this->_getParam("degree_id");
            $id = $this->_getParam("id");
            $componentmaster = new Application_Model_Component();

            //====Ashutosh==//
            $componentmaster = new Application_Model_Component();
            //====[end]====//
            $this->view->weightage = $componentmaster->getActiveRecords();
            //echo $session_id;
            if(!empty($id)){
                $res_batch_attendance = $batch_attendance->getRecordById($id);
                $Category_data['id'] = $res_batch_attendance;
                $Category_data['short_code'] = $res_batch_attendance['batch'];
                $this->view->category_data = $Category_data['id'];
                //echo "<pre>";print_r($Category_data);
                //exit;    
            }     
            else{
               $res_batch_attendance['u_id'] = ''; 
               //$res_batch_attendance['attended_class'] = ''; 
            
                $yearArr = $term_model->getRecordByCmnTerms($term_id,$session_id);
               // echo '<pre>';
                $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
                $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
                $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));


                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
              
                if(!empty($session_id)){
                  $academic_res_on_dept = $academic_model->getAcademicOnDept($honors_id,$session_id,$degree_id);
                  $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));
                    foreach($academic_id_Arr as $key => $value){
                        if(!in_array($value, $academic_res_on_dept)){
                            unset($academic_id_Arr[$key]);
                        }
                    }
                }
                $StudentPortal_model = new Application_Model_StudentPortal();
                $this->view->course_id = $course_id;
                $filter_acad = array();
                foreach ($academic_id_Arr as $key => $value) {
                    if ($value) {
                        if($degree_id!=1){
                           
                           
                            $newData[$value] = $StudentPortal_model->getstudentsdetailattend($value,$term_id);  
                            
                            if($course_id){
                            $raw_data[$value] = $course_learning->getStudentsForElectiveByCourse($academic_id_Arr[$key], $course_id, $termidarr[$key],'electives');
                            
                            if(count($newData[$value])>0){
                            $newData[$value] = $this->selectData($raw_data[$value], array('students', 'student_id', 'stu_id','father_name','reg_no','exam_roll','short_code','roll_no','stu_status'), count($raw_data[$value]));}}
                            if(!in_array($newVal['academic_id'],$filter_acad)){
                                $filter_acad[]= $value;
                                foreach ($newData[$value] as $Studkey => $newVal) {

                                    $newVal['term_id'] = $termidarr[$key];
                                    $newVal['academic_id'] = $academic_id_Arr[$key];

                                    $newVal['attended_class'] = $res_batch_attendance['attended_class'];

                                    if($newVal['stu_id'] == $res_batch_attendance['u_id'] && !empty($id))
                                    $Category_data[] = $newVal;
                                        else if(empty($id))
                                    $Category_data[] = $newVal;
                                }
                            }
                        }
                        else if (!$ct_id) {
                           
                            $newData[$value] = $StudentPortal_model->getstudentsdetailattend($value,$term_id);  
                           
                            if(!in_array($newVal['academic_id'],$filter_acad)){
                                $filter_acad[]= $value;
                                foreach ($newData[$value] as $Studkey => $newVal) {

                                    $newVal['term_id'] = $termidarr[$key];
                                    $newVal['academic_id'] = $academic_id_Arr[$key];

                                    $newVal['attended_class'] = $res_batch_attendance['attended_class'];

                                    if($newVal['stu_id'] == $res_batch_attendance['u_id'] && !empty($id))
                                    $Category_data[] = $newVal;
                                        else if(empty($id))
                                    $Category_data[] = $newVal;
                                }
                            }
                        }else{
                            
                            $raw_data[$value] = $course_learning->getStudentsForElectiveByCourse($academic_id_Arr[$key], $course_id, $termidarr[$key],'electives');
                             //echo '<pre>'; print_r($raw_data);exit;
                            $newData[$value] = $this->selectData($raw_data[$value], array('students', 'student_id', 'stu_id','father_name','reg_no','exam_roll','short_code','roll_no'), count($raw_data[$value]));
                            foreach ($newData[$value] as $Studkey => $newVal) {

                                $newVal['term_id'] = $termidarr[$key];
                                $newVal['academic_id'] = $academic_id_Arr[$key];

                                $newVal['attended_class'] = $res_batch_attendance['attended_class'];

                                if($newVal['stu_id'] == $res_batch_attendance['u_id'] && !empty($id))
                                $Category_data[] = $newVal;
                                else if(empty($id))
                                $Category_data[] = $newVal;
                            }

                        }
                    }
                }
                $this->view->category_data = $Category_data;
            }
        }
    }
    //Student details for daily attenddance
    public function ajaxGetStudentsDetailsForDailyAttendAction(){
        $term_model = new Application_Model_TermMaster();
        $course_learning = new Application_Model_ElectiveSelection();
        $core_course_learning = new Application_Model_Corecourselearning();
        $daily_attendance = new Application_Model_DailyAttendanceModel();
        $academic_model = new Application_Model_Academic();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $ct_id = $this->_getParam("ct_id");
            $employee_id = $this->_getParam("employee_id");
            $department_id = $this->_getParam("department_id");
            $course_id = $this->_getParam("course_id");
            $term_id = $this->_getParam("term_id");
            $grade_allocate_id = $this->_getParam("grade_allocate_id");
            $honors_id= $this->_getParam("honors_id");
            $session_id= $this->_getParam("session_id");
            $degree_id= $this->_getParam("degree_id");
            $id = $this->_getParam("id");
            $componentmaster = new Application_Model_Component();

            //====Ashutosh==//
            $componentmaster = new Application_Model_Component();
            //====[end]====//
            $this->view->weightage = $componentmaster->getActiveRecords();
            //echo $session_id;
            if(!empty($id)){
                $res_batch_attendance = $daily_attendance->getRecordById($id);
                $Category_data['id'] = $res_batch_attendance;
                $Category_data['short_code'] = $res_batch_attendance['batch'];
                $this->view->category_data = $Category_data['id'];
                //echo "<pre>";print_r($Category_data);
                //exit;    
            }     
            else{
               $res_batch_attendance['u_id'] = ''; 
               //$res_batch_attendance['attended_class'] = ''; 
            
                $yearArr = $term_model->getRecordByCmnTerms($term_id,$session_id);
               // echo '<pre>';
                $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
                $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
                $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));


                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
              
                if(!empty($session_id)){
                  $academic_res_on_dept = $academic_model->getAcademicOnDept($honors_id,$session_id,$degree_id);
                  $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));
                    foreach($academic_id_Arr as $key => $value){
                        if(!in_array($value, $academic_res_on_dept)){
                            unset($academic_id_Arr[$key]);
                        }
                    }
                }
                $StudentPortal_model = new Application_Model_StudentPortal();
                $this->view->course_id = $course_id;
                $filter_acad = array();
                foreach ($academic_id_Arr as $key => $value) {
                    if ($value) {
                        if($degree_id!=1){
                           
                           
                            $newData[$value] = $StudentPortal_model->getstudentsdetailattend($value,$term_id);  
                            
                            if($course_id){
                            $raw_data[$value] = $course_learning->getStudentsForElectiveByCourse($academic_id_Arr[$key], $course_id, $termidarr[$key],'electives');
                            
                            if(count($newData[$value])>0){
                            $newData[$value] = $this->selectData($raw_data[$value], array('students', 'student_id', 'stu_id','father_name','reg_no','exam_roll','short_code','roll_no','stu_status'), count($raw_data[$value]));}}
                            if(!in_array($newVal['academic_id'],$filter_acad)){
                                $filter_acad[]= $value;
                                foreach ($newData[$value] as $Studkey => $newVal) {

                                    $newVal['term_id'] = $termidarr[$key];
                                    $newVal['academic_id'] = $academic_id_Arr[$key];

                                    $newVal['attended_class'] = $res_batch_attendance['attended_class'];

                                    if($newVal['stu_id'] == $res_batch_attendance['u_id'] && !empty($id))
                                    $Category_data[] = $newVal;
                                    
                                //echo '<pre>'; print_r($Category_data);exit;
                                        else if(empty($id))
                                    $Category_data[] = $newVal;
                                }
                            }
                        }
                        else if (!$ct_id) {
                           
                            $newData[$value] = $StudentPortal_model->getstudentsdetailattend($value,$term_id);  
                           
                            if(!in_array($newVal['academic_id'],$filter_acad)){
                                $filter_acad[]= $value;
                                foreach ($newData[$value] as $Studkey => $newVal) {

                                    $newVal['term_id'] = $termidarr[$key];
                                    $newVal['academic_id'] = $academic_id_Arr[$key];

                                    $newVal['attended_class'] = $res_batch_attendance['attended_class'];

                                    if($newVal['stu_id'] == $res_batch_attendance['u_id'] && !empty($id))
                                    $Category_data[] = $newVal;
                                        else if(empty($id))
                                    $Category_data[] = $newVal;
                                }
                            }
                        }else{
                            
                            $raw_data[$value] = $course_learning->getStudentsForElectiveByCourse($academic_id_Arr[$key], $course_id, $termidarr[$key],'electives');
                             //echo '<pre>'; print_r($raw_data);exit;
                            $newData[$value] = $this->selectData($raw_data[$value], array('students', 'student_id', 'stu_id','father_name','reg_no','exam_roll','short_code','roll_no'), count($raw_data[$value]));
                            foreach ($newData[$value] as $Studkey => $newVal) {

                                $newVal['term_id'] = $termidarr[$key];
                                $newVal['academic_id'] = $academic_id_Arr[$key];

                                $newVal['attended_class'] = $res_batch_attendance['attended_class'];

                                if($newVal['stu_id'] == $res_batch_attendance['u_id'] && !empty($id))
                                $Category_data[] = $newVal;
                                else if(empty($id))
                                $Category_data[] = $newVal;
                            }

                        }
                    }
                }
                //echo '<pre>'; print_r($Category_data);exit;
                $this->view->category_data = $Category_data;
            }
        }
    }
    
    
    //get department for employee
    public function ajaxGetDepartmentByEmplAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $emp_id = $this->_getParam("employee_id");
            //print_r($short_code); die;
            $department_model = new Application_Model_Department();
            $result = $department_model->getRecordByEmpId($emp_id);


            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['id'] . '" >' . $val['department'] . '</option>';
            }
        }die;
    }
    //To check existed daily batch attendance
    public function ajaxCheckExistedDailyAttendanceAction(){
        $dailyAttendanceModel = new Application_Model_DailyAttendanceModel();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()){
            $effective_date = $this->_getParam("effective_date"); 
            $date = explode('/',$effective_date);
            $effective_date= $date[2]."/".$date[1]."/".$date[0];
            $data['effective_date']=$effective_date;
            
            $session = $this->_getParam("session");
            $term_id = $this->_getParam("term_id");
            $degree_id = $this->_getParam("degree_id");
            $period = $this->_getParam("period");
            $teacher_dept = $this->_getParam("teacher_dept");
            $employee_id = $this->_getParam("employee_id");
           //echo $ge_id; exit;
            $existedData=$dailyAttendanceModel->get_existed_daily_attendance_data($term_id,$session,$data['effective_date'],$degree_id,$period,$teacher_dept,$employee_id);
           
            echo $existedData['period'];
            
        }die;
    }
    //To delete daily attendance
    public function ajaxDeleteDailyAttendAction(){
        $dailyAttendanceModel = new Application_Model_DailyAttendanceModel();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()){
            $delete_id = $this->_getParam("delete_id"); 
            
            $dailyAttendanceModel->deleteDailyAttendance($delete_id);
            
        }die;
    }

    public function ajaxGetDailyAttendAction(){
        $dailyAttendanceModel = new Application_Model_DailyAttendanceModel();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()){
            $delete_id = $this->_getParam("delete_id"); 
            
            $data=$dailyAttendanceModel->getDailyAttendance($delete_id);
            echo$data['id'] ;
            
        }die;
    }
}