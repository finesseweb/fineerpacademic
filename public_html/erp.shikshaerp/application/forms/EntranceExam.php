<?php

/* 
    Author: Kedar Kumar
    Summary: This Form is used to handle The Batch Attendance
    Date: 03 Oct. 2019
*/
class Application_Form_EntranceExam extends Zend_Form
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
        
        $applicant_name = $this->createElement('text', 'applicant_name')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('placeholder','Full name as per the Matriculation Certificate')
                ->setAttrib('required','required')->setRequired(true)
                ->setAttrib('data-toggle', 'albphabets')
                //->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($applicant_name);
        $applicant_number = $this->createElement('text', 'applicant_no')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required','required')->setRequired(true)
                //->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($applicant_number);
        $email = $this->createElement('text', 'email_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('placeholder','example@gmail.com')
                ->setAttrib('required','required')->setRequired(true)
                ->setAttrib('pattern', '^[a-zA-Z0-9._-]+@[a-zA-Z]+\.[a-z]+$')
                ->setAttrib('title', 'example@gmail.com')
                ->setRequired(true)
                   
                ->removeDecorator("htmlTag");
        $this->addElement($email);
        $mibile_no = $this->createElement('text', 'phone_number')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required','required')->setRequired(true)
                ->setAttrib('data-toggle', 'number')
                //->setAttrib('autocomplete', 'off')
                ->setAttrib('data-toggle', 'number')
                ->removeDecorator("htmlTag");
        $this->addElement($mibile_no);
        
        $password = $this->createElement('password', 'password')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required','required')->setRequired(true)
                //->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($password);
        $cnf_password = $this->createElement('text', 'cnf_password')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required','required')->setRequired(true)
                //->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($cnf_password);
    }    
	
}
?>
