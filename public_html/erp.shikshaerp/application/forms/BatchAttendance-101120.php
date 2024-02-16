<?php
//ini_set('display_errors', '1');
/* 
    Author: Kedar Kumar
    Summary: This Form is used to handle The Batch Attendance
    Date: 03 Oct. 2019
*/
class Application_Form_BatchAttendance extends Zend_Form
{
	public function init()
	{
        
        $session_model = new Application_Model_Session();
		$data = $session_model->getDropDownList();
		$session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            //->setAttrib('required','required')
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
                
        $session_year = new Application_Model_Session();
		$data = $session_year->getDropDownList();
		//print_r($data); die;
		$session_filter = $this->createElement('select','session_filter')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Filter by Session'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($session_filter);
        $degree_model = new Application_Model_Degree();
        $data = $degree_model->getDropDownList();
         $degree_id = $this->createElement('select', 'degree_id')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')
                ->setAttrib('class', array('form-control'))
                 ->addMultioptions(array(''=>'--select--'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($degree_id);
        $effective_date = $this->createElement('text', 'effective_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control','monthYearPicker'))
                ->setAttrib('required','true')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($effective_date);    
        $conducted_class = $this->createElement('text', 'conducted_class')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required','true')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($conducted_class);    
        $required_percentage = $this->createElement('text', 'required_percentage')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required','true')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($required_percentage);    
        $Department_model = new Application_Model_Department();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$department = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setAttrib('required','true')
							->addMultiOptions(array('0' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($department);
                 $Department_model = new Application_Model_Session();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		
		
		$Ge_model = new Application_Model_Ge();
		$data = $Ge_model->getDropDownList();
		//print_r($data); die;
		$Ge_id = $this->createElement('select','ge_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                       // ->setAttrib('required','required')
							->addMultiOptions(array('0' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($Ge_id);
                
		$courseGroupModel = new Application_Model_Ge();
		$data = $courseGroupModel->getDropDownListForcourseGroup();
		//print_r($data); die;
		$courseGroupModel = $this->createElement('select','course_group')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                       // ->setAttrib('required','required')
							->addMultiOptions(array('-1' => 'Filter By Course Group'))
                            ->addMultiOptions(array('0' => 'Core Course'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($courseGroupModel);
                
                
        $declaredTerms =  new Application_Model_Declaredterms();
        $data = $declaredTerms->getDropDownList();
           $term = $this->createElement('select', 'cmn_terms')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
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
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
                                      $this->addElement($cc_id);
		

		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDepartments();
        $department_id = $this->createElement('select','department_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
				->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($department_id);
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getEmployeeIds();
		$employee_id = $this->createElement('select','employee_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control '))
               ->setAttrib('required','required')
                ->addMultiOptions(array('0' => 'Select'))
				->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($employee_id); 
        
        
        $component_details = new Application_Model_Component();
        $defined_details = $component_details->getDropDownList1();
         $component_paper =  $this->createElement('select', 'component_paper')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->setRegisterInArrayValidator(false)
                ->setAttrib('multiple', 'multiple')
                ->removeDecorator("htmlTag")
                ->addMultiOptions($defined_details);
        $this->addElement($component_paper);
			
	}
	
}
?>
