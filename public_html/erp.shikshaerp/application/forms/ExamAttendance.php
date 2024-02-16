<?php
//ini_set('display_errors', '1');
/* 
    Author: Kedar Kumar
    Summary: This Form is used to handle The Batch Attendance
    Date: 03 Oct. 2019
*/
class Application_Form_ExamAttendance extends Zend_Form
{
	public function init()
	{
        
//        $exam_model = new Application_Model_ExamScheduleModel();
//		$data = $exam_model->getDropDownList();
//                //print_r($data);die;
//		$exam_date = $this->createElement('select','exam_date')
//							->removeDecorator('label')
//							->setAttrib('class',array('form-control','chosen-select'))
//                                                        ->setAttrib('required','required')
//							->addMultiOptions(array('' => 'Select Date'))
//							->addMultiOptions($data)
//							->removeDecorator("htmlTag");
//                                                       $this->addElement($exam_date);
//       
//         $subject = $this->createElement('select', 'subject')
//                ->removeDecorator('label')
//                ->setAttrib('required', 'required')
//                ->setAttrib('class', array('form-control'))
//                 ->addMultioptions(array(''=>'select Date first'))
//                ->removeDecorator('htmlTag');
//        $this->addElement($subject);
        
        
        $session_year = new Application_Model_Session();
		$data = $session_year->getDropDownList();
		//print_r($data); die;
		$session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')->setRequired(true)
							->addMultiOptions(array('' => 'Select Session'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                                                          $this->addElement($session_id);
                                                          
                                                          
               
	
                $semester_id = $this->createElement('select','semester')
               ->removeDecorator('label')
               ->setAttrib('class',array('form-control','chosen-select'))
               ->setAttrib('required','required')->setRequired(true)
	        ->addMultiOptions(array('' => 'Filter by Session'))
		->removeDecorator("htmlTag");
                 $this->addElement($semester_id);
                 
             $core_model = new Application_Model_Coursecategory();
              $data = $core_model->getDropDownList();                                             
                $cc_id = $this->createElement('select','cc_id')
                 ->removeDecorator('label')
		->setAttrib('class',array('form-control','chosen-select'))
               ->setAttrib('required','required')->setRequired(true)
		->addMultiOptions(array('' => 'Select'))
               ->addMultiOptions($data)
                   ->removeDecorator("htmlTag");
                  $this->addElement($cc_id);
         
			
	}
	
}
?>
