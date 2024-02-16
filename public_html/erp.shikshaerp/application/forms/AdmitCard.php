<?php

class Application_Form_AdmitCard extends Zend_Form
{
  public function init()
  {
    
        
            
             
       $stu_id = $this->createElement('text', 'stu_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('pattern', '^F{1}-\d{4}-\d+$')
                ->setAttrib('title', 'Please Enter in this Format F-XXXX-XXXX')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($stu_id);
        
         $otp = $this->createElement('text', 'otp')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('pattern', '^\d+$')
                ->setAttrib('title', '0000')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($otp);
        $phone = $this->createElement('text', 'phone')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('pattern', '^\d{10}$')
                ->setAttrib('title', 'Please Enter correct 10 digit mobile number.')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($phone);
  
  }
  
}