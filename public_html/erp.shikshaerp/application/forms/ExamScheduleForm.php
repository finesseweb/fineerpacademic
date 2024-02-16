<?php
/* 
    Author: Kedar Kumar
    Date: 21 Oct. 2019
    Summary: This form is made for exam schedule
*/
class Application_Form_ExamScheduleForm extends Zend_Form{
    
    public function init(){
        $session_model = new Application_Model_Session();
		$data = $session_model->getDropDownList();
		$session_id = $this->createElement('select','session_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        //->setAttrib('required','required')
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
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
        $effective_date = $this->createElement('text', 'exam_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control','monthYearPicker'))
                ->setAttrib('required','true')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($effective_date);    
        
        $Department_model = new Application_Model_Department();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$department = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                     //   ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
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
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($Ge_id);
                
                
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
		$cc_id = $this->createElement('select','cc_id')
			->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
            ->setAttrib('required','required')->setRequired(true)
            ->removeDecorator("htmlTag")
            ->addMultiOptions(array('' => 'Select'))
            ->addMultiOptions($data);
                $this->addElement($cc_id);
		
                
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDepartments();
        $department_id = $this->createElement('select','department_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')->setRequired(true)
                ->addMultiOptions(array(''=>'Select'))
				->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($department_id);
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getEmployeeIds();
		$employee_id = $this->createElement('select','employee_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control '))
               ->setAttrib('required','required')->setRequired(true)
                ->addMultiOptions(array(''=>'Select'))
				->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($employee_id);
        $component_paper =  $this->createElement('select', 'component_paper')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
            'ESE(T)' => 'ESE(T)',
            'ESE(P)' => 'ESE(P)'));
        $this->addElement($component_paper);
        
        $examSchedule_model = new Application_Model_ExamScheduleModel();
		$data = $examSchedule_model->getDurationFromList();
		$time_from = $this->createElement('select','time_from')
			->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
            ->setAttrib('required','required')->setRequired(true)
            ->removeDecorator("htmlTag")
            ->addMultiOptions(array('' => 'Select'))
            ->addMultiOptions($data);
                $this->addElement($time_from);
                
        $examSchedule_model = new Application_Model_ExamScheduleModel();
		$data = $examSchedule_model->getDurationToList();
		$time_to = $this->createElement('select','time_to')
			->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
            ->setAttrib('required','required')->setRequired(true)
            ->removeDecorator("htmlTag")
            ->addMultiOptions(array('' => 'Select'))
            ->addMultiOptions($data);
                $this->addElement($time_to);
    }	
}
?>
