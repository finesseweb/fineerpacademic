<?php
class Application_Form_StudentReport extends Zend_Form
{
    public function init()
    {
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id);
		     $year_drop_down = new Application_Model_Year();
        $year_arr = $year_drop_down->getDropDownList();
		$year_id = $this->createElement('select','year_id')
					    ->removeDecorator('label')
						->setAttrib('class',array('form-control','chosen-select'))
						->setAttrib('required','required')
						->addMultiOptions(array('' => 'Select'))
						->addMultiOptions($year_arr)
                        
						->removeDecorator("htmlTag");
		$this->addElement($year_id);
	
	    $StudentPortal_model = new Application_Model_StudentPortal();
		$data = $StudentPortal_model->getDropDownList();
		//print_r($data); die;
		$stu_id = $this->createElement('select','stu_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						    //->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($stu_id);
        
		$degree_model = new Application_Model_Degree();
        $data = $degree_model->getDropDownList();
         $degree_id = $this->createElement('select', 'degree_id')
                ->removeDecorator('label')
                ->setAttrib('required', 'required')
                ->setAttrib('class', array('form-control'))
                 ->addMultioptions(array(''=>'--select--'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($degree_id);
        
        $Coursecategory_model = new Application_Model_Coursecategory();
		$data = $Coursecategory_model->getDropDownList();
		//print_r($data); die;
            $cc_id = $this->createElement('select','cc_id')
            ->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
            ->setAttrib('required','required')
            ->removeDecorator("htmlTag")
            ->addMultiOptions(array('' => 'Select'))
            ->addMultiOptions($data);
        $this->addElement($cc_id);
        
        $Ge_model = new Application_Model_Ge();
		$data = $Ge_model->getDropDownList();
		//print_r($data); die;
		$Ge_id = $this->createElement('select','ge_id')
            ->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
            ->addMultiOptions(array('0' => 'Select'))
            ->addMultiOptions($data)
            ->removeDecorator("htmlTag");
        $this->addElement($Ge_id);
        
        $Department_model = new Application_Model_Department();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$department = $this->createElement('select','department')
            ->removeDecorator('label')
            ->setAttrib('class',array('form-control','chosen-select'))
                                     //   ->setAttrib('required','required')
            ->addMultiOptions(array('0' => 'Select'))
            ->addMultiOptions($data)
            ->removeDecorator("htmlTag");
        $this->addElement($department);
}
}
    ?>