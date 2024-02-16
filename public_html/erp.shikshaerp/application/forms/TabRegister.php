<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Form_TabRegister extends Zend_Form
{
	public function init()
	{
			

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
		
		
		
                $term_id = $this->createElement('select','term_id')
					->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')->setRequired(true)
							->addMultiOptions(array('' => 'Select'))
                                                        ->setRegisterInArrayValidator(false)
							->removeDecorator("htmlTag");
                
        $this->addElement($term_id);
        
}

        }