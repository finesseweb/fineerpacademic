<?php

/* 
    Author: Kedar Kumar
    Summary: This Form is used to handle Online Entrance Exam Form Step2
    Date: 10 Jan. 2020
*/
class Application_Form_MultiStepExamFormStep2 extends Zend_Form {
    
    
	public function init(){
        
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
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('data-toggle', 'albphabets')
            //->setAttrib('readonly','readonly')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($applicant_name);
        
        $email_id = $this->createElement('text', 'email_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
               // ->setAttrib('readonly','readonly')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($email_id);
        
        $phone = $this->createElement('text', 'phone_number')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                //->setAttrib('data-toggle', 'number')
                //->setAttrib('readonly','readonly')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($phone);
        
        $dob = $this->createElement('text', 'dob_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('placeholder','As per the Matric Certificate')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($dob);
        $gender = $this->createElement('select', 'gender')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
            '1' => 'Female',
            '2' => 'Transgender'));
        $this->addElement($gender);
        
        $aadhar = $this->createElement('text', 'aadhar_number')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('placeholder','Enter 12 digits aadhar number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($aadhar);
        
        $nationality = $this->createElement('select', 'nationality')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
            'Indian' => 'Indian',
            'foreigner' => 'Foreigner',
            'NRI' => 'NRI'));
        $this->addElement($nationality);
        
        $religion = $this->createElement('select', 'religion')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
                        'Hindu' => 'Hindu',
                        'Christian' => 'Christian',
                        'Muslim' => 'Muslim',
                        'Sikh' => 'Sikh',
                        'Parsi' => 'Parsi',
                        'Buddhism' => 'Buddhism',
                        'Jainism' => 'Jainism',
                        'others' => 'Any Other'));
        $this->addElement($religion);
        
        $others_religion = $this->createElement('text', 'others_religion')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($others_religion);
        $father_name = $this->createElement('text', 'father_name')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('placeholder','As per the Matric Certificate')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($father_name);
        
        $father_qual = $this->createElement('text', 'father_qual')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($father_qual);
        $father_contact = $this->createElement('text', 'father_contact')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('data-toggle', 'number')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($father_contact);
        $father_qual = $this->createElement('text', 'father_occup')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            //->setAttrib('placeholder',"Enter Your Father's Occupation")
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($father_qual);
        
        $mother_name = $this->createElement('text', 'mother_name')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('placeholder','As per the Matric Certificate')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($mother_name);
        $mother_contact = $this->createElement('text', 'mother_contact')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'number')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($mother_contact);
        
        $mother_qual = $this->createElement('text', 'mother_qual')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($mother_qual);
        $mother_qual = $this->createElement('text', 'mother_occup')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($mother_qual);
        
        $guard_name = $this->createElement('text', 'guard_name')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($guard_name);
        
        $guard_qual = $this->createElement('text', 'guard_qual')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($guard_qual);
        
        $guard_occup = $this->createElement('text', 'guard_occup')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($guard_occup);
        
        $guard_contact = $this->createElement('text', 'guard_contact')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'number')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($guard_contact);
        
        $p_address = $this->createElement('text', 'p_address')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('data-toggle', 'address_albphabets')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($p_address);
        
        $p_homeTown = $this->createElement('text', 'p_homeTown')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'address_albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($p_homeTown);
        
        $p_postOffice = $this->createElement('text', 'p_postOffice')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'address_albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($p_postOffice);
        
        $p_policeStn = $this->createElement('text', 'p_policeSt')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'address_albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($p_policeStn);
        
        $p_district = $this->createElement('text', 'p_district')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($p_district);
        
        $p_state = $this->createElement('text', 'p_state')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($p_state);
        
        $p_code = $this->createElement('text', 'p_code_number')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($p_code);
        $l_address = $this->createElement('text', 'l_address')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'address_albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($l_address);
        
        $l_homeTown = $this->createElement('text', 'l_homeTown')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'address_albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($l_homeTown);
        
        $l_postOffice = $this->createElement('text', 'l_postOffice')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'address_albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($l_postOffice);
        
        $l_policeStn = $this->createElement('text', 'l_policeSt')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'address_albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($l_policeStn);
        
        $l_district = $this->createElement('text', 'l_district')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($l_district);
        
        $l_state = $this->createElement('text', 'l_state')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($l_state);
        
        $l_code = $this->createElement('text', 'l_code_number')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($l_code);
        
        
        
        $blood_group = $this->createElement('select', 'blood_group')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
                        'AB+' => 'AB+',
                        'AB-' => 'AB-',
                        'A+' => 'A+',
                        'A-' => 'A-',
                        'B+' => 'B+',
                        'B-' => 'B-',
                        'O+' => 'O+',
                        'O-' => 'O-'));
        $this->addElement($blood_group);
    }	
}
?>