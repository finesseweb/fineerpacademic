<?php

/* 
    Author: Kedar Kumar
    Summary: This Form is used to handle Online Entrance Exam Multi Step Form
    Date: 23 Oct. 2019
*/
class Application_Form_SanctionedSeatMaster extends Zend_Form
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
        
		$session_id = $this->createElement('Select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                           
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
        
        $core_course = $this->createElement('select', 'core_course')
                ->removeDecorator('label')
                //->setAttrib('required', 'required')
                 //->setDisableInArrayValidator(true)
                ->setAttrib('class', array('form-control','chosen-select'))
                ->addMultioptions(array(''=>'Select'))
                //->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($core_course);
        
        $generic_elective = $this->createElement('select', 'generic_elective')
                ->removeDecorator('label')
                //->setAttrib('required', 'required')
                //->setDisableInArrayValidator(true)
                ->setAttrib('class', array('form-control','chosen-select'))
                ->addMultioptions(array(''=>'Select'))
                //->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($generic_elective);
        $max_seat = $this->createElement('text', 'max_seat')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                //->setAttrib('readonly','readonly')
                ->setAttrib('autocomplete', 'off')
                ->removeDecorator("htmlTag");
        $this->addElement($max_seat);
        
        $status_filter = $this->createElement('Select','status_filter')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))

                ->addMultioptions(array(''=>'Select'))
                ->addMultioptions(array('1'=>'Approved'))
                ->addMultioptions(array('0'=>'Rejected'))
                ->addMultioptions(array('2'=>'Pending'))
                ->removeDecorator("htmlTag");
            $this->addElement($status_filter);
            
        $pay_mode = $this->createElement('Select','payment_mode')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))

                ->addMultioptions(array(''=>'Select'))
                ->addMultioptions(array('1'=>'Online'))
                ->addMultioptions(array('0'=>'DD'))
                ->addMultioptions(array('2'=>'NEFT/RTGS'))
                ->removeDecorator("htmlTag");
            $this->addElement($pay_mode);
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

