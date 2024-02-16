<?php

class GradeAllocationController extends Zend_Controller_Action {

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
    public $cur_datetime = NULL;
    private $accessConfig = NULL;
    private $Aecc_course = Null;

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
        $this->view->administrator_role = $this->roleConfig;
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
        $this->cur_datetime = date('Y-m-d H:i:s');
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        if ($data->role_id == 0)
            $this->_redirect('student-portal/grade-sheet');
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
        $this->view->action_name = 'gradeallocation';
        $this->view->sub_title_name = 'gradeallocation';
        $this->accessConfig->setAccess('SA_ACAD_GRADE_ALLOCATION');
        $GradeAllocation_model = new Application_Model_GradeAllocation();
        $GradeAllocation_form = new Application_Form_GradeAllocation();
        $batchAttendance_form = new Application_Form_BatchAttendance();
        $GradeAllocatonItems_model = new Application_Model_GradeAllocationItems();
        $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
        $academic_model = new Application_Model_Academic();
        $student_info = new Application_Model_StudentPortal();
        $grade_id = $this->_getParam("id");
        //print_r($feehead_id);die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $GradeAllocation_form;
if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
     $this->view->csrftokn=$token;
        switch ($type) {
            case "add":
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocation_form->getValues();
                        if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){
                        // $data['term_id'] = $this->getRequest()->getPost('term_id');
                        $data['course_id'] = $this->getRequest()->getPost('course_id');
                        $grade = $this->getRequest()->getPost('grade');

                        $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();


                        foreach (array_filter($grade['student_id']) as $k => $student_id) {


                            $academic_id = $grade['academic_id_' . $student_id . ''][0];
                            $data['term_id'] = $grade['term_id_' . $student_id . ''][0];
                            $data['academic_id'] = $academic_id;

                            //======================changed coz of term and academic is in array form====================//  
                            $studentNum = $student_info->getstudents($data['academic_id']);
                            

                            if (count($studentNum) > 0) {
                                $data['added_by'] = $this->login_storage->id;
                                $data['added_date'] = $this->cur_datetime;
                                $data['added_by_ip_address'] = $_SERVER['REMOTE_ADDR'];
                                if($_POST['department']){
                                    $data['department'] = $data['department'];
                                }
                                else {
                                    $academic_details = $academic_model->getRecord($data['academic_id']);
                                        $data['department'] = $academic_details['department'];
                                    }
                                    $data['flag'] = 'R';
                                //  print_r($data);die;
                                $grade_id = $GradeAllocation_model->isGradeAllocated1($academic_id, $data['employee_id'], $data['term_id'], $data['course_id']);
                                if (!$grade_id){
                                    $last_insert_id = $GradeAllocation_model->insert($data);
                                }
                                else{
                                    $last_insert_id = $grade_id;
                                }
                              
                                $Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
                                $penalty = $Coursewisepenalties_model->getStudentAbsenceRecord($academic_id, $data['term_id']);


                                $penalty_master = array('academic_id' => $academic_id, 'term_id' => $data['term_id'], 'status' => 0);


                                if (empty($penalty)) {
                                    $penalty_master_id = $Coursewisepenalties_model->insert($penalty_master);
                                } else {
                                    $penalty_master_id = $penalty['id'];
                                }
                            }

                            //==========================================[END]================================================//
                                
                                $grade_data['grade_allocation_id'] = $last_insert_id;
                                $grade_data['student_id'] = $student_id;
                                $grade_data['grade_value'] = implode(",", $grade['grade_value_' . $student_id . '']);
                                $grade_data['number_value'] = implode(",", $grade['number_value_' . $student_id . '']);
                                
                                
                                $grade_data['component_id'] = implode(",", $grade['component_id']);
                                //print_r($grade_data);
                                $GradeAllocatonItems_model->insert($grade_data);
                                $penalty_item = $CoursewisepenaltiesItems_model->getStudentTermPenalty($penalty_master_id, $data['term_id'], $student_id);
                                
                                //==========panelties is stoped saving ===============//
                                  //$penalty_value = $this->getRequest()->getPost('student_penalties_' . $student_id);
                                //=============[END]===============================//
                                $penalty_value = '0';
                                //Fetching Credit value
                                $Corecourselearning_model = new Application_Model_Corecourselearning();
                                $cc_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($academic_id, $data['term_id'], $data['course_id']);
                                if (empty($penalty_item)) {//If penalty item row not exists, insert it as a new row
                                    $item_data['id'] = $penalty_master_id;
                                    $item_data['term_id'] = $data['term_id'];
                                    $item_data['student_id'] = $student_id;
                                    $item_data['academic_courses'] = $data['course_id'];
                                    $item_data['academic_credits'] = !empty($cc_detail['credit_value'])?$cc_detail['credit_value']:0;
                                    $item_data['absence'] = $penalty_value;
                                    $CoursewisepenaltiesItems_model->insert($item_data);
                                } else {//If penalty item exist, update row
                                    $penalty_courses = explode(',', $penalty_item['academic_courses']);
                                    $penalty_credits = explode(',', $penalty_item['academic_credits']);
                                    $penalty_absense = explode(',', $penalty_item['absence']);

                                    $penalty_courses[] = $data['course_id'];
                                    $penalty_credits[] = $penalty_credits;
                                    $penalty_absense[] = $penalty_value;

                                    $penalty_courses = implode(',', $penalty_courses);
                                    $penalty_credits = implode(',', $penalty_credits);
                                    $penalty_absense = implode(',', $penalty_absense);

                                    $item_data['academic_courses'] = $penalty_courses;
                                    $item_data['academic_credits'] = !empty($penalty_credits)?$penalty_credits:0;
                                    $item_data['absence'] = $penalty_absense;
                                    $where = 'item_id = ' . $penalty_item['item_id'];
                                    //print_r($item_data);echo $where;exit;
                                    $CoursewisepenaltiesItems_model->update($item_data, $where);
                                }
                            } 
                            unset($_SESSION["token"]);
                            $_SESSION['message_class'] = 'alert-success';
                            $this->_flashMessenger->addMessage('Grade Allocation Successfully added');
                           $this->_redirect('grade-allocation/index');
                              }else {
                        $message="Invalid Token";
		        $_SESSION['message_class'] = 'alert-danger';
                       $this->_flashMessenger->addMessage($message);
                      $this->_redirect('grade-allocation/index');
                }   }
                        } //die;

                        
                    } 
                
                break;
            case 'edit':
                $result1 = $GradeAllocation_model->getRecord($grade_id);



                $result2 = $GradeAllocation_model->getGrouped($result1['session'], $result1['employee_id'], $result1['cmn_terms'], $result1['course_id']);



                //$this->view->result_grade = $result_grade;
                //echo '<pre>',print_r($result_grade);die;

                $GradeAllocation_form->populate($result1);
                $this->view->grade_allocate_id = $result2[0]['grade_arr'];

                //Checking if it approved by D
                $GradeAllocationReportItems_model = new Application_Model_GradeAllocationReportItems();

                $isExist1 = $GradeAllocationReportItems_model->isGradeReportPublished($result1['academic_id'], $result1['term_id'], $result1['course_id']);

                $this->view->isGradeReportPublished = $isExist1;

                $this->view->grade_detail = $result1;

                //$this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocation_form->getValues();
                        $grade = $this->getRequest()->getPost('grade');
                       if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){
                       /************ Saving Previous data in Log table *************/
                     foreach(explode(',',$result2[0]['grade_arr']) as $ex_key => $gra_value){   
                         $grade_id = $gra_value;
                          
                        $grade_log = array();
                        $grade_log['master'] = $result1;
                        
                        $grade_log['items'] = $GradeAllocatonItems_model->getRecords($grade_id);
                        $GradeAllocationLog = new Application_Model_GradeAllocationLog();
                        $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();
                        
                if(!isset($_POST['reval_date'])){
                      $GradeAllocatonItems_model->trashItems($grade_id);  
                }
                else{
                if(empty($_POST['reval_date']))
            {$this->_refresh(5,'/academic/grade-allocation/index/type/edit/id/'.$grade_id,"Revaluation Date Should not be empty !");}}
                    foreach (array_filter($grade['student_id']) as $k => $student_id) {
                            
                            
                         //   echo "<pre>";print_r($grade);exit;
                              if($grade['academic_id_' . $student_id . '_'.$grade_id][0]){
                                        $academic_id = $grade['academic_id_' . $student_id . '_'.$grade_id][0];
                                        $data['term_id'] = $grade['term_id_' . $student_id . '_'.$grade_id][0];
                                        $data['academic_id'] = $academic_id;

                              $log_col_val = array(
                            'grade_id' => $grade_id,
                            'grade_detail' => json_encode($grade_log),
                            'grade_type' => 'CORE',
                            'added_date' => $this->cur_datetime,
                            'added_by' => $this->login_storage->id,
                            'ip_address' => $_SERVER['REMOTE_ADDR'],
                            'updated_by' =>$_SERVER['REMOTE_ADDR'],
                            'updated_date' => $this->cur_datetime,
                            'flag' => 'R'
                        );
                              
                        $GradeAllocationLog->insert($log_col_val);
                        /*                         * ********** Saving Previous data in Log table ************ */
                        foreach (explode(',', $result2[0]['grade_arr']) as $ex_key => $gra_value) {
                            $grade_id = $gra_value;

                            $grade_log = array();
                            $grade_log['master'] = $result1;
                            $grade_log['updated_by']  = $_SERVER['REMOTE_ADDR'];
                            $grade_log['updated_date']  =  $this->cur_datetime;
                            $grade_log['items'] = $GradeAllocatonItems_model->getRecords($grade_id);
                            $GradeAllocationLog = new Application_Model_GradeAllocationLog();
                            $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();

                            if(!isset($_POST['reval_date']))
                            $GradeAllocatonItems_model->trashItems($grade_id);

                            foreach (array_filter($grade['student_id']) as $k => $student_id) {


                                //   echo "<pre>";print_r($grade);exit;
                                if ($grade['academic_id_' . $student_id . '_' . $grade_id][0]) {
                                    $academic_id = $grade['academic_id_' . $student_id . '_' . $grade_id][0];
                                    $data['term_id'] = $grade['term_id_' . $student_id . '_' . $grade_id][0];
                                    $data['academic_id'] = $academic_id;

                                    $log_col_val = array(
                                        'grade_id' => $grade_id,
                                        'grade_detail' => json_encode($grade_log),
                                        'grade_type' => 'CORE',
                                        'added_date' => $this->cur_datetime,
                                        'added_by' => $this->login_storage->id,
                                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                                       
                                    );

                                    $GradeAllocationLog->insert($log_col_val);
                                    /*                                     * ********** Saving Previous data in Log table ************ */

//                        $data['term_id'] = $this->getRequest()->getPost('term_id');
                                    $data['course_id'] = $this->getRequest()->getPost('course_id');
//                        $academic_id = $this->getRequest()->getPost('academic_id');
                                    $isFinalSubmit = $this->getRequest()->getPost('final_submit');
                                    if ($isFinalSubmit) {
                                        $data['published_by_faculty'] = 1;
                                        $data['published_by_faculty_date'] = $this->cur_datetime;
                                    }
                                    
                                    
                                      unset($data['department']);
                                    $data['flag'] = 'R';
                                  
                                    
                                    //$data['component_id'] = $this->getRequest()->getPost('component_id');
                                    $GradeAllocation_model->update($data, array('grade_id=?' => $grade_id));

                                    //Inserting/Updating Penalties
                                    $Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
                                    // $penalty = $Coursewisepenalties_model->getStudentAbsenceRecord($academic_id, $data['term_id']);
                                    $penalty_master = array('academic_id' => $academic_id, 'term_id' => $data['term_id'], 'status' => 0);
                                    if (empty($penalty)) {
                                        $penalty_master_id = $Coursewisepenalties_model->insert($penalty_master);
                                    } else {
                                        $penalty_master_id = $penalty['id'];
                                    }

                                    $grade_data['grade_allocation_id'] = $grade_id;
                                    $grade_data['student_id'] = $student_id;
                                    $grade_data['grade_value'] = implode(",", $grade['grade_value_' . $student_id . '_' . $grade_id]);
                                    $grade_data['number_value'] = implode(",", $grade['number_value_' . $student_id . '_' . $grade_id]);
                                    $grade_data['component_id'] = implode(",", $grade['component_id']);
                                    if(isset($_POST['reval_date'])){
                                        $date_reval = explode("-",$_POST['reval_date']);
                                          $grade_data['reval_date'] = $date_reval[2]."-".$date_reval[1]."-".$date_reval[0]." ".date("h:i:s");
                                    $GradeAllocatonItems_model->update($grade_data, array('grade_allocation_id=?' => $grade_id,"student_id=?"=>$student_id,"number_value!=?"=>$grade_data['number_value']));
                                    }
                                    else
                                    {
                                        $GradeAllocatonItems_model->insert($grade_data) ;
                                    }

                                    //print_r($penalty_courses);print_r($penalty_credits);exit;
//                            $penalty_value = $this->getRequest()->getPost('student_penalties_' . $student_id);
                                    $penalty_value = 0;
                                    //Getting Credit Value of the Course
                                    $Corecourselearning_model = new Application_Model_Corecourselearning();
                                    $cc_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($academic_id, $data['term_id'], $data['course_id']);
                                    $penalty_item = $CoursewisepenaltiesItems_model->getStudentTermPenalty($penalty_master_id, $data['term_id'], $grade_data['student_id']);
                                    if (empty($penalty_item)) {//If penalty item row not exists, insert it as a new row
                                        $item_data['id'] = $penalty_master_id;
                                        $item_data['term_id'] = $data['term_id'];
                                        $item_data['student_id'] = $student_id;
                                        $item_data['academic_courses'] = $data['course_id'];
                                        $item_data['academic_credits'] = !empty($cc_detail['credit_value']) ? $cc_detail['credit_value'] : 0;
                                        $item_data['absence'] = $penalty_value;
                                        $CoursewisepenaltiesItems_model->insert($item_data);
                                    } else {
                                        $penalty_item_id = $penalty_item['item_id']; //Getting unique id of penalty item

                                        $penalty_item['academic_courses'] = trim($penalty_item['academic_courses']);
                                        if (empty($penalty_item['academic_courses'])) {
                                            $penalty_courses = array();
                                            $penalty_credits = array();
                                            $penalty_absense = array();
                                        } else {
                                            $penalty_courses = explode(',', $penalty_item['academic_courses']);
                                            $penalty_credits = explode(',', $penalty_item['academic_credits']);
                                            $penalty_absense = explode(',', $penalty_item['absence']);
                                        }


                                        if (empty($penalty_courses)) {//If there is no value in column, insert it simply
                                            $penalty_courses[] = $data['course_id'];
                                            $penalty_absense[] = $penalty_value;
                                            $penalty_credits[] = $cc_detail['credit_value'];
                                        } else {
                                            if (in_array($data['course_id'], $penalty_courses)) {//If course id already exits, it means penalty is already entered for this course,so we need to replace penalty value on same order
                                                $course_id_pos = array_search($data['course_id'], $penalty_courses); //Find the index position of course id
                                                $penalty_courses[$course_id_pos] = $data['course_id'];
                                                $penalty_credits[$course_id_pos] = $cc_detail['credit_value'];
                                                $penalty_absense[$course_id_pos] = $penalty_value;
                                            } else {//If Course_id alredy exist, replace its value. In this case, no need to update course_id and credit_value
                                                $penalty_courses[] = $data['course_id'];
                                                $penalty_credits[] = !empty($penalty_credits) ? $penalty_credits : 0;
                                                $penalty_absense[] = $penalty_value;
                                            }
                                        }
                                        //print_r($penalty_courses);print_r($penalty_credits);exit;
                                        $penalty_courses = implode(',', $penalty_courses);
                                        $penalty_credits = implode(',', $penalty_credits);
                                        $penalty_absense = implode(',', $penalty_absense);
                                        $item_data['academic_courses'] = $penalty_courses;
                                        $item_data['academic_credits'] = $penalty_credits;
                                        $item_data['absence'] = $penalty_absense;
                                        $where = 'item_id = ' . $penalty_item_id;
                                        //print_r($item_data);echo $where;exit;
                                        $CoursewisepenaltiesItems_model->update($item_data, $where);
                                    }
                                } else {
                                    continue;
                                }
                            }
                        }
                        unset($_SESSION["token"]);
                        $_SESSION['message_class'] = 'alert-success';
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('grade-allocation/index');
                        
                            
//               
                    } 
                }
                
                }
                
                   }else {
                     $message="Invalid Token";
		    $_SESSION['message_class'] = 'alert-danger';
                     $this->_flashMessenger->addMessage($message);
                    $this->_redirect('grade-allocation/index');
                }   }
                                }
                
              
                                }
                break;
            default:
                $this->view->form = $batchAttendance_form;
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                //echo $empl_id;exit;
                if (in_array($role_id, $this->roleConfig)) {
                    $result = $GradeAllocation_model->getRecordsNew();
                } else {
                    $result = $GradeAllocation_model->getRecordsByFacultyIdNew($empl_id);
                    //echo "<pre>";print_r($result);exit;
                }

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
               
    public function backPaperAction() {
        $this->view->action_name = 'gradeallocation';
        $this->view->sub_title_name = 'gradeallocation';
        $this->accessConfig->setAccess('SA_ACAD_BACK_GRADE_ALLOCATION');
        $GradeAllocation_model = new Application_Model_GradeAllocation();
        $GradeAllocation_form = new Application_Form_GradeAllocation();
        $batchAttendance_form = new Application_Form_BatchAttendance();
        $GradeAllocatonItems_model = new Application_Model_BackGradeAllocationItems();
        $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
        $academic_model = new Application_Model_Academic();
        $student_info = new Application_Model_StudentPortal();
        $grade_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $GradeAllocation_form;

  if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
          $this->view->csrftokn=$token;
        switch ($type) {
            case "add":
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocation_form->getValues();
                         if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){
                        // $data['term_id'] = $this->getRequest()->getPost('term_id');
                        $data['course_id'] = $this->getRequest()->getPost('course_id');
                        $grade = $this->getRequest()->getPost('grade');

                        $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();

                        // echo "<pre>";print_r($grade);exit;

                        foreach (array_filter($grade['student_id']) as $k => $student_id) {


                            $academic_id = $grade['academic_id_' . $student_id . ''][0];
                            $data['term_id'] = $grade['term_id_' . $student_id . ''][0];
                            $data['academic_id'] = $academic_id;

                            //======================changed coz of term and academic is in array form====================//  
                            $studentNum = $student_info->getstudents($data['academic_id']);

                            if (count($studentNum) > 0) {
                                $data['added_by'] = $this->login_storage->id;
                                $data['added_date'] = $this->cur_datetime;
                                $data['added_by_ip_address'] = $_SERVER['REMOTE_ADDR'];
                                if($_POST['department']){
                                        $data['department'] = $data['department'];
                                }
                                else {
                                    $academic_details = $academic_model->getRecord($data['academic_id']);
                                        $data['department'] = $academic_details['department'];
                                    }
                                    
                                    $data['flag'] = 'B';
                                    
                                $grade_id = $GradeAllocation_model->isGradeAllocated1($academic_id, $data['employee_id'], $data['term_id'], $data['course_id'],'B');

                                if (!$grade_id){
                                    $last_insert_id = $GradeAllocation_model->insert($data);
                                }
                                else{
                                    $last_insert_id = $grade_id;
                                }

                                $Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
                                $penalty = $Coursewisepenalties_model->getStudentAbsenceRecord($academic_id, $data['term_id']);


                                $penalty_master = array('academic_id' => $academic_id, 'term_id' => $data['term_id'], 'status' => 0);


                                if (empty($penalty)) {
                                    $penalty_master_id = $Coursewisepenalties_model->insert($penalty_master);
                                } else {
                                    $penalty_master_id = $penalty['id'];
                                }
                            }

                            //==========================================[END]================================================//
                                
                                $grade_data['grade_allocation_id'] = $last_insert_id;
                                $grade_data['student_id'] = $student_id;
                                $grade_data['grade_value'] = implode(",", $grade['grade_value_' . $student_id . '']);
                                $grade_data['number_value'] = implode(",", $grade['number_value_' . $student_id . '']);
                                
                                
                                $grade_data['component_id'] = implode(",", $grade['component_id']);
                                //print_r($grade_data);
                                $GradeAllocatonItems_model->insert($grade_data);
                                $penalty_item = $CoursewisepenaltiesItems_model->getStudentTermPenalty($penalty_master_id, $data['term_id'], $student_id);
                                
                                //==========panelties is stoped saving ===============//
                                  //$penalty_value = $this->getRequest()->getPost('student_penalties_' . $student_id);
                                //=============[END]===============================//
                                $penalty_value = '0';
                                //Fetching Credit value
                                $Corecourselearning_model = new Application_Model_Corecourselearning();
                                $cc_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($academic_id, $data['term_id'], $data['course_id']);
                                if (empty($penalty_item)) {//If penalty item row not exists, insert it as a new row
                                    $item_data['id'] = $penalty_master_id;
                                    $item_data['term_id'] = $data['term_id'];
                                    $item_data['student_id'] = $student_id;
                                    $item_data['academic_courses'] = $data['course_id'];
                                    $item_data['academic_credits'] = !empty($cc_detail['credit_value'])?$cc_detail['credit_value']:0;
                                    $item_data['absence'] = $penalty_value;
                                    $CoursewisepenaltiesItems_model->insert($item_data);
                                } else {//If penalty item exist, update row
                                    $penalty_courses = explode(',', $penalty_item['academic_courses']);
                                    $penalty_credits = explode(',', $penalty_item['academic_credits']);
                                    $penalty_absense = explode(',', $penalty_item['absence']);

                                    $penalty_courses[] = $data['course_id'];
                                    $penalty_credits[] = $penalty_credits;
                                    $penalty_absense[] = $penalty_value;

                                    $penalty_courses = implode(',', $penalty_courses);
                                    $penalty_credits = implode(',', $penalty_credits);
                                    $penalty_absense = implode(',', $penalty_absense);

                                    $item_data['academic_courses'] = $penalty_courses;
                                    $item_data['academic_credits'] = !empty($penalty_credits)?$penalty_credits:0;
                                    $item_data['absence'] = $penalty_absense;
                                    $where = 'item_id = ' . $penalty_item['item_id'];
                                    //print_r($item_data);echo $where;exit;
                                    $CoursewisepenaltiesItems_model->update($item_data, $where);
                                }
                            } //die;
                            unset($_SESSION["token"]);
                         $_SESSION['message_class'] = 'alert-success';
                        $this->_flashMessenger->addMessage('Grade Allocation Successfully added');
                        $this->_redirect('grade-allocation/back-paper');

                            }else {
                     $message="Invalid Token";
		    $_SESSION['message_class'] = 'alert-danger';
                     $this->_flashMessenger->addMessage($message);
                    $this->_redirect('grade-allocation/back-paper');
                }   }
                        } //die;

                        
                    } 
                
                break;
            case 'edit':
                $result1 = $GradeAllocation_model->getRecord($grade_id);

                

                $result2 = $GradeAllocation_model->getGrouped($result1['session'], $result1['employee_id'], $result1['cmn_terms'], $result1['course_id'],'B');


                //$this->view->result_grade = $result_grade;
                //echo '<pre>',print_r($result_grade);die;

                $GradeAllocation_form->populate($result1);
                $this->view->grade_allocate_id = $result2[0]['grade_arr'];

                //Checking if it approved by D
                $GradeAllocationReportItems_model = new Application_Model_GradeAllocationReportItems();

                $isExist1 = $GradeAllocationReportItems_model->isGradeReportPublished($result1['academic_id'], $result1['term_id'], $result1['course_id']);

                $this->view->isGradeReportPublished = $isExist1;

                $this->view->grade_detail = $result1;
                //$this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocation_form->getValues();
                        // echo '<pre>',print_r($data); echo $token;die;
                        if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){
                        $grade = $this->getRequest()->getPost('grade');
                       /************ Saving Previous data in Log table *************/
                     foreach(explode(',',$result2[0]['grade_arr']) as $ex_key => $gra_value){   
                         $grade_id = $gra_value;
                          
                        $grade_log = array();
                        $grade_log['master'] = $result1;
                        
                        $grade_log['items'] = $GradeAllocatonItems_model->getRecords($grade_id);
                        $GradeAllocationLog = new Application_Model_GradeAllocationLog();
                        $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();
                        
                        if(!$_POST['reval']){
                      $GradeAllocatonItems_model->trashItems($grade_id);
                        }
                        else
                         if(empty($_POST['reval_date'])){
                     {$this->_refresh(5,'/academic/grade-allocation/index/type/edit/id/'.$grade_id,"Revaluation Date Should not be empty !");}}
                        foreach (array_filter($grade['student_id']) as $k => $student_id) {
                            
                            
                         //   echo "<pre>";print_r($grade);exit;
                              if($grade['academic_id_' . $student_id . '_'.$grade_id][0]){
                                        $academic_id = $grade['academic_id_' . $student_id . '_'.$grade_id][0];
                                        $data['term_id'] = $grade['term_id_' . $student_id . '_'.$grade_id][0];
                                        $data['academic_id'] = $academic_id;

                              $log_col_val = array(
                            'grade_id' => $grade_id,
                            'grade_detail' => json_encode($grade_log),
                            'grade_type' => 'CORE',
                            'added_date' => $this->cur_datetime,
                            'added_by' => $this->login_storage->id,
                            'ip_address' => $_SERVER['REMOTE_ADDR']
                        );
                              
                        $GradeAllocationLog->insert($log_col_val);
                        /*                         * ********** Saving Previous data in Log table ************ */
                        foreach (explode(',', $result2[0]['grade_arr']) as $ex_key => $gra_value) {
                            $grade_id = $gra_value;

                            $grade_log = array();
                            $grade_log['master'] = $result1;

                            $grade_log['items'] = $GradeAllocatonItems_model->getRecords($grade_id);
                            $GradeAllocationLog = new Application_Model_GradeAllocationLog();
                            $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();

                              if(!$_POST['reval'])
                         $GradeAllocatonItems_model->trashItems($grade_id);

                            foreach (array_filter($grade['student_id']) as $k => $student_id) {


                                //   echo "<pre>";print_r($grade);exit;
                                if ($grade['academic_id_' . $student_id . '_' . $grade_id][0]) {
                                    $academic_id = $grade['academic_id_' . $student_id . '_' . $grade_id][0];
                                    $data['term_id'] = $grade['term_id_' . $student_id . '_' . $grade_id][0];
                                    $data['academic_id'] = $academic_id;

                                    $log_col_val = array(
                                        'grade_id' => $grade_id,
                                        'grade_detail' => json_encode($grade_log),
                                        'grade_type' => 'CORE',
                                        'added_date' => $this->cur_datetime,
                                        'added_by' => $this->login_storage->id,
                                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                                        'updated_by' =>$_SERVER['REMOTE_ADDR'],
                            'updated_date' => $this->cur_datetime,
                            'flag' => 'B'
                                    );

                                    $GradeAllocationLog->insert($log_col_val);
                                    /*                                     * ********** Saving Previous data in Log table ************ */

//                        $data['term_id'] = $this->getRequest()->getPost('term_id');
                                    $data['course_id'] = $this->getRequest()->getPost('course_id');
//                        $academic_id = $this->getRequest()->getPost('academic_id');
                                    $isFinalSubmit = $this->getRequest()->getPost('final_submit');
                                    if ($isFinalSubmit) {
                                        $data['published_by_faculty'] = 1;
                                        $data['published_by_faculty_date'] = $this->cur_datetime;
                                    }
                                      
                                        unset($data['department']);
                                
                                
                                    
                                    $data['flag'] = 'B';
                                    //$data['component_id'] = $this->getRequest()->getPost('component_id');
                                    $GradeAllocation_model->update($data, array('grade_id=?' => $grade_id));

                                    //Inserting/Updating Penalties
                                    $Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
                                    // $penalty = $Coursewisepenalties_model->getStudentAbsenceRecord($academic_id, $data['term_id']);
                                    $penalty_master = array('academic_id' => $academic_id, 'term_id' => $data['term_id'], 'status' => 0);
                                    if (empty($penalty)) {
                                        $penalty_master_id = $Coursewisepenalties_model->insert($penalty_master);
                                    } else {
                                        $penalty_master_id = $penalty['id'];
                                    }

                                    $grade_data['grade_allocation_id'] = $grade_id;
                                    $grade_data['student_id'] = $student_id;
                                    $grade_data['grade_value'] = implode(",", $grade['grade_value_' . $student_id . '_' . $grade_id]);
                                    $grade_data['number_value'] = implode(",", $grade['number_value_' . $student_id . '_' . $grade_id]);
                                    $grade_data['component_id'] = implode(",", $grade['component_id']);
                                  
                                    
                                    if($_POST['reval']){
                                          $date_reval = explode("-",$_POST['reval_date']);
                                        $grade_data['reval_date'] = $date_reval[2]."-".$date_reval[1]."-".$date_reval[0]." ".date("h:i:s");
                                      $GradeAllocatonItems_model->update($grade_data, array('grade_allocation_id=?' => $grade_id,"student_id=?"=>$student_id,"number_value!=?"=>$grade_data['number_value']));}
                                    else{
                                    $GradeAllocatonItems_model->insert($grade_data);}
                                    //print_r($penalty_courses);print_r($penalty_credits);exit;
//                            $penalty_value = $this->getRequest()->getPost('student_penalties_' . $student_id);
                                    $penalty_value = 0;
                                    //Getting Credit Value of the Course
                                    $Corecourselearning_model = new Application_Model_Corecourselearning();
                                    $cc_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($academic_id, $data['term_id'], $data['course_id']);
                                    $penalty_item = $CoursewisepenaltiesItems_model->getStudentTermPenalty($penalty_master_id, $data['term_id'], $grade_data['student_id']);
                                    if (empty($penalty_item)) {//If penalty item row not exists, insert it as a new row
                                        $item_data['id'] = $penalty_master_id;
                                        $item_data['term_id'] = $data['term_id'];
                                        $item_data['student_id'] = $student_id;
                                        $item_data['academic_courses'] = $data['course_id'];
                                        $item_data['academic_credits'] = !empty($cc_detail['credit_value']) ? $cc_detail['credit_value'] : 0;
                                        $item_data['absence'] = $penalty_value;
                                        $CoursewisepenaltiesItems_model->insert($item_data);
                                    } else {
                                        $penalty_item_id = $penalty_item['item_id']; //Getting unique id of penalty item

                                        $penalty_item['academic_courses'] = trim($penalty_item['academic_courses']);
                                        if (empty($penalty_item['academic_courses'])) {
                                            $penalty_courses = array();
                                            $penalty_credits = array();
                                            $penalty_absense = array();
                                        } else {
                                            $penalty_courses = explode(',', $penalty_item['academic_courses']);
                                            $penalty_credits = explode(',', $penalty_item['academic_credits']);
                                            $penalty_absense = explode(',', $penalty_item['absence']);
                                        }


                                        if (empty($penalty_courses)) {//If there is no value in column, insert it simply
                                            $penalty_courses[] = $data['course_id'];
                                            $penalty_absense[] = $penalty_value;
                                            $penalty_credits[] = $cc_detail['credit_value'];
                                        } else {
                                            if (in_array($data['course_id'], $penalty_courses)) {//If course id already exits, it means penalty is already entered for this course,so we need to replace penalty value on same order
                                                $course_id_pos = array_search($data['course_id'], $penalty_courses); //Find the index position of course id
                                                $penalty_courses[$course_id_pos] = $data['course_id'];
                                                $penalty_credits[$course_id_pos] = $cc_detail['credit_value'];
                                                $penalty_absense[$course_id_pos] = $penalty_value;
                                            } else {//If Course_id alredy exist, replace its value. In this case, no need to update course_id and credit_value
                                                $penalty_courses[] = $data['course_id'];
                                                $penalty_credits[] = !empty($penalty_credits) ? $penalty_credits : 0;
                                                $penalty_absense[] = $penalty_value;
                                            }
                                        }
                                        //print_r($penalty_courses);print_r($penalty_credits);exit;
                                        $penalty_courses = implode(',', $penalty_courses);
                                        $penalty_credits = implode(',', $penalty_credits);
                                        $penalty_absense = implode(',', $penalty_absense);
                                        $item_data['academic_courses'] = $penalty_courses;
                                        $item_data['academic_credits'] = $penalty_credits;
                                        $item_data['absence'] = $penalty_absense;
                                        $where = 'item_id = ' . $penalty_item_id;
                                        //print_r($item_data);echo $where;exit;
                                        $CoursewisepenaltiesItems_model->update($item_data, $where);
                                          
                                    }
                                } else {
                                    continue;
                                }
                            }
                        }
                        unset($_SESSION["token"]);
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('grade-allocation/back-paper');
                    } 
                }
                
                }
                 }else {
                     $message="Invalid Token";
		    $_SESSION['message_class'] = 'alert-danger';
                     $this->_flashMessenger->addMessage($message);
                    $this->_redirect('grade-allocation/back-paper');
                }   }
                
                                }}
                break;
            default:
                $this->view->form = $batchAttendance_form;
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                //echo $empl_id;exit;
                if (in_array($role_id, $this->roleConfig)) {
                    $result = $GradeAllocation_model->getRecordsNew('B');
                } else {
                    $result = $GradeAllocation_model->getRecordsByFacultyIdNew($empl_id,'B');
                    //echo "<pre>";print_r($result);exit;
                }

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
               

    public function ajaxGetGradeDetailsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $course_id = $this->_getParam('course_id');
            $term_id = $this->_getParam("term_id");
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getEmployeeTerms($academic_year_id, $department_id, $employee_id, $term_id, $course_id);
            $this->view->result = $result;
        }
    }

    public function ajaxGetGradeDetailsTrAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
               $pay = $this->_getParam("examtype");
            $attendance = $this->_getParam("attend");
            $archive = $this->_getParam("archive");
            $prev = $this->_getParam("prev");
            
            $term_model = new Application_Model_TermMaster();
            $tr_report = new Application_Model_TabulationReport();
            $academic_details = new Application_Model_Academic();
            $department = new Application_Model_Department();
            $dept_id = $academic_details->getRecord($academic_year_id)['department'];
            $session = $academic_details->getRecord($academic_year_id)['session'];
            $degree_id = $department->getRecord($dept_id);
            $dept_name = $degree_id['department'];
            $degree_id = $degree_id['degree_id'];
            if($degree_id)
               $passpercent = $this->getPasspercent($degree_id,$session);
                
                
            $id  = $tr_report->getRecordsAcademicTerm($academic_year_id,$term_id);
            $result = $term_model->getTermOnDat1($term_id, $academic_year_id);
            $this->view->passpercent = $passpercent;
            $this->view->id =$id;
            $this->view->result = $result;
            $this->view->acad = $academic_year_id;
            $this->view->term = $term_id;
            $this->view->dept_name = $dept_name;
            $this->view->pay =$pay;
             $this->view->archive =$archive;
              $this->view->prev =$prev;
            $this->view->attend =$attendance;
        }
//            $htmlcontent = $this->view->render('grade-allocation/ajax-get-grade-details-tr.phtml');
//            echo $htmlcontent;
//                exit;
//         if ($check == 'admin' || $mode == 'view') {
//                echo $htmlcontent;
//                exit;
//            }//======for PDF
//            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $stuname . '-' .$filename,'L',$pge_height );
//        }
    }
    
    
    
       public function ajaxGetGradeDetailsTrAllAction() {
           $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
              $archive = $this->_getParam("archive");
            $term_model = new Application_Model_TermMaster();
            $tr_report = new Application_Model_TabulationReport();
            $academic_details = new Application_Model_Academic();
            $department = new Application_Model_Department();
            $dept_id = $academic_details->getRecord($academic_year_id)['department'];
            $session =$academic_details->getRecord($academic_year_id)['session'];
            $degree_id = $department->getRecord($dept_id);
            $dept_name = $degree_id['department'];
            $degree_id = $degree_id['degree_id'];
            if($degree_id)
               $passpercent = $this->getPasspercent($degree_id,$session);
                
                
           // $id  = $tr_report->getRecordsAcademicTerm($academic_year_id,$term_id);
            $result = $term_model->getRecordByAcademicId( $academic_year_id);
            $this->view->passpercent = $passpercent;
            $this->view->id ='';
            $this->view->session = $session; 
            $this->view->result = $result;
            $this->view->acad = $academic_year_id;
            $this->view->term = $term_id;
            $this->view->archive =$archive;
            $this->view->dept_name = $dept_name;
        }
//            $htmlcontent = $this->view->render('grade-allocation/ajax-get-grade-details-tr.phtml');
//            echo $htmlcontent;
//                exit;
//         if ($check == 'admin' || $mode == 'view') {
//                echo $htmlcontent;
//                exit;
//            }//======for PDF
//            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $stuname . '-' .$filename,'L',$pge_height );
//        }
    }
    
    
    public function getGradeDetailsTrAction() {
        //$this->_helper->layout->disableLayout();
       // if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_id");
            $term_id = $this->_getParam("term");
            $term_model = new Application_Model_TermMaster();
            $tr_report = new Application_Model_TabulationReport();
            $academic_details = new Application_Model_Academic();
            $department = new Application_Model_Department();
            $dept_id = $academic_details->getRecord($academic_year_id)['department'];
           $session =$academic_details->getRecord($academic_year_id)['session'];
            
            $degree_id = $department->getRecord($dept_id);
            $dept_name = $degree_id['department'];
            $pdfheader = 'pwc';
            $pdffooter = 'footer';
            $degree_id = $degree_id['degree_id'];
           
            if($degree_id)
               $passpercent = $this->getPasspercent($degree_id,$session);
                
                
            $id  = $tr_report->getRecordsAcademicTerm($academic_year_id,$term_id);
            $result = $term_model->getTermOnDat1($term_id, $academic_year_id);
            
            $this->view->passpercent = $passpercent;
            $this->view->id =$id;
            $this->view->result = $result;
            $this->view->acad = $academic_year_id;
            $this->view->term = $term_id;
            $this->view->dept_name = $dept_name;
        //}
            $htmlcontent = $this->view->render('grade-allocation/get-grade-details-tr.phtml');
            echo $htmlcontent;
                exit;
//         if ($check == 'admin' || $mode == 'view') {
//                echo $htmlcontent;
//                exit;
//            }//======for PDF
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $file,'L',$pge_height );
//        }
    }
    public function ajaxGetGradeDetailsTrBackAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()){
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
             $pay = $this->_getParam("examtype");
            $term_model = new Application_Model_TermMaster();
            $tr_report = new Application_Model_TabulationReport();
            $academic_details = new Application_Model_Academic();
            $department = new Application_Model_Department();
              $dept_id = $academic_details->getRecord($academic_year_id)['department'];
           $session =$academic_details->getRecord($academic_year_id)['session'];
            $id  = $tr_report->getRecordsAcademicTerm($academic_year_id,$term_id,'B');
            $result = $term_model->getTermOnDat1($term_id, $academic_year_id);
            $degree_id = $department->getRecord($dept_id);
            $degree_id = $degree_id['degree_id'];
            
               if($degree_id)
               $passpercent = $this->getPasspercent($degree_id,$session);
               
               
               $this->view->passpercent = $passpercent;  
            $this->view->id =$id;
            $this->view->pay =$pay;
            $this->view->result = $result;
        }
    }

    public function ajaxGetGradeDetailsNewAction() {
        $course_learning = new Application_Model_Corecourselearning();
        $Aeccge_course = new Application_Model_Aeccge();
        $term_model = new Application_Model_TermMaster();
        $academic_model = new Application_Model_Academic();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $session_id = $this->_getParam("session_id");
            $course_id = $this->_getParam('course_id');
            $cmn_terms = $this->_getParam("cmn_term");
            $cc_id = $this->_getParam("cc_id");
            $ge_id = $this->_getParam("ge");
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $grade_allocation_id = $this->_getParam("grade_allocate_id");
            $yearArr = $term_model->getRecordByCmnTerms($cmn_terms, $session_id);
            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));
            $ge_course_all = array();
            foreach ($academic_id_Arr as $key => $value) {
                $ge_course[$key] = $Aeccge_course->getRecordByDepartment($department_id, $academic_id_Arr[$key], $termidarr[$key], $cc_id);
                $temp = $this->mergData($ge_course[$key], array('ge_id'), count($ge_course[$key]));
                foreach ($temp as $new_key => $new_val) {
                    $ge_course_all[] = $new_val;
                }
            }
            $ge_course = array();
            $ge_course = array_values($ge_course_all);
            //   $ge_course = $this->mergData($ge_course, array('ge_id'), count($ge_course));
            $main_res = array();
            $result = array();
            foreach ($academic_id_Arr as $acadkey => $value) {
                $academic_year_id = $academic_id_Arr[$acadkey];
                $term_id = $termidarr[$acadkey];
                $result[$term_id] = $EmployeeAllotment_model->getEmployeeTermsNew1($academic_year_id, $department_id, $employee_id, $term_id, $course_id, $cc_id);
                $checkResult = $this->mergData($result[$term_id], array('course_id'), count($result[$term_id]));
                if ($ge_id) {

                    $credit_course = $course_learning->getCoreCouseDetailByTermAcademicCourseidAll($academic_year_id, $term_id, $cc_id, $ge_id);
                    $credit_course = $this->mergData($credit_course, array('course_id'), count($credit_course));


                    foreach ($checkResult as $key => $value1) {
                        if (!in_array($value1, $credit_course)) {
                            unset($result[$term_id][$key]);
                        } else {
                            $main_res[$term_id][] = $result[$term_id][$key];
                        }
                    }
                } else {
                    $main_res[] = $result[$term_id];
                }
            }



            $result = array();
            $check_arr = array();

            foreach ($main_res as $key => $value) {
                foreach ($value as $key => $value) {
                    if (!in_array($value['course_id'], $check_arr)) {
                        $check_arr[] = $value['course_id'];
                        $result[] = $value;
                    }
                }
            }
            $refres_res = array();
            foreach ($result as $key => $value) {
                foreach ($academic_id_Arr as $acadkey => $acad_val) {
                    $result[$key]['academic_year_id'] = $academic_id_Arr[$acadkey];
                    $result[$key]['term_id'] = $termidarr[$acadkey];
                    $refres_res[] = $result[$key];
                }
            }




            $this->view->ge = !empty($ge_course) ? $ge_course : 0;
            $this->view->aecc = $this->aeccConfig[0];
            $this->view->result = $refres_res;
        }
    }


    public function ajaxGetExistingComponentAction() {
        $component = new Application_Model_EvaluationComponentsItems();
        $gradde_allocation = new Application_Model_GradeAllocationItems();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $ec_id = $this->_getParam("type_id");
            $result = $component->getRecordAll($ec_id);
            $result = $gradde_allocation->getRecordsOnComponentID($result['id']);
            echo count($result) > 0 ? 1 : 0;
            die;
        }
    }

    public function ajaxGetGradeDetailsPrintNewAction() {

        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
        $course_learning = new Application_Model_Corecourselearning();
        $Aeccge_course = new Application_Model_Aeccge();
        $grade_report_id = $this->_getParam("id");
        $this->view->grade_report_id = $grade_report_id;
        //print_r($grade_report_id); die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $GradeAllocationReport_form;
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
            $result = $GradeAllocationReport_model->getRecord($grade_report_id);

            $department_id = $result['department_id'];
            // print_r($department_id);die;
            $employee_id = $result['employee_id'];
            $academic_year_id = $result['academic_id'];
            $term_id = $result['term_id'];
            $course_id = $result['course_id'];
            $cc_id = $result['cc_id'];


            $ge_course = $Aeccge_course->getRecordByDepartment($department_id, $academic_year_id, $term_id, $cc_id);
            $ge_course = $this->mergData($ge_course, array('ge_id'), count($ge_course));


            //$ge_course = $this->mergData($ge_course, array('ge_id'), count($ge_course));

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result1 = $EmployeeAllotment_model->getEmployeeTermsNew1($academic_year_id, $department_id, $employee_id, $term_id, $course_id, $cc_id);




            $this->view->ge = !empty($ge_course) ? $ge_course : 0;
            $this->view->aecc = $this->aeccConfig[0];
            $this->view->result1 = $result1;
        }
    }

    //pdf Print

    public function gradePdfAction() {
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
        $grade_report_id = $this->_getParam("id");
        $this->view->grade_report_id = $grade_report_id;
        //print_r($grade_report_id); die;
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $result = $GradeAllocationReport_model->getRecord($grade_report_id);
        //print_r($result);die;
        $department_id = $result['department_id'];
        // print_r($department_id);die;
        $employee_id = $result['employee_id'];
        $academic_year_id = $result['academic_id'];
        $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
        $result1 = $EmployeeAllotment_model->getEmployeeTerms($academic_year_id, $department_id, $employee_id);
        //print_r($result1);die;
        $this->view->result1 = $result1;
        $pdfheader = $this->view->render('grade-allocation/pdfheader.phtml');
        $pdffooter = $this->view->render('grade-allocation/pdffooter.phtml');
        $htmlcontent = $this->view->render('grade-allocation/grade-pdf.phtml');
        $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Grade Allocation Report Details");
    }

    public function ajaxGetGradeAllocationAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $year = $this->_getParam("year");
            $GradeAllocation_model = new Application_Model_GradeAllocation();
            $TermMaster_model = new Application_Model_TermMaster();
            $Corecourselearning_model = new Application_Model_Corecourselearning();
            $StudentPortal_model = new Application_Model_StudentPortal();
            if ($academic_year_id) {
                $Category_data = $StudentPortal_model->getstudentsyearwise($academic_year_id, $year);
                $this->view->Category_data = $Category_data;
                $Term_data = $TermMaster_model->getGradeTermName($academic_year_id);
                //print_r($Term_data); die;
                //$this->view->Category_data = $Category_data;
                /*  foreach($Term_data as $k=>$val){
                  $term_id = $val['term_id'];
                  $Course_data=$Corecourselearning_model->getGradeCourseName($academic_year_id,$term_id);
                  //  print_r($Course_data);die;
                  } */
                //print_r($Course_data); die;
                $this->view->Term_data = $Term_data;
            }
        }
    }

    public function ajaxGetStudentDetailsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $employee_id = $this->_getParam("employee_id");
            $department_id = $this->_getParam("department_id");
            $course_id = $this->_getParam("course_id");
            $term_id = $this->_getParam("term_id");
            $grade_allocate_id = $this->_getParam("grade_allocate_id");



            //print_r($term_id);die;
            $GradeAllocation_model = new Application_Model_GradeAllocation();
            $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
            $weight = $EvaluationComponentsItems_model->getWeightage($academic_year_id, $employee_id, $department_id, $course_id, $term_id);

            $ReferenceGradeMasterItems_model = new Application_Model_ReferenceGradeMasterItems();
            $ref_grades = $ReferenceGradeMasterItems_model->getRecordsByAcademicId(0);
            $this->view->ref_grades = $ref_grades;

            $this->view->weightage = $weight;
            $StudentPortal_model = new Application_Model_StudentPortal();

            //Fetch Penalty items
            $penalty_all_items = array();
            $Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
            $penalty = $Coursewisepenalties_model->getStudentAbsenceRecord($academic_year_id, $term_id);

            if (!empty($penalty)) {
                $penalty_master_id = $penalty['id'];
                $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();
                $penalty_all_items = $CoursewisepenaltiesItems_model->getPenaltyItemByMasterIdTermId($penalty_master_id, $term_id);
            }

            $this->view->penalty_all_items = $penalty_all_items;
            $this->view->course_id = $course_id;

            if ($grade_allocate_id) {
                $result = $GradeAllocation_model->getStudentRecords($grade_allocate_id);
                //	print_r($result);die;
                $this->view->result = $result;
            } else {
                if ($academic_year_id) {
                    $Category_data = $StudentPortal_model->getstudentsdetails($academic_year_id);
                    $this->view->category_data = $Category_data;
                }
            }
        }
    }

    //=========================[DUPLICATE FUNCTION FOR STUDENT DETAILS]===================//
    public function ajaxGetStudentDetailsNewAction() {
        $term_model = new Application_Model_TermMaster();
        $course_learning = new Application_Model_ElectiveSelection();
        $academic_model = new Application_Model_Academic();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $ct_id = $this->_getParam("ct_id");
            $employee_id = $this->_getParam("employee_id");
            $department_id = $this->_getParam("department_id");
            $course_id = $this->_getParam("course_id");
            $term_id = $this->_getParam("term_id");
            $grade_allocate_id = $this->_getParam("grade_allocate_id");
            $honors_id = $this->_getParam("honors_id");
            $session_id = $this->_getParam("session_id");
            $degree_id = $this->_getParam("degree_id");
            $limit = $this->_getParam("limit");
            $offset = $this->_getParam("offset");
            $attend = $this->_getParam("attend");
            $pay = $this->_getParam("payment");

            if(!empty($ct_id))
            $yearArr = $term_model->getRecordByCmnTerms($term_id,$session_id);
            else
             $yearArr = $term_model->getRecordByCmnTerms($term_id,$session_id,$ct_id,$course_id);   
             $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
             $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
             
             $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));
            
             $GradeAllocation_model = new Application_Model_GradeAllocation();
            $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
            if(!$honors_id)
           $dept_arr = $GradeAllocation_model->getGroupebyCourseid($session_id,$course_id,'R');
            if (!empty($session_id)) {
                if(!$dept_arr)
                    $dept_arr = '';
                $academic_res_on_dept = $academic_model->getAcademicOnDept($honors_id, $session_id,$degree_id,explode(',',$dept_arr['department']));
                $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));
                foreach ($academic_id_Arr as $key => $value) {
                    if (!in_array($value, $academic_res_on_dept)) {
					  unset($academic_id_Arr[$key]);
                    }
                }
            }
            
            
            
                $weight= $EvaluationComponentsItems_model->getWeightage(0, $employee_id, $department_id, $course_id, 0);

            $ReferenceGradeMasterItems_model = new Application_Model_ReferenceGradeMasterItems();
            $ref_grades = $ReferenceGradeMasterItems_model->getRecordsByAcademicId(0, $degree_id,$session_id);

            $arr_ref_number_grade_all = $this->mergData($ref_grades, array('number_grade'), count($ref_grades));
            $arr_ref_number_grade_all = array_filter($arr_ref_number_grade_all, function($value) {
                return $value > 0;
            });
            $min_pass_percent = min($arr_ref_number_grade_all);
            $min_pass_percent = count($ref_grades) == 0 ? 0 : $min_pass_percent;
             
            $range = $ReferenceGradeMasterItems_model->getRecordsByNumgrade($min_pass_percent,$degree_id,$session_id);
            
            $min_pass_percent = count($ref_grades) == 0 ? 0 : min($range);
           
            $this->view->min_pass_percent = (int) $min_pass_percent;
            $this->view->ref_grades = $ref_grades;


            
            $this->view->weightage = $weight;
            $StudentPortal_model = new Application_Model_StudentPortal();
            
            $penalty_all_items = array();
            $Coursewisepenalties_model = new Application_Model_Coursewisepenalties();


            $this->view->course_id = $course_id;

            if ($grade_allocate_id) {

                $result = $GradeAllocation_model->getStudentRecordsNewWithAttendance($grade_allocate_id,$course_id,$ct_id);
               
    // echo "<pre>";print_r($result);exit;
                if (!empty($ct_id)) {
                    $check = array();
                    foreach ($academic_id_Arr as $key => $value) {
                        $raw_data[$value] = $course_learning->getStudentsForElectiveByCourse($academic_id_Arr[$key], $course_id, $termidarr[$key],'electives','',$ct_id,true);
                        $newData[$value] =$raw_data[$value];
                        foreach ($newData[$value] as $Studkey => $newVal) {
                            $newVal['term_id'] = $termidarr[$key];
                            $newVal['academic_id'] = $academic_id_Arr[$key];
                            $Category_data[] = $newVal;
                        }
                    }
             
                    $Category_data = $this->mergData($result, array('student_id'), count($result));
                    foreach ($result as $key => $value) {
                        if (!in_array($value['student_id'], $Category_data)) {
                            unset($result[$key]);
                        }
                    }
                }
                $this->view->result = $result;
            } else {
            
                foreach ($academic_id_Arr as $key => $value) {
                    if ($value) {
                        if (!$ct_id) {
                            $newData[$value] = $StudentPortal_model->getstudentsdetailsWithAttendence($value,$term_id,$course_id,$ct_id,100,0,$attend,$pay);
                        } else {
                            if($course_id)
                            $raw_data[$value] = $course_learning->getStudentsForElectiveByCourse($academic_id_Arr[$key], $course_id, $termidarr[$key], 'electives','',$ct_id,$attend,$pay);
                            $newData[$value] = $raw_data[$value];
                        }
                        foreach ($newData[$value] as $Studkey => $newVal) {
                            $newVal['term_id'] = $termidarr[$key];
                            $newVal['academic_id'] = $academic_id_Arr[$key];
                            $Category_data[] = $newVal;
                        }
                    }
                }
                $this->view->category_data = $Category_data;
                
                
            }
        }
    }

    //=================================[END DUPLICATE FUNCTION FOR STUDENT DETAILS]====================================//
    //=========================[DUPLICATE FUNCTION FOR STUDENT new DETAILS]===================//
       public function ajaxGetStudentDetailsNewBackAction() {
        $term_model = new Application_Model_TermMaster();
        $course_learning = new Application_Model_ElectiveSelection();
        $academic_model = new Application_Model_Academic();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $ct_id = $this->_getParam("ct_id");
            $employee_id = $this->_getParam("employee_id");
            $department_id = $this->_getParam("department_id");
            $course_id = $this->_getParam("course_id");
            $term_id = $this->_getParam("term_id");
            $grade_allocate_id = $this->_getParam("grade_allocate_id");
            $honors_id = $this->_getParam("honors_id");
            $session_id = $this->_getParam("session_id");
            $degree_id = $this->_getParam("degree_id");
            $pay = $this->_getParam("payment");
          
            $yearArr = $term_model->getRecordByCmnTerms($term_id,$session_id);

            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));
            
            $GradeAllocation_model = new Application_Model_GradeAllocation();
            $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
            if(!$honors_id)
           $dept_arr = $GradeAllocation_model->getGroupebyCourseid($session_id,$course_id,'B');

            if (!empty($session_id)) {
                if(!$dept_arr)
                    $dept_arr = '';
                $academic_res_on_dept = $academic_model->getAcademicOnDept($honors_id, $session_id,$degree_id,explode(',',$dept_arr['department']));
                $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));
                foreach ($academic_id_Arr as $key => $value) {
                    if (!in_array($value, $academic_res_on_dept)) {
					  unset($academic_id_Arr[$key]);
                    }
                }
            }
            
            
            
                $weight= $EvaluationComponentsItems_model->getWeightage(0, $employee_id, $department_id, $course_id, 0);

            $ReferenceGradeMasterItems_model = new Application_Model_ReferenceGradeMasterItems();
            $ref_grades = $ReferenceGradeMasterItems_model->getRecordsByAcademicId(0, $degree_id,$session_id);

            $arr_ref_number_grade_all = $this->mergData($ref_grades, array('number_grade'), count($ref_grades));
            $arr_ref_number_grade_all = array_filter($arr_ref_number_grade_all, function($value) {
                return $value > 0;
            });
            $min_pass_percent = min($arr_ref_number_grade_all);

            $min_pass_percent = count($ref_grades) == 0 ? 0 : $min_pass_percent;

            $range = $ReferenceGradeMasterItems_model->getRecordsByNumgrade($min_pass_percent,$degree_id,$session_id);



            $min_pass_percent = count($ref_grades) == 0 ? 0 : min($range);

            $this->view->min_pass_percent = (int) $min_pass_percent;
            $this->view->ref_grades = $ref_grades;


            // echo "<prE>";print_r($weight);exit;
            $this->view->weightage = $weight;
            $StudentPortal_model = new Application_Model_StudentPortal();
            $session_id = !$ct_id?'':$session_id;

            $this->view->course_id = $course_id;

            if ($grade_allocate_id) {
               
                $result = $GradeAllocation_model->getStudentRecordsNewBack($grade_allocate_id,true,$term_id);
             
                    $check = array();
                   
                    foreach ($academic_id_Arr as $key => $value) {
                        $raw_data[$value] = $course_learning->getStudentsForElectiveByBackCourse($academic_id_Arr[$key], $course_id, $term_id,  'electives',$session_id,1);
                        $newData[$value] = $raw_data[$value];
                        foreach ($newData[$value] as $Studkey => $newVal) {
                            $newVal['term_id'] = $termidarr[$key];
                            $newVal['academic_id'] = $academic_id_Arr[$key];
                            $Category_data[] = $newVal;
                        }
                    }
                    $Category_data = $this->mergData($result, array('student_id'), count($result));
                    foreach ($result as $key => $value) {
                        if (!in_array($value['student_id'], $Category_data)) {
                            unset($result[$key]);
                        }
                    }
                

                $this->view->result = $result;
            } else {
                $cat = array();
                foreach ($academic_id_Arr as $key => $value) {
                    if ($value) {
                       
                            if($course_id)
                            $raw_data[$value] = $course_learning->getStudentsForElectiveByBackCourse($academic_id_Arr[$key], $course_id, $term_id, 'electives',$session_id,$pay);
                            $newData[$value] = $raw_data[$value];
                      
                        foreach ($newData[$value] as $Studkey => $newVal) {
                            if(!in_array($newVal['student_id'],$cat)){
                            $newVal['term_id'] = $termidarr[$key];
                            $newVal['academic_id'] = $academic_id_Arr[$key];
                            $Category_data[] = $newVal;
                            $cat[] = $newVal['student_id'];
                            }
                    }
                }
                }
            }
                $this->view->category_data = $Category_data;
            
        }
    }

    //=================================[END DUPLICATE FUNCTION FOR STUDENT DETAILS]====================================//


    public function ajaxGetCourseGroupAction() {
        $term_model = new Application_Model_TermMaster();
        $course_learning = new Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {


            $cc_id = $this->_getParam("cc_id");
            $term_id = $this->_getParam("term_id");
            $session = $this->_getParam("session");
            $yearArr = $term_model->getRecordByCmnTerms($term_id, $session);

            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));


            $existData = array();
            foreach ($academic_id_Arr as $key => $value) {
                $newData[$value] = $course_learning->getCourseRecordbyCcId($academic_id_Arr[$key], $cc_id, $termidarr[$key]);
                foreach ($newData[$value] as $Studkey => $newVal) {
                    if (!in_array($newVal['ge_id'], $existData)) {
                        $existData[] = $newVal['ge_id'];
                        $courseData[] = $newVal;
                    }
                }
            }
            echo "<option value = ''>--Select--</option>";
            foreach ($courseData as $key => $value) {
                echo "<option value='{$value["ge_id"]}'>{$value["general_elective_name"]}</ >";
            }
        }die;
    }
    
    
    
    
      public function ajaxGetCourseGroupWithEmplAction() {
        $term_model = new Application_Model_TermMaster();
        $course_learning = new Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {


            $cc_id = $this->_getParam("cc_id");
            $term_id = $this->_getParam("term_id");
            $session = $this->_getParam("session");
            $empl_id = $this->_getParam("employee_id");
            $yearArr = $term_model->getRecordByCmnTerms($term_id, $session);

            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));


            $existData = array();
            foreach ($academic_id_Arr as $key => $value) {
                $newData[$value] = $course_learning->getCourseRecordbyCcIdEmpl1($academic_id_Arr[$key], $cc_id, $termidarr[$key],$empl_id);
                foreach ($newData[$value] as $Studkey => $newVal) {
                    if (!in_array($newVal['ge_id'], $existData)) {
                        $existData[] = $newVal['ge_id'];
                        $courseData[] = $newVal;
                    }
                }
            }
            echo "<option value = ''>--Select--</option>";
            foreach ($courseData as $key => $value) {
                echo "<option value='{$value["ge_id"]}'>{$value["general_elective_name"]}</ >";
            }
        }die;
    }
      public function ajaxGetCourseGroupWithEmplForDailyAction() {
        $term_model = new Application_Model_TermMaster();
        $course_learning = new Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
;
            $degree = $this->_getParam("degree_id");
            $empl_id = $this->_getParam("employee_id");
           
                $newData = $course_learning->getCourseRecordbyCcIdEmpForDaily($empl_id,$degree);
                
            
            echo "<option value = ''>--Select--</option>";
            foreach ($newData as $key => $value) {
                echo "<option value='{$value["ge_id"]}'>{$value["general_elective_name"]}</ >";
            }
        }die;
    }
    
    
    
    

    public function ajaxGetTermsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $grade_allocation_id = $this->_getParam("grade_allocate_id");

            if ($grade_allocation_id) {

                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getTerms($academic_year_id, $department_id, $employee_id);
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Terms</label>';
            echo '<select type="text" name="term_id" id="term_id" class="form-control">';
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['term_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . ' >' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

    public function ajaxGetCoursesAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $grade_allocation_id = $this->_getParam("grade_allocate_id");
            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getComponentCourses($academic_year_id, $department_id, $employee_id, $term_id);
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control">';
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {
                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

    public function ajaxGetCourseCodeAction() {
        $course_details = new Application_Model_Course();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $course_id = $this->_getParam("course_id");
            $result = $course_details->getRecord($course_id);

            echo $result['course_code'];
        } die;
    }

    public function ajaxGetBaseOfCourseAction() {
        $term_model = new Application_Model_TermMaster();
        $academic_model = new Application_Model_Academic();
        $course_learning = new Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {


            //$department_id = $this->_getParam("department_id");
            $department_id = '';
            $employee_id = $this->_getParam("employee_id");
            $ct_id = $this->_getParam("Ct_id");
            $term_id = $this->_getParam("term_id");
            $cmn_term = $term_id;
            $ge_id = $this->_getParam('ge_id');
            $cc_dept = $this->_getParam('cc_dept');
            $session = $this->_getParam('session');




            $grade_allocation_id = $this->_getParam("grade_allocate_id");

            $yearArr = $term_model->getRecordByCmnTerms($term_id, $session);
            

            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));
            //echo '<pre>'; print_r($academic_id_Arr);exit;
            //===============unset Academic according to session====================// 
            if (!empty($cc_dept)) {
                $academic_res_on_dept = $academic_model->getAcademicOnDept($cc_dept);
                $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));

                foreach ($academic_id_Arr as $key => $value) {
                    if (!in_array($value, $academic_res_on_dept)) {
                        unset($academic_id_Arr[$key]);
                    }
                }
            }


            // $academic_id_Arr = $term_model->getRecordByYearId($year_id);

            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $main_res = array();
            foreach ($academic_id_Arr as $key => $value) {
                $result[$key]['course'] = $EmployeeAllotment_model->getComponentCourses($academic_id_Arr[$key], $department_id, $employee_id, $termidarr[$key],$cc_dept);
                
                $credit_course[$key]['credit_course'] = $course_learning->getCoreCouseDetailByTermAcademicCourseidAll($academic_id_Arr[$key], $termidarr[$key], $ct_id, $ge_id,$cmn_term);
               
                $credit_course[$key]['credit_course'] = $this->mergData($credit_course[$key]['credit_course'], array('course_id'), count($credit_course[$key]['credit_course']));
                
              

                foreach ($result[$key]['course'] as $removal_key => $removal) {
                    foreach ($credit_course[$key]['credit_course'] as $credit_cecker => $credit_val) {
                        if (array_key_exists($credit_val, $result[$key]['course'])) {
                            $main_res[$credit_val] = $result[$key]['course'][$credit_val];
                        }
                    }
                }
            }
            echo '<div class="col-sm-3 employee_class course_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control chosen-select">';
            echo '<option value="">Select</option>';
            foreach ($main_res as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {
                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }
    //Get course by employee Id : kedar : 23 Nov 2020
    public function ajaxGetBaseOfCourseByEmployeeAction() {
        $term_model = new Application_Model_TermMaster();
        $academic_model = new Application_Model_Academic();
        $course_learning = new Application_Model_Corecourselearning();
        $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {


            $dept_id = $this->_getParam("department");
            $employee_id = $this->_getParam("employee_id");
            $ct_id = $this->_getParam("Ct_id");
            $term_id = $this->_getParam("term_id");
            $cmn_term = $term_id;
            $department_id='';
            $ge_id = $this->_getParam('ge_id');
            $cc_dept = $this->_getParam('cc_dept');
            $session = $this->_getParam('session');


            $academic_id = $academic_model->getAcademicId($dept_id,$session);
             
            $term_id = $term_model->getTermId($academic_id['academic_year_id'],$term_id);
            //echo '<pre>'; print_r($term_id);exit;

           
            //$result = $EmployeeAllotment_model->getComponentCoursesByEmployee($academic_id['academic_year_id'], $department_id, $employee_id, $term_id['term_id'],$cc_dept);
             
             $credit_course = $course_learning->getGeCouseDetailByEmployee($employee_id, $term_id['term_id'], $ct_id, $ge_id,$cmn_term);
            
            echo '<div class="col-sm-3 employee_class course_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control chosen-select">';
            echo '<option value="">Select</option>';
            foreach($credit_course as $k => $val){
                $selected = '';
                
                echo '<option value="' . $val['course_id']. '" ' . $selected . '>' . $val['course_name'] . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }
    //End
    //=====for back papers ============================//
    public function ajaxGetBaseOfBackCourseAction() {
        $term_model = new Application_Model_TermMaster();
        $academic_model = new Application_Model_Academic();
        $course_learning = new Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {


            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $ct_id = $this->_getParam("Ct_id");
            $term_id = $this->_getParam("term_id");
            $cmn_term = $term_id;
            $ge_id = $this->_getParam('ge_id');
            $cc_dept = $this->_getParam('cc_dept');
            $session = $this->_getParam('session');




            $grade_allocation_id = $this->_getParam("grade_allocate_id");

            $yearArr = $term_model->getRecordByCmnTerms($term_id, $session);


            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));




            if (!empty($cc_dept)) {
                $academic_res_on_dept = $academic_model->getAcademicOnDept($cc_dept);
                $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));

                foreach ($academic_id_Arr as $key => $value) {
                    if (!in_array($value, $academic_res_on_dept)) {
                        unset($academic_id_Arr[$key]);
                    }
                }
            }


            // $academic_id_Arr = $term_model->getRecordByYearId($year_id);

            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $main_res = array();

            foreach ($academic_id_Arr as $key => $value) {
                $result[$key]['course'] = $EmployeeAllotment_model->getComponentCourses($academic_id_Arr[$key], $department_id, $employee_id, $termidarr[$key],$cc_dept,'B');

                $credit_course[$key]['credit_course'] = $course_learning->getCoreCouseDetailByTermAcademicCourseidAll($academic_id_Arr[$key], $termidarr[$key], $ct_id, $ge_id,$cmn_term);
                $credit_course[$key]['credit_course'] = $this->mergData($credit_course[$key]['credit_course'], array('course_id'), count($credit_course[$key]['credit_course']));

                foreach ($result[$key]['course'] as $removal_key => $removal) {
                    foreach ($credit_course[$key]['credit_course'] as $credit_cecker => $credit_val) {
                        if (array_key_exists($credit_val, $result[$key]['course'])) {
                            $main_res[$credit_val] = $result[$key]['course'][$credit_val];
                        }
                    }
                }
            }

            echo '<div class="col-sm-3 employee_class course_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control chosen-select" required>';
            echo '<option value="">Select</option>';
            //print_r($main_res);
            foreach ($main_res as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {
                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }
//=================[END]====================================================================//
    
    
    public function ajaxGetCoursesEditNew1Action() {
        $term_model = new Application_Model_TermMaster();
        $academic_model = new Application_Model_Academic();
        $course_learning = new Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $ct_id = $this->_getParam("Ct_id");
            $term_id = $this->_getParam("term_id");
            $cmn_term = $term_id;
            $ge_id = $this->_getParam('ge_id');
            $cc_dept = $this->_getParam('cc_dept');
            $session = $this->_getParam('session');

            $grade_allocation_id = $this->_getParam("grade_allocate_id");
           

            $yearArr = $term_model->getRecordByCmnTerms($term_id, $session);
            
            

            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));
//            $year_id = $yearidarr[0];
//            
//
//            $academic_id_Arr = $term_model->getRecordByYearId($year_id, $term_id);
//
//
//            $academicYrId = $this->mergData($academic_id_Arr, array('academic_year_id'), count($academic_id_Arr));
//
//            $academic_id_Arr = array_values(array_unique($academicYrId));
            //===============unset Academic according to session====================//
            if (!empty($cc_dept)) {
                $academic_res_on_dept = $academic_model->getAcademicOnDept($cc_dept);
                $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));

                foreach ($academic_id_Arr as $key => $value) {
                    if (!in_array($value, $academic_res_on_dept)) {
                        unset($academic_id_Arr[$key]);
                    }
                }
            }

            // $academic_id_Arr = $term_model->getRecordByYearId($year_id);

            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                 
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);

            }

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $main_res = array();

            foreach ($academic_id_Arr as $key => $value) {
                $result[$key]['course'] = $EmployeeAllotment_model->getEditComponentCourses($academic_id_Arr[$key], $department_id, $employee_id, $termidarr[$key]);

                $credit_course[$key]['credit_course'] = $course_learning->getCoreCouseDetailByTermAcademicCourseidAll($academic_id_Arr[$key], $termidarr[$key], $ct_id, $ge_id,$cmn_term);
                $credit_course[$key]['credit_course'] = $this->mergData($credit_course[$key]['credit_course'], array('course_id'), count($credit_course[$key]['credit_course']));
                   

                foreach ($result[$key]['course'] as $removal_key => $removal) {
                    foreach ($credit_course[$key]['credit_course'] as $credit_cecker => $credit_val) {
                        if (array_key_exists($credit_val, $result[$key]['course'])) {
                            $main_res[$credit_val] = $result[$key]['course'][$credit_val];
                        }
                    }
                }
            }        
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control chosen-select" required>';
            echo '<option value="">Select</option>';


            foreach ($main_res as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }
    public function ajaxGetCoursesEditNew1BackAction() {
        $term_model = new Application_Model_TermMaster();
        $academic_model = new Application_Model_Academic();
        $course_learning = new Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $ct_id = $this->_getParam("Ct_id");
            $term_id = $this->_getParam("term_id");
            $cmn_term = $term_id;
            $ge_id = $this->_getParam('ge_id');
            $cc_dept = $this->_getParam('cc_dept');
            $session = $this->_getParam('session');

            $grade_allocation_id = $this->_getParam("grade_allocate_id");

            $yearArr = $term_model->getRecordByCmnTerms($term_id, $session);

            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));



//
//            $year_id = $yearidarr[0];
//            
//           
//
//            $academic_id_Arr = $term_model->getRecordByYearId($year_id, $term_id);
//
//
//            $academicYrId = $this->mergData($academic_id_Arr, array('academic_year_id'), count($academic_id_Arr));
//
//            $academic_id_Arr = array_values(array_unique($academicYrId));
            //===============unset Academic according to session====================// 
            if (!empty($cc_dept)) {
                $academic_res_on_dept = $academic_model->getAcademicOnDept($cc_dept);
                $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));

                foreach ($academic_id_Arr as $key => $value) {
                    if (!in_array($value, $academic_res_on_dept)) {
                        unset($academic_id_Arr[$key]);
                    }
                }
            }

            // $academic_id_Arr = $term_model->getRecordByYearId($year_id);

            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $main_res = array();

            foreach ($academic_id_Arr as $key => $value) {
                $result[$key]['course'] = $EmployeeAllotment_model->getEditComponentCourses($academic_id_Arr[$key], $department_id, $employee_id, $termidarr[$key]);

                $credit_course[$key]['credit_course'] = $course_learning->getCoreCouseDetailByTermAcademicCourseidAll($academic_id_Arr[$key], $termidarr[$key], $ct_id, $ge_id,$cmn_term);
                $credit_course[$key]['credit_course'] = $this->mergData($credit_course[$key]['credit_course'], array('course_id'), count($credit_course[$key]['credit_course']));

                foreach ($result[$key]['course'] as $removal_key => $removal) {
                    foreach ($credit_course[$key]['credit_course'] as $credit_cecker => $credit_val) {
                        if (array_key_exists($credit_val, $result[$key]['course'])) {
                            $main_res[$credit_val] = $result[$key]['course'][$credit_val];
                        }
                    }
                }
            }
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control chosen-select" required>';
            echo '<option value="">Select</option>';


            foreach ($main_res as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

    public function ajaxGetCoursesEditAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");

            $grade_allocation_id = $this->_getParam("grade_allocate_id");
            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }


            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getEditComponentCourses($academic_year_id, $department_id, $employee_id, $term_id);

            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control chosen-select">';
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

    //Ashutosh

    public function ajaxGetCoursesEditNewAction() {




        $term_model = new Application_Model_TermMaster();
        $academic_model = new Application_Model_Academic();
        $course_learning = new Application_Model_Corecourselearning();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {


            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $ct_id = $this->_getParam("ct_id");
            $term_id = $this->_getParam("term_id");
            $cmn_term = $term_id;
            $ge_id = $this->_getParam('ge_id');
            $cc_dept = $this->_getParam('cc_dept');
            $session = $this->_getParam('session');




            $grade_allocation_id = $this->_getParam("grade_allocate_id");

            $yearArr = $term_model->getRecordByCmnTerms($term_id, $session);

            $yearidarr = $this->mergData($yearArr, array('year_id'), count($yearArr));
            $termidarr = $this->mergData($yearArr, array('term_id'), count($yearArr));
            $academic_id_Arr = $this->mergData($yearArr, array('academic_year_id'), count($yearArr));




//            $year_id = $yearidarr[0];
//            
//           
//
//            $academic_id_Arr = $term_model->getRecordByYearId($year_id, $term_id);
//
//
//            $academicYrId = $this->mergData($academic_id_Arr, array('academic_year_id'), count($academic_id_Arr));
//
//            $academic_id_Arr = array_values(array_unique($academicYrId));
            //===============unset Academic according to session====================// 
            if (!empty($cc_dept)) {
                $academic_res_on_dept = $academic_model->getAcademicOnDept($cc_dept);
                $academic_res_on_dept = $this->mergData($academic_res_on_dept, array('academic_year_id'), count($academic_res_on_dept));

                foreach ($academic_id_Arr as $key => $value) {
                    if (!in_array($value, $academic_res_on_dept)) {
                        unset($academic_id_Arr[$key]);
                    }
                }
            }


            // $academic_id_Arr = $term_model->getRecordByYearId($year_id);

            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $main_res = array();

            foreach ($academic_id_Arr as $key => $value) {
                $result[$key]['course'] = $EmployeeAllotment_model->getEditComponentCoursesNew($academic_id_Arr[$key], $department_id, $employee_id, $termidarr[$key], $ct_id);

                $credit_course[$key]['credit_course'] = $course_learning->getCoreCouseDetailByTermAcademicCourseidAll($academic_id_Arr[$key], $termidarr[$key], $ct_id, $ge_id,$cmn_term);
                $credit_course[$key]['credit_course'] = $this->mergData($credit_course[$key]['credit_course'], array('course_id'), count($credit_course[$key]['credit_course']));

                foreach ($result[$key]['course'] as $removal_key => $removal) {
                    foreach ($credit_course[$key]['credit_course'] as $credit_cecker => $credit_val) {
                        if (array_key_exists($credit_val, $result[$key]['course'])) {
                            $main_res[$credit_val] = $result[$key]['course'][$credit_val];
                        }
                    }
                }
            }


            //        echo "<prE>";print_r($main_res);exit;


            $evalComponent = new Application_Model_EvaluationComponents();
            foreach ($main_res as $course_id => $course_name) {//========filter those course only have registered evaluation components
                $component_res = $evalComponent->getComponents(0, $department_id, $employee_id, 0, $course_id);

                if (count($component_res) == 0) {
                    unset($main_res[$course_id]);
                }
            }

            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control chosen-select">';
            echo '<option value="">Select</option>';
            foreach ($main_res as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {
                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

/// ramesh  //	
    public function ajaxGetEvaluationComponentsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");
            $grade_allocation_id = $this->_getParam("grade_allocate_id");
            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }
            $EvaluationComponents_model = new Application_Model_EvaluationComponents();
            $result = $EvaluationComponents_model->getComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id);
            /* echo '<div class="col-sm-3 employee_class">';
              echo  '<div class="form-group">';
              echo  '<label class="control-label">Components</label>';
              echo '<select type="text" name="component_id" id="component_id" class="form-control">';
              echo '<option value="">Select</option>';
              foreach($result as $k => $val)
              {
              $selected ='';
              if($k == $grade_allocate_result['component_id']){

              $selected = "selected";
              }
              echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';

              }
              echo '</select>';
              echo '</div></div>'; */
        }die;
    }

    public function ajaxGetGradeEvaluationComponentsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");

            $EvaluationComponents_model = new Application_Model_EvaluationComponents();
            $result = $EvaluationComponents_model->getGradeAddComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id);
            /* echo '<div class="col-sm-3 employee_class">';
              echo  '<div class="form-group">';
              echo  '<label class="control-label">Components</label>';
              echo '<select type="text" name="component_id" id="component_id" class="form-control">';
              echo '<option value="">Select</option>';
              foreach($result as $k => $val)
              {

              echo '<option value="'.$k.'">'.$val.'</option>';

              }
              echo '</select>';
              echo '</div></div>'; */
        }die;
    }
    
    
    public function ajaxGetPrevDateAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
         $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
               $term_model = new Application_Model_TermMaster();      
               $term_id1 = $term_model->getTermRecordsbycmn($academic_year_id,$term_id)['term_id'];
               if($term_id1){
              $term_id = $term_id1;}
            $GradeAllocationItems_model = new Application_Model_GradeAllocation();
          $result =  $GradeAllocationItems_model->getDistinctmonthofdeletedgrdes($academic_year_id,$term_id);
             echo '<option value="">--Select--</option>';
              foreach($result as $k => $val)
              {

              echo '<option value="'.$val['vald'].'">'.$val['display'].'</option>';

              }
            }die;
    }

/// ramesh  //		
    public function ajaxGetEmployeeAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            // print_r($academic_id); die;
            $HRMModel_model = new Application_Model_HRMModel();
            $result = $HRMModel_model->getEmployee($department_id);
            $val = in_array($_SESSION['admin_login']['admin_login']->role_id, $this->roleConfig);
            $empl_id = $_SESSION['admin_login']['admin_login']->empl_id;


            if ($empl_id && $val != 1) {
                foreach ($result as $key => $value) {
                    $result = array();
                    if ($empl_id == $key) {
                        $result[$key] = $value;
                        break;
                    }
                }
            }


            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }die;
    }

    public function gradeAllocationReportAction() {

        $this->view->action_name = 'Grade Allocation Report';
        $this->view->sub_title_name = 'grade-allocation-report';
        $this->accessConfig->setAccess('SA_ACAD_REVIEW_PUBLISH');
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
        $GradeAllocationReportItems_model = new Application_Model_GradeAllocationReportItems();
        //$GradeAllocatonReportItems_model = new Application_Model_GradeAllocationReportItems();
        $grade_report_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $GradeAllocationReport_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    $data = $GradeAllocationReport_form->getValues();
                    $data = $_POST;
                    $arr = array_values(array_unique($_POST['academic_id']));
                    foreach ($arr as $new_key => $new_val) {
                        unset($data['dataTable_length']);
                        unset($data['grade']);
                        unset($data['ge']);
                        $academic_term_arr = explode('_', $arr[$new_key]);
                        $academic = $academic_term_arr[0];
                        $term = $academic_term_arr[1];


                        $data['course_id'] = $this->getRequest()->getPost('course_id');
                        $data['academic_id'] = $academic;
                        $data['term_id'] = $term;
                        $data['added_by'] = $this->login_storage->id;
                        $data['added_date'] = $this->cur_datetime;
                        $data['added_by_ip_address'] = $_SERVER['REMOTE_ADDR'];

                        $last_insert_id = $GradeAllocationReport_model->insert($data);

                        $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
                        $employee_result = $EmployeeAllotment_model->getEmployeeTerms($academic, $data['department_id'], $data['employee_id'], $term, $data['course_id']);

                        $ge_id = new Application_Model_Corecourselearning();




                        for ($i = 0; $i < count($employee_result); $i++) {
                            $grade = $_POST['grade'];
                            for ($k = 0; $k < count($_POST['grade']['student_id_' . $academic . '_' . $term . '_' . $employee_result[$i]['course_id']]); $k++) {
                                $item_data['report_id'] = $last_insert_id;
                                $item_data['term_id'] = $employee_result[$i]['term_id'];
                                $item_data['course_id'] = $employee_result[$i]['course_id'];

                                $ge = $ge_id->getCoreCouseDetailByTermAcademicCourse($academic, $item_data['term_id'], $item_data['course_id']);

                                $item_data['student_id'] = $grade['student_id_' . $academic . '_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']][$k];
                                $item_data['ge_id'] = $ge['ge_id'] ? $ge['ge_id'] : 0;
                                $item_data['component_grades'] = $grade['grades_' . $academic . '_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']][$k];
                                $item_data['component_weightages'] = $grade['weightages_' . $academic . '_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']][$k];
                                $item_data['grade_point'] = $grade['grade_point_' . $academic . '_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']][$k];

                                $GradeAllocationReportItems_model->insert($item_data);
                            }
                        }
                    }

                    $this->_flashMessenger->addMessage('Grade Report Successfully added');

                    $this->_redirect('grade-allocation/grade-allocation-report');
                }


                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                //echo $empl_id;exit;

                $result = $GradeAllocationReport_model->getRecords();
                foreach ($result as $key => $course) {

                    $result[$key]['course_id'] = $GradeAllocationReport_model->getCourseInfo($course['course_id'])['course_name'];
                }


                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );

                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function tabulationRegisterAction() {


        $this->view->action_name = 'tr';
        $this->view->sub_title_name = 'trc';
        $this->accessConfig->setAccess('SA_ACAD_TRC');
    //    $this->view->type = $type;
       // $this->view->form = $tab_form;
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReportItems_model = new Application_Model_GradeAllocationReportItems();
        $tabulationReport = new Application_Model_TabulationReport();
        $ge_master = new Application_Model_Ge();
        $tabulationReportItem = new Application_Model_TabulationReportItems();
        $ge_id = new Application_Model_Corecourselearning();
        $GradeAllocationReport_form = new Application_Form_TabRegister();
        $this->view->form = $GradeAllocationReport_form;


        if ($this->getRequest()->isPost()) {
             if ($GradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
                    $data = $GradeAllocationReport_form->getValues();
                    $course_ids = array_unique($_POST['course_ids']);
                    $stu_ids = array_unique($_POST['stu_ids']);  
       
                    //===============tabl_register=====//
                    $academic = $data['academic_year_id'];
                             $term = $data['term_id'];
                             $datatblReport['academic_id'] = $academic;
                             $datatblReport['term_id'] = $term;
                             $datatblReport['added_by'] = $this->login_storage->id;
                             $datatblReport['added_date'] = $this->cur_datetime;
                             $datatblReport['added_by_ip_address'] = $_SERVER['REMOTE_ADDR']; 
                             $datatblReport['flag'] = 'R';
                             if($_POST['saved_id']==0){
                             $last_insert_id_tabl = $tabulationReport->insert($datatblReport);}
                             else
                             {
                                 
                                 $last_insert_id_tabl =$_POST['saved_id'];
                               // $tabulationReport->trashItems($last_insert_id_tabl);
                             }
                      $academic = $data['academic_year_id'];
                   //   echo "<pre>";print_r($stu_ids);exit;
                    foreach($stu_ids as $key => $stu_id){
                            
                             
                             
                             $datatblReportItems['tabl_id'] =  $last_insert_id_tabl;
                             $datatblReportItems['student_id'] =  $_POST['stu_ids'][$key];
                             $datatblReportItems['sgpa'] =  $_POST['sgpa'][$key];
                             $datatblReportItems['fail_in_ct_ids'] =  $_POST['fail_in'][$key];
                             $datatblReportItems['course_id'] =  $_POST['fcourse_ids'][$key];
                             $datatblReportItems['grade_point'] =  $_POST['gp'][$key];
                             $datatblReportItems['promotion_text'] =  $_POST['promotion'][$key];
                             $datatblReportItems['final_remarks'] =  $_POST['f_remark'][$key];
                             $datatblReportItems['total_credit_point'] =  $_POST['tot_credit_point'][$key];
                             $datatblReportItems['total_grade_point'] =  $_POST['tot_grade_point'][$key];
                             
                                if($_POST['saved_id']==0){
                            $tabulationReportItem->insert($datatblReportItems);
                              }
                              else
                              {
                                 $tabulationReportItem->update($datatblReportItems,array('tabl_id=?'=>$last_insert_id_tabl,'student_id=?'=>$datatblReportItems['student_id'])); 
                              }
                    }
                    
                    //===========================[END]=======================//
                   if($_POST['saved_id']==0){
                  foreach($course_ids as $course_key_index => $course_id){
                      
                      $data['course_id'] = $course_id;
                    
                    unset($data['academic_year_id']);
                    $term = $data['term_id'];
                    $data['academic_id'] = $academic;
                    $data['term_id'] = $term;
                    $data['added_by'] = $this->login_storage->id;
                    $data['added_date'] = $this->cur_datetime;
                      $data['flag'] = 'R';
                    $data['added_by_ip_address'] = $_SERVER['REMOTE_ADDR'];
     if(!empty($course_id))
     $last_insert_id = $GradeAllocationReport_model->insert($data);
      
      foreach($stu_ids as $key => $stu_id){
                    $grade = $_POST['grade'];
         
                for ($k = 0; $k < count($_POST['grade']['grades_'. $stu_id . $academic . '_' . $term . '_' . $course_id]); $k++) {
                    $item_data['report_id'] = $last_insert_id;
                    $item_data['term_id'] = $term;
                    $item_data['course_id'] = $course_id;
                    $item_data['tabl_id'] = $last_insert_id_tabl;
                    $ge = $ge_id->getCoreCouseDetailByTermAcademicCourse($academic, $item_data['term_id'], $item_data['course_id']);

                    $item_data['student_id'] = $stu_id;
                    $item_data['sort'] = '';
                    $item_data['ge_id'] = $ge['ge_id'] ? $ge['ge_id'] : 0;
                    if($item_data['ge_id'] != 0)
                    $name = $ge_master->getRecord($item_data['ge_id'])['general_elective_name'];
                    else
                        $name = '';
                    preg_match_all('/\bAECC/', $name, $output_array);
                    if(!empty($output_array[0]))
                     $item_data['sort'] = 3;
                    preg_match_all('/\bSEC/', $name, $output_array);
                    if(!empty($output_array[0]))
                        $item_data['sort'] = 4;
                    preg_match_all('/\bDSE/', $name, $output_array);
                    if(!empty($output_array[0]))
                        $item_data['sort'] = 5;
                    else if($item_data['ge_id'] == 0){
                        $item_data['sort'] = 1;
                    }
                else if(empty($item_data['sort'])){
                    $item_data['sort'] = 2;
                 }
                    
                    
                    $item_data['component_grades'] = $grade['grades_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    $item_data['component_weightages'] = $grade['weightages_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    $item_data['grade_point'] = $grade['grade_point_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    if(!empty($course_id))
                            $GradeAllocationReportItems_model->insert($item_data);
                    }
                  }
                  
                    }
             }
            $this->_flashMessenger->addMessage('Grade Report Successfully added');

            $this->_redirect('grade-allocation/tabulation-register');
             }
             
        } else {
            $messages = $this->_flashMessenger->getMessages();
            $this->view->messages = $messages;
        }
    }
    
    
    
      public function tabulationRegisterAllAction() {


        $this->view->action_name = 'tr';
        $this->view->sub_title_name = 'trc';
        $this->accessConfig->setAccess('SA_ACAD_TRC_ALL');
    //    $this->view->type = $type;
       // $this->view->form = $tab_form;
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReportItems_model = new Application_Model_GradeAllocationReportItems();
        $tabulationReport = new Application_Model_TabulationReport();
        $ge_master = new Application_Model_Ge();
        $tabulationReportItem = new Application_Model_TabulationReportItems();
        $ge_id = new Application_Model_Corecourselearning();
        $GradeAllocationReport_form = new Application_Form_TabRegister();
        $this->view->form = $GradeAllocationReport_form;


        if ($this->getRequest()->isPost()) {
             if ($GradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
                    $data = $GradeAllocationReport_form->getValues();
                    $course_ids = array_unique($_POST['course_ids']);
                    $stu_ids = array_unique($_POST['stu_ids']);  
       
                    //===============tabl_register=====//
                    $academic = $data['academic_year_id'];
                             $term = $data['term_id'];
                             $datatblReport['academic_id'] = $academic;
                             $datatblReport['term_id'] = $term;
                             $datatblReport['added_by'] = $this->login_storage->id;
                             $datatblReport['added_date'] = $this->cur_datetime;
                             $datatblReport['added_by_ip_address'] = $_SERVER['REMOTE_ADDR']; 
                             $datatblReport['flag'] = 'R';
                             if($_POST['saved_id']==0){
                             $last_insert_id_tabl = $tabulationReport->insert($datatblReport);}
                             else
                             {
                                 
                                 $last_insert_id_tabl =$_POST['saved_id'];
                               // $tabulationReport->trashItems($last_insert_id_tabl);
                             }
                      $academic = $data['academic_year_id'];
                   //   echo "<pre>";print_r($stu_ids);exit;
                    foreach($stu_ids as $key => $stu_id){
                            
                             
                             
                             $datatblReportItems['tabl_id'] =  $last_insert_id_tabl;
                             $datatblReportItems['student_id'] =  $_POST['stu_ids'][$key];
                             $datatblReportItems['sgpa'] =  $_POST['sgpa'][$key];
                             $datatblReportItems['fail_in_ct_ids'] =  $_POST['fail_in'][$key];
                             $datatblReportItems['course_id'] =  $_POST['fcourse_ids'][$key];
                             $datatblReportItems['grade_point'] =  $_POST['gp'][$key];
                             $datatblReportItems['promotion_text'] =  $_POST['promotion'][$key];
                             $datatblReportItems['final_remarks'] =  $_POST['f_remark'][$key];
                             $datatblReportItems['total_credit_point'] =  $_POST['tot_credit_point'][$key];
                             $datatblReportItems['total_grade_point'] =  $_POST['tot_grade_point'][$key];
                             
                                if($_POST['saved_id']==0){
                            $tabulationReportItem->insert($datatblReportItems);
                              }
                              else
                              {
                                 $tabulationReportItem->update($datatblReportItems,array('tabl_id=?'=>$last_insert_id_tabl,'student_id=?'=>$datatblReportItems['student_id'])); 
                              }
                    }
                    
                    //===========================[END]=======================//
                   if($_POST['saved_id']==0){
                  foreach($course_ids as $course_key_index => $course_id){
                      
                      $data['course_id'] = $course_id;
                    
                    unset($data['academic_year_id']);
                    $term = $data['term_id'];
                    $data['academic_id'] = $academic;
                    $data['term_id'] = $term;
                    $data['added_by'] = $this->login_storage->id;
                    $data['added_date'] = $this->cur_datetime;
                      $data['flag'] = 'R';
                    $data['added_by_ip_address'] = $_SERVER['REMOTE_ADDR'];
     if(!empty($course_id))
     $last_insert_id = $GradeAllocationReport_model->insert($data);
      
      foreach($stu_ids as $key => $stu_id){
                    $grade = $_POST['grade'];
         
                for ($k = 0; $k < count($_POST['grade']['grades_'. $stu_id . $academic . '_' . $term . '_' . $course_id]); $k++) {
                    $item_data['report_id'] = $last_insert_id;
                    $item_data['term_id'] = $term;
                    $item_data['course_id'] = $course_id;
                    $item_data['tabl_id'] = $last_insert_id_tabl;
                    $ge = $ge_id->getCoreCouseDetailByTermAcademicCourse($academic, $item_data['term_id'], $item_data['course_id']);

                    $item_data['student_id'] = $stu_id;
                    $item_data['sort'] = '';
                    $item_data['ge_id'] = $ge['ge_id'] ? $ge['ge_id'] : 0;
                    if($item_data['ge_id'] != 0)
                    $name = $ge_master->getRecord($item_data['ge_id'])['general_elective_name'];
                    else
                        $name = '';
                    preg_match_all('/\bAECC/', $name, $output_array);
                    if(!empty($output_array[0]))
                     $item_data['sort'] = 3;
                    preg_match_all('/\bSEC/', $name, $output_array);
                    if(!empty($output_array[0]))
                        $item_data['sort'] = 4;
                    preg_match_all('/\bDSE/', $name, $output_array);
                    if(!empty($output_array[0]))
                        $item_data['sort'] = 5;
                    else if($item_data['ge_id'] == 0){
                        $item_data['sort'] = 1;
                    }
                else if(empty($item_data['sort'])){
                    $item_data['sort'] = 2;
                 }
                    
                    
                    $item_data['component_grades'] = $grade['grades_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    $item_data['component_weightages'] = $grade['weightages_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    $item_data['grade_point'] = $grade['grade_point_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    if(!empty($course_id))
                            $GradeAllocationReportItems_model->insert($item_data);
                    }
                  }
                  
                    }
             }
            $this->_flashMessenger->addMessage('Grade Report Successfully added');

            $this->_redirect('grade-allocation/tabulation-register');
             }
             
        } else {
            $messages = $this->_flashMessenger->getMessages();
            $this->view->messages = $messages;
        }
    }
    
    
    public function tabulationRegisterBackAction() {
        $this->view->action_name = 'tr';
        $this->view->sub_title_name = 'trnc';
        $this->accessConfig->setAccess('SA_ACAD_TRNC');
       // $this->view->type = $type;
        //$this->view->form = $tab_form;
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReportItems_model = new Application_Model_BackGradeAllocationReportItems();
          $ge_master = new Application_Model_Ge();
        $backStudentDetails = new Application_Model_BackSelectionItems(); 
        $tabulationReport = new Application_Model_TabulationReport();
        $tabulationReportItem = new Application_Model_BackTabulationReportItems();
        $GradeAllocationReport_form = new Application_Form_TabRegister();
        $this->view->form = $GradeAllocationReport_form;
        if ($this->getRequest()->isPost()) {
             if ($GradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
                 
                    $data = $GradeAllocationReport_form->getValues();
                    $course_ids = array_unique($_POST['course_ids']);
                    $stu_ids = array_unique($_POST['stu_ids']);  
                   
                    //===============tabl_register=====//
                            $academic = $data['academic_year_id'];
                             $term = $data['term_id'];
                             $datatblReport['academic_id'] = $academic;
                             $datatblReport['term_id'] = $term;
                             $datatblReport['added_by'] = $this->login_storage->id;
                             $datatblReport['added_date'] = $this->cur_datetime;
                             $datatblReport['added_by_ip_address'] = $_SERVER['REMOTE_ADDR']; 
                             $datatblReport['flag'] = 'B';
                     if($_POST['saved_id']==0){
                             $last_insert_id_tabl = $tabulationReport->insert($datatblReport);}
                             else
                             {
                                 //$datatblReport['added_date'] = $this->cur_datetime;
                                 $last_insert_id_tabl =$_POST['saved_id'];
                                // $tabulationReport->trashBackItems($last_insert_id_tabl);
                             }
                      $academic = $data['academic_year_id'];
                    foreach($stu_ids as $key => $stu_id){
                            
                             
                             
                             $datatblReportItems['tabl_id'] =  $last_insert_id_tabl;
                             $datatblReportItems['student_id'] =  $_POST['stu_ids'][$key];
                             $datatblReportItems['sgpa'] =  $_POST['sgpa'][$key];
                             $datatblReportItems['fail_in_ct_ids'] =  $_POST['fail_in'][$key];
                              $datatblReportItems['course_id'] =  $_POST['fcourse_ids'][$key];
                              $datatblReportItems['grade_point'] =  $_POST['gp'][$key];
                             $datatblReportItems['total_credit_point'] =  $_POST['tot_credit_point'][$key];
                             $datatblReportItems['total_grade_point'] =  $_POST['tot_grade_point'][$key];
                              if($_POST['saved_id']==0){
                            $tabulationReportItem->insert($datatblReportItems);
                              }
                              else
                              {
                                 $tabulationReportItem->update($datatblReportItems,array('tabl_id=?'=>$last_insert_id_tabl,'student_id=?'=>$datatblReportItems['student_id'])); 
                              }
                    }
                    
                    //===========================[END]=======================//
            if($_POST['saved_id']==0){  
                  foreach($course_ids as $course_key_index => $course_id){
                      
                    $data['course_id'] = $course_id;
                    
                    unset($data['academic_year_id']);
                    $term = $data['term_id'];
                    $data['academic_id'] = $academic;
                    $data['term_id'] = $term;
                    $data['added_by'] = $this->login_storage->id;
                    $data['added_date'] = $this->cur_datetime;
                    $data['added_by_ip_address'] = $_SERVER['REMOTE_ADDR'];
                    $data['flag'] = 'B';
     $last_insert_id = $GradeAllocationReport_model->insert($data);
      
      foreach($stu_ids as $key => $stu_id){
                    $grade = $_POST['grade'];
            $ge_id = new Application_Model_Corecourselearning();
                    //============[Update back students]==================//
                if(!empty($_POST['c_id'][$key])){
                                $where = array( 'terms =?' => $term,
                                    'electives in (?)'=>explode(',',$_POST['c_id'][$key]),
                                    'students_id = ?'=>$stu_id);
                    $backStudentDetails->update(array('fail_status'=>1), $where);
                    }
//                    //===================[END Updating]=================//
                for ($k = 0; $k < count($_POST['grade']['grades_'. $stu_id . $academic . '_' . $term . '_' . $course_id]); $k++) {
                
                    $item_data['report_id'] = $last_insert_id;
                    $item_data['term_id'] = $term;
                    $item_data['course_id'] = $course_id;
                    $item_data['tabl_id'] = $last_insert_id_tabl;
                    $ge = $ge_id->getCoreCouseDetailByTermAcademicCourse($academic, $item_data['term_id'], $item_data['course_id']);

                    $item_data['student_id'] = $stu_id;
                    
                    $item_data['ge_id'] = $ge['ge_id'] ? $ge['ge_id'] : 0;
                    
                      if($item_data['ge_id'] != 0)
                    $name = $ge_master->getRecord($item_data['ge_id'])['general_elective_name'];
                    else
                        $name = '';
                    preg_match_all('/\bAECC/', $name, $output_array);
                    if(!empty($output_array[0]))
                     $item_data['sort'] = 3;
                    preg_match_all('/\bSEC/', $name, $output_array);
                    if(!empty($output_array[0]))
                        $item_data['sort'] = 4;
                    preg_match_all('/\bDSE/', $name, $output_array);
                    if(!empty($output_array[0]))
                        $item_data['sort'] = 5;
                    else if($item_data['ge_id'] == 0){
                        $item_data['sort'] = 1;
                    }
                else if(empty($item_data['sort'])){
                    $item_data['sort'] = 2;
                 }
                    $item_data['component_grades'] = $grade['grades_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    $item_data['component_weightages'] = $grade['weightages_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    $item_data['grade_point'] = $grade['grade_point_' . $stu_id . $academic . '_' . $term . '_' . $course_id][$k];
                    
                             
            $GradeAllocationReportItems_model->insert($item_data);
                }
                  }
                  
                    }
             }
            $this->_flashMessenger->addMessage('Grade Report Successfully added');

            $this->_redirect('grade-allocation/tabulation-register-back');
             }
             
        } else {
            $messages = $this->_flashMessenger->getMessages();
            $this->view->messages = $messages;
        }
    }
    
    
    public function ajaxGetStudentMarksAction(){
        
         $eval_items = new Application_Model_EvaluationComponentsItems();
    $course_learning = new Application_Model_ElectiveSelection();
                    $StudentPortal_model = new Application_Model_StudentPortal();
                      
                        $Corecourselearning_model = new Application_Model_Corecourselearning();
                         $acd_details = new Application_Model_Academic();
                        $deg_details = new Application_Model_Department();
                          $term_details = new Application_Model_TermMaster();
                             $semester_rule = new Application_Model_SemesterRule();
                              $course_details = new Application_Model_Course();
                                 $GradeAllocation_model = new Application_Model_GradeAllocation();
        
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $d_id = $acd_details->getRecord($item_result[0]['academic_year_id'])['department'];
                        $degree_id= $deg_details->getRecord($d_id)['degree_id'];
                      
                        $cmn_term = $term_details->getRecord($item_result[0]['term_id'])['cmn_terms'];
                     
                        $details = $semester_rule->checkRow($degree_id, $cmn_term);
               //========================[END]========================//
               
                    $category_data = $StudentPortal_model->getstudentsdetails($item_result[0]['academic_year_id'],$cmn_term);
                    
                    $this->view->d_id = $d_id;
                    $this->view->cmn_term = $cmn_term;
                    $this->view->details = $details;
                    $this->view->category_data = $category_data;
                    
            
            
    }
        
    }

    public function ajaxGetEmployeeValidationAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");
            $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
            $result = $GradeAllocationReport_model->getGradeAllocateCount($academic_year_id, $department_id, $employee_id, $term_id, $course_id);
            // echo'<pre>';print_r(count($result)); die;
            $counts = $result['employee_count'];
            // print_r($counts);die;
            echo json_encode($counts);
            die;
            //echo '<pre>'; print_r($counts); die;
            // $this->view->result = $result;
        }
    }

    public function ajaxGetEmployeeValidationNewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $term_id = $this->_getParam("term_id");
            $cc = $this->_getParam("ge");
            $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
            $result = $GradeAllocationReport_model->getGradeAllocateCount1($academic_year_id, $department_id, $employee_id, $term_id, $cc);
            // echo'<pre>';print_r(count($result)); die;
            $counts = $result['employee_count'];
            // print_r($counts);die;
            echo json_encode($counts);
            die;
            //echo '<pre>'; print_r($counts); die;
            // $this->view->result = $result;
        }
    }
    
    //Added by kedar to filter data :03 Dec
    public function ajaxGetStudentByCourseGroupAction(){
        
        $this->_helper->layout->disableLayout();
        $GradeAllocation_model = new Application_Model_GradeAllocation();
     
        
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $session_id = $this->_getParam("session_id");
            $degree_id = $this->_getParam("degree_id");
            $course_group = $this->_getParam("course_group");
            
            
            if ($session_id) {
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                
                $result = $GradeAllocation_model->getRecordsByCourseGroup($session_id,$course_group,$degree_id);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
            }
        }
    }
    
    
    
    
    public function getPasspercent($degree_id='',$session){
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
   
  

}

