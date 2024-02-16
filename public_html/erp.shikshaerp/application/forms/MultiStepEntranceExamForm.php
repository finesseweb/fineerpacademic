<?php

/* 
    Author: Kedar Kumar
    Summary: This Form is used to handle Online Entrance Exam Multi Step Form
    Date: 23 Oct. 2019
*/
class Application_Form_MultiStepEntranceExamForm extends Zend_Form
{
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
        
        //$session_model = new Application_Model_Session();
		//$data = $session_model->getDropDownList();
		$session_id = $this->createElement('Select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            //->setAttrib('required','required')
							//->addMultiOptions(array('' => 'Select'))
							//->addMultiOptions($data)
                            ->addMultioptions(array(''=>'Select'))
                            ->addMultioptions(array('7'=>'2020-2023'))
                            ->addMultioptions(array('8'=>'2020-2022'))
                            ->addMultioptions(array('9'=>'2020-2021'))
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
        $degree_model = new Application_Model_Degree();
        $data = $degree_model->getDropDownListBED();
        $degree_id = $this->createElement('select', 'degree_id')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('class', array('form-control','chosen-select'))
                ->addMultioptions(array(''=>'Select'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($degree_id);
        $department_type_model = new Application_Model_DepartmentType();
        $data = $department_type_model->getDepartmentType();
        $department_type = $this->createElement('select', 'course')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')->setRequired(true)
                ->setAttrib('class', array('form-control','chosen-select'))
                ->addMultioptions(array(''=>'Select'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($department_type);
        //Added: 07 o1ct 2020
        $Academic_year_model = new Application_Model_AcademicYear();
		$data = $Academic_year_model->getDropDownList();
		//print_r($data); die;
		$year_id = $this->createElement('select','academic_year_list')
                    ->removeDecorator('label')
                    ->setAttrib('class',array('form-control','chosen-select'))
                    ->setAttrib('required','required')->setRequired(true)

                    ->addMultiOptions(array('' => 'Select'))
                    ->addMultiOptions($data)
                    ->removeDecorator("htmlTag");
        $this->addElement($year_id);
    }
	
}
?>

