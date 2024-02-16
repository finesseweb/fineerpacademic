<?php

/*
 Author: Kedar
 Date: 18 Oct. 2019
 Summary: Exam fee form
 */


class Application_Form_EntranceExamSchedule extends Zend_Form
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
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('class', array('form-control'))
                 ->addMultioptions(array(''=>'--select--'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($degree_id);
        
        $Department_model = new Application_Model_DepartmentType();
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
       
        $feeForm_start_date = $this->createElement('text', 'feeForm_start_date')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examFeePicker'))
            ->setAttrib('required','true')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($feeForm_start_date);    
           
            
        $feeForm_end_date = $this->createElement('text', 'feeForm_end_date')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examFeePicker'))
            ->setAttrib('required','true')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($feeForm_end_date);    
           
          
        $exam_date = $this->createElement('text', 'exam_date')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examFeePicker'))
            //->setAttrib('required','true')
            ->setAttrib('autocomplete', 'off')
            ->removeDecorator("htmlTag");
            $this->addElement($exam_date);  
            
        $examFee = $this->createElement('text', 'examFee')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required','true')->setRequired(true)
            ->setAttrib('data-toggle', 'number')
            ->removeDecorator("htmlTag");
            $this->addElement($examFee);      
            
            
           $examtime_start = $this->createElement('text', 'examtime_start')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examtime'))
            ->setAttrib('required','true')
            ->removeDecorator("htmlTag");
            $this->addElement($examtime_start);  
            
             $examtime_end = $this->createElement('text', 'examtime_end')
            ->removeDecorator('label')->setAttrib('class', array('form-control','examtime'))
            ->setAttrib('required','true')->setRequired(true)
            ->removeDecorator("htmlTag");
            $this->addElement($examtime_end);  
         
        $product_id = $this->createElement('text', 'product_id')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required','true')->setRequired(true)
            ->setAttrib('autocomplete', 'off')
           ->setValue('PRINCIPAL_PWC')
            ->removeDecorator("htmlTag");
            $this->addElement($product_id);      
            
        $account_no = $this->createElement('text', 'account_no')
            ->removeDecorator('label')->setAttrib('class', array('form-control'))
            ->setAttrib('required','true')->setRequired(true)
            ->setAttrib('data-toggle', 'number')
            ->setAttrib('autocomplete', 'off')
            ->setValue('848200301000006')
            ->removeDecorator("htmlTag");
            $this->addElement($account_no);      
            
    }
}