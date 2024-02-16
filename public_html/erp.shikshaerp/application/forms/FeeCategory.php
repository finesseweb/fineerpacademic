<?php

class Application_Form_FeeCategory extends Zend_Form
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
        
          $accname = new Application_Model_Account();
	   $data =  $accname->getDropDownList();
	      $fund_id = $this->createElement('select', 'fund_type')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('class', array('form-control'))
                 ->addMultioptions(array(''=>'--select--'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($fund_id);
        
      
	    $Department_model = new Application_Model_Session();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')->setRequired(true)
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
                
                    $Department_model = new Application_Model_DepartmentType();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$dept_id = $this->createElement('select','dept_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')->setRequired(true)
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($dept_id);
	
	}
	
}