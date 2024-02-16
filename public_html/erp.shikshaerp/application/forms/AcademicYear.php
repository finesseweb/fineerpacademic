<?php

class Application_Form_AcademicYear extends Zend_Form {

    public function init() {

        
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
        
        $status = $this->createElement('select', 'active_year')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->addMultioptions(array('' => 'Select', '1' => 'Active', '0' => 'Inactive'))
                ->removeDecorator('htmlTag');
        $this->addElement($status);
        
		$academic_year = $this->createElement('text','academic_year')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($academic_year);
        
        $Academic_year_model = new Application_Model_AcademicYear();
		$data = $Academic_year_model->getDropDownList();
		//print_r($data); die;
		$year_id = $this->createElement('select','academic_year_list')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setRequired(true)
           
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($year_id);
                
        
        
        $effective_date = $this->createElement('text', 'effective_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control','monthYearPicker'))
                ->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($effective_date);  
          
    }

}
