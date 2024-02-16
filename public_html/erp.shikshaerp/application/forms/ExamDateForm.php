<?php
//ini_set('display_errors', '1');
/* 
    Author: Kedar Kumar
    Summary: This Form is used to handle The Examination Date and Result Publish date
    Date: 22 Jan. 2021
*/
class Application_Form_ExamDateForm extends Zend_Form
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
        $Academic_year_model = new Application_Model_AcademicYear();
		$data = $Academic_year_model->getDropDownList();
		$year_id = $this->createElement('select','academic_year')
                    ->removeDecorator('label')
                    ->setAttrib('class',array('form-control','chosen-select'))
                    ->setAttrib('required','required')
                        ->setRequired(true)

                    ->addMultiOptions(array('' => 'Select'))
                    ->addMultiOptions($data)
                    ->removeDecorator("htmlTag");
        $this->addElement($year_id);
        
        $session_model = new Application_Model_Session();
		$data = $session_model->getDropDownList();
		$session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                        
                            ->setAttrib('required','required')
                            ->setRequired(true)
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
                
        $session_year = new Application_Model_Session();
		
        
        
        $exam_date = $this->createElement('text', 'exam_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control','datepicker'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($exam_date); 
        
        $result_publish_date = $this->createElement('text', 'result_publish_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control','datepicker'))
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($result_publish_date);  
        
        $re_evaluation_date = $this->createElement('text', 'reval_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control','datepicker'))
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($re_evaluation_date);    
          
        
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
                
        $examType = $this->createElement('select', 'exam_type')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setAttrib('class', array('form-control','chosen-select'))
                ->addMultioptions(array(
                    1 =>'Collegiate',
                    2   =>'Non-Collegiate'))
                ->removeDecorator('htmlTag');
        $this->addElement($examType);
        
        
        $status = $this->createElement('select', 'status')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setAttrib('class', array('form-control','chosen-select'))
                ->addMultioptions(array(
                    1 =>'Active',
                    2=>'Inactive'))
                ->removeDecorator('htmlTag');
        $this->addElement($status);
        
        $Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')->setRequired(true)
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id);
        
        
                                
    }    
        
}
?>
