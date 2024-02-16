<?php

class Application_Form_Application extends Zend_Form
{
	public function init()
	{
	    
	    //Added by Kedar 16 Nov 2019
             $otpCheck = $this->createElement('text','otp_field')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                     ->setAttrib('placeholder','Enter Your Form Id')
                      ->setAttrib('pattern', '\d+')
                ->setAttrib('title', 'xxx..')
                ->setRequired(true)
                   
                ->removeDecorator("htmlTag");
        $this->addElement($otpCheck);
        //End

             $dob = $this->createElement('text','dob')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                     ->setAttrib('placeholder','dd/mm/yy')
                ->setRequired(true)
                     ->setAttrib('disabled', 'disabled')
                ->removeDecorator("htmlTag");
        $this->addElement($dob);
        
	$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
                        ->setRequired(true)
                                                         ->setAttrib('disabled', 'disabled')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($academic_year_id);
        
        $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
            
        
            $term_b_id = $this->createElement('select', 'term_b_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                    ->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_b_id);
        
             $course_id = $this->createElement('select', 'course_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
               // ->setAttrib('style',array('display:none')) 
                       ->setAttrib('required', 'required')
                     ->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                //->addMultiOptions($data1)
               ->setRegisterInArrayValidator(false);
        $this->addElement($course_id);	
        	
		   $stu_id = $this->createElement('text', 'stu_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                           ->setRequired(true)
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_id);
        
           $stu_name = $this->createElement('text', 'stu_name')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                   ->setRequired(true)
                   ->setAttrib('disabled', 'disabled')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_name);
        
        
	
	}
	
}