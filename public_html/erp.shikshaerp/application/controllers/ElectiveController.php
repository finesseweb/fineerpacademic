<?php

class ElectiveController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    Private $_unit_id = null;
    private $accessConfig = NULL;

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

        if ($data->role_id == 0)
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

//Commented By sailaja

    /*  public function selectionAction() {

      $this->view->action_name = 'selection';

      $this->view->sub_title_name = 'Elective';

      $Course_model = new Application_Model_Course();

      $elective_courses = $Course_model->getElectiveNamesDropDown();

      $this->view->elective_courses = $elective_courses;

      $Elective_model = new Application_Model_ElectiveSelection();

      $ElectiveItems_model = new Application_Model_ElectiveSelectionItems();

      $Elective_form = new Application_Form_ElectiveSelection();

      $elective_id = $this->_getParam("id");

      $type = $this->_getParam("type");

      $Electivecourse_model = new Application_Model_ElectiveCourseLearning();

      $this->view->type = $type;

      $this->view->form = $Elective_form;



      switch ($type) {

      case "add":

      if ($this->getRequest()->isPost()) {

      if ($Elective_form->isValid($this->getRequest()->getPost())) {

      $data = $Elective_form->getValues();

      $academic_year_id = $data['academic_year_id'];

      $last_insert_id = $Elective_model->insert($data);

      $students_electives = $this->getRequest()->getPost('Elective');

      // foreach($students_electives['students_id'] as $key=>$students_id){

      //print_r(count($students_electives['students_id']));die;

      $i=0;

      // print_r($_POST);die;

      $student_model = new Application_Model_StudentPortal();

      $student_data=$student_model->getstudents($academic_year_id);

      // print_r(count($student_data));die;

      // print_r(count($students_electives['elective_name']));die;

      $term = $students_electives['term_ids'][0];





      $course_count = $Electivecourse_model->getTermCount($academic_year_id,$term);



      for($m=0;$m<count($student_data);$m++){

      //print_r();die;

      $elect_data['elective_id'] = $last_insert_id;

      $elect_data['students_id'] = $students_electives['students_id'][$m];

      $elect_data['term_ids'] = 4;

      $elect_data['terms'] = $students_electives['term_ids'][0];

      //print_r($elect_data);die;

      $i=0;

      $count_id = count($course_count);

      //echo'<pre>';print_r($count_id);die;

      for($j=1;$j<=$count_id;$j++) {

      //print_r($j);die;

      $elect_data['elective_name'] = $j;

      $elect_data['electives'] = $_POST['electives_'.$students_electives['students_id'][$m].'_'.$students_electives['term_ids'][0].'_'.$i.''];

      //echo'<pre>';print_r($elect_data);die;

      $ElectiveItems_model->insert($elect_data);

      $i++;

      }

      }



      $term1 = $students_electives['term_ids'][1];

      $course_count1 = $Electivecourse_model->getTermCount($academic_year_id,$term1);

      for($m=0;$m<count($students_electives['students_id']);$m++){

      //print_r();die;

      $elect_data['elective_id'] = $last_insert_id;

      $elect_data['students_id'] = $students_electives['students_id'][$m];

      $elect_data['term_ids'] = 5;

      $elect_data['terms'] = $students_electives['term_ids'][1];



      $count_id = count($course_count1);

      //print_r($count_id);die;

      $i=0;

      for($j=1;$j<=$count_id;$j++) {

      $elect_data['elective_name'] = $j;

      $elect_data['electives'] = $_POST['electives_'.$students_electives['students_id'][$m].'_'.$students_electives['term_ids'][1].'_'.$i.''];

      //echo'<pre>';print_r($elect_data);

      $ElectiveItems_model->insert($elect_data);

      $i++;

      }

      }



      $this->_flashMessenger->addMessage('Electivies added Successfully ');



      $this->_redirect('elective/selection');



      }

      }





      break;

      case 'edit':

      $result = $Elective_model->getRecord($elective_id);

      $Elective_form->populate($result);

      $this->view->students_name = $elective_id;



      $this->view->result = $result;

      $academic_year_id = $result['academic_year_id'];

      //print_r($result);die;



      if ($this->getRequest()->isPost()) {

      if ($Elective_form->isValid($this->getRequest()->getPost())) {

      $data = $Elective_form->getValues();

      $Elective_model->update($data, array('elective_id=?' => $elective_id));

      $students_electives = $this->getRequest()->getPost('Elective');



      // echo'<pre>';print_r($_POST);die;

      $ElectiveItems_model->trashItems($elective_id);

      $i=0;

      // print_r($_POST);die;

      $student_model = new Application_Model_StudentPortal();

      $student_data=$student_model->getstudents($academic_year_id);

      //print_r(count($student_data));die;

      // print_r(count($students_electives['elective_name']));die;

      $term = $students_electives['term_ids'][0];

      $course_count = $Electivecourse_model->getTermCount($academic_year_id,$term);

      for($m=0;$m<count($student_data);$m++){

      //print_r();die;

      $elect_data['elective_id'] = $elective_id;

      $elect_data['students_id'] = $students_electives['students_id'][$m];

      $elect_data['term_ids'] = 4;

      $elect_data['terms'] = $students_electives['term_ids'][0];

      $count_id = count($course_count);

      //print_r($course_count[$k1]['counts']);die;

      $i=0;

      for($j=1;$j<=$count_id;$j++) {

      //print_r($j);die;

      $elect_data['elective_name'] = $j;

      $elect_data['electives'] = $_POST['electives_'.$students_electives['students_id'][$m].'_'.$students_electives['term_ids'][0].'_'.$i.''];

      //echo'<pre>';print_r($elect_data);die;

      $ElectiveItems_model->insert($elect_data);

      $i++;

      }

      }



      $term1 = $students_electives['term_ids'][1];

      $course_count1 = $Electivecourse_model->getTermCount($academic_year_id,$term1);

      for($m=0;$m<count($students_electives['students_id']);$m++){

      //print_r();die;

      $elect_data['elective_id'] = $elective_id;

      $elect_data['students_id'] = $students_electives['students_id'][$m];

      $elect_data['term_ids'] = 5;

      $elect_data['terms'] = $students_electives['term_ids'][1];



      $count_id = count($course_count1);

      //print_r($count_id);die;

      $i=0;

      for($j=1;$j<=$count_id;$j++) {

      $elect_data['elective_name'] = $j;

      $elect_data['electives'] = $_POST['electives_'.$students_electives['students_id'][$m].'_'.$students_electives['term_ids'][1].'_'.$i.''];

      //echo'<pre>';print_r($elect_data);

      $ElectiveItems_model->insert($elect_data);

      $i++;

      }

      }







      $this->_flashMessenger->addMessage('Elective Details Updated Successfully');

      $this->_redirect('elective/selection');

      } else {

      }

      }

      break;

      case 'delete':

      $data['status'] = 2;

      if ($elective_id){

      $Elective_model->update($data, array('elective_id=?' => $elective_id));

      $this->_flashMessenger->addMessage('Elective Details Deleted Successfully');

      $this->_redirect('elective/selection');

      }

      break;

      default:

      $messages = $this->_flashMessenger->getMessages();

      $this->view->messages = $messages;

      $result = $Elective_model->getRecords();

      //print_r($result);die;

      $page = $this->_getParam('page', 1);

      $paginator_data = array(

      'page' => $page,

      'result' => $result

      );

      $this->view->paginator = $this->_act->pagination($paginator_data);

      break;

      }

      } */

    public function selectionAction() {

        $this->view->action_name = 'selection';

        $this->view->sub_title_name = 'Elective';
        $this->accessConfig->setAccess('SA_ACAD_E_SELECTION');
        $Course_model = new Application_Model_Course();
        $elective_courses = $Course_model->getElectiveNamesDropDown();
        $this->view->elective_courses = $elective_courses;
        $Elective_model = new Application_Model_ElectiveSelection();
        $ElectiveItems_model = new Application_Model_ElectiveSelectionItems();
        $academic_details = new Application_Model_Academic();
        $student_details = new Application_Model_StudentPortal();
        $department_model = new Application_Model_Department();
        $term_details = new Application_Model_TermMaster();
        $Elective_form = new Application_Form_ElectiveSelection();
        $elective_id = $this->_getParam("id");
        $term_id = $this->_getParam("term");
        $course_id = $this->_getParam("course");
        $type = $this->_getParam("type");
        $Electivecourse_model = new Application_Model_ElectiveCourseLearning();
        $this->view->type = $type;

        $this->view->form = $Elective_form;
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        switch ($type) {

            case "add":
                if ($this->getRequest()->isPost()) {

                    if ($this->getRequest()->getPost()) {

                        unset($data['course_id']);
                        $terms_id = $_POST['term_id2'];
                        $i = 0;
                        //print_r($_POST['csrftoken']);die;
                        if (!empty($_POST['csrftoken'])) {
                            if ($_POST['csrftoken'] === $token) {
                                foreach ($_POST['selected_students'] as $key => $value) {
                                    $data['student_id'] = $value;
                                    $_POST['term_id2'] = $terms_id;
                                    $result = $student_details->getStudenInfo($data['student_id']);
                                    $_POST['term_id2'] = $term_details->getTermRecordsbycmnelective($result['academic_id'], $terms_id);
                                    $data['academic_year_id'] = $result['academic_id'];
                                    $data['term_id'] = $_POST['term_id2'];

                                    $course_details = explode('_', $_POST['course_id2']);
                                    $id = $Elective_model->isExist($data['academic_year_id'], $data['term_id'], $data['student_id'], $course_details[0]);
                                    //echo '<pre>';  print_r($data);die;
                                    if (!$id) {
                                        $lastinsertid = $Elective_model->insert($data);
                                    } else {
                                        $ElectiveItems_model->trashItems($id);
                                        $det = $academic_details->getRecord($data['academic_year_id']);
                                        $lastinsertid = $id;
                                    }

                                    $elective_id = $lastinsertid;

                                    $elective_data['elective_id'] = $elective_id;
                                    $elective_data['electives'] = $course_details[0];
                                    $elective_data['students_id'] = $data['student_id'];
                                    $elective_data['terms'] = $data['term_id'];
                                    $elective_data['ge_id'] = $course_details[1];
                                    $elective_data['credit_value'] = $course_details[2];

                                    $ElectiveItems_model->insert($elective_data);
                                }

                                $this->_flashMessenger->addMessage('Electivies added Successfully ');
                                $_SEESION['message_color'] = 'alert alert-success';
                                $this->_redirect('elective/selection');
                            } else {
                                $message = "Invalid Token";
                                $_SESSION['message_class'] = 'alert-danger';
                                $this->_flashMessenger->addMessage($message);
                                $this->_redirect('elective/selection');
                            }
                        }
                    }
                }

                break;

            case 'edit':
                $academic_id = $elective_id;

                $elective_details = new Application_Model_ElectiveSelectionItems();
                $terms_info = $term_details->getRecord($term_id);
                if (!empty($course_id)) {

                    $result = $elective_details->getCoreCouseDetailByTermGeStudentAllwithcourse($academic_id, $terms_info['cmn_terms'], $course_id);
                    $academic_result = $this->mergData($result, array('academic_id'), count($result));
                    $session_depat = $academic_details->getRecord($academic_id);
                    $result['session'] = $session_depat['session'];
                    $degree_id = $department_model->getRecord($session_depat['department']);
                    $result['degree_id'] = $degree_id['degree_id'];

                    $result['academic_year_id'] = $academic_result;
                    $this->view->student_ids = json_encode($this->mergData($result, array('students_id'), count($result)));
                } else {
                    $course_id = '';
                    $result['academic_year_id'] = $academic_id;
                }
                $this->view->term_id = $terms_info['cmn_terms'];
                $this->view->course_id = $course_id;
                $Elective_form->populate($result);


                if ($this->getRequest()->isPost()) {

                    if ($this->getRequest()->getPost()) {
                        // print_r($_POST['csrftoken']);die;
                        if (!empty($_POST['csrftoken'])) {
                            if ($_POST['csrftoken'] === $token) {
                                $course_details = explode('_', $_POST['course_id2']);
                                $terms_id = $_POST['term_id2'];
                                foreach ($_POST['selected_students'] as $key => $value) {
                                    $data['student_id'] = $value;
                                    $result1 = $student_details->getStudenInfo($data['student_id']);
                                    $_POST['term_id2'] = $terms_id;
                                    $_POST['term_id2'] = $term_details->getTermRecordsbycmnelective($result1['academic_id'], $terms_id);
                                    $data['term_id'] = $_POST['term_id2'];

                                    $id = $Elective_model->isExist($result1['academic_id'], $data['term_id'], $data['student_id'], $course_details[0]);

                                    if (!empty($id)) {
                                        $ElectiveItems_model->trashItems($id);
                                    }
                                }
                                $_SESSION['message_class'] = 'alert-success';
                                $this->_flashMessenger->addMessage('Elective Details Updated Successfully');
                                $this->_redirect('elective/selection');
                            } else {
                                $message = "Invalid Token";
                                $_SESSION['message_class'] = 'alert-danger';
                                $this->_flashMessenger->addMessage($message);
                                $this->_redirect('elective/selection');
                            }
                        }
                    } else {
                        
                    }
                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($elective_id) {

                    $Elective_model->update($data, array('elective_id=?' => $elective_id));

                    $this->_flashMessenger->addMessage('Elective Details Deleted Successfully');

                    $this->_redirect('elective/selection');
                }

                break;

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $Elective_model->getRecords();
                $academic_details = new Application_Model_Academic();
                foreach ($result as $key => $value) {
                    if ($value['academic_year_id']) {
                        $result[$key]['batch-code'] = $academic_details->getRecord($value['academic_year_id'])['short_code'];
                    }
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

    public function ajaxGetSelectionAction() {
        $this->_helper->layout->disableLayout();
        $academic_Model = new Application_Model_Academic();
         $Elective_model = new Application_Model_ElectiveSelection();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            //echo '<pre>';print_r($batch_id);die;
            $result = $Elective_model->getRecordsByAcademicId($batch_id);
                $academic_details = new Application_Model_Academic();
                foreach ($result as $key => $value) {
                    if ($value['academic_year_id']) {
                        $result[$key]['batch-code'] = $academic_details->getRecord($value['academic_year_id'])['short_code'];
                    }
                }

                $page = $this->_getParam('page', 1);

                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
           
        }
    }

    public function ajaxGetAcademicsAction() {
        $this->_helper->layout->disableLayout();
        $academic_Model = new Application_Model_Academic();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $degree_id = $this->_getParam("degree_id");
            $session_id = $this->_getParam("session_id");
            $result = $academic_Model->getAcademics($session_id, $degree_id);
            echo json_encode($result);
            die;
        }
    }

    public function ajaxGetCourseAction() {
        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $core_course_master = new Application_Model_Corecourselearning();
            $elective_course = new Application_Model_ElectiveSelectionItems();
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $cc_id = $this->_getParam("cc_id");
            if ($cc_id != 0) {
                $existingCourse = $elective_course->getByTermId($term_id);
                $arr_course = $this->mergData($existingCourse, array('course_ids'), count($existingCourse));
                $course_details = $core_course_master->getGeCourses($academic_year_id, $term_id);
                $course_ids = $this->selectData($course_details, array('course_id'), count($course_details));

                $parent_data = $this->stackData($course_ids, $course_details);

                echo "<option>--Select--</option>";
                foreach ($parent_data as $course_id => $value) {
                    foreach ($value as $key => $value) {
                        if (in_array($course_id, $arr_course)) {
                            
                        } else {
                            echo "<option value ='{$course_id}_{$value['ge_id']}_{$value['credit_value']}'>{$value['course_name']}</option>";
                        }
                    }
                }
            } else {
                $course_details = $core_course_master->getGeCourses1($academic_year_id, $term_id);
                echo "<option>--Select--</option>";
                foreach ($course_details as $key => $value) {
                    echo "<option value ='0'>{$value['course_name']}</option>";
                }
            }
        }die;
    }

    public function ajaxGetCourseEditAction() {
        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $core_course_master = new Application_Model_Corecourselearning();

            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $details = new Application_Model_TermMaster();
            $result = $details->getRecordsBycmnTerms($term_id);
            echo "<pre>";
            print_r($result);
            exit;
            $course_details = $core_course_master->getGeCourses($academic_year_id, $term_id);
            echo "<option>--Select--</option>";
            foreach ($course_details as $key => $value) {
                echo "<option value ='{$value['course_id']}_{$value['ge_id']}_{$value['credit_value']}'>{$value['course_name']}</option>";
            }
        }die;
    }

    public function ajaxGetStudentElectivesAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");

            $elective_id = $this->_getParam("students_name");

            //print_r($academic_year_id);die;

            $Elective_model = new Application_Model_ElectiveSelection();

            $ElectiveItems_model = new Application_Model_ElectiveSelectionItems();

            if (!empty($elective_id)) {

                //print_r($elective_id);die;

                $student_model = new Application_Model_StudentPortal();

                $student_data = $student_model->getstudents($academic_year_id);

                $this->view->student_data = $student_data;

                $Electivecourse_model = new Application_Model_ElectiveCourseLearning();

                $Electivecourse = $Electivecourse_model->getTerms($academic_year_id);

                // print_r($Electivecourse);die;

                $this->view->Electivecourse = $Electivecourse;

                //print_r($student_data);die;

                $this->view->academic_year_id = $academic_year_id;

                $ElectiveItems_data = $ElectiveItems_model->getItemsRecords($elective_id);

                //echo'<pre>';print_r($ElectiveItems_data);die;

                $this->view->ElectiveItems_data = $ElectiveItems_data;

                //echo $academic_year_id;die;	

                $course_model = new Application_Model_Course();

                $course_data = $course_model->getElectiveNamesDropDown();

                //print_r($course_data);die;

                $this->view->course_data = $course_data;
            } else {

                if ($academic_year_id) {

                    $student_model = new Application_Model_StudentPortal();

                    $student_data = $student_model->getstudents($academic_year_id);

                    //print_r($student_data);die;

                    $this->view->student_data = $student_data;

                    $Electivecourse_model = new Application_Model_ElectiveCourseLearning();

                    $Electivecourse = $Electivecourse_model->getTerms($academic_year_id);

                    //print_r($Electivecourse);die;



                    $this->view->Electivecourse = $Electivecourse;





                    //print_r($Electivecourse[$k]['term_id']);die;
                    //$term_id = $Electivecourse[$k]['term_id'];
                    // $Electivecourse_count = $Electivecourse_model->getTermCount($academic_year_id,$term_id);
                    //print_r($Electivecourse_count);die;
                    // $this->view->Electivecourse_count= $Electivecourse_count;



                    $this->view->academic_year_id = $academic_year_id;

                    //  $electives = count($Electivecourse_count);
                    //  $this->view->electives = $electives;

                    $course_model = new Application_Model_Course();

                    $course_data = $course_model->getElectiveNamesDropDown();

                    //print_r($course_data);die;

                    $this->view->course_data = $course_data;
                }
            }
        }
    }

    public function ajaxGetCheckAcademicDataAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");

            //$year_id = $this->_getParam("year_id");
            //echo $academic_year_id;die;

            $Elective_model = new Application_Model_ElectiveSelection();

            $grade_result = $Elective_model->getValidAcademicRecord($academic_year_id);

            $counts = count($grade_result['elective_id']);

            //print_r($counts);die;

            echo json_encode($counts);
            die;

            $this->view->grade_result = $grade_result;
        }
    }

    public function ajaxGetStudentsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");
            $StudentPortal_model = new Application_Model_ElectiveSelectionItems();
            $academic_year_id = implode(',', $academic_year_id);
            if ($course_id != 0) {
                $data = $StudentPortal_model->getelectivestudentDetails($academic_year_id, $term_id, explode('_', $course_id)[0]);
            } else {

                $student_model = new Application_Model_StudentPortal();
                $data = $student_model->getstudentsbyacademics($academic_year_id, $term_id);
            }

            echo '<option value="">Select</option>';
            foreach ($data as $k => $val) {
                echo '<option value="' . $val['student_id'] . '">' . $val['name'] . '</option>';
            }
        }die;
    }

    public function ajaxGetStudents1Action() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $StudentPortal_model = new Application_Model_StudentPortal();
            $data = $StudentPortal_model->getstudents1($academic_year_id);
            echo '<option value="">Select</option>';
            foreach ($data as $k => $val) {
                echo '<option value="' . $val['student_id'] . '">' . $val['students'] . '</option>';
            }
        }die;
    }

    public function ajaxGetAcademicTermsAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");

            $TermMaster_model = new Application_Model_TermMaster();

            $data = $TermMaster_model->getAcademicTerms($academic_year_id);

            echo '<option value="">Select</option>';

            foreach ($data as $k => $val) {

                echo '<option value="' . $k . '">' . $val . '</option>';
            }
        }die;
    }

    public function ajaxGetTermWiseElectiveCoursesAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");

            $term_id = $this->_getParam("term_id");

            $ElectiveCourseLearning_model = new Application_Model_ElectiveCourseLearning();

            $data = $ElectiveCourseLearning_model->getTermWiseCourses($academic_year_id, $term_id);

            echo '<option value="">Select</option>';

            foreach ($data as $k => $val) {

                echo '<option value="' . $k . '">' . $val . '</option>';
            }
        }die;
    }

    public function ajaxGetTermWiseElectiveCoursesEditAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");

            $term_id = $this->_getParam("term_id");

            $elective_increment_id = $this->_getParam("elective_increment_id");

            $ElectiveCourseLearning_model = new Application_Model_ElectiveCourseLearning();

            $data = $ElectiveCourseLearning_model->getTermWiseCourses($academic_year_id, $term_id);

            $ElectiveSelection_model = new Application_Model_ElectiveSelection();

            $result = $ElectiveSelection_model->getSelectedElectives($elective_increment_id);



            echo '<option value="">Select</option>';



            foreach ($data as $k => $val) {

                $selected = '';

                foreach ($result as $r => $res) {

                    if ($k == $res['electives']) {



                        $selected = "selected";
                    }
                }

                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
        }die;
    }

    public function ajaxGetCheckStutermsAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");

            $student_id = $this->_getParam("student_id");

            $term_id = $this->_getParam("term_id");

            //echo $academic_year_id;die;

            $Elective_model = new Application_Model_ElectiveSelection();

            $grade_result = $Elective_model->getValidStudentsRecord($academic_year_id, $student_id, $term_id);

            $counts = count($grade_result['elective_id']);

            //print_r($counts);die;

            echo json_encode($counts);
            die;

            //$this->view->grade_result = $grade_result;
        }
    }

    public function ajaxGetElectiveCreditsRecordAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");

            $elective_id = $this->_getParam("elective_id");

            $term_id = $this->_getParam("term_id");

            //echo $academic_year_id;die;

            $ElectiveCourse_model = new Application_Model_ElectiveCourseLearning();

            $electiveCredits_result = $ElectiveCourse_model->getElectiveCreditsRecord($academic_year_id, $term_id, $elective_id);

            //print_r($electiveCredits_result);

            echo json_encode($electiveCredits_result);
        }die;
    }

    public function ajaxGetTermElectiveCreditsAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");

            $term_id = $this->_getParam("term_id");

            //echo $academic_year_id;die;

            $Term_model = new Application_Model_TermMaster();

            $electiveTermCredits_data = $Term_model->getElectivesTermCredits($academic_year_id, $term_id);

            //echo"<pre>";print_r($electiveTermCredits_data);exit;

            echo json_encode($electiveTermCredits_data);
        }die;
    }

}
