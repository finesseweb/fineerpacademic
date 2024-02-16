<?php

/*
 Author: Kedar
 Date: 18 Oct. 2019
 Summary: Exam fee form
 */


class Application_Form_Coursefee extends Zend_Form
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
        
        $degree_model = new Application_Model_Degree();
        $data = $degree_model->getDropDownList();
         $degree_id = $this->createElement('select', 'degree_id')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')
                 ->setRequired(true)
                ->setAttrib('class', array('form-control'))
                 ->addMultioptions(array(''=>'--select--'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($degree_id);
        
        $Department_model = new Application_Model_Department();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$department_id = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            //->setAttrib(array('multiple' => 'multiple'))
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($department_id);
                 $Department_model = new Application_Model_Session();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$session_id = $this->createElement('select','session_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        //->setAttrib('required','required')
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
                 $declaredTerms =  new Application_Model_Declaredterms();
        $data = $declaredTerms->getDropDownList();
           $term = $this->createElement('select', 'cmn_terms')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                   ->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
                $this->addElement($term);
        $feeForm_start_date = $this->createElement('text', 'feeForm_start_date')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examFeePicker'))
            ->setAttrib('required','true')
                ->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($feeForm_start_date);    
        $feeForm_end_date = $this->createElement('text', 'feeForm_end_date')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examFeePicker'))
            ->setAttrib('required','true')
                ->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($feeForm_end_date);    
        $feeForm_extended_date = $this->createElement('text', 'feeForm_extended_date')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examFeePicker'))
          
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($feeForm_extended_date);  
            
             $exam_date = $this->createElement('text', 'exam_date')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examFeePicker'))
          
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($exam_date);  
        $examFee = $this->createElement('text', 'examFee')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required','true')
            ->setRequired(true)
            ->setAttrib('data-toggle', 'number')
            ->removeDecorator("htmlTag");
            $this->addElement($examFee);      
        $fineFee = $this->createElement('text', 'fineFee')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
      
            ->setAttrib('data-toggle', 'number')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($fineFee);     
            
            $perday = $this->createElement('text', 'perday')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
      
            ->setAttrib('data-toggle', 'number')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($perday);     
        $product_id = $this->createElement('text', 'product_id')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required','true')
                ->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($product_id);      
            
        $account_no = $this->createElement('text', 'account_no')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required','true')
                ->setRequired(true)
            ->setAttrib('data-toggle', 'number')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($account_no); 
            
             $exam_type = $this->createElement('select', 'exam_type')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required','true')
                ->setRequired(true)
            ->setAttrib('data-toggle', 'number')
            ->setAttrib('autocomplete', 'off')
             ->addMultiOptions(array('' => 'Select','1' => 'Collegiate','2' => 'Non-Collegiate'))
            ->removeDecorator("htmlTag");
            $this->addElement($exam_type); 
            
    }
}