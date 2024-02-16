<?php

class Application_Form_Coursetype extends Zend_Form
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
	$ct_name = $this->createElement('text','ct_name')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							 ->setRequired(true)
							// ->setValue(0)
						    ->setAttrib('required','required')
							->removeDecorator("htmlTag");
			$this->addElement($ct_name);
			
			
			$ct_description = $this->createElement('textarea','ct_description')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setAttrib('required','required')
                                ->setRequired(true)
				->setAttrib('rows', '2')
				//->setRequired(true)
				->removeDecorator("htmlTag");
			$this->addElement($ct_description);
			
			
			 $degree_model = new Application_Model_Degree();
        $data = $degree_model->getDropDownList();
         $degree_id = $this->createElement('select', 'degree_id')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')
                 ->setRequired(true)
                ->setAttrib('class', array('form-control'))
                 ->addMultioptions(array(''=>'--select--'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($degree_id);
			
		


			
	}
	
}