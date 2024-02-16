<?php



class FeeInstallmentController extends Zend_Controller_Action {



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

        $this->view->sub_title_name = 'Fee Installment';

        $this->accessConfig->setAccess("SA_ACAD_FEE_CAT");

        $Category_model = new Application_Model_FeeInstallment();
       
		$Category_form = new Application_Form_FeeInstallment();
 

		//$Termdate_model = new Application_Model_Termdate();

		$term_id = $this->_getParam("term_id");
		$acc_id = $this->_getParam("acad");

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
                           // echo '<pre>'; print_r($data);  die();
                             $insertData=array(
                                       'academic_year_id'=> $data['academic_year_id'],
                                       'term_id'=> $data['term_id'],
                                     
                    );
                             if(!empty($data['csrftoken'])) {
                               if($data['csrftoken']===$token ){
			foreach($_POST['Installment'] as $key=>$val )
                            {
                             	$fee_data=array("academic_year_id"=>$this->getRequest()->getPost('academic_year_id'),
						 "term_id"=>$this->getRequest()->getPost('term_id'),
						 "fees"=>$_POST['fees'],
						 "amount"=>$_POST['Installment'][$key],
						 "endDate"=>$_POST['installmentDate'][$key],
						  "description"=>$_POST['description'][$key]
									    );

							$Category_model->insert($fee_data);	
                                                        unset($_SESSION["token"]);
								

						}

							//print_r($data); die; 
                                                       $_SESSION['message_class'] = 'alert-success';
							$this->_flashMessenger->addMessage('Category Added Successfully');

                                                  $this->_redirect('fee-Installment/index?v2');

					 }  else {
                                                       $message="Invalid Token";
                                                       $_SESSION['message_class'] = 'alert-danger';
                                                       $this->_flashMessenger->addMessage($message);
                                                      $this->_redirect('fee-Installment/index?v2');
                                                  }
                                                      }		

					}

				}

		

				break;

           case "edit":

		  

					$this->view->type = $type;
                    $ftype = $this->_getParam("ftype");
					$this->view->form = $Category_form;

					$result = $Category_model->getRecordbyacadfeeId($acc_id,$term_id,$ftype);

				

					$this->view->item_result = $result;
					$this->view->fees = $result[0]['fees'];
				//echo "<pre>";print_r($this->view->fees);die;
					$item_result['academic_year_id'] = $result[0]['academic_year_id'];
					$item_result['term_id'] = $result[0]['term_id'];
					$Category_form->populate($item_result);

						if ($this->getRequest()->isPost()) {

							if ($Category_form->isValid($this->getRequest()->getPost())) {

							//echo 'dsd'; die;

							$data = $Category_form->getValues();
                                                     //  echo '<pre>'; print_r($data);  die(); 
                                                        if(!empty($data['csrftoken'])) {
                                                        if($data['csrftoken']===$token ){
							$Category_model->delete(array('academic_year_id=?' => $acc_id,'term_id=?'=>$term_id,'md5(fees)=?'=>$ftype));
							
								foreach($_POST['Installment'] as $key=>$val )

						{

						  

						$fee_data=array("academic_year_id"=>$this->getRequest()->getPost('academic_year_id'),
						                 "term_id"=>$this->getRequest()->getPost('term_id'),
									    "fees"=>$_POST['fees'],
									    "amount"=>$_POST['Installment'][$key],
									    "endDate"=>$_POST['installmentDate'][$key],
									    "description"=>$_POST['description'][$key]
									    );

							$Category_model->insert($fee_data);	
                                                            unset($_SESSION["token"]);
								

						}

                                                   $_SESSION['message_class'] = 'alert-success'; 
						    $this->_flashMessenger->addMessage('Category Updated Successfully');

                                                     $this->_redirect('fee-Installment/index?v2');
                                                     
                                                   }  else {
                                                       $message="Invalid Token";
                                                       $_SESSION['message_class'] = 'alert-danger';
                                                       $this->_flashMessenger->addMessage($message);
                                                      $this->_redirect('fee-Installment/index?v2');
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
             //   echo "<pre>";print_r($result);exit;

                $page = $this->_getParam('page', 1);

                $paginator_data = array(

                    'page' => $page,

                    'result' => $result

                );

                $this->view->paginator = $this->_act->pagination($paginator_data);

                break;

        }

    }
    
    
    	public function ajaxGetFeeAction(){
	    	$this->_helper->layout->disableLayout();

		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
	     $academic_id = $this->_getParam("academic_id");
        $semester = $this->_getParam("term_id");
        $studetails = new Application_Model_StudentPortal();
        $feeStructure = new Application_Model_FeeStructure();
       
         $struct_id = $feeStructure->getStructId($academic_id);
         //echo $struct_id;die;
        $feeitems = new Application_Model_FeeStructureTermItems();
        
       
        $fee = $feeitems->getFee($struct_id,$semester);
     //   echo "<prE>"; print_r($fee);exit;
       // echo "<option value=''>Select Fees</option>";  
        foreach($fee as $key => $value){
            if($value['totalfee']!=0)
            echo "<option value={$value['totalfee']}-{$value['acc_name']}>{$value['totalfee']}</option>";    
            }die;
		}
	}

}