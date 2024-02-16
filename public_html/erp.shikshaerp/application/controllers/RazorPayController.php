<?php

class RazorPayController extends Zend_Controller_Action {



    private $_siteurl = null;

    private $_db = null;

    private $_flashMessenger = null;

    private $_authontication = null;

    private $_agentsdata = null;

    private $_usersdata = null;

    private $_act = null;

    private $_adminsettings = null;

	Private $_unit_id = null;

        private $cur_datetime;

        public $year_id = array(

            1 => 'First Year',

            2 => 'Second Year'        

        );

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

        if( isset($data) ){

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

           if($data->role_id == 0)

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

        $this->view->action_name = 'razr';

        $this->view->sub_title_name = 'razr';

        $this->accessConfig->setAccess('SA_ACAD_RAZR');

        $razor = new Application_Model_Razordb();

		

		$allotment_id = $this->_getParam("id");

        $type = $this->_getParam("type");

		$this->view->type = $type;

      

		

        switch ($type) {

            case "add":    

                if ($this->getRequest()->isPost()) {

                    if ($ExperientialAllotment_form->isValid($this->getRequest()->getPost())) {

                        $data = $ExperientialAllotment_form->getValues();					

                        $last_insert_id = $ExperientialAllotment_model->insert($data);

						//print_r($last_insert_id);die;

						$employee_allocation = $this->getRequest()->getPost('employee');

						//print_r($employee_allocation);die;

					    foreach($employee_allocation['credit_id'] as $key=>$credit_id){

							//print_r($employee_allocation);die;

                        $item_data['allotment_id'] = $last_insert_id;

						$item_data['course_id'] = $employee_allocation['course_id'][$key];

						$item_data['year_id'] = $employee_allocation['year_id'][$key];

						$item_data['credit_id'] = $credit_id;

						$item_data['department_id'] = $employee_allocation['department_id'][$key];

						$item_data['employee_id'] = $employee_allocation['employee_id'][$key];

						//print_r($item_data);die;

						$ExperientialAllotmentItems_model->insert($item_data);

						}

                        $this->_flashMessenger->addMessage('Employee Successfully added');

                        $this->_redirect('experiential/learning-allotment'); 

						}

                    }

                break;

            case 'edit': 

                $result = $ExperientialAllotment_model->getRecord($allotment_id);

				$this->view->emp_allotment_id = $allotment_id;

				$item_result= $ExperientialAllotmentItems_model->getRecords($allotment_id);

				//print_r($item_result);die;

				//$this->view->item_result = $item_result;

                $ExperientialAllotment_form->populate($result);

				$this->view->result = $result;

                if ($this->getRequest()->isPost()) {

                    if ($ExperientialAllotment_form->isValid($this->getRequest()->getPost())) {

                        $data = $ExperientialAllotment_form->getValues();

						$ExperientialAllotment_model->update($data, array('allotment_id=?' => $allotment_id));

						$employee_allocation = $this->getRequest()->getPost('employee');

						

				         //print_r($employee_allocation);die;

					    $employee_allocation = $this->getRequest()->getPost('employee');

						//print_r($employee_allocation);die;

						$ExperientialAllotmentItems_model->trashItems($allotment_id);

					    foreach($employee_allocation['credit_id'] as $key=>$credit_id){

					   //print_r($employee_allocation);die;

                        $item_data['allotment_id'] = $allotment_id;

						$item_data['course_id'] = $employee_allocation['course_id'][$key];

						$item_data['year_id'] = $employee_allocation['year_id'][$key];

						$item_data['credit_id'] = $credit_id;

						$item_data['department_id'] = $employee_allocation['department_id'][$key];

						$item_data['employee_id'] = $employee_allocation['employee_id'][$key];

						//print_r($item_data);die;

						//$ExperientialAllotmentItems_model->update($item_data, array('allotment_id=?' => $allotment_id));

						$ExperientialAllotmentItems_model->insert($item_data);

						}

											

                        $this->_flashMessenger->addMessage('Details Updated Successfully');

                        $this->_redirect('experiential/learning-allotment');

                    } else {         

                    }

                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($allotment_id){

                    $ExperientialAllotment_model->update($data, array('allotment_id=?' => $allotment_id));

					$this->_flashMessenger->addMessage('Details Deleted Successfully');

					$this->_redirect('experiential/learning-allotment');

				}

                break;

            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $application = new Application_Model_EntranceExamScheduleModel();
               
                $result = $razor->getRecords();
                
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;

        }

    }

}