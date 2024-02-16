<?php
class PromotionController extends Zend_Controller_Action {



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
        $this->view->action_name = 'Promotion';
        $this->view->sub_title_name = 'Promotion';
        $this->accessConfig->setAccess('SA_ACAD_PROM');
        $Form_validation = new Application_Model_FormValidation();
        $promotion_form = new Application_Form_PromotionStructure();
        $promotionRuleModel = new Application_Model_SemesterRule();
        $semester_id = $this->_getParam("id");
        //print_r($semester_id);die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $promotion_form;
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    
                        $data = $_POST;
                       // print_r($data);exit;
                        //$last_insert_id = $FeeStructure_model->insert($data);
                        
                       $semesterRuleData = array(
                           'semester' => $data['semester'],
                           'nextSem' => $data['nextSem'],
                           'degree_id' =>$data['degree_id'],
                           'credit_score' =>$Form_validation->checkValidInput($data['credit_score']),
                           'semester_paper_count' => $data['semester_paper_count'],
                           'appeared_paper' => $data['appeared_paper'],
                           'component_paper' => implode(',',$data['component_paper'])  
                        );
                       if(!empty($_POST['csrftoken'])) {
                       if($_POST['csrftoken']===$token ){
                        $promotionRuleModel->insert($semesterRuleData);
                      unset($_SESSION["token"]);
                        $this->_flashMessenger->addMessage('Promotion Rule Successfully added');

                        $this->_redirect('promotion');
                              }  else {
                                     $message="Invalid Token";
                                    $_SESSION['message_class'] = 'alert-danger';
                                    $this->_flashMessenger->addMessage($message);
                                     $this->_redirect('promotion');
                                                  }
                                                      }	
                 

                }





                break;

            case 'edit':

                $result = $promotionRuleModel->getRecordById($semester_id);

                $result['component_paper'] = explode(',',$result['component_paper']);

                $promotion_form->populate($result);

                $this->view->result = $result;

                 //print_r($this->view->result);
 
                if ($this->getRequest()->isPost()) {

                   if ($promotion_form->isValid($this->getRequest()->getPost())) {
//print_r($_POST);exit;
                       $data = $promotion_form->getValues();
                       
                
                        $data['component_paper'] = implode(',',$_POST['component_paper']);

                        $promotionRuleModel->update($data, array('id=?' => $semester_id));



                        //$Fee = $this->getRequest()->getPost('Fee');

                        //print_r($Fee);die;

                       // $Scholarship_model->trashItems($feehead_id); //Delete Fields in Company	

                        $this->_flashMessenger->addMessage('Details Updated Successfully');

                        $this->_redirect('promotion');

                    } else {

                        //$this->_redirect('fee-heads/index');						

                    }

                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($semester_id) {

                    $promotionRuleModel->update($data, array('id=?' => $semester_id));

                    //$FeeHeadItems_model->update($data, array('feehead_id=?' => $feehead_id));

                    $this->_flashMessenger->addMessage('Details Deleted Successfully');

                    $this->_redirect('promotion');

                }

                break;

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $promotionRuleModel->getRecords();

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
    public function ajaxGetPreexistPromotionDataAction(){
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()){
            $promotionRuleModel = new Application_Model_SemesterRule();
            $degree_id = $this->_getParam("degree_id");
            $cmn_term = $this->_getParam("cmn_terms");
            $checkPrev = $promotionRuleModel->checkRow($degree_id,$cmn_term);
           if($checkPrev){
                echo $Display = 1;
            }else{
                echo $Display=0;
             
            }
        }die;
    }


}

