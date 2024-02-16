<?php
class Application_Form_ElectiveSelection extends Zend_Form
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
        
        $Academic_year_model = new Application_Model_AcademicYear();
		$data = $Academic_year_model->getDropDownList();
		//print_r($data); die;
		$year_id = $this->createElement('select','academic_year')
                    ->removeDecorator('label')
                    ->setAttrib('class',array('form-control','chosen-select'))
                    ->setAttrib('required','required')
                        ->setRequired(true)

                    ->addMultiOptions(array('' => 'Select'))
                    ->addMultiOptions($data)
                    ->removeDecorator("htmlTag");
        $this->addElement($year_id);
                 $Department_model = new Application_Model_Session();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        //->setAttrib('required','required')
							//->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($session_id);
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')->setRequired(true)
						   ->setAttrib('multiple','multiple')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id);
        
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$batch_id = $this->createElement('select','batch_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setAttrib('required','required')->setRequired(true)
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($batch_id);
		$student_id = $this->createElement('select','course_id')
		              ->removeDecorator('label')
					  ->setAttrib('class',array('form-control','chosen-select'))
					  ->setAttrib('required','required')->setRequired(true)
					  ->removeDecorator("htmlTag")
					  ->addMultiOptions(array(''=>'Select'));
		$this->addElement($student_id);	
                
                
        $this->addElement($academic_year_id);
		$student_id = $this->createElement('select','course_id2')
		              ->removeDecorator('label')
					  ->setAttrib('class',array('form-control','chosen-select'))
					  ->setAttrib('required','required')->setRequired(true)
					  ->removeDecorator("htmlTag")
					  ->addMultiOptions(array(''=>'Select'));
		$this->addElement($student_id);	
                
                   $declaredTerms =  new Application_Model_Declaredterms();
        $data = $declaredTerms->getDropDownList();
         $term_id = $this->createElement('select','term_id')
                    ->removeDecorator('label')
                     ->setAttrib('class',array('form-control','chosen-select'))
                     ->setAttrib('required','required')->setRequired(true)
                     ->removeDecorator("htmlTag")
                     ->addMultiOptions(array(''=>'Select'))
                 ->addMultiOptions($data);
        $this->addElement($term_id);	
        
        
             $term_id = $this->createElement('select','term_id2')
                    ->removeDecorator('label')
                     ->setAttrib('class',array('form-control','chosen-select'))
                     ->setAttrib('required','required')->setRequired(true)
                     ->removeDecorator("htmlTag")
                     ->addMultiOptions(array(''=>'Select'))
                 ->addMultiOptions($data);
        $this->addElement($term_id);
        
        //For Academic Year
        $Academic_year_model = new Application_Model_AcademicYear();
		$data = $Academic_year_model->getDropDownList();
		//print_r($data); die;
		$year_id = $this->createElement('select','academic_year')
                    ->removeDecorator('label')
                    ->setAttrib('class',array('form-control','chosen-select'))
                    ->setAttrib('required','required')
                        ->setRequired(true)

                    ->addMultiOptions(array('' => 'Select'))
                    ->addMultiOptions($data)
                    ->removeDecorator("htmlTag");
        $this->addElement($year_id);
        
		
		
	}
                   
    
	
	}
		?>