<?php
//ini_set('display_errors', '1');
/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class ReportController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    private $_checkUser = null;
    private $accessConfig = NULL;
    private $_base_url = NULL;

    public function init() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        require_once APPLICATION_PATH . '/configs/access_level.inc';

        $this->accessConfig = new accessLevel();
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $this->_action = $this->getRequest()->getActionName();
        $this->aeccConfig = $config_role = $zendConfig->aecc_course->toArray();
        //access role id
        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $this->login_storage = $data = $storage->admin_login;
        $this->view->login_storage = $data;
        
        $this->_base_url = $config['host'];
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

        $this->_checkUser = $user = $_POST['username'];
        $password = $_POST['password'];


        if ($user != 'admin' && $password != 'admin@123') {
            $this->authonticate();
        }
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        if ($data->role_id == 0)
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

    //follow up
    public function reportAction() {
        $this->accessConfig->setAccess("SA_ACAD_GRADE_REPORT");

        $this->view->action_name = 'enquiry';
        $this->view->sub_title_name = 'report';
        $Report_model = new Application_Model_Report();
        $Report_form = new Application_Form_Report();
        //$Followup_id = $this->_getParam("id");
        $type = $this->_getParam("type");


        $date = new Zend_Date();
        $currentDate = $date->toString('Y-MM-d');
        $this->view->currentDate = $currentDate;
        $startYear = $date->toString('Y');
        $nextYear = $startYear + 1;
        $AcademicYear = $startYear . '-' . $nextYear;

        $NextYear1 = $nextYear + 1;
        $NextYear = $nextYear . '-' . $NextYear1;

        $this->view->AcademicYear = $AcademicYear;
        $this->view->NextYear = $NextYear;
        switch ($type) {
            case "search":
                $this->view->type = $type;
                $this->view->Report_form = $Report_form;
                // $data = $this->getRequest()->getPost();

                if ($this->getRequest()->isPost()) {
                    if ($Report_form->isValid($this->getRequest()->getPost())) {
                        $data = $Report_form->getValues();
                        $export = $this->getRequest()->getPost('export');

                        $branch = '';
                        $search_type = $data['search_type'];
                        $country = $data['country_id'];
                        $state = $data['state_id'];
                        $city = $data['city_id'];
                        $location = $data['location_id'];
                        $branch_id = $data['branch_id'];
                        if (is_array($branch_id)) {
                            $branch = implode(',', $branch_id);
                        }



                        $from_date = $data['from_date'];

                        $to_date = $data['to_date'];

                        $academic_year = $data['academic_year'];

                        $program = $data['program_id'];

                        $subprogram = $data['subprogram_id'];

                        $frequency = $data['frequency_id'];

                        $counselor = $data['counselor_id'];

                        $why_esperanza = $data['why_esperanza'];

                        $how_know_esperanza = $data['how_know_esperanza'];

                        $type_of_enquiry = $data['type_of_enquiry'];

                        $source_of_enquiry = $data['source_of_enquiry'];

                        $enquiry_mindset = $data['enquiry_mind_set'];

                        $enquiry_date = $data['enquiry_date'];

                        $occupation_id = $data['occupation_id'];

                        $company_id = $data['company_id'];

                        $reference = $data['reference'];
                        $searchResult = $Report_model->getSearchRecords($search_type, $country, $state, $city, $location, $branch, $from_date, $to_date, $academic_year, $program, $subprogram, $frequency, $counselor, $why_esperanza, $how_know_esperanza, $type_of_enquiry, $source_of_enquiry, $enquiry_mindset, $enquiry_date, $occupation_id, $company_id, $reference);
                        $val = $Report_form->populate($data);

                        $this->view->searchResult = $searchResult;
                        if (isset($export)) {
                            $exportResult = $Report_model->getExportSearchRecords($search_type, $country, $state, $city, $location, $branch, $from_date, $to_date, $academic_year, $program, $subprogram, $frequency, $counselor, $why_esperanza, $how_know_esperanza, $type_of_enquiry, $source_of_enquiry, $enquiry_mindset, $enquiry_date, $occupation_id, $company_id, $reference);

                            //echo "<pre>";print_r($exportResult);die;
                            $heading = array("Country", "State", "City", "Location", "Branch", "Counselor", "Academic Year", "Enquiry ID", "Enquiry Date & Time", "Next Followed Up Date", "Child Name", "Father Name", "Mother Name", "Fathers Mobile", "Mothers Mobile", "Fathers Email", "Mothers Email", "Subprogram", "Frequency", "Fathers Company", "Mothers Company", "Father Occupation Name", "Mother Occupation Name", "Type of Enquiry", "Source of Enquiry", "Enquiry Mindset", "Why Us", "How do you know about us", "Reference", "Comments");
                            $exceldata = $exportResult;


                            $this->_act->createExcel($heading, $exceldata, "Enquiry Details");
                        }
                    }
                } else {
                    $this->_redirect('report/report');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $this->view->Report_form = $Report_form;
                $result = $Report_model->getRecords();

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function getEnquiryVewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $id = $this->_getParam("id");
            if ($id) {
                $Enquiry_Model = new Application_Model_Enquiry();
                $EnquiryItems_model = new Application_Model_EnquiryItems();
                $result = $Enquiry_Model->getEnquiryInfoForReport($id);
                $EnquiryItems_info = $EnquiryItems_model->getItemRecord($result['enquiry_id']);
                //$fees_type=$Fees_type_model->getRecords();	

                $this->view->enquiry_info = $result;
                $this->view->item_info = $EnquiryItems_info;
                //$this->view->fees_type = $fees_type;
            }
        }
    }

    
        public function studentDetailsAction(){
        $this->view->action_name = 'examreport';
        $this->view->sub_title_name = 'examreport';
        $this->accessConfig->setAccess('SA_ACAD_SHEET');
        $student_form =  new Application_Form_ElectiveSelection();;
        $this->view->form = $student_form;
        $student_model = new Application_Model_StudentPortal();
        /*$result = $student_model->getRecordsfordetails();
        //echo "<pre>";print_r($result);exit;
        $this->view->paginator = $result;*/
    }
    
        public function ajaxGetStudentByDeptAction(){
//        $this->_helper->layout->disableLayout();
//        $student_model = new Application_Model_StudentPortal();
//        $term_master = new Application_Model_TermMaster();
//        $Elective_model = new Application_Model_ElectiveSelection();
//        $course_credit_master = new Application_Model_Corecourselearning();
//        $ElectiveItems_model = new Application_Model_ElectiveSelectionItems();
//        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
//            $session_id = $this->_getParam("session_id");
//            $department_id = $this->_getParam("department_id");
//            $degree_id = $this->_getParam("degree_id");
//            
//           
//            
//            if ($session_id) {
//                $messages = $this->_flashMessenger->getMessages();
//                $this->view->messages = $messages;
//                $result = $student_model->getRecordsBySession($session_id,$department_id);
//                $this->view->paginator = $result;
//            }
//        }
            
            
            $this->_helper->layout->disableLayout();
            if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$term_id = $this->_getParam("term_id");
			$course_id = $this->_getParam("course_id");
                        $col = $this->_getParam("col");
                        $type = $this->_getParam("examtype");
                        $attendance = $this->_getParam('attend');
                        $course_code = $this->_getParam("course_code");
			            $StudentPortal_model = new Application_Model_ElectiveSelectionItems();
                        $backStudentElective = new Application_Model_BackSelectionItems();
                        $coursede = new Application_Model_ApplicantCourseDetailModel();
                        $course_model = new Application_Model_Course();
                        // echo $type; die;
                        $academic_year_id = implode(',',$academic_year_id);
                        if($col == 1){
                                    if($course_id !=0){
                                    $result = $StudentPortal_model->getelectivestudentDetails1($academic_year_id,$term_id,explode('_',$course_id)[0],$type,$attendance);}
                                    else
                                    {
                                        $student_model = new Application_Model_StudentPortal();
                                        $result=$student_model->getstudentsbyacademics($academic_year_id,$term_id,$type,$attendance);
                                    }
                        }
                        else if($col == 2)
                        {
                          
                            if($course_id !=0){
                            $result = $backStudentElective->getelectivestudentDetails1($academic_year_id,$term_id,explode('_',$course_id)[0],1,$type);
                            }
                            else{
                             $course =   $course_model->getCourseCode($course_code);
                             $result =    $backStudentElective->getelectivestudentDetails1($academic_year_id,$term_id,$course['course_id'],0,$type);
                            }
                        }
                        else
                        {
                          
                           $result =  $coursede->getRecordByCourse($academic_year_id);
                            
                        }
			        
                         $this->view->paginator = $result;
		}
            
            
            
    }
    public function studentreportAction() {
        $this->view->action_name = 'studentreport';
        $this->view->sub_title_name = 'studentreport';
        $this->accessConfig->setAccess('SA_ACAD_FINAL_GRADE');
        $student_report_form = new Application_Form_StudentReport();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }

    public function studentTermReportAction() {
        $this->view->action_name = 'student-term-report';
        $this->view->sub_title_name = 'student-term-report';
        $this->accessConfig->setAccess('SA_ACAD_TERM_GRADE_SHEET');
        $student_report_form = new Application_Form_StudentReport();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }
    public function studentAttendanceReportAction() {
        $this->view->action_name = 'student-term-report';
        $this->view->sub_title_name = 'student-term-report';
        $this->accessConfig->setAccess('SA_ACAD_ATTENDANCE_REPORT');
        $student_report_form = new Application_Form_StudentReport();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }
    public function studentBackTermReportAction() {
        $this->view->action_name = 'student-term-report';
        $this->view->sub_title_name = 'student-term-report';
        $this->accessConfig->setAccess('SA_ACAD_TERM_GRADE_SHEET');
        $student_report_form = new Application_Form_StudentReport();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }

    public function ajaxGetStudentNamesAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            if ($academic_year_id) {
                $StudentPortal_model = new Application_Model_StudentPortal();
                $student_data = $StudentPortal_model->getStudentNames($academic_year_id);
                //print_r($SubProgram);die;
                echo '<option value="">Select </option>';
                foreach ($student_data as $k => $val) {
                    echo '<option value="' . $k . '" >' . $val . '</option>';
                }
                die;
            }
        }
    }

    public function ajaxValidateDirectGradeAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");
            $student_id = $this->_getParam("student_id");
            if ($academic_year_id && $term_id && $course_id && $student_id) {
                $DirectFinalGrade_model = new Application_Model_DirectFinalGrade();
                echo $DirectFinalGrade_model->isGradeExist($academic_year_id, $term_id, $course_id, $student_id);

                die;
            }
        }
    }
    
    public function getAttendanceReportAction(){
        $this->_helper->layout->disableLayout();
        $studentreport_model = new Application_Model_StudentReport();
        $result = $studentreport_model->getRecords();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department = $this->_getParam("department");
            $course_id = $this->_getParam("course_id");
            $ge_id = $this->_getParam("ge_id");
            $year_id = $this->_getParam("year_id");
            $term_id = $this->_getParam("term_id");
            $monthFrom = $this->_getParam('monthFrom');
            $monthTo = $this->_getParam('monthTo');
            $academic_id = $this->_getParam('academic_id');
            $this->view->department = $department;
            $this->view->course_id = $course_id;
            $this->view->ge_id = $ge_id;
            $this->view->term_id = $term_id;
            $this->view->year_id = $year_id;
            $this->view->monthFrom = $monthFrom;
            $this->view->monthTo = $monthTo;
            
            $this->view->academic_id = $academic_id;
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

    public function studentreportPdfAction() {
        $this->view->close = '';
        $st_id = $this->_getParam("id");
        $academic_id = $this->_getParam("academic_id");
        $studentreport_model = new Application_Model_StudentReport();
        $result = $studentreport_model->getRecord($st_id);
        $this->view->result = $result;
        $Academic_model = new Application_Model_Academic();
        $academic_result = $Academic_model->getRecord($academic_id);
        $this->view->academic_result = $academic_result;

        $pdfheader = $this->view->render('report/pdfheader.phtml');
        $pdffooter = $this->view->render('report/pdffooter.phtml');
        $htmlcontent = $this->view->render('report/studentreport-pdf.phtml');
        $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Student Provisional Report");
    }

    public function studentgradesheetreportPdfAction() {
        $this->view->close = '';
        $stu_id = $this->_getParam("id");
        $this->view->stu_id = $stu_id;
        $academic_id = $this->_getParam("acd_id");
        $this->view->academic_id = $academic_id;
        $year_id = $this->_getParam("year");
        $this->view->year_id = $year_id;
        $CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
        $result = $CourseGradeAfterpenalties_model->getGradeSheetRecord($academic_id, $year_id, $stu_id);
        //echo'<pre>';print_r($result);die;
        $this->view->grade_result = $result;

        $Ele_result = $CourseGradeAfterpenalties_model->getGradeSheetElectivesRecord($academic_id, $year_id, $stu_id);
        //echo'<pre>';print_r($Ele_result);die;
        $this->view->Ele_result = $Ele_result;

        $studentreport_model = new Application_Model_StudentReport();
        $stu_result = $studentreport_model->getRecord($stu_id);
        $this->view->stu_result = $stu_result;

        $Academic_model = new Application_Model_Academic();
        $academic = $Academic_model->getRecord($academic_id);
        $this->view->academic_data = $academic;

        $ExprCourseGradeAftrPenalties_model = new Application_Model_ExprCourseGradeAftrPenalties();
        $ExprCourseGradeAftrPenaltyItems_model = new Application_Model_ExprCourseGradeAftrPenaltyItems();

        $ExperientialLearning_model = new Application_Model_ExperientialLearning();
        $Experiential_data = $ExperientialLearning_model->getExperRecords($academic_id, $year_id);
        $this->view->Experiential_data = $Experiential_data;

        // $ExprCourse_data = $ExprCourseGradeAftrPenalties_model->getGradeSheetRecords($academic_id,$year_id,$stu_id);
        //echo'<pre>';print_r($ExprCourse_data);die;
        // $this->view->ExprCourse_data = $ExprCourse_data;
        //$pdfheader = $this->view->render('report/pdfheader.phtml');
        //$pdffooter = $this->view->render('report/pdffooter.phtml');				
        $htmlcontent = $this->view->render('report/studentgradesheetreport-pdf.phtml');
        $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Student Report Details");
    }

   

    public function directFinalGradeAction(){
        $this->view->action_name = 'direct-final-grade';
        $this->view->sub_title_name = 'direct-final-grade';
        $this->accessConfig->setAccess('SA_ACAD_DIRECT_FINAL_GRADE');
        $DirectFinalGrade_model = new Application_Model_DirectFinalGrade();
        $Corecourselearning_form = new Application_Form_DirectFinalGrade();
        $ccl_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Corecourselearning_form;
        $cur_datetime = date('Y-m-d H:i:s');
        $user_id = $this->login_storage->id;
        $ip = $_SERVER['REMOTE_ADDR'];
        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($Corecourselearning_form->isValid($this->getRequest()->getPost())) {
                        $final_grade = $this->getRequest()->getPost('final_grade');
                        $data = $Corecourselearning_form->getValues();
                        $Corecourselearning_model = new Application_Model_Corecourselearning();
                        $course_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($data['academic_year_id'], $data['term_id'], $data['course_id']);
                        //print_r($data);exit;
                        $data['final_grade'] = $final_grade;
                        $data['credit_value'] = $course_detail['credit_value'];
                        $data['grade_credit_multiplied'] = $final_grade * $course_detail['credit_value'];
                        $data['added_on'] = $cur_datetime;
                        $data['added_by'] = $user_id;
                        $data['updated_on'] = $cur_datetime;
                        $data['updated_by'] = $user_id;
                        $data['added_by_ip'] = $ip;
                        $data['updated_by_ip'] = $ip;

                        $DirectFinalGrade_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('report/direct-final-grade');
                    }
                }
                break;
            case 'edit':
                $result = $DirectFinalGrade_model->getRecordDetail($ccl_id);
                $this->view->final_grade_detail = $result;
                if ($this->getRequest()->isPost()) {
                    $data['final_grade'] = $this->getRequest()->getPost('final_grade');
                    $Corecourselearning_model = new Application_Model_Corecourselearning();
                    $course_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($result['academic_year_id'], $result['term_id'], $result['course_id']);
                    $data['credit_value'] = $course_detail['credit_value'];
                    $data['grade_credit_multiplied'] = $final_grade * $course_detail['credit_value'];
                    $data['updated_on'] = $cur_datetime;
                    $data['updated_by'] = $user_id;
                    $data['updated_by_ip'] = $ip;
                    //print_r($data);echo $ccl_id;exit;
                    $DirectFinalGrade_model->update($data, array('id=?' => $ccl_id));

                    $this->_flashMessenger->addMessage('Details Updated Successfully');
                    $this->_redirect('report/direct-final-grade');
                }

                break;
            case 'delete':
                $data['deleted'] = 1;
                if ($ccl_id) {
                    $DirectFinalGrade_model->update($data, array('id=?' => $ccl_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('report/direct-final-grade');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $DirectFinalGrade_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }


    public function secondyeargradesheetreportAction(){
      $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("id");
        $academic_id = $this->_getParam("acd_id");
        $year_id = $this->_getParam("year");
        $mode = $this->_getParam("mode");
        $term_id = $this->_getParam("term");
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
        $prom_text = array();
        $term_iterator = array();
        $single = false;
        $pge_height = 150;
        $publishDate = "";
        $last_term_id = 0;
           if(in_array($academic_id,array(63,65)))
                                        {
                                            $publishDate = "2019-06-22";
                                        }
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
                             $prom_text[$value['term_id']] = $result['get_student_sgpa']['promotion_text']=='--'?'Not Promoted':$result['get_student_sgpa']['promotion_text'];
                              $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $term_iterator[] = $value['term_id'];
                            $stuname = $result['student_details']['stu_fname'];
          $course_ids = $this->mergData($student_course_marks[$value['term_id']], array('course_id'), count($student_course_marks[$value['term_id']]));                 
          
          $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $value['term_id'], $course_ids));
         // echo "<pre>"; print_r($course_credit_info[$value['term_id']]['sgpa_span']);exit;
                            $pge_height+=120;
                            $last_term_id =  $value['term_id'];
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
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $stuname = $result['student_details']['stu_fname'];
                            $filename =  '-'.$term_details[$value['term_id']]['term_name'].'-Grade-Report';
                         //$pge_height+=count($result['core_course_span'])*20;
                         $pge_height+=80;
                            
                            $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));         
                            $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
       
                           
            }
             $single = true;
        }
       //---------------------[END]-------------------------// 
        
        $pge_height+=150;
        
        $degree_id = $academic_master->getAcademicDegree($academic_id);
        
        if(!empty($degree_id))
        $passpercent = $this->getPasspercent($degree_id);
    
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
        
         if($publishDate){
            $this->view->publish_dates = $publishDate;
        }
        else{
         $this->view->publish_dates = $tabl_date;
        }
        
        if($term_id)
        $this->view->term_id = $term_id;
        else if($last_term_id)
            $this->view->term_id = $last_term_id;
            else
            $this->view->term_id=0;
        
        $this->view->url = $url;
        
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
                
                 $htmlcontent = $this->view->render('report/secondyeargradesheetreport.phtml');
        }
        //------------------------------[Print Option According to Mode]-------------------//
        
         if(empty($term_id)){
            $backcontent = $this->view->render('report/backpage.phtml');
         }
         else if($term_id){
             $pge_height = 320;
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
    

    
    
    
    
    public function attendancereportAction(){

        $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("id");
        $academic_id = $this->_getParam("acd_id");
        $year_id = $this->_getParam("year");
        $mode = $this->_getParam("mode");
        $term_id = $this->_getParam("term");
        $monthFrom = $this->_getParam('from');
        $monthTo = $this->_getParam('to');
        $pge_height = 150;
        $department_id = $this->_getParam("department");
        $course_id = $this->_getParam("course_id");
        $ge_id=$this->_getParam("ge_id");
        
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        
        $ge_master= new Application_Model_Ge();
        $term_master = new Application_Model_TermMaster();
        $academic_master = new Application_Model_Academic();
        $attendance_info = new Application_Model_BatchAttendance();
        $department = new Application_Model_Department();
        $session = new Application_Model_Session();
        $course_details = new Application_Model_Course();
        if($ge_id){
            $ge_name=$ge_master->getRecord($ge_id);
        }
        
        $semesterName= $term_master->getTermRecordsbycmn($academic_id,$term_id);
        //echo '<pre>'; print_r($semesterName); exit;
        $course_code = $course_details->getRecord($course_id);
        $academic_details    = $academic_master->getRecord($academic_id);
        $department_name = $department->getRecord($academic_details['department'])['department'];
        $session_name = $session->getRecord($academic_details['session'])['session'];
        //$term_result = $term_master->getTermRecordsByTerm($academic_id, $term_id);
        // $selected_cmn_terms = $this->selectData($term_result, array('cmn_terms'), count($term_result));
        //$term_arr = $this->mergData($selected_cmn_terms, array('cmn_terms'), count($selected_cmn_terms));
        //$filename = $academic_details['short_code'];
        $attendance_result =  $attendance_info->getAttendanceOnMonthRange1($department_id,$course_id,$ge_id,$term_id, $monthFrom, $monthTo);
         
        $ids = $this->mergData($attendance_result,array('attendance_master_id'),count($attendance_result));
          
            $attendance_result = $attendance_info->getAttendanceOnMonthRange2($ids);
         
             $u_ids = $this->selectData($attendance_result,array('u_id'),count($attendance_result));
         
          $attendance_result_new = $attendance_info->getAttendanceOnMonthRange($ids);
           //echo "<pre>";print_r($attendance_result_new);
          $pge_height = $pge_height+count($attendance_result_new)*12;
          $distinct_date = $attendance_info->distinctDate($department_id,$course_id,$ge_id, $term_id, $monthFrom, $monthTo);
           //  echo "<pre>";print_r($distinct_date);exit;
          $parentMonth = $this->selectData($distinct_date, array('month'),count($distinct_date));
          
          $distinct_date = $this->stackData($parentMonth, $distinct_date);
            $data = $this->stackData($u_ids, $attendance_result_new);
             
            $monthFrom = explode('-',$monthFrom);
            $monthTo = explode('-',$monthTo);
            $month_arr = $monthFrom;
            $monthTo_arr = $monthTo;
            $monthFrom =  "$month_arr[1]-$month_arr[0]";
            $monthTo = "$monthTo_arr[1]-$monthTo_arr[0]";

            $date1 = new DateTime($monthFrom.'-20');
            $date2 = new DateTime($monthTo.'-20');
            $diff = $date1->diff($date2);
            $month_diff =  (($diff->format('%y') * 12) + $diff->format('%m')) ;
             
                $this->view->attendanceInfo = $attendance_result;
                $this->view->monthDiff = $month_diff;
                $this->view->month = $monthTo_arr[0];
                $this->view->startYear = $month_arr[1];
                $this->view->monthTo = $monthTo;
                $this->view->endYear = $monthTo_arr[1];
               
                $this->view->department = $department_name;
                $this->view->course_code = $course_code;
                $this->view->sem_name = $semesterName;
                $this->view->ge_name = $ge_name;
                $this->view->monthFrom = $month_arr[0];
                $this->view->batch = $academic_details['short_code'];
                $this->view->session = $session_name;
                $this->view->data = $data;
                $this->view->distinctdate = $distinct_date;
                
        
 
        //------------------------------[Print Option According to Mode]-------------------//
         $htmlcontent = $this->view->render('report/attendancereport.phtml');
         echo '<pre>'; print_r($htmlcontent); exit;
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, 'Attendance-report-for' . '-' .$filename,'L',$pge_height );
       //-----------------------[END]--------------------//     
    }
    
    public function attendancereportViewAction(){

        $this->_helper->layout->disableLayout();
		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
        $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("id");
        $academic_id = $this->_getParam("acd_id");
        $year_id = $this->_getParam("year");
        $mode = $this->_getParam("mode");
        $term_id = $this->_getParam("term");
        $monthFrom = $this->_getParam('from');
        $monthTo = $this->_getParam('to');
        $pge_height = 150;
        $department_id = $this->_getParam("department");
        $course_id = $this->_getParam("course_id");
        $ge_id=$this->_getParam("ge_id");
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        
        $term_master = new Application_Model_TermMaster();
        $academic_master = new Application_Model_Academic();
        $attendance_info = new Application_Model_BatchAttendance();
        $department = new Application_Model_Department();
        $session = new Application_Model_Session();
       
        
        //$academic_details    = $academic_master->getRecord($academic_id);
        //$department_name = $department->getRecord($academic_details['department'])['department'];
        //$session_name = $session->getRecord($academic_details['session'])['session'];
        //$term_result = $term_master->getTermRecordsByTerm($academic_id, $term_id);
        // $selected_cmn_terms = $this->selectData($term_result, array('cmn_terms'), count($term_result));
        //$term_arr = $this->mergData($selected_cmn_terms, array('cmn_terms'), count($selected_cmn_terms));
        //$filename = $academic_details['short_code'];
        $attendance_result =  $attendance_info->getAttendanceOnMonthRange1($department_id,$course_id,$ge_id,$term_id, $monthFrom, $monthTo);
         
        $ids = $this->mergData($attendance_result,array('attendance_master_id'),count($attendance_result));
           
            $attendance_result = $attendance_info->getAttendanceOnMonthRange2($ids);
         
            $u_ids = $this->selectData($attendance_result,array('u_id'),count($attendance_result));
         
          $attendance_result_new = $attendance_info->getAttendanceOnMonthRange($ids);
          //echo "<pre>";print_r($attendance_result_new);die;
          $pge_height = $pge_height+count($attendance_result_new)*12;
          
          $distinct_date = $attendance_info->distinctDate($department_id,$course_id,$ge_id, $term_id, $monthFrom, $monthTo);
          //echo "<pre>";print_r($distinct_date);
          $parentMonth = $this->selectData($distinct_date, array('month'),count($distinct_date));
           // echo "<pre>";print_r($parentMonth);
          $distinct_date = $this->stackData($parentMonth, $distinct_date);
            $data = $this->stackData($u_ids, $attendance_result_new);
            
            $monthFrom = explode('-',$monthFrom);
            $monthTo = explode('-',$monthTo);
            $month_arr = $monthFrom;
            $monthTo_arr = $monthTo;
            $monthFrom =  "$month_arr[1]-$month_arr[0]";
            $monthTo = "$monthTo_arr[1]-$monthTo_arr[0]";

            $date1 = new DateTime($monthFrom.'-20');
            $date2 = new DateTime($monthTo.'-20');
            $diff = $date1->diff($date2);
            $month_diff =  (($diff->format('%y') * 12) + $diff->format('%m')) ;
                 //echo '<pre>';print_r($data); exit;
                $this->view->attendanceInfo = $attendance_result;
                $this->view->monthDiff = $month_diff;
                $this->view->month = $monthTo_arr[0];
                $this->view->startYear = $month_arr[1];
                $this->view->monthTo = $monthTo;
                $this->view->endYear = $monthTo_arr[1];
               
                $this->view->department = $department_name;
                $this->view->monthFrom = $month_arr[0];
                $this->view->batch = $academic_details['short_code'];
                $this->view->session = $session_name;
                $this->view->data = $data;
                $this->view->distinctdate = $distinct_date;
        }
 
      
    }
    
    public function backPaperReportAction(){
      //  die;

 
        $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("id");
        $academic_id = $this->_getParam("acd_id");
        $year_id = $this->_getParam("year");
        $mode = $this->_getParam("mode");
        $term_id = $this->_getParam("term");
             $term_master = new Application_Model_TermMaster();
         $term_id = $term_master->getTermRecordsbycmn($academic_id,$term_id)['term_id'];
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
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
            
            //echo "<pre>"; print_r($item_result);exit;
            $pge_height = 0;
            foreach($term_result as $key => $value){
            
                    $result = $this->getBackReports($stu_id,$academic_id,$year_id,$value['term_id']);
                    //echo "<pre>";print_r($result);exit;
                    if($result){
                            $student_course_marks[$value['term_id']] = $result['student_course_marks'];
                            $get_student_sgpa[$value['term_id']] = $result['get_student_sgpa']['sgpa'];
                            $tabl_date[$value['term_id']] = $result['get_student_sgpa']['added_date'];
                            $get_student_fail_in[$value['term_id']] = $result['get_student_sgpa']['fail_in_ct_ids'];
                              $student_details = $result['student_details'];
                            $academic_details[$value['term_id']] = $result['academic_details'];
                            $formated_result[$value['term_id']] = $result['formated_result'];
                            $core_course_span[$value['term_id']] = count($result['core_course_span']);
                            $max[$value['term_id']] = $result['max_num'];
                            $term_details[$value['term_id']] = $result['term_details'];
                            $term_iterator[] = $value['term_id'];
                            $stuname = $result['student_details']['stu_fname'];
                            $course_ids = $this->mergData($student_course_marks[$term_id], array('course_id'), count($student_course_marks[$term_id]));        
                             echo "<pre>"; print_r($course_ids);exit;
                            $course_credit_info[$value['term_id']]['sgpa_span'] = count($core_course_master->getCoreCousecreditCountno($academic_id, $term_id, $course_ids));
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
        $passpercent = $this->getPasspercent($degree_id);
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
        $this->view->url = $url;
        //===================[GRADE SHEET NUMBER]===============================//
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $stu_id);
                $this->view->gradesheet_number = $gradesheet_number;
        //------------------------------[Print Option According to Mode]-------------------//
         $htmlcontent = $this->view->render('application/ajax-get-back-grade-view.phtml');
         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }//======for PDF
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $stuname . '-' .$filename,'L',$pge_height );
       //-----------------------[END]--------------------//       
    }
    
    
    
    
    
    public function getPasspercent($degree_id=''){
            try{  
                if(!empty($degree_id)){
            $ReferenceGradeMasterItems_model = new Application_Model_ReferenceGradeMasterItems();
            $ref_grades = $ReferenceGradeMasterItems_model->getRecordsByAcademicId(0, $degree_id);

            $arr_ref_number_grade_all = $this->mergData($ref_grades, array('number_grade'), count($ref_grades));
            $arr_ref_number_grade_all = array_filter($arr_ref_number_grade_all, function($value) {
                return $value > 0;
            });
            $min_pass_percent = min($arr_ref_number_grade_all);

            $min_pass_percent = count($ref_grades) == 0 ? 0 : $min_pass_percent;

            $range = $ReferenceGradeMasterItems_model->getRecordsByNumgrade($min_pass_percent,$degree_id);



            $min_pass_percent = count($ref_grades) == 0 ? 0 : min($range);
            
            return $min_pass_percent;
                }
                throw new Exception('$degree_id should not be empty !');
            }
            catch(Exception $e){
                echo $e->getMessage();die;
            }
        
        
    }
    
    
    
    
  //==================get fail pass student details===========================//  
   public function getReports($stu_id,$academic_id,$year_id,$term_id){
       
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
      

        $result['tabulation_id'] = $result['get_student_sgpa']['tabl_id'];
        
        if(!$result['tabulation_id']){
            return false;
       }
        
        $result['student_course_marks'] = $this->getStudentMarks($academic_id, $result['tabulation_id'], $stu_id, $term_id);
        
  
        $result['student_details'] = $student_info->getRecord($stu_id);
        $result['term_details'] = $term_details->getRecord($term_id);
        $result['academic_details'] = $academic_details->getRecord($academic_id);
        return $result;
       
       
       
   }
  //==================get fail pass student details===========================//  
   public function getBackReports($stu_id,$academic_id,$year_id,$term_id){
     //  exit;
       $result = array();
               $student_info = new Application_Model_StudentPortal();
        $tabulation_report = new Application_Model_TabulationReport();
        $eval_component = new Application_Model_EvaluationComponentsItems();
        $term_details = new Application_Model_TermMaster();
        $academic_details = new Application_Model_Academic();
        $Corecourselearning_model = new Application_Model_Corecourselearning();
        
        $result['core_course_span'] = 0;
//             $term_id = $term_details->getTermRecordsbycmn($academic_id,$term_id)['term_id'];
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
       
        $result['get_student_sgpa'] = $tabulation_report->fetchBackStudentSgpa($academic_id, $term_id, $stu_id);

        $result['tabulation_id'] = $result['get_student_sgpa']['tabl_id'];
        if(!$result['tabulation_id']){
            return false;
       }
        $result['student_course_marks'] = $this->getBackStudentMarks($academic_id, $result['tabulation_id'], $stu_id, $term_id);
     //   echo $academic_id;die;
        $result['student_details'] = $student_info->getRecord($stu_id);
        $result['term_details'] = $term_details->getRecord($term_id);
        $result['academic_details'] = $academic_details->getRecord($academic_id);
      //echo "<pre>";print_r($result);exit;
        return $result;
       
   }
    
    public function secondyeargradesheetreportNEWAction(){

        $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("id");
        $academic_id = $this->_getParam("acd_id");
        $year_id = $this->_getParam("year");
        $mode = $this->_getParam("mode");
        $term_id = $this->_getParam("term");
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;

        $student_info = new Application_Model_StudentPortal();
        $tabulation_report = new Application_Model_TabulationReport();
        $eval_component = new Application_Model_EvaluationComponentsItems();
        $term_details = new Application_Model_TermMaster();
        $academic_details = new Application_Model_Academic();
        $Corecourselearning_model = new Application_Model_Corecourselearning();
        $core_course_span = $Corecourselearning_model->getcorecourse($academic_id, $term_id);
        
        $cc_id = $Corecourselearning_model->getOnCC($academic_id, $term_id);
        
        $course_type_result = $this->geStudentCourse($academic_id, $term_id,$stu_id);
      
        
        $course_ids = $this->selectData($course_type_result, array('course_id'),count($course_type_result));
        $compo_count = array();
        $key_val = array();
        foreach($course_ids as $key => $value){
            $component_res = $eval_component->getRecordsByCourse($value['course_id']);
            $compo_count[] = count($component_res);
            $key_val[count($component_res)] =  $component_res;
        }
        
            $max_num = max($compo_count);
          
        
        $formated_result =  $this->stackData($cc_id,$course_type_result);
        
        $get_student_sgpa = $tabulation_report->fetchStudentSgpa($academic_id, $term_id, $stu_id);
        
        $tabulation_id = $get_student_sgpa['tabl_id'];
        
        $student_course_marks = $this->getStudentMarks($academic_id, $tabulation_id, $stu_id, $term_id);

        $student_details = $student_info->getRecord($stu_id);
        $term_details = $term_details->getRecord($term_id);
        $academic_details = $academic_details->getRecord($academic_id);

        $this->view->student_marks_details = $student_course_marks;
        $this->view->stu_sgpa = $get_student_sgpa['sgpa'];
        $this->view->stu_fail_in = $get_student_sgpa['fail_in_ct_ids'];
        $this->view->stu_details = $student_details;
        $this->view->stu_term_details = $term_details;
        $this->view->stu_academic_details = $academic_details;
        $this->view->stu_course = $formated_result;
        $this->view->header_component = $key_val[$max_num];
        $this->view->corespan = count($core_course_span);

        $htmlcontent = $this->view->render('report/secondyeargradesheetreport.phtml');
        //

         if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $student_details['stu_fname'] . '-' . $academic_details['short_code'] . '-'.$term_details['term_name'].'-Gradde-Report');
    }
    
    public function geStudentCourse($academic_id,$term_id,$stu_id){
        $Corecourselearning_model = new Application_Model_Corecourselearning();
          $core_courses = $Corecourselearning_model->getcorecourse($academic_id, $term_id);
          $student_ge = $Corecourselearning_model->getStudentGE($academic_id, $term_id,$stu_id);
          $course_type_result = array_merge($core_courses,$student_ge);
          return $course_type_result;
    }
    
    public function geStudentBackPaperCourse($academic_id,$term_id,$stu_id){
        $Corecourselearning_model = new Application_Model_Corecourselearning();
          //$core_courses = $Corecourselearning_model->getcorecourse($academic_id, $term_id);
          $student_ge = $Corecourselearning_model->getBackStudentGE($academic_id, $term_id,$stu_id);
          $course_type_result = $student_ge;
      // echo "<pre>"; print_r($course_type_result);exit;
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

    public function ajaxGetTodoListReportAction() {
        $empl_id = $this->_getParam('empl_id');
        $toDo_model = new Application_Model_ToDo();
        $not_started_in_percent = $in_progress_in_percent = $completed_in_percent = 0;


        if ($empl_id) {
            $not_started = $toDo_model->getTodoData($empl_id, 1);
            $completed = $toDo_model->getTodoData($empl_id, 3);
            $in_progress = $toDo_model->getTodoData($empl_id, 2);

            $total = $not_started + $completed + $in_progress;
            //coverting in percentage 
            $not_started_in_percent = round(($not_started / $total) * 100);
            $in_progress_in_percent = round(($in_progress / $total) * 100);
            $completed_in_percent = round(($completed / $total) * 100);
        } else {
            $not_started = $toDo_model->getTodoDataByStatus(1);
            $completed = $toDo_model->getTodoDataByStatus(3);
            $in_progress = $toDo_model->getTodoDataByStatus(2);
            $total = $not_started + $completed + $in_progress;
            $not_started_in_percent = round(($not_started / $total) * 100);
            $in_progress_in_percent = round(($in_progress / $total) * 100);
            $completed_in_percent = round(($completed / $total) * 100);
        }
        echo json_encode(array('in_progress' => $in_progress_in_percent,
            'not_started' => $not_started_in_percent,
            'completed' => $completed_in_percent,
            'not_started_in_no' => $not_started,
            'in_progress_in_no' => $in_progress,
            'completed_in_no' => $completed, 'total' => $total));

        exit;
    }

    public function studentCgpaReportAction() {
        $this->view->action_name = 'student-cgpa-report';
        $this->view->sub_title_name = 'Participants GPA/CGPA Report';
        $this->accessConfig->setAccess('SA_ACAD_CGPA_GPA');
        $student_report_form = new Application_Form_StudentReport();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }

    private function filterGPA($grades, $exp_grades, $c_type, $term_id, $student_id, $is_gpa) {
        $rs = 0;
        if ($c_type == 'term') {//If current grade is term
            //echo $batch_id. $c_type. $term_id. $student_id. $is_gpa;exit;
            //print_r($grades);
            //print_r($exp_grades);exit;
            foreach ($grades as $row) {
                if (($term_id == $row['term_id']) && $student_id == $row['student_id']) {
                    if ($is_gpa) {
                        $rs = $row['final_grade'];
                    } else {
                        $rs = $row['cgpa'];
                    }
                    break;
                }
            }
        } elseif ($c_type == 'el') {

            foreach ($exp_grades as $row) {
                if (($term_id == $row['course_id']) && $student_id == $row['student_id']) {
                    if ($is_gpa) {
                        $rs = $row['final_grade_point'];
                    } else {
                        $rs = $row['cgpa'];
                    }
                    break;
                }
            }
        }
        return $rs;
    }

    public function ajaxStudentCgpaReportAction() {
        $this->_helper->layout->disableLayout();
        $academic_year_id = $this->_getParam("academic_id");
        $academic_model = new Application_Model_Academic();
        $batch_map = $academic_model->getAcademicDesignOrderByDate($academic_year_id);
        $this->view->batch_map = $batch_map;
        //print_r($batch_map);exit;

        $CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
        $grades = $CourseGradeAfterpenalties_model->getAllGradesByBatch($academic_year_id);

        $expGradeAllocation_model = new Application_Model_ExperientialGradeAllocation();
        $exp_grades = $expGradeAllocation_model->getAllGradesByBatch($academic_year_id);


        $StudentPortal_model = new Application_Model_StudentPortal();
        $students = $StudentPortal_model->getStudentsSortByName($academic_year_id);




        $student_grades = array();
        foreach ($students as $student) {
            $student_grade = array();
            //$student_grade['student_id'] = $student['student_id'];
            $student_grade['stu_id'] = $student['stu_id'];
            $student_grade['stu_name'] = $student['stu_fname'] . ' ' . $student['stu_lname'];
            foreach ($batch_map as $map) {//GPA
                $student_grade[$map['c_type'] . $map['id']] = $this->filterGPA($grades, $exp_grades, $map['c_type'], $map['id'], $student['student_id'], TRUE);
            }
            foreach ($batch_map as $map) {//CGPA
                $student_grade[$map['c_type'] . $map['id'] . 'cgpa'] = $this->filterGPA($grades, $exp_grades, $map['c_type'], $map['id'], $student['student_id'], FALSE);
            }
            $student_grades[] = $student_grade;
        }
        $this->view->student_grades = $student_grades;
    }

    public function ajaxGetRecentTermAndBatchAction() {

        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $my_date = $this->_getParam('my_date');
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


        echo json_encode($result['recent']);
        exit;
    }

// get batchscheduleSessions
    public function ajaxGetBatchSceduleSessionsAction() {
        $term_id = '';
        $term_id = $this->_getParam('term_id');
        $academic_year_id = $this->_getParam('batch_id');
        $limit = (int) $this->_getParam('top_id');
        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $course_details = new Application_Model_Attendance();

        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnDat1($term_id, $academic_year_id);


        $term_start = explode("/", $result[0]['start_date']);
        $term_end = explode("/", $result[0]['end_date']);
        $start = $term_start[2] . "-" . $term_start[1] . "-" . $term_start[0];
        $end = $term_end[2] . "-" . $term_end[1] . "-" . $term_end[0];

        //getting all the courses 

        $courses = $course_details->getCourseDetails($term_id, $academic_year_id);

        $version_id = $term->getMaxVersion(date('d-m-Y', strtotime($start)));

        //   print_r($start); exit;
        if (count(Courses) > 0) {
            $result['course_details'] = $this->getCourseNames($courses, $start, $end, $academic_year_id, $term_id, $version_id['version'], $limit);
            // print_r($result['course_count']);exit;
        } else {
            echo 'No Courses';
            exit;
        }
        foreach ($result['course_details'] as $key => $value) {
            $result['course_details'][$key]['course_count'] = $result['course_details']['course_count'][$key];
        }
        echo json_encode($result['course_details']);
        exit;
    }

    //function to get course names
    public function getCourseNames(array $courses, $start_date, $end_date, $batch_id, $term_id, $version, $limit = '3') {
        $details = new Application_Model_Attendance();
        $course_name = new Application_Model_TermMaster();
        $class_master = new Application_Model_ClassMaster();
        $result = array();
        $i = 0;
        $no_of_classes = $class_master->getRecordByTermIdAndBatch($term_id, $batch_id);
        //========[CLASS FIELD NAME FROM DATABASE ATTENDANCE]=========//
        $join_arr[] = 'class';
        $join_arr[] = 'faculty';
        foreach ($join_arr as $key => $value) {
            for ($dcl = 1; $dcl <= $no_of_classes; $dcl++)
                $class_arr[] = $value . "_$dcl";
            $faculty_arr[] = $value . "_$dcl";
        }
        $all_arr = array();
        foreach ($courses as $key) {
            $result[$key['course_id']] = $course_name->getCourseName($key['course_id']);
            $result1[$key['course']] = $details->getCourseCoordinator($key['course_id'], $batch, $term_id);
            foreach ($class_arr as $key1 => $value) {
                if ($details->getClass($value, $key['course_id']) > 0) {
                    $all_arr[$key['course_id']]['course_name'] = $result[$key['course_id']]['course_code'];
                    $all_arr[$key['course_id']]['DMI_Faculty'] += $details->getFacultyId($faculty_arr[$key1], 'EMP-F', $value, $result1[$key['course']]['employee_id'], $key['course_id']);
                    $all_arr[$key['course_id']]['Visiting_Faculty'] += $details->getFacultyId($faculty_arr[$key1], 'VF', $value, '', $key['course_id']);
                    $all_arr[$key['course_id']]['course_coodinator'] += $details->getFacultyId($faculty_arr[$key1], $result1[$key['course']]['employee_id'], $value, '', $key['course_id']);
                    $all_arr[$key['course_id']]['average'] = ((int) $all_arr[$key['course_id']]['DMI Faculty'] + (int) $all_arr[$key['course_id']]['Visiting Faculty']) + (int) $all_arr[$key['course_id']]['course_coodinator'] / 2;
                } else {
                    $all_arr[$key['course_id']]['course_name'] = $result[$key['course_id']]['course_code'];
                    $all_arr[$key['course_id']]['DMI_Faculty'] += 0;
                    $all_arr[$key['course_id']]['Visiting_Faculty'] += 0;
                    $all_arr[$key['course_id']]['course_coodinator'] += 0;
                }
            }
        }


        //==========[REPORT FOR PANALTIES]===========//
        $penalities_student = array();
        $Suffled_penalty_value = array();
        $result_sorted = array();
        $max_penalties_value = array();
        foreach ($courses as $key) {
            $result[$key['course_id']] = $course_name->getCourseName($key['course_id']);
            $penalties_student = $course_name->getPenalties($key['course_id'], $term_id);
            foreach ($penalties_student as $penalty_key => $penalty) {
                $temp = array_map('floatVal', explode(',', $penalty['academic_grades']));
                $Suffled_penalty_value[$key['course_id']][$penalty_key] = $temp[$penalty['courses_position'] - 1];
                asort($Suffled_penalty_value[$key['course_id']]);
                $Suffled_penalty_value[$key['course_id']] = array_reverse($Suffled_penalty_value[$key['course_id']]);
                $result_sorted[$key['course_id']]['top'] = array_slice(array_unique($Suffled_penalty_value[$key['course_id']]), 0, $limit);
                $result_sorted[$key['course_id']]['course_name'] = $result[$key['course_id']]['course_code'];
            }
        }
        // 

        foreach ($result_sorted as $key => $value) {

            if (count($value['top']) < $limit) {
                for ($i = count($value['top']); $i <= ((int) $limit - count($value['top'])); $i++) {
                    $result_sorted[$key]['top'][$i] = 0;
                }
            }
        }

        // echo "<pre>"; print_r($result_sorted);exit;

        $result['faculty_sessions'] = $all_arr;
        $result['student_penalties'] = $result_sorted;
        return $result;
    }

    public function ajaxGetBatchSceduleSessions1Action() {
        $term_id = '';
        $term_id = $this->_getParam('term_id');
        $academic_year_id = $this->_getParam('batch_id');
        $empl_id = $this->_getParam('empl');
        $limit = (int) $this->_getParam('top_id');
        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $course_details = new Application_Model_Attendance();
        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnDat1($term_id, $academic_year_id);


        $term_start = explode("/", $result[0]['start_date']);
        $term_end = explode("/", $result[0]['end_date']);
        $start = date_create($term_start[2] . "-" . $term_start[1] . "-" . $term_start[0]);
        $end = date_create($term_end[2] . "-" . $term_end[1] . "-" . $term_end[0]);

        //getting all the courses 

        $courses = $course_details->getCourseDetails($term_id, $academic_year_id, $empl_id);
        //print_r($courses);exit;
        $version_id = $term->getMaxVersion(date_format($start, "d-m-Y"));


        if (count(Courses) > 0) {
            $result['course_details'] = $this->getCourseNames1($courses, date_format($start, "Y-m-d"), date_format($end, "Y-m-d"), $academic_year_id, $term_id, $version_id['version'], $limit);
            // print_r($result['course_count']);exit;
        } else {
            echo 'No Courses';
            exit;
        }
        foreach ($result['course_details'] as $key => $value) {
            $result['course_details'][$key]['course_count'] = $result['course_details']['course_count'][$key];
        }
        echo json_encode($result['course_details']);
        exit;
    }

    //function to get course names
    public function getCourseNames1(array $courses, $start_date, $end_date, $batch_id, $term_id, $version, $limit = '3') {

        $course_name = new Application_Model_TermMaster();
        $result = array();
        $i = 0;
        foreach ($courses as $key) {
            $result[$key['course_id']] = $course_name->getCourseName($key['course_id']);
        }

        foreach ($result as $key => $value) {
            $result['course_count'][$key] = (int) $course_name->getCourseReport1($value['course_code'], $batch_id, $term_id, $version);
        }

        return $result;
    }

    public function getCourseNameOnlyAction() {
        $course_id = $this->_getParam('course_id');
        $result[$course_id] = $course_name->getCourseName($course_id);
        echo json_encode($course_id);
        exit;
    }

    public function ajaxGetRatingInstructorAction() {
        $course = $this->_getParam('course');
        $instructor = $this->_getParam('instructor');
        $question_model = new Application_Model_Questionnaire();
        $rating_model = new Application_Model_RatingMaster();
        $student_feed_model = new Application_Model_StudentFeed();
        $instructor_feed = new Application_Model_InstructorFeed();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam('term_id');
            $batch_id = $this->_getParam('academic_year_id');
            $course_id = $this->_getParam('course_id');
            $instructor = $this->_getParam('instructor_id');

            $result['rating_five_no'] = $rating_five = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor, $course_id, 5);
            $result['rating_four_no'] = $rating_four = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor, $course_id, 4);
            $result['rating_three_no'] = $rating_three = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor, $course_id, 3);
            $result['rating_two_no'] = $rating_two = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor, $course_id, 2);
            $result['rating_one_no'] = $rating_one = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor, $course_id, 1);

            $result['rating_five_label'] = ucfirst($student_feed_model->getRatingLabel(5));
            $result['rating_four_label'] = ucfirst($student_feed_model->getRatingLabel(4));
            $result['rating_three_label'] = ucfirst($student_feed_model->getRatingLabel(3));
            $result['rating_two_label'] = ucfirst($student_feed_model->getRatingLabel(2));
            $result['rating_one_label'] = ucfirst($student_feed_model->getRatingLabel(1));

            //==[toal number of ratings]=======//
            $result['total_no'] = $total_rating = $rating_one + $rating_two + $rating_three + $rating_four + $rating_five;
            //=====[finding percentage]=======//
            $result['rating_five_percent'] = $rating_five_percent = round(($rating_five / $total_rating) * 100) . "%";
            $result['rating_four_percent'] = $rating_four_percent = round(($rating_four / $total_rating) * 100) . "%";
            $result['rating_three_percent'] = $rating_three_percent = round(($rating_three / $total_rating) * 100) . "%";
            $result['rating_two_percent'] = $rating_two_percent = round(($rating_two / $total_rating) * 100) . "%";
            $result['rating_one_percent'] = $rating_one_percent = round(($rating_one / $total_rating) * 100) . "%";

            echo json_encode($result);
            exit;
        }
    }

    public function ajaxGetRatingAction() {
        $course = $this->_getParam('course');
        $instructor = $this->_getParam('instructor');
        $question_model = new Application_Model_Questionnaire();
        $rating_model = new Application_Model_RatingMaster();
        $student_feed_model = new Application_Model_StudentFeed();
        $instructor_feed = new Application_Model_InstructorFeed();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam('term_id');
            $batch_id = $this->_getParam('academic_year_id');
            $course_id = $this->_getParam('course_id');
            $instructor = $this->_getParam('instructor_id');


            $result['rating_five_no'] = $rating_five = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 5);
            $result['rating_four_no'] = $rating_four = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 4);
            $result['rating_three_no'] = $rating_three = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 3);
            $result['rating_two_no'] = $rating_two = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 2);

            $result['rating_one_no'] = $rating_one = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 1);

            $result['rating_five_label'] = ucfirst($student_feed_model->getRatingLabel(5));
            $result['rating_four_label'] = ucfirst($student_feed_model->getRatingLabel(4));
            $result['rating_three_label'] = ucfirst($student_feed_model->getRatingLabel(3));
            $result['rating_two_label'] = ucfirst($student_feed_model->getRatingLabel(2));
            $result['rating_one_label'] = ucfirst($student_feed_model->getRatingLabel(1));
            //==[toal number of ratings]=======//
            $result['total_no'] = $total_rating = $rating_one + $rating_two + $rating_three + $rating_four + $rating_five;
            //=====[finding percentage]=======//
            $result['rating_five_percent'] = $rating_five_percent = round(($rating_five / $total_rating) * 100) . "%";
            $result['rating_four_percent'] = $rating_four_percent = round(($rating_four / $total_rating) * 100) . "%";
            $result['rating_three_percent'] = $rating_three_percent = round(($rating_three / $total_rating) * 100) . "%";
            $result['rating_two_percent'] = $rating_two_percent = round(($rating_two / $total_rating) * 100) . "%";
            $result['rating_one_percent'] = $rating_one_percent = round(($rating_one / $total_rating) * 100) . "%";

            echo json_encode($result);
            exit;
            $this->view->result = $result;
        }
    }

    public function ajaxGetCourseAction() {
        $course_details = new Application_Model_Attendance();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('academic_year_id');
            $result = $course_details->getCourseDetails($term_id, $batch_id);
            //  print_r($result);exit;
            foreach ($result as $value) {
                echo '<option value="' . $value['course_id'] . '" >' . $value['course_code'] . '</option>';
            }
        }die;
    }

    public function ajaxGetQuestionAction() {
        $ratings1 = new Application_Model_RatingMaster();

        $questions = new Application_Model_Questionnaire();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');
            $course_id = $this->_getParam('course');
            $empl_id = $this->_getParam('empl');

            $ratings = $ratings1->getRecords1();
            $result['question_array'] = array();
            $result['feed_question'] = $questions->getAllQuestionByEmpl($batch_id, $term_id, $course_id, $empl_id);
            foreach ($ratings as $rating_key => $rating_value) {
                foreach ($result['feed_question'] as $ques_key => $ques_value) {
                    $question = $ques_value['question'];
                    $result['question_array'][$questions->getAllQuestionName($question)][0] = $questions->getAllQuestionName($question);
                }
            }
            foreach ($ratings as $rating_key => $rating_value) {
                $feed = $rating_value['rating_value'];
                foreach ($result['feed_question'] as $ques_key => $ques_value) {
                    $question = $ques_value['question'];

                    $result['feed'][$ratings1->getRecordsByRatings($feed)][$questions->getAllQuestionName($question)] = $questions->getAllQuestionByEmplFeed($batch_id, $term_id, $course_id, $empl_id, $feed, $question);
                }
            }

            foreach ($result['feed'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    foreach ($result['question_array'] as $ques_key => $ques_value) {
                        if ($key1 === $ques_key) {
                            $result['question_array'][$ques_key][count($result['question_array'][$ques_key])] = $value1;
                        }
                    }
                }
            }


            foreach ($result['question_array'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    if ($key1 == count($result['question_array'][$key]) - 1)
                        $result['question_array'][$key][count($result['question_array'][$key])] = '';
                }
            }

            //   echo "<pre>";print_r($result['question_array']);exit;
            $result['all_question'] = $questions->getAllQuestionByQuestionType(2);
            echo json_encode($result);
            exit;
        }
    }

    public function ajaxGetFacultyAction() {
        $employee_model = new Application_Model_HRMModel();
        $faculty = new Application_Model_Attendance();
        $class_master = new Application_Model_ClassMaster();
        $term_id = $this->_getParam('term_id');
        $batch_id = $this->_getParam('academic_year_id');
        $course_id = $this->_getParam('course_id');
        /* $result = $faculty->getFaculty($term_id, $batch_id, $course_id); */
        $no_of_classes = $class_master->getRecordByTermIdAndBatch($term_id, $batch_id);


        $result = $faculty->getFeedFaculty($term_id, $batch_id, $course_id, $no_of_classes);
        $index = 0;
        $facultyFeed = array();
        foreach ($result as $key => $value) {
            foreach ($value as $new_key => $new_value) {
                if ($new_value == $course_id) {
                    $fac = explode('_', $new_key);
                    $facultyFeed[$index] = $value['faculty_' . $fac[1]];
                    $index++;
                }
            }
        }


        $uniqfaculty = array_unique($facultyFeed);
        //======[GETTING NAME OF AL THE EMPLYOEE]=======//
        for ($i = 0; $i < count($uniqfaculty); $i++) {
            $empl_name[$i] = $employee_model->getAllEmployee($uniqfaculty[$i])[0];
        }
        //=========[SETTING SELECT BOX]=========//


        echo '<option value="">Select</option>';
        if (count($empl_name) > 0) {
            foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
            }die;
        }

        die;
    }
    
    
    
    
    ///  admit card added by raushan
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
                    $stu_id=md5(trim($_POST['stu_id'])); 
                    
                   $getvalue =$allow_model-> getRecordforadmit($_POST['stu_id'],$_POST['semester']);
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
                    //$this->view->semester = '2nd Semes';
                   
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                   $term_obj = new Application_Model_TermMaster();
                   $term_details =  $term_obj->getTermRecords($acad_term_arr['batch_id'],$acad_term_arr['term_id']);
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =   'October '.$result['exam_year']  ;
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
    
      public function admitcardnoncollegiateAction() {
    //       http_response_code(500);
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
                        //print_r($_POST);
                        //die();
                    $stu_id=md5(trim($_POST['stu_id'])); 
                    $this->view->fid = $_POST['stu_id'];
                    $result =$student_exam_form->getNonRecordbyfid($_POST['stu_id']);
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
                    $result['examination'] =   'October '.$result['exam_year']  ;
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
            
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   $this->view->semester = '2nd Semester';
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
                  
                      
                   }

                    }
                }


                break;

            default:
             $admit_form = new Application_Form_AdmitCard();
           
                break;
        }
    }
    
          public function studentTermAdmitcardAction() {
        $this->view->action_name = 'student-term-admitcard';
        $this->view->sub_title_name = 'student-term-admitcard';
        $this->accessConfig->setAccess('SA_ACAD_TERM_GRADE_SHEET');
        $student_report_form = new Application_Form_StudentsAdmitcard();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }
	
	
	 public function studentNonugAdmitcardAction() {
        $this->view->action_name = 'student-nonug-admitcard';
        $this->view->sub_title_name = 'student-nonug-admitcard';
        $this->accessConfig->setAccess('SA_ACAD_TERM_GRADE_SHEET');
        $student_report_form = new Application_Form_NonUgAdmitcard();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }
	
	 public function studentNonpgAdmitcardAction() {
        $this->view->action_name = 'student-nonpg-admitcard';
        $this->view->sub_title_name = 'student-nonpg-admitcard';
        $this->accessConfig->setAccess('SA_ACAD_TERM_GRADE_SHEET');
        $student_report_form = new Application_Form_NonPgAdmitcard();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }
    
    
    
        
    
    public function ajaxGetStudentDetailsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            if ($academic_year_id) {
                $StudentPortal_model = new Application_Model_StudentPortal();
                $student_data = $StudentPortal_model->getStudentDetails($academic_year_id);
                //print_r($SubProgram);die;
                echo '<option value="">Select </option>';
                foreach ($student_data as $k => $val) {
                    echo '<option value="' . $k . '" >' . $val . '</option>';
                }
                die;
            }
        }
    }
    
    
    public function ajaxGetNonugDetailsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            if ($academic_year_id) {
                $StudentPortal_model = new Application_Model_StudentPortal();
                $student_data = $StudentPortal_model->getNonugDetails($academic_year_id);
                //print_r($SubProgram);die;
                echo '<option value="">Select </option>';
                foreach ($student_data as $k => $val) {
                    echo '<option value="' . $k . '" >' . $val . '</option>';
                }
                die;
            }
        }
    }
public function ajaxGetNonpgDetailsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            if ($academic_year_id) {
                $StudentPortal_model = new Application_Model_StudentPortal();
                $student_data = $StudentPortal_model->getNonpgDetails($academic_year_id);
                //print_r($SubProgram);die;
                echo '<option value="">Select </option>';
                foreach ($student_data as $k => $val) {
                    echo '<option value="' . $k . '" >' . $val . '</option>';
                }
                die;
            }
        }
    }
    
      public function getStudentAdmitcardAction() {
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
                $result = $Studentreport_model->getStudentadmitRecord($academic_id, $stu_id);
              
                $this->view->corecourseresult = $result;
            }
        }
    }
    
    
    public function getNonugAdmitcardAction() {
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
                $result = $Studentreport_model->getNonugadmitRecord($academic_id, $stu_id);
                $this->view->corecourseresult = $result;
            }
        }
    }
    
    
    
     public function getNonpgAdmitcardAction() {
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
                $result = $Studentreport_model->getNonpgadmitRecord($academic_id, $stu_id);
                $this->view->corecourseresult = $result;
            }
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
                       // die();
                    $stu_id=md5(trim($st_id)); 
                    
                   $getvalue =$allow_model-> getRecordforadmit($st_id,$semester);
                   if(empty($getvalue)){
                       echo "<script>
                          alert('You enter Invalid Form Id OR you Choose Wrong Semester');
                          window.location.href='application/admitcard';
                          </script>"; 
                       die();
                   }
                    $this->view->fid = $st_id;
                    $result =$student_exam_form->getRecordbyfid($stu_id);
                    $exam_roll = $result['examination_id'];
                  //  echo "<pre>"; print_r($result);exit;
                    $checkk=$allow_model->checkRecordByUid($stu_id); 
                    $this->view->stu_allow = $checkk;
               // echo "<pre>"; print_r($result);exit;
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
                    $result['examination'] =   'October'.$result['exam_year']  ;
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
                    $result['examination'] =   'Oct. '.$result['exam_year']  ;
                     $this->view->feeamount = $fee_pay['examFee'];
                      $currentdate= date('Y-m-d');
            
                   $this->view->exam_roll = $result['examination_id'];
                   $this->view->stu_pic_path = $stu_image['filename'];
                   $this->view->stu_roll = $stu_image['roll_no'];
                   $this->view->ge_id = $this->aeccConfig[0];
                   $this->view->stu_name = $result['stu_name'];
                   $this->view->stu_fname = $result['fname'];
                   $this->view->semester = '2nd Semester';
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
                    $stu_image = $assignment_model->getImage($stu_id);
                   $ac_details = $academic_details->getRecord($result['academic_year_id']);
                 
                    $this->view->payactivaion = $result['payment_status'];
                    $acad_term_arr['batch_id'] = $result['academic_year_id'];
                   $acad_term_arr['term_id'] = $result['term_id'];
                  
                 
                    $result['year'] = date('Y'); 
                    $result['exam_year'] = date('Y'); 
                    $result['college'] = 'Patna Women\'s College';
                    $result['examination'] =   'Oct '.$result['exam_year']  ;
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

    /* =======start 07-01-2018 public function ajaxGetFacultyAction() {
      $employee_model = new Application_Model_HRMModel();
      $faculty = new Application_Model_Attendance();
      $term_id = $this->_getParam('term_id');
      $batch_id = $this->_getParam('academic_year_id');
      $course_id = $this->_getParam('course_id');
      $result = $faculty->getFaculty($term_id, $batch_id, $course_id);

      $result[0]['faculty_id'] .= ',' . $result[0]['employee_id'];
      // print_r($course_cordinatior);exit;
      $faculty_id = explode(',', $result[0]['faculty_id']);
      $visiting_faculty = explode(',', $result[0]['visiting_faculty_id']);

      //======[MERGING BOTH FACULTY ARRAY]=======//
      $faculty_arr = array_merge($faculty_id, $visiting_faculty);

      //====={MAKING ARRAY UNIQUE}==============//
      $all_unique_faculty = array_unique($faculty_arr);

      //======[GETTING NAME OF AL THE EMPLYOEE]=======//
      for ($i = 0; $i < count($all_unique_faculty); $i++) {
      if ($all_unique_faculty[$i] != 'NA')
      $empl_name[$i] = $employee_model->getAllEmployee($all_unique_faculty[$i])[0];
      }
      //=========[SETTING SELECT BOX]=========//

      if (count($empl_name) > 0) {
      foreach ($empl_name as $key => $value) {
      echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
      }die;
      }

      die;
      } *///=======End 07-01-2018 by ashutosh 
    
    //Payment Transaction repert : Date: 27 Oct 2020 : Kedar
    public function paymentTxnReportAction(){
        $this->view->action_name = 'direct-final-grade';
        $this->view->sub_title_name = 'direct-final-grade';
        $this->accessConfig->setAccess('SA_ACAD_TXN_DETAILS');
        $payment_form =  new Application_Form_PayManager();
        $this->view->form = $payment_form;    
    }
    public function ajaxGetTxnRecordAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $feeCollection = new Application_Model_FeesCollection();
            $to_date = $this->_getParam("to_date");
            $from_date = $this->_getParam("from_date");
            $dept = $this->_getParam("department");
            $sem = $this->_getParam("sem");
          
            $result = $feeCollection->getPayRecordsByMerSingleForFilterData($to_date,$from_date,$dept,$sem);
            $paginator_data = array(
                'result' => $result
            );
            
            //echo"<pre>";print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
           
         
        }
    }
    public function studentFeeInstallmentAction(){
        $this->view->action_name = 'direct-final-grade';
        $this->view->sub_title_name = 'direct-final-grade';
        $this->accessConfig->setAccess('SA_ACAD_FEE_INSDETAIL');
        $payment_form =  new Application_Form_PayManager();
        $this->view->form = $payment_form;    
    }
    public function ajaxGetFeesInstallmentRecordAction(){
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $feeCollection = new Application_Model_FeesCollection();
            $dept = $this->_getParam("department");
            $sem = $this->_getParam("sem");
          
            $result = $feeCollection->getStudentFeeInstallmentRecords($dept,$sem);
            $paginator_data = array(
                'result' => $result
            );
            
            //echo"<pre>";print_r($paginator_data);exit;
            $this->view->paginator = $this->_act->pagination($paginator_data);
           
         
        }
    }
    
}
