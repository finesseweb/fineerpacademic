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
        

        
        
	
	}
	
}