<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



class Application_Form_Ge extends Zend_Form
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
        
		$general_elective_name = $this->createElement('text','general_elective_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
				->setRequired(true)
				->setAttrib('required','required')
                ->removeDecorator("htmlTag");
                $this->addElement($general_elective_name);
		
	
		$status = $this->createElement('select','status')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')->setRequired(true)
							->removeDecorator("htmlTag")
							->addMultiOptions(array('0' => 'Active','2'=>'Inactive'));
                    $this->addElement($status);
                    
        $Department_model = new Application_Model_Department();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$department = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setAttrib('required','true')->setRequired(true)
							->addMultiOptions(array('0' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($department);
                 $Department_model = new Application_Model_Session();
		$data = $Department_model->getDropDownList();
        
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
		
	
	}
	
}


