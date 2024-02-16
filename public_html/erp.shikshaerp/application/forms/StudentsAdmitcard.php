<?php
class Application_Form_StudentsAdmitcard extends Zend_Form
{
    public function init()
    {
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        $csrftoken = $this->createElement('hidden', 'csrftoken')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setValue($token)
                ->removeDecorator("htmlTag");
        $this->addElement($csrftoken);
        
        $session_year = new Application_Model_Session();
		$data = $session_year->getDropDownList();
        $session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setAttrib('required','required')->setRequired(true)
							->addMultiOptions(array('' => 'Select Session'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($session_id);
        $Academic_year_model = new Application_Model_AcademicYear();
		$data = $Academic_year_model->getDropDownList();
		//print_r($data); die;
		$year_id = $this->createElement('select','academic_year')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setAttrib('required','required')->setRequired(true)
           
							->addMultiOptions(array('' => 'Select Academic Year'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($year_id);
        
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')->setRequired(true)
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id);
        
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$currentBatch = $this->createElement('select','current_batch')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')->setRequired(true)
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($currentBatch);
        
        $department_type = new Application_Model_DepartmentType();
        $data = $department_type->getDropDownList();
        $department_type = $this->createElement('select', 'stream')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
            ->setRequired(true)
                 ->addMultioptions(array(''=>'Select'))
                  ->addMultioptions($data)
                
                ->removeDecorator('htmlTag');
        $this->addElement($department_type);
        
        $publish_date = $this->createElement('text', 'publish_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control','monthYearPicker'))
                ->setAttrib('required','required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($publish_date); 
		    $year_drop_down = new Application_Model_Year();
        $year_arr = $year_drop_down->getDropDownList();
		$year_id = $this->createElement('select','year_id')
					    ->removeDecorator('label')
						->setAttrib('class',array('form-control','chosen-select'))
						->setAttrib('required','required')->setRequired(true)
						->addMultiOptions(array('' => 'Select'))
						->addMultiOptions($year_arr)
                        
						->removeDecorator("htmlTag");
		$this->addElement($year_id);
	
	    $StudentPortal_model = new Application_Model_StudentPortal();
		$data = $StudentPortal_model->getDropDownList();
		//print_r($data); die;
		$stu_id = $this->createElement('select','stu_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						    //->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($stu_id);
        
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
        
        $Coursecategory_model = new Application_Model_Coursecategory();
		$data = $Coursecategory_model->getDropDownList();
		//print_r($data); die;
            $cc_id = $this->createElement('select','cc_id')
            ->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
            ->setAttrib('required','required')->setRequired(true)
            ->removeDecorator("htmlTag")
            ->addMultiOptions(array('' => 'Select'))
            ->addMultiOptions($data);
        $this->addElement($cc_id);
        
        $Ge_model = new Application_Model_Ge();
		$data = $Ge_model->getDropDownList();
		//print_r($data); die;
		$Ge_id = $this->createElement('select','ge_id')
            ->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
            ->addMultiOptions(array('0' => 'Select'))
            ->addMultiOptions($data)
            ->removeDecorator("htmlTag");
        $this->addElement($Ge_id);
        
        $Department_model = new Application_Model_Department();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$department = $this->createElement('select','department')
            ->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
                                     //   ->setAttrib('required','required')
            ->addMultiOptions(array('0' => 'Select'))
            ->addMultiOptions($data)
            ->removeDecorator("htmlTag");
        $this->addElement($department);
}
}
    ?>