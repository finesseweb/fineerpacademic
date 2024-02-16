<?php 

//ini_set('display_errors', '1');
class MultiStepFormController extends Zend_Controller_Action {
    
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
    private $_sms = NULL;
    private $_razor = NULL;
    private $_pay_mode = Null;
    private $_base_url= Null;
    
    private $mainconfig = null;
    
  

    public function init() {

        $zendConfig = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        require_once APPLICATION_PATH . '/configs/access_level.inc';
        $this->accessConfig = new accessLevel();

        $config = $zendConfig->mainconfig->toArray();
        $this->mainconfig['public_path'] = $config['host'];
      
        $this->_sms = $zendConfig->sms->toArray();
         $this->_pay_mode = $zendConfig->pay_mode;
        $this->_razor = $zendConfig->razor->toArray();

        $this->view->mainconfig = $config;
        $this->_base_url = $config['host'];
        $this->_action = $this->getRequest()->getActionName();

        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->aeccConfig = $config_role = $zendConfig->aecc_course->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("user_login");
        $this->login_storage = $data = $storage->user_login;
        /*  
            $storage = new Zend_Session_Namespace("admin_login");
            $this->login_storage = $data = $storage->admin_login; 
        */
        $this->view->login_storage = $data; 
        //echo '<pre>'; print_r($_SESSION); exit;
        $data = $_SESSION['user_login']['user_login'];
        $storage->unique_id =  $_SESSION['user_login']['unique_id'];
          $user_log = new Application_Model_UserLog();
        if ($data['app_id']) {
            $user_log_details = $user_log->getRecordByemplId($data['app_id'], $_SESSION['user_login']['unique_id']);
            // echo $data['app_id']; die;
            // $storage->user_login->app_id = $data['app_id'];
            if ($user_log_details) {

                if (!isset($_COOKIE['user_login_status'])) {
                    $user_log->update(array("status" => 0), array("empl_id=?" => $data['app_id'], "unique_id=?" => $_SESSION['user_login']['unique_id'], "status=?" => 1));
                    $storage->unsetAll();
                    setcookie('user_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
                    $this->_redirect("entrance-exam-form");
                } else {
                    $this->view->role_set = $data->role_set;
                }
            } else {
                $storage->unsetAll();
                setcookie('user_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false);
                $this->_redirect("entrance-exam-form");
            }
        }
 else {
      $storage->unsetAll();
     setcookie('user_login_status', "", time() - (60 * $this->cookie_expire), "/", "", false); 
      $this->_redirect("entrance-exam-form");
 }
        
         $this->_helper->layout->setLayout("applicationlayout");


        $this->_flashMessenger = $this->_helper->FlashMessenger;
        //$this->authonticate();

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
        $multi_step_entrance_form = new Application_Form_MultiStepEntranceExamForm();
        $Form_validation = new Application_Model_FormValidation();
        $this->view->form = $multi_step_entrance_form;
        $type = $this->_getParam("type");
        $application_id = $this->_getParam("a_id");
        $this->view->a_id = $application_id;
        $entrance_model = new Application_Model_ApplicantRegisterationModel();
        $applicantCourseDetailModel = new Application_Model_ApplicantCourseDetailModel();
        $applicantEducationalDetailModel = new Application_Model_ApplicantEducationalDetailModel();
        $applicantPersonalDetailModel = new Application_Model_ApplicantPersonalDetailModel();
        $applicantPaymentDetailModel = new Application_Model_ApplicantPaymentDetailModel();
        $this->view->type = $type;
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token']; 
        switch ($type) {
            case "step1":
                //echo '<pre>'; print_r($_SESSION);exit;
                if($application_id){
                    $followUpData = $entrance_model->getRecordByAppNo($application_id);
                    $populateData = $applicantCourseDetailModel->getsavedData($application_id);
                   
                    if($populateData){
                        $multi_step_entrance_form->populate($populateData);
                        //echo '<pre>'; print_r($populateData);
                        $this->view->paginator = $populateData;
                    }
                    if ($this->getRequest()->getPost()) {
                        $data = $this->getRequest()->getPost();
                        //echo '<pre>'; print_r($data);exit;
                        if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){
                            if(empty($data['course']))
                                    $this->_refresh(3,$this->base_url."multi-step-form/index/type/step1/a_id/{$application_id}",'Course field is missing....');
                            if($data['degree_id'] == 1){

                                if(empty($data['firstChoice']))
                                    $this->_refresh(3,$this->base_url."multi-step-form/index/type/step1/a_id/{$application_id}",'Core Course field is missing....');
                                if(empty($data['geFirst']))
                                    $this->_refresh(3,$this->base_url."multi-step-form/index/type/step1/a_id/{$application_id}",'Generic Elective field is missing....');
                            }
                            //echo '<pre>'; print_r($data);exit;
                            if(empty($this->getRequest()->getPost('firstChoice'))){   
                                $data['firstChoice'] ='0';
                            }
                            if(empty($this->getRequest()->getPost('geFirst'))){   
                                $data['geFirst'] ='0';
                            }
                            if(empty($this->getRequest()->getPost('secondChoice'))){   
                                $data['secondChoice'] ='0';
                            }
                            if(empty($this->getRequest()->getPost('geSecond'))){   
                                $data['geSecond'] ='0';
                            }
                            if(empty($this->getRequest()->getPost('aecc2'))){   
                                $data['aecc2'] ='0';
                            }
                            //For User Id
                            if(isset($_SESSION['user_login']['user_login']))
                                $empl_id=$_SESSION['user_login']['user_login']['app_id'];
                            if(isset($_SESSION['admin_login']['admin_login']))
                                $empl_id=$_SESSION['admin_login']['admin_login']->user_id;
                            $applicantCourseData = array(
                               'application_no' => $followUpData['application_no'],
                               'applicant_name' => $followUpData['applicant_name'],
                               'email_id' => $followUpData['email_id'],
                               'phone' => $followUpData['phone_number'],
                               'degree_id' => $data['degree_id'],
                               'course' => $data['course'],
                               'session' => $data['session'],
                               'core_course1' =>$data['firstChoice'],
                               'ge1' =>$data['geFirst'],
                               'aecc1' => $data['aecc1'],
                               'core_course2' => $data['secondChoice'],
                               'ge2' => $data['geSecond'], 
                               'aecc2' => $data['aecc2'],
                               'comp_evs' => $data['evs1'],
                               'srutiny_flag'=>1,
                               'scrutiny_date'=> date('Y-m-d'),
                               'user_id'=>$empl_id,
                               'ip_address'=> $_SERVER['REMOTE_ADDR']
                            );

                        $savedData = $applicantCourseDetailModel->getsavedData($application_id);
                        unset($_SESSION["token"]);
                        if($savedData){
                            //echo '<pre>'; print_r($applicantCourseData);exit;
                                $applicantCourseDetailModel->update($applicantCourseData,array('application_no=?' => $savedData['application_no']));
                                $paymentTableUpdateData = array(
                                    'course'=>$data['course'],
                                    'srutiny_flag'=>1,
                                    'scrutiny_date'=> date('Y-m-d')
                                );
                                //echo '<pre>'; print_r($paymentTableUpdateData);exit;
                                $updatePaymentTable=$applicantPaymentDetailModel->update($paymentTableUpdateData,array('application_no=?' => $savedData['application_no']));
                                if($updatePaymentTable){
                                $this->_redirect($this->_base_url."multi-step-form/index/type/step2/a_id/{$application_id}");
                                }else{
                                    echo 'Update Failed.';
                                    $this->_redirect($this->_base_url."multi-step-form/index/type/step2/a_id/{$application_id}");
                                }

                        }else{
                            //echo '<pre>'; print_r($applicantCourseData);exit;
                            $applicantCourseDetailModel->insert($applicantCourseData);
                            if($applicantCourseDetailModel){
                                $this->_redirect($this->base_url."multi-step-form/index/type/step2/a_id/{$application_id}"); 

                            }else{
                                $this->_redirect($this->base_url."multi-step-form/index/type/step1/a_id/{$application_id}"); 
                            }
                        }
                }else{
                    $this->_refresh(3,$this->_base_url."multi-step-form/index/type/step1/a_id/{$application_id}",'Invalid Token ..');
                }
                }
            }
        }
                
                break;
            case 'step2':
               
                $applicantPersonalDetailForm = new Application_Form_MultiStepExamFormStep2();
                $this->view->form = $applicantPersonalDetailForm;
                
                  //echo $application_id;
                if($application_id){
                    $followUpData = $entrance_model->getRecordByAppNo($application_id);
                    //echo '<pre>'; print_r($followUpData);exit;
                    $applicantPersonalDetailForm->populate($followUpData);
                    
                    $populatedData = $applicantPersonalDetailModel->getsavedData($application_id);
                    //echo '<pre>'; print_r($populatedData);exit;
                    if(!empty($populatedData)){
                        $applicantPersonalDetailForm->populate($populatedData);
                        //echo '<pre>'; print_r($populatedData);exit;
                        $this->view->paginator = $populatedData;
                    }
                       
                        if ($this->getRequest()->getPost()) {
                        $data = $this->getRequest()->getPost();
                        //echo '<pre>'; print_r($data);exit;
                        if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){
                           
                            if($data['religion'] !== 'others'){
                                unset($data['others_religion']);
                            }
                           
                            $applicantPersonalData = array(
                               'application_no' => $followUpData['application_no'],
                               'applicant_name' => $Form_validation->checkAlphabetsInput($data['applicant_name']),
                               'email_id' =>$data['email_id'],
                               'phone_number' => $Form_validation->checkNumberInput($data['phone_number']),
                               'dob_date' => $data['dob_date'],
                               'gender' =>$data['gender'],
                               'aadhar_number' =>$Form_validation->checkNumberInput($data['aadhar_number']),
                               'nationality' => $data['nationality'],
                               'religion' => $data['religion'],
                               'others_religion' => $Form_validation->checkAlphabetsInput($data['others_religion']),
                               'father_name' => $Form_validation->checkAlphabetsInput($data['father_name']), 
                               'father_qual' => $Form_validation->checkAlphabetsInput($data['father_qual']),
                               'father_occup' => $Form_validation->checkAlphabetsInput($data['father_occup']),
                               'mother_name' => $Form_validation->checkAlphabetsInput($data['mother_name']),
                               'mother_qual' => $Form_validation->checkAlphabetsInput($data['mother_qual']),
                               'mother_occup' => $Form_validation->checkAlphabetsInput($data['mother_occup']),
                               'guard_name' => $Form_validation->checkAlphabetsInput($data['guard_name']),
                               'guard_qual' => $Form_validation->checkAlphabetsInput($data['guard_qual']),
                               'guard_occup' => $Form_validation->checkAlphabetsInput($data['guard_occup']),
                               'guard_contact' =>$Form_validation->checkNumberInput($data['guard_contact']),
                               'father_contact' =>$Form_validation->checkNumberInput($data['father_contact']),
                               'mother_contact' =>$Form_validation->checkNumberInput($data['mother_contact']),
                               'p_address' =>$Form_validation->checkValidInput($data['p_address']),
                               'p_homeTown' => $Form_validation->checkValidInput($data['p_homeTown']),
                               'p_postOffice' => $Form_validation->checkValidInput($data['p_postOffice']),
                               'p_policeSt' => $Form_validation->checkValidInput($data['p_policeSt']), 
                               'p_district' => $Form_validation->checkValidInput($data['p_district']),
                               'p_state' => $Form_validation->checkAlphabetsInput($data['p_state']),
                               'p_code_number' =>$Form_validation->checkNumberInput($data['p_code_number']),
                               'same_address' =>$data['sameAddress'],
                               'l_address' =>$Form_validation->checkValidInput($data['l_address']),
                               'l_homeTown' => $Form_validation->checkValidInput($data['l_homeTown']),
                               'l_postOffice' => $Form_validation->checkAlphabetsInput($data['l_postOffice']),
                               'l_policeSt' =>$Form_validation->checkAlphabetsInput($data['l_policeSt']), 
                               'l_district' => $Form_validation->checkAlphabetsInput($data['l_district']),
                               'l_state' => $Form_validation->checkValidInput($data['l_state']),
                               'l_code_number' => $data['l_code_number'],
                               'blood_group' =>$Form_validation->checkValidInput($data['blood_group'])

                            );
                          
                         $savedData = $applicantPersonalDetailModel->getsavedData($application_id);
                         unset($_SESSION["token"]);
                         if($savedData){
                            
                            $applicantPersonalDetailModel->update($applicantPersonalData,array('application_no=?' => $savedData['application_no']));
                       
                             $this->_redirect($this->_base_url."multi-step-form/index/type/step3/a_id/{$application_id}"); 
                        
                           
                    }else{
                             
                        $applicantPersonalDetailModel->insert($applicantPersonalData); 
                        if($applicantPersonalDetailModel){
                            $this->_redirect($this->_base_url."multi-step-form/index/type/step3/a_id/{$application_id}"); 
                            $this->view->type = 'step3'; 
                        }else{
                            $this->_redirect($this->_base_url."multi-step-form/index/type/step2/a_id/{$application_id}"); 
                        }
                    } 
                    
                }else{
                    $this->_refresh(3,$this->_base_url."multi-step-form/index/type/step2/a_id/{$application_id}",'Invalid Token ..');
                }
            }
        }
    }
                
                break;
            case 'step3':
                $applicantEducationalDetailForm = new Application_Form_MultiStepExamFormStep3();
                $this->view->form = $applicantEducationalDetailForm;
                if($application_id){
                    $followUpData = $applicantCourseDetailModel->getRecordByAppNo($application_id);
                    $this->view->followup = $followUpData;
                    //echo '<pre>'; print_r($followUpData); exit;
                    $app_number=$followUpData['application_no'];
                    $populatedData = $applicantEducationalDetailModel->getsavedData($application_id);
                    //echo '<pre>'; print_r($populatedData);exit;
                    if(!empty($populatedData)){
                        $applicantEducationalDetailForm->populate($populatedData);
                        $this->view->paginator = $populatedData;     
                    }
                        //echo '<pre>'; print_r($_POST); exit;
                       if ($this->getRequest()->getPost()) {
                        $data = $this->getRequest()->getPost();
                        //echo '<pre>'; print_r($data);exit;
                        if(!empty($data['csrftoken'])) {
                        if($data['csrftoken']===$token ){
                        //echo '<pre>'; print_r($data); exit;
                        $dirPath1 = realpath(APPLICATION_PATH . '/../public/images') .'/applicant_photo/';
                        $dirPath2 = realpath(APPLICATION_PATH . '/../public/images') .'/applicant_edu_certificate/';
                        $dirPath3 = realpath(APPLICATION_PATH . '/../public/images') .'/applicant_caste_certificate/';
                        $dirPath4 = realpath(APPLICATION_PATH . '/../public/images') .'/applicant_baptism/';

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
                            if(!empty($_FILES['casteCertificate']['name'])){
                                $caste_temp = explode(".", $_FILES["casteCertificate"]["name"]);
                                $casteCertificate = $app_number . '.' . end($caste_temp);
                                $cast_temp = $_FILES['casteCertificate']['tmp_name'];
                                //echo $_FILES["casteCertificate"]["name"];exit;
                            }
                            
                            if(!empty($_FILES['educertificate']['name'])){
                                $marks_temp = explode(".", $_FILES["educertificate"]["name"]);
                                $marks_sheet = $app_number . '.' . end($marks_temp);
                                $edu_temp = $_FILES['educertificate']['tmp_name'];
                            }
                            
                            if(!empty($_FILES['photo']['name'])){
                                $tem_name = $_FILES['photo']['tmp_name'];
                                $temp = explode(".",$_FILES["photo"]["name"]);
                                $photo = $app_number . '.' . end($temp);
                            }
                            
                            if(!empty($_FILES['baptism']['name'])){
                                $temp_baptism = explode(".", $_FILES["baptism"]["name"]);
                                $baptism = $app_number . '.' . end($temp_baptism);
                                $bapt_temp=$_FILES['baptism']['tmp_name'];
                            }
                            
                        //For Mime Type Check
                        $image_mime_edu = $this->getMimeType($edu_temp);    
                        $image_mime_caste = $this->getMimeType($cast_temp);    
                        $image_mime_photo = $this->getMimeType($tem_name);      
                        $image_mime_bapt = $this->getMimeType($bapt_temp);    
                            
                        //echo '<pre>'; print_r($image_mime_photo);exit;    
                        $EduFileextensionName = explode (".", $_FILES['educertificate']['name']); 
                        $CasteFileextensionName = explode (".", $_FILES['casteCertificate']['name']); 
                        $baptFileextensionName = explode (".", $_FILES['baptism']['name']); 
                        $photoFileextensionName = explode (".", $_FILES['photo']['name']); 
                        //echo '<pre>'; print_r($FileextensionName[1]);exit;
                        $array = array(
                            pdf => 'pdf', 
                            jpg => 'jpg',
                            jpeg => 'jpeg', 
                            PNG => 'PNG',
                            JPG=>'JPG',
                            png=>'png'
                        );
                        $eduCertValidate = array_search($EduFileextensionName[1],$array);
                        $casteCertValidate = array_search($CasteFileextensionName[1],$array);
                        $baptCertValidate = array_search($baptFileextensionName[1],$array);
                        $photoCertValidate = array_search($photoFileextensionName[1],$array);
                        //echo '<pre>'; print_r($eduCertValidate);exit;
                        
                            if(file_exists($_FILES['casteCertificate']['tmp_name'])){
                                if(!empty($casteCertValidate) && !empty($image_mime_caste)){
                                move_uploaded_file($cast_temp, $dirPath3 . $casteCertificate);
                                }else{
                            $this->_refresh(3,$this->_base_url."multi-step-form/index/type/step3/a_id/{$application_id}",'Invalid Image Type ..');
                                 }
                            }
                            else{
                               move_uploaded_file($cast_temp, $dirPath3 . $casteCertificate);
                            }
                            
                                //echo '<pre>'; print_r($eduCertValidate);exit;
                        
                            if(file_exists($_FILES['educertificate']['tmp_name']) && !empty($image_mime_edu)){
                                
                                if(!empty($eduCertValidate)){
                                    move_uploaded_file($edu_temp, $dirPath2 . $marks_sheet);
                                } else{
                                    
                            $this->_refresh(3,$this->_base_url."multi-step-form/index/type/step3/a_id/{$application_id}",'Invalid Image Type ..');
                                }
                            }
                            else{
                                move_uploaded_file($edu_temp, $dirPath2 . $marks_sheet);
                            }
                        
                        
                            if(file_exists($_FILES['baptism']['tmp_name'])){
                                if(!empty($baptCertValidate) && !empty($image_mime_bapt)){
                                move_uploaded_file($sign_temp, $dirPath4 . $baptism);
                                }else{
                            $this->_refresh(3,$this->_base_url."multi-step-form/index/type/step3/a_id/{$application_id}",'Invalid Image Type ..');
                                }
                            }
                            else{
                                move_uploaded_file($sign_temp, $dirPath4 . $baptism);
                            }
                        
                        
                            if(file_exists($_FILES['photo']['tmp_name'])){
                                if(!empty($photoCertValidate) && !empty($image_mime_photo)){
                                move_uploaded_file($tem_name, $dirPath1 . $photo);
                                }else{
                            $this->_refresh(3,$this->_base_url."multi-step-form/index/type/step3/a_id/{$application_id}",'Invalid Image Type ..');
                                }
                            }
                            else{
                                move_uploaded_file($tem_name, $dirPath1 . $photo);
                            }
                        

                        if(file_exists($_FILES['photo']['tmp_name'])){
                            if(!move_uploaded_file($tem_name, $dirPath1 . $photo))
                            { 
                                  switch ($_FILES['photo']['error']) {
                                    case 1:
                                        echo "Exceed File size limit ..Please Upload your photo size 50 to 100kb <br/><a href ='$this->base_url.multi-step-form/index/type/step3/a_id/{$application_id}'>Back</a>";
                                        header( "refresh:3;url=$this->base_url.multi-step-form/index/type/step3/a_id/{$application_id}" );die;
                                        break;
                                    default:
                                        continue;
                                    }
                                    
                                
                            }
                        }
                        else{
                            if(!move_uploaded_file($tem_name, $dirPath1 . $photo))
                            { 
                                switch ($_FILES['photo']['error']) {
                                    case 1:
                                        echo "Exceed File size limit ..Please Upload your photo size 50 to 100kb <br/><a href ='$this->base_url.multi-step-form/index/type/step3/a_id/{$application_id}'>Back</a>";
                                         header( "refresh:3;url=$this->base_url.multi-step-form/index/type/step3/a_id/{$application_id}" );die;
                                        break;
                                    default:
                                        continue;
                                    }
                                   
                                
                            }
                        }
                        
                          
                        $file_edu['educertificate'] = "public/images/applicant_edu_certificate/" .$marks_sheet;
                        $file_caste['casteCertificate'] = "public/images/applicant_caste_certificate/" .$casteCertificate;
                        $file_sign['baptism'] = "public/images/applicant_baptism/" .$baptism;
                        $file_photo['photo'] = "public/images/applicant_photo/" .$photo;
                        
                        $data['ExtraCurricularSelection']= implode(',',$data['ExtraCurricularSelection']);
                        $data['application_no'] =  $followUpData['application_no'];
                        $data['applicant_name'] =  $followUpData['applicant_name'];
                        $data['email_id'] =  $followUpData['email_id'];
                        $data['phone'] =  $followUpData['phone'];
                        
                        $data['educertificate'] = $file_edu['educertificate'];
                        $data['casteCertificate'] =$file_caste['casteCertificate'];
                        $data['photo'] = $file_photo['photo'];
                        $data['baptism'] = $file_sign['baptism'];
                        
                        $savedData = $applicantEducationalDetailModel->getsavedData($application_id);  
                        unset($_SESSION["token"]);
                        //echo '<pre>'; print_r($savedData); exit;
                        if($savedData){
                                //echo '<pre>'; print_r($_FILES);exit;
                                if(empty($_FILES['photo']['name'])){
                                    unset($data['photo']); 
                                }
                                if(empty($_FILES['baptism']['name'])){
                                    unset($data['baptism']);
                                }
                                if(empty($_FILES['educertificate']['name'])){
                                    unset($data['educertificate']); 
                                }
                                if(empty($_FILES['casteCertificate']['name'])){
                                    unset($data['casteCertificate']); 
                                }
                                if(empty($data['certificate_no'])){
                                    $data['certificate_no']='N/A';
                                }
                                if(empty($data['certificate_issued'])){
                                    $data['certificate_issued']='N/A';
                                }
                             //echo '<pre>'; print_r($data);exit;
                                unset($data['csrftoken']);
                                $applicantEducationalDetailModel->update($data,array('application_no=?' => $savedData['application_no']));
                            $this->_redirect($this->_base_url."multi-step-form/index/type/step4/a_id/{$application_id}"); 
                           
                        }else{ 
                             //echo '<pre>'; print_r($data);exit;
                            if(empty($_FILES['baptism']['name'])){
                                unset($data['baptism']);
                            }
                            if(empty($_FILES['casteCertificate']['name'])){
                                unset($data['casteCertificate']); 
                            }
                            if(empty($_FILES['educertificate']['name'])){
                                unset($data['educertificate']); 
                            }
                             // echo '<pre>'; print_r($data);exit;
                                 unset($data['csrftoken']);
                                $insertData=$applicantEducationalDetailModel->insert($data);
                            
                             //echo '<pre>'; print_r($data.'sm');exit;
                            if($insertData){
                                $this->_redirect($this->base_url."multi-step-form/index/type/step4/a_id/{$application_id}"); 
                                $this->view->type = 'step4'; 
                            }else{
                                $this->_redirect($this->base_url."multi-step-form/index/type/step3/a_id/{$application_id}"); 
                            }
                        }
                    }else{
                    $this->_refresh(3,$this->base_url."multi-step-form/index/type/step3/a_id/{$application_id}",'Invalid Tokens..');
                    }
                }
            }
        }
                break;
                case 'step4':
                    $allFormData = new Application_Model_ApplicantCourseDetailModel();
                    //echo $application_id; exit;
                    $application_no = $application_id;
                    if($application_no){
                        $formFilledData=$allFormData->getAllFormFilledData($application_no);         
                        //echo"<pre>";print_r($formFilledData);exit;
                        $this->view->paginator = $formFilledData;
                    }
                  
                    break;
                case 'step5':
                    $allFormData = new Application_Model_ApplicantCourseDetailModel();
                    $challanData= new Application_Model_ApplicantPaymentDetailModel();
                    $application_no = $application_id;
                    if($application_no){
                        $formFilledData=$allFormData->getAllFormFilledData($application_no);
                        $challan_no = $challanData->getChallan($application_no);
                        //echo"<pre>";print_r($formFilledData);exit;
                        $this->view->paymode = $this->_pay_mode;
                        $this->view->paginator = $formFilledData;
                        $this->view->challan = $challan_no;
                    }
                    break;
            default:
                break;
                
        }
    }
    
    //End
    public function paymentAction(){
        $application_id = $this->_getParam("a_id");
        $this->view->a_id = $application_id;
        $pay_form = new Application_Form_PaymentForm();
        $this->view->form = $pay_form;
        $applicantPaymentDetailModel = new Application_Model_ApplicantPaymentDetailModel();
        
        $keyId= $this->_razor['keyId'];
        $keySecret= $this->_razor['keySecret'];
        $displayCurrency = $this->_razor['displayCurrency'];
        $api = new Application_Model_Razor($keyId, $keySecret);
        if(isset($_POST['pay']))
            $type = $_POST['pay']; 
            $this->view->type = $type;
        switch ($type) {
            case "pay": 
                if($application_id){
             
                $applicantData = new Application_Model_ApplicantCourseDetailModel();
                $applicationdData=$applicantData->getAllFormFilledData($application_id);
                $this->view->paginator = $applicationdData;
                if ($this->getRequest()->getPost()) {
                $data = $this->getRequest()->getPost();
                //echo '<pre>'; print_r($data);exit;
                if(!empty($data['csrftoken'])) {
                if($data['csrftoken']===$token ){
                $insertData = array(
                    'application_no' =>$data['application_no'],
                    'applicant_name' =>$data['applicant_name'],
                    'form_id' =>$data['form_id'],
                    'email_id' =>$data['email_id'],
                    'phone' =>$data['phone'],
                    'course' =>$data['course'],
                    'form_fee' =>$data['form_fee'],
                    'submit_date' => date('Y/m/d'),
                    'payment_mode' =>'Online'
                );
                
                //Payment Gateway Integration
                    $orderData = [
                        'receipt'         => rand(1000, 9999),
                        'amount'          => $data['form_fee'] * 100, // 2000 rupees in paise
                        'currency'        => 'INR',
                        'payment_capture' => 1 // auto capture
                    ];
                    //echo '<pre>'; print_r($orderData);exit;
                    $razorpayOrder = $api->order->create($orderData);
                    $razorpayOrderId = $razorpayOrder['id'];
                    $displayAmount = $amount = round($orderData['amount']);
                   
                    $checkout = 'automatic';

                        $paydata = [
                            "key"               => $keyId,
                            "amount"            => $amount,
                            "name"              => "Patna Women's College",
                            "description"       => "Patna Women's College,Patna",
                            "image"             => "{$this->mainconfig['public_path']}img/logo.png",
                            "prefill"           => [
                            "name"              =>  $data['applicant_name'],
                            "email"             =>  $data['email_id'],
                            "contact"           =>  $data['phone'],
                            ],
                            "notes"             => [
                            "address"           => "XYZ PWC",
                            "merchant_order_id" => rand(1000, 9999),
                            ],
                            "theme"             => [
                            "color"             => "#F37254"
                            ],
                            "order_id"          => $razorpayOrderId,
                        ];

                        if ($displayCurrency !== 'INR')
                        {
                            $paydata['display_currency']  = $displayCurrency;
                            $paydata['display_amount']    = $displayAmount;
                        }
                        
                       // $json = json_encode($paydata); 
                        $this->view->pay_data = $paydata;
             
                     
                $checkPrev = $applicantPaymentDetailModel->checkRow2($data['form_id']);
                unset($_SESSION["token"]);
                if(!empty($checkPrev)){
                    $upsert = $applicantPaymentDetailModel->update($insertData,array('form_id=?'=>$checkPrev));
                    //echo '<pre>'; print_r($upsert); exit;
                }else{
                        
                    $insertData['payment_status'] =1;
                    if( $insertData['payment_status'] == 1 ){
                        $generateRoll=$applicantPaymentDetailModel->checkCourseForRoll($data['course']);
                        //echo '<pre>';print_r($generateRoll) ;exit;
                        if($generateRoll){
                            $insertData['roll_no'] = 1+$generateRoll['roll_no'];
                        }else{
                            $insertData['roll_no'] = 1;
                        }    
                    }
                    // echo '<pre>'; print_r($data);exit;
                    $last_id = $applicantPaymentDetailModel->insert($insertData);
                    if($last_id){
                       
                    }
                }
            }else{
               $this->_refresh(3,$this->base_url."multi-step-form/payment/a_id/{$application_id}",'Invalid Token ..'); 
            }
        }
        }
    }
             
                
                break;
            default :
                $applicantData = new Application_Model_ApplicantCourseDetailModel();
                $applicationdData=$applicantData->getAllFormFilledData($application_id);
                $this->view->paymode = $this->_pay_mode;
                $this->view->paginator = $applicationdData;
        
        }
        
    }
    
    //Ajax Area
    public function ajaxGetCourseAction(){
       $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $degree_id = $this->_getParam("degree_id");
            //print_r($short_code); die;
            $academic_model = new Application_Model_Academic();
            $result = $academic_model->getCourseByDegreeId($degree_id);

            echo "<pre>";print_r($result);
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                //echo "<pre>";print_r($val);exit;
                echo '<option value="' . $val['id'] . '" >' .$val['department_type'] . '</option>';
            }
        }die;
    }
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
            $paginator_data = array(
                    'result' => $result
                );
                //echo"<pre>";print_r($paginator_data);exit;
                $this->view->paginator = $this->_act->pagination($paginator_data);
           
        }
    }
     public function ajaxGetSessionAction(){
       $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $degree_id = $this->_getParam("degree_id");
            //print_r($short_code); die;
            $academic_model = new Application_Model_Academic();
            $result = $academic_model->getSessionByCourseId($degree_id);
            foreach ($result as $values){
                echo '<option value="' . $values['session_id'] . '" >' .$values['session'] . '</option>';
            }
            
           
        }die;
    }
    public function checkFormStepAction(){
       $a_id = $this->_getParam("a_id");
        
        $personal_model = new Application_Model_ApplicantPersonalDetailModel();
        $educational_model = new Application_Model_ApplicantEducationalDetailModel();
        $course_model = new Application_Model_ApplicantCourseDetailModel();
        
        $course_result = $course_model->getsavedData($a_id);
        
        $personal_result = $personal_model->getsavedData($a_id);
        
        $educational_result = $educational_model->getsavedData($a_id);
        
		// echo '<pre>'; print_r( $this->baseUrl); exit;
        if(!empty($educational_result) && !empty($personal_result) && !empty($course_result)){
            echo 'preview_ok';
            //echo $this->_base_url."multi-step-form/index/type/step2/a_id/$a_id";
             
        } else if(!empty($course_result) && !empty($personal_result)){
			echo 'educational_ok';
			//echo $this->_base_url."multi-step-form/index/type/step1/a_id/$a_id";
           
        } else if(!empty($course_result)){
			echo 'personal_ok';
            //echo $this->_base_url."multi-step-form/index/type/step1/a_id/$a_id";
        } else{
            echo 'false';
        }
       die;
    }
    public function ajaxGetGeAction(){
        $Aeccge_course = new Application_Model_Aeccge();
        $batch = new Application_Model_Academic();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_id");
            
            $result = $batch->getRecord($academic_year_id);
           // echo '<pre>'; print_r($result);
            $department_id = $result['department'];
          //  echo $department_id;die;
            $result = $Aeccge_course->getRecordByDepartment2($department_id,$academic_year_id);
            $paginator_data = array(
                                    'result' => $result
                                );
               //echo"<pre>";print_r($paginator_data);exit;
               $this->view->paginator = $this->_act->pagination($paginator_data);
            }
    }
    //End
    //Ajax function for preview Application Form :Date:17 Jan 2020 Kedar
    public function ajaxGenerateFormIdAction(){
        $allFormData = new Application_Model_ApplicantCourseDetailModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $application_no = $this->_getParam("application_no");
            
            $formId=$allFormData->generateFormId($application_no);
            if($formId){
                //$this->_redirect($this->base_url."multi-step-form/index/type/step5/a_id/{$application_no}"); 
            }
            echo $formId;
        }die;
    }
    
 public function verifyAction(){
            $success = true;

            $error = "Payment Failed";

            if (empty($_POST['razorpay_payment_id']) === false)
            {
                   $keyId= $this->_razor['keyId'];
        $keySecret= $this->_razor['keySecret'];
                $api = new Application_Model_Razor($keyId, $keySecret);
                $signature = new Application_Model_RazorVerify();
                try
                {
                    // Please note that the razorpay order ID must
                    // come from a trusted source (session here, but
                    // could be database or something else)
                   
                    $attributes = array(
                        'razorpay_order_id' => $_POST['razorpay_order_id'],
                        'razorpay_payment_id' => $_POST['razorpay_payment_id'],
                        'razorpay_signature' => $_POST['razorpay_signature'],
                    );

                    $api->utility->verifyPaymentSignature($attributes);
                    $payment = $api->payment->fetch($_POST['razorpay_payment_id']);
                    if($payment->card_id)
                    $card = $api->card->fetch($payment->card_id);
                }
                catch(SignatureVerificationError $e)
                {
                    $success = false;
                    $error = 'Razorpay Error : ' . $e->getMessage();
                }
            }
           
            
            $_POST['status']=$success===true?"success":"Failure";
            $razordb = new Application_Model_Razordb();
            $razrcd = new Application_Model_Razorcard();
            $_POST['response'] = $payment;
            $payment->created_at = Date("Y-m-d h:i:S",strtotime($payment->created_at));
            $_POST['response']['applicant_id'] = $payment->notes->applicant_no;
            unset($_POST['response']['notes']);
            $_POST['response']['type'] = "App Form";
          
            $data = (array)$_POST['response'];
          //  echo "<pre>"; print_r($data);exit;
            $last_insert_id = $razordb->insert($data["\0*\0"."attributes"]);
            if($last_insert_id && $payment->card_id){
                
                $card['razr_id'] = $last_insert_id;
                $card = (array)$card;
                 
                $card = $card["\0*\0"."attributes"];
              
                 $razrcd->insert($card);
            }
            $returnVal = $success===true?$_POST:$error;
            $this->view->data = $returnVal;

            }
    //end
    //UpsertPayment Mode
    public function ajaxInsertOfflineModeAction(){
        $paymentData = new Application_Model_ApplicantPaymentDetailModel();
        $courseData = new Application_Model_ApplicantCourseDetailModel();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $form_id= $this->_getParam("form_id");
            $applicant_detail=$courseData->getApplicationNumber($form_id);
            $application_no=$applicant_detail['application_no'];
            //echo '<pre>'; print_r($application_no); exit;
            $getChallan=$paymentData->getChallan();
            $maxChallan= $getChallan['challan_no'];
            $checkexistedData= $paymentData->checkRow($form_id);
            
            $insertData= array(
                'application_no' =>$application_no,
                'applicant_name' =>$this->_getParam("applicant_name"),
                'form_id' =>$this->_getParam("form_id"),
                'email_id' =>$this->_getParam("email_id"),
                'phone' =>$this->_getParam("phone"),
                'course' =>$this->_getParam("course"),
                'challan_no' =>$this->_getParam("challan"),
                'payment_mode' =>'Offline',
                
                'submit_date' => date('Y/m/d')
            );
            // echo '<pre>'; print_r($insertData);exit;
            
          
            if($checkexistedData){
                $upadateData=$paymentData->update($insertData,array("form_id=?" =>$insertData['form_id'] ));  
            }else{
                 $insertData['challan_no'] = $maxChallan+1;
                $payId=$paymentData->insert($insertData);
             
            }
         
            $allFormData = new Application_Model_ApplicantCourseDetailModel();
            $challanData= new Application_Model_ApplicantPaymentDetailModel();
            
                $formFilledData=$allFormData->getAllFormFilledData(md5($application_no));
                $challan_no = $challanData->getChallan(md5($application_no));
                //echo"<pre>";print_r($formFilledData);exit;
               $this->view->paginator = $formFilledData;
               $this->view->challan = $challan_no;
            
        }
    }
    public function fileUploadValidationAction(){
         $file_type = $this->_getParam("file_type");
        //echo '<pre>'; print_r($file_type); exit;
        switch ($file_type[0]) {
          case 'jpeg':
                 echo 1;
              break;
              case 'JPG':
                 echo 1;
                break;
           case 'PNG':
                 echo 1;
                break;
            case 'jpg':
                 echo 1;
                break;
           case 'png':
                echo 1;
                break;
           case 'pdf':
                echo 1;
                break;
           default:
                echo 0;
        }die;
    }
    //Photo Validation Date:17 APril
    public function photoUploadValidationAction(){
         $file_type = $this->_getParam("file_type");
        //echo '<pre>'; print_r($file_type); exit;
        switch ($file_type[0]) {
           case 'jpeg':
                echo 1;
                break;
			case 'JPEG':
                echo 1;
                break;
           case 'jpg':
                 echo 1;
                break;
           case 'JPG':
                 echo 1;
                break;
           case 'PNG':
                 echo 1;
                break;
           case 'png':
                echo 1;
                break;
           
           default:
                echo 0;
        }die;
    }
    
    public function atompaymentAction() {
         
         
         $transactionRequest = new Application_Model_TransactionRequest();
         
        $application_id = $this->_getParam("a_id");
        $this->view->a_id = $application_id;
        $pay_form = new Application_Form_PaymentForm();
        $this->view->form = $pay_form;
        $applicantPaymentDetailModel = new Application_Model_ApplicantPaymentDetailModel();
          $applicationrecord= new Application_Model_ApplicationFeesSubmit();
       // echo $applicantpaymentreord;
       // die();
          if($application_id){
            $applicantData = new Application_Model_ApplicantCourseDetailModel();
            
            
                $applicationdData=$applicantData->getAllFormFilledData($application_id);
                //echo '<pre>'; print_r($applicationdData);exit;
                $this->view->paginator = $applicationdData;
            
            if ($this->getRequest()->getPost()) {
                $data = $this->getRequest()->getPost();
                //echo '<pre>'; print_r($data);exit;
                if(!empty($data['csrftoken'])) {
                if($data['csrftoken']===$token ){
                //print_r($data);
               //die();
            
                $insertData = array(
                        'application_no' =>$data['application_no'],
                        'applicant_name' =>$data['applicant_name'],
                        'form_id' =>$data['form_id'],
                        'email_id' =>$data['email_id'],
                        'phone' =>$data['phone'],
                        'course' =>$data['course'],
                        'form_fee' =>$data['form_fee'],
                        'submit_date' => date('Y/m/d'),
                        'payment_mode' =>'Online'
                );
                
                 
                $application_id = md5($data['application_no']);
                
                        $status_pay = $applicantPaymentDetailModel->payedApplicant($data['application_no']);
                      //  echo "<pre>";print_r($status_pay);exit;
                
                  $checkPrev = $applicantPaymentDetailModel->checkRow2($data['form_id']);
                //echo"<pre>";print_r($checkPrev);exit; 
                // Insert or update Applicant field on the basis of email id in the database
              //  if(!empty($checkPrev)){
                  //  $upsert = $applicantPaymentDetailModel->update($insertData,array('form_id=?'=>$checkPrev));
                    
                    //echo '<pre>'; print_r($upsert); exit;
               /// }else{
                        
//                    $insertData['payment_status'] =1;
//                    if( $insertData['payment_status'] == 1 ){
//                        $generateRoll=$applicantPaymentDetailModel->checkCourseForRoll($data['course']);
//                        //echo '<pre>';print_r($generateRoll) ;exit;
//                        if($generateRoll){
//                            $insertData['roll_no'] = 1+$generateRoll['roll_no'];
//                        }else{
//                            $insertData['roll_no'] = 1;
//                        }    
//                    }
                    // echo '<pre>'; print_r($data);exit;
                    
                    $insertvalue =  $applicantPaymentDetailModel->insert($insertData);
                    $lastId=$applicantPaymentDetailModel->getAdapter()->lastInsertId();
                    $insertrecord = array(
                        'application_id' =>$data['application_no'],
                        'stu_name' =>$data['applicant_name'],
                        'form_id' =>$data['form_id'],
                        'exam_fee' =>$data['form_fee'],
                        'submit_date' => date('Y-m-d'),
                        'payment_id' =>$lastId
                );
                
            if(!$status_pay){
                    $applicationrecord->insert($insertrecord);
                   
            }
            else{
                 echo "You have already made a payment.to download admit card follow the link below <br/><a href ='$this->base_url.entrance-exam-form/document-dashboard/a_id/{$application_id}'>click here</a><p class='text-success'>or your page will be Automaticaly redirect to download page in 5 sec...</p> ";
                                        header( "refresh:5;url=$this->base_url.entrance-exam-form/document-dashboard/a_id/{$application_id}" );die;
                
            }
             unset($_SESSION["token"]);
                   //echo  $lastId ;
                    
                    
                //}
                
                //die();
                        $dept = new Application_Model_DepartmentType();
                        $coursename = $dept->getRecord($data['course'])['department_type'];
                        date_default_timezone_set('Asia/Calcutta');
                        $datenow = date("d/m/Y h:m:s");
                        $transactionDate = str_replace(" ", "%20", $datenow);

                        $transactionId = rand(1,1000000);

                        //Setting all values here
                        $transactionRequest->setMode("live");
                        $transactionRequest->setLogin(53243);
                        $transactionRequest->setPassword("Patna@1234");
                        $transactionRequest->setProductId($data['product_id']);
                        $transactionRequest->setAmount($data['form_fee']);
                        $transactionRequest->setTransactionCurrency("INR");
                        $transactionRequest->setTransactionAmount($data['form_fee']);
                        $transactionRequest->setReturnUrl($this->_base_url  . 'multi-step-form/response/?cid='.$data['course']."_$lastId" );
                        $transactionRequest->setClientCode(53243);
                        $transactionRequest->setTransactionId($transactionId);
                        $transactionRequest->setTransactionDate($transactionDate);
                        $transactionRequest->setCustomerName($data['applicant_name']);
                        $transactionRequest->setCustomerEmailId($data['email_id']);
                        $transactionRequest->setCustomerMobile($data['phone']);
                        $transactionRequest->setCustomerBillingAddress($data['form_id']."|".$coursename);
                       $transactionRequest->setCustomerId($data['application_no']);
                        //$transactionRequest->setCustomerId($data['application_no']."|".$data['form_id']);
                        $transactionRequest->setCustomerAccount("639827");
                        $transactionRequest->setReqHashKey("b338c08a991313f12c");

                        $url = $transactionRequest->getPGUrl();
                        $this->_redirect($url);
            }else{
                $this->_refresh(3,$this->base_url."multi-step-form/atompayment/a_id/{$application_id}",'Invalid Token ..');
            }
        }
    }
    }
}
     
     
     
      public function responseAction(){
          require_once APPLICATION_PATH . '/public/atompay/TransactionResponse.php';
             $transactionResponse = new Application_Model_TransactionResponse();
          //  $transactionResponse = new Application_Model_TransactionResponse();
          
            $transactionResponse = new TransactionResponse();
            $transactionResponse->setRespHashKey("8b013258b7a4b89428");
            
             $getid = $_GET['cid'];
             
             $getdata = explode("_",$getid);
           
             
             $cid = $getdata['0'];
             $uid = $getdata['1'];
             
            // echo   $cid;
            //echo   $uid;
             //die();
               $transactionResponse->validateResponse($_POST);  
              
              $this->view->vaild_response = $_POST;  
              
           // echo "Transaction Processed <br/>";
           // print_r($response);
           // die();
           
          if($_POST['f_code']=='Ok'){
             $applicantPaymentDetailModel = new Application_Model_ApplicantPaymentDetailModel();
             $applicationrecord= new Application_Model_ApplicationFeesSubmit();   
             $generateRoll=$applicantPaymentDetailModel->checkCourseForRoll($cid);

                        if($generateRoll){
                           $generateroll = 1+$generateRoll['roll_no'];
                       }else{
                          $generateroll = 1;
                        }    
             $updatedata = array('payment_status'=>1,'roll_no'=>$generateroll);
             $upsert = $applicantPaymentDetailModel->update($updatedata,array('id=?'=>$uid,'roll_no=?'=>0));
              $updatepayment = array(
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
              
            $applicationrecord->update($updatepayment,array('payment_id=?'=>$uid));
           }
           
           
          // die();
        
          
      }    
    //End
}
