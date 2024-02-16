<?php

class Application_Form_GradeAllocationReport extends Zend_Form
{
	public function init()
	{
            $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                 $config_role = $zendConfig->role_administrator->toArray();
                 $val = in_array($_SESSION['admin_login']['admin_login']->role_id, $config_role);
                 
//            $Academic_model = new Application_Model_Academic();
//		$data = $Academic_model->getDropDownList();
//		//print_r($data); die;
//		$academic_id = $this->createElement('select','academic_id')
//							->removeDecorator('label')
//							->setAttrib('class',array('form-control','chosen-select'))
//						   ->setAttrib('required','required')
//							->removeDecorator("htmlTag")
//							->addMultiOptions(array('' => 'Select'))
//							->addMultiOptions($data);
//        $this->addElement($academic_id);
                 
                 $degree_model = new Application_Model_Degree();
        $data = $degree_model->getDropDownList();
         $degree_id = $this->createElement('select', 'degree_id')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('class', array('form-control'))
                 ->addMultioptions(array(''=>'--select--'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($degree_id);
                 
                   $Department_model = new Application_Model_Session();
		$data = $Department_model->getDropDownList();
                 $session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        //->setAttrib('required','required')
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
                
  
                
                $declaredTerms =  new Application_Model_Declaredterms();
        $data = $declaredTerms->getDropDownList();
           $term = $this->createElement('select', 'cmn_terms')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                   ->addMultiOptions($data);
                   $this->addElement($term);
                
        
        
            $Coursecategory_model = new Application_Model_Coursecategory();
		$data = $Coursecategory_model->getDropDownList();
		//print_r($data); die;
                                    $cc_id = $this->createElement('select','cc_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
                                      $this->addElement($cc_id);
        
        
        
                                        $ge_id = $this->createElement('select','ge')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
							->addMultiOptions(array('' => 'Select'))
							//->addMultiOptions($data)
							->removeDecorator("htmlTag");
                                        $this->addElement($ge_id);
		
	/*	$year = $this->createElement('select','year')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						    ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select',
							                        '1'=>'First Year',
													'2'=>'Second Year'));
        $this->addElement($year); */
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDepartments();
        $department_id = $this->createElement('select','department_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->addMultiOptions(array(''=>'Select'))
				->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($department_id);
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getEmployeeIds();
                $empl_id = $_SESSION['admin_login']['admin_login']->empl_id;
            
                 if($empl_id && $val != 1){
                foreach($data as $key => $value)
                {
                       if($empl_id == $key ){
                           $data = array();
                           $data[$key] = $value;
                           break;
                       }
                }
                 }
                
		$employee_id = $this->createElement('select','employee_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->addMultiOptions(array(''=>'Select'))
				->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($employee_id);
        $term_id = $this->createElement('select','term_id')
					->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
                                                        ->setRegisterInArrayValidator(false)
							->removeDecorator("htmlTag");
		$this->addElement($term_id);

			
	}
	
}