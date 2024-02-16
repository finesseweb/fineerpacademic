<?php

class Application_Form_CreditMaster extends Zend_Form
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
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		
		$credit_value = $this->createElement('text','credit_value')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                        
				//->setAttrib('readonly', 'readonly')
				->removeDecorator("htmlTag");
                
                
        $this->addElement($credit_value);
		
		$credit_name = $this->createElement('text','credit_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				//->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($credit_name);
        
        
                $course_cat = new Application_Model_Coursecategory();
		
		$data = $course_cat->getDropDownList();
		$credit_type = $this->createElement('select','credit_type')
                            ->removeDecorator('label')
                            ->setAttrib('class',array('form-control','chosen-select'))
                            ->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
		$this->addElement($credit_type);
		
		
		$credit_desc = $this->createElement('textarea','credit_desc')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($credit_desc);
	
	
	}
	
}