<?php

class Application_Form_ReferenceGradeMaster extends Zend_Form
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
		
//		$Academic_model = new Application_Model_Academic();
//		$data = $Academic_model->getDropDownList();
//		 $academic_year_id = $this->createElement('select','academic_year_id')
//                ->removeDecorator('label')->setAttrib('class',array('form-control'))
//				->setAttrib('required','required')
//                ->setRequired(true)
//				->addMultioptions(array(""=>"Select"))
//				->addMultioptions($data)
//                ->removeDecorator("htmlTag");
//        $this->addElement($academic_year_id);
            
            
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
		//print_r($data); die;
		$session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')->setRequired(true)
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
	
	}
	
}