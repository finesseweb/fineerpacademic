<?php

class Application_Form_PayManager extends Zend_Form
{
	public function init()
	{
	    
        //End
        
        
             $dob = $this->createElement('text','mer_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($dob);
        
             $dob = $this->createElement('text','tran_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($dob);
        

             $dob = $this->createElement('text','to_Date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                     ->setAttrib('placeholder','dd/mm/yy')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($dob);
        
         $dob = $this->createElement('text','from_Date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                     ->setAttrib('placeholder','dd/mm/yy')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($dob);
        
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

        

        
        
	
	}
	
}