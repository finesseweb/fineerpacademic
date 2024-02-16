<?php

class Application_Form_Datables extends Zend_Form
{
	public function init()
	{
        
                $course_cat = new Application_Model_DtSet();
		
		$data = $course_cat->getDropDownList();
		$credit_type = $this->createElement('select','url')
                            ->removeDecorator('label')
                            ->setAttrib('class',array('form-control','chosen-select'))
                            ->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
		$this->addElement($credit_type);
	
	}
	
}