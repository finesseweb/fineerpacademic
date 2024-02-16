<?php

/* 
    Author: Kedar Kumar
    Summary: This Form is used to handle Online Entrance Exam Form Step2
    Date: 10 Jan. 2020
*/
class Application_Form_MultiStepExamFormStep3 extends Zend_Form {
    
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
        $caste = $this->createElement('text', 'caste')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('data-toggle', 'albphabets')
            ->setAttrib('required', 'required')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($caste);
        
       
        
        $caste_category = $this->createElement('select', 'caste_category')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
                        'General' => 'General ',
                        'EWS' => 'EWS',
                        'BC-1(EBC)' => 'BC-1(EBC)',
                        'BC-2(OBC)' => 'BC-2(OBC)',
                        'SC' => 'SC',
                        'ST' => 'ST'));
        $this->addElement($caste_category);
        
        $certificate_issued = $this->createElement('select', 'certificate_issued')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
                        'DM' => 'The District Magistrate',
                        'SDO' => 'Sub Divisional Officer ',
                        'CO' => 'Circle Officer'));
        $this->addElement($certificate_issued);
        
        $certificate_no = $this->createElement('text', 'certificate_no')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
        $this->addElement($certificate_no);
        
        $matric_school = $this->createElement('text', 'mat_school')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($matric_school);
        $school = $this->createElement('text', 'school')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($school);
        $grad_school = $this->createElement('text', 'grad_school')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($grad_school);
        $bca_school = $this->createElement('text', 'bca_school')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($bca_school);
        $gM_school = $this->createElement('text', 'grad_math_school')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($gM_school);
        $certification_school = $this->createElement('text', 'cert_school')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($certification_school);
        $other_school = $this->createElement('text', 'other_school')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($other_school);
        
        $m_board = $this->createElement('text', 'mat_board')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($m_board);
        $i_board = $this->createElement('text', 'i_board')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($i_board);
        $grad_board = $this->createElement('text', 'grad_board')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($grad_board);
        $bca_board = $this->createElement('text', 'bca_board')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($bca_board);
        $gM_board = $this->createElement('text', 'grad_math_board')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($gM_board);
        $certification_board = $this->createElement('text', 'cert_board')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($certification_board);
        $other_board = $this->createElement('text', 'other_board')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($other_board);
        
        
        $matric_division = $this->createElement('text', 'mat_division')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('autocomplete', 'off')
                ->setAttrib('data-toggle', 'address_albphabets')
                ->removeDecorator("htmlTag");
        $this->addElement($matric_division);
        
        $i_division = $this->createElement('text', 'i_division')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                 ->setAttrib('data-toggle', 'address_albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($i_division);
        $grad_division = $this->createElement('text', 'grad_division')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                 ->setAttrib('data-toggle', 'address_albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($grad_division);
        
        $bca_division = $this->createElement('text', 'bca_division')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'address_albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($bca_division);
        $gM_division = $this->createElement('text', 'grad_math_division')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'address_albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($gM_division);
        $certification_division = $this->createElement('text', 'cert_division')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'address_albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($certification_division);
        $other_division = $this->createElement('text', 'other_division')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                 ->setAttrib('data-toggle', 'address_albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($other_division);
        
        $matric_subject = $this->createElement('text', 'mat_subject')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($matric_subject);
        $subject = $this->createElement('text', 'subject')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($subject);
        
        $grad_subject = $this->createElement('text', 'grad_subject')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($grad_subject);
        $bca_subject = $this->createElement('text', 'bca_subject')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($bca_subject);
        $gM_subject = $this->createElement('text', 'grad_math_subject')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($gM_subject);
        $certification_subject = $this->createElement('text', 'cert_subject')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($certification_subject);
        $other_subjectt = $this->createElement('text', 'other_subject')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($other_subjectt);
        
        $matric_pass_year = $this->createElement('text', 'mat_pass_year')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($matric_pass_year);
        
        $i_pass_year = $this->createElement('text', 'i_pass_year')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($i_pass_year);
        
        $grad_pass_year = $this->createElement('text', 'grad_pass_year')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($grad_pass_year);
        
        $bca_pass_year = $this->createElement('text', 'bca_pass_year')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($bca_pass_year);
        
        $gM_pass_year = $this->createElement('text', 'grad_math_pass_year')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($gM_pass_year);
        
        $certification_pass_year = $this->createElement('text', 'cert_pass_year')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($certification_pass_year);
        
        $other_pass_year = $this->createElement('text', 'other_pass_year')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('autocomplete', 'off')
                ->setAttrib('data-toggle', 'number')
                ->removeDecorator("htmlTag");
        $this->addElement($other_pass_year);
        
        $i_roll = $this->createElement('text', 'i_roll')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('data-toggle', 'address_albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($i_roll);
        
        $obt_marks = $this->createElement('text', 'obt_marks_number')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'number')->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($obt_marks);
        
        $total_marks = $this->createElement('text', 'total_marks_number')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($total_marks);
        
        $percent = $this->createElement('text', 'percent')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('readonly', 'readonly')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($percent);
        
        
        
        $l_institution = $this->createElement('text', 'l_institution')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($l_institution);
        
        $l_place = $this->createElement('text', 'l_place')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required', 'required')
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($l_place);
        
        
        $last_board = $this->createElement('text', 'last_board')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                ->setAttrib('data-toggle', 'albphabets')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($last_board);
        
        $l_p_year = $this->createElement('text', 'l_p_year')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                ->setAttrib('data-toggle', 'number')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($l_p_year);
       
    }	
}
?>