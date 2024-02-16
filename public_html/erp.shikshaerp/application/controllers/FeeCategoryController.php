<?php



class FeeCategoryController extends Zend_Controller_Action {



    private $_siteurl = null;

    private $_db = null;

    private $_flashMessenger = null;

    private $_authontication = null;

    private $_agentsdata = null;

    private $_usersdata = null;

    private $_act = null;

    private $_adminsettings = null;

	Private $_unit_id = null;

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

        $this->view->action_name = 'Fees';

        $this->view->sub_title_name = 'Fee Category';

        $this->accessConfig->setAccess("SA_ACAD_FEE_CAT");

        $Category_model = new Application_Model_FeeCategory();

		$Category_form = new Application_Form_FeeCategory();

		//$Termdate_model = new Application_Model_Termdate();

		$category_id = $this->_getParam("id");
		$acc_id = $this->_getParam("acc_id");
		$session_id_p = $this->_getParam("session");
		$dept_id_p = $this->_getParam("dept");

        $type = $this->_getParam("type");

		$this->view->type = $type;

        $this->view->form = $Category_form;	
if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        switch ($type) {

            case "add":

			

			  $this->view->type = $type;

                $this->view->form = $Category_form;

				

                if ($this->getRequest()->isPost()) {

                    if ($Category_form->isValid($this->getRequest()->getPost())) {

                            $data = $Category_form->getValues();
                             $insertData=array(
                                       'degree_id'=> $data['degree_id'],
                                       'fund_type'=> $data['fund_type'],
                                       'session'=>$data['session'],
                                       'dept_id'=>$data['dept_id']
                    );
                            //  echo '<pre>'; print_r($data);  die();
                              if(!empty($data['csrftoken'])) {
                               if($data['csrftoken']===$token ){

							foreach($_POST['category'] as $key=>$feehead_name )

						{

						  

						$fee_data=array("fund_type"=>$this->getRequest()->getPost('fund_type'),
						                 "degree_id"=>$this->getRequest()->getPost('degree_id'),
						                 "session_id" => $this->getRequest()->getPost('session'),
						                 "dept_id" => $this->getRequest()->getPost('dept_id'),
                                                                  "category_name"=>$feehead_name );

							//print_r($fee_data);die;

							$Category_model->insert($fee_data);	
                                                        unset($_SESSION["token"]);
								

						}

							//print_r($data); die; 
                                                         $_SESSION['message_class'] = 'alert-success';
							$this->_flashMessenger->addMessage('Category Added Successfully');

                                              $this->_redirect('fee-category/index?v2');

                                            }  else {
                                                       $message="Invalid Token";
                                                       $_SESSION['message_class'] = 'alert-danger';
                                                       $this->_flashMessenger->addMessage($message);
                                                      $this->_redirect('fee-category/index?v2');
                                                  }
                                                      }	

					}

				}

		

				break;

           case "edit":

		  

					$this->view->type = $type;

					$this->view->form = $Category_form;

					$result = $Category_model->getRecordbyDegreeId3($category_id,$acc_id,$session_id_p,$dept_id_p);

				//	echo "<pre>";print_r($result);die;

					$this->view->item_result = $result;
					$item_result['degree_id'] = $result[0]['degree_id'];
                    $item_result['fund_type'] = $result[0]['fund_type'];
                    $item_result['session'] = $result[0]['session_id'];
                    $item_result['dept_id'] = $result[0]['dept_id'];
					$Category_form->populate($item_result);

						if ($this->getRequest()->isPost()) {

							if ($Category_form->isValid($this->getRequest()->getPost())) {

							//echo 'dsd'; die;

							$data = $Category_form->getValues();
                                                         // echo '<pre>'; print_r($data);  die();
                                                          if(!empty($data['csrftoken'])) {
                                                           if($data['csrftoken']===$token ){
							$Category_model->delete(array('degree_id=?' => $category_id,'fund_type=?'=>$acc_id,'session_id=?'=>$session_id_p,'dept_id=?'=>$dept_id_p,'category_id not in (?)'=>$_POST['category_id']));
							
							 	foreach($_POST['category_id'] as $key=>$feehead_id )

						{

						  

						$fee_data=array("fund_type"=>$this->getRequest()->getPost('fund_type'),
						                 "degree_id"=>$this->getRequest()->getPost('degree_id'),
						                  "session_id" => $this->getRequest()->getPost('session'),
						                  "dept_id" => $this->getRequest()->getPost('dept_id'),

									    "category_name"=>$_POST['category'][$key] );

							//print_r($fee_data);die;

							$Category_model->update($fee_data,array('category_id =?'=>$feehead_id));	

								

						}
						
							foreach($_POST['category'] as $key=>$feehead_name )

						{
						    if(!isset($_POST['category_id'][$key])){
	                                    $fee_data=array("fund_type"=>$this->getRequest()->getPost('fund_type'),
						                 "degree_id"=>$this->getRequest()->getPost('degree_id'),

									    "category_name"=>$feehead_name );

							$Category_model->insert($fee_data);
                                                        unset($_SESSION["token"]);
						    }
						
						}	
                                                   $_SESSION['message_class'] = 'alert-success';
						    $this->_flashMessenger->addMessage('Category Updated Successfully');

                                          $this->_redirect('fee-category/index?v2');
                                          }  else {
                                                       $message="Invalid Token";
                                                       $_SESSION['message_class'] = 'alert-danger';
                                                       $this->_flashMessenger->addMessage($message);
                                                      $this->_redirect('fee-category/index?v2');
                                                  }
                                                      }	

						} else {         

                      }						

					}

                break;

            case 'delete':

                $data['status'] = 2;

                if ($category_id){

                    $Category_model->update($data, array('category_id=?' => $category_id));

					$this->_flashMessenger->addMessage('Category Details Deleted Successfully');

					$this->_redirect('fee-category/index');

				}

                break;

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $Category_model->getRecords();

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