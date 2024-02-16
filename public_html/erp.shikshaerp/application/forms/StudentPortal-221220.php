<?php

class Application_Form_StudentPortal extends Zend_Form {
 var $year_of_passing='10-01-2011';
 public function __construct($aecc_id){
   $this->init($aecc_id);
 }

    public function init($aecc_id) {
       
        //Added by kedar
        $session_year = new Application_Model_Session();
		$data = $session_year->getDropDownList();
		//print_r($data); die;
		$session_id = $this->createElement('select','session')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select Session'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($session_id);
        $Academic_year_model = new Application_Model_AcademicYear();
		$data = $Academic_year_model->getDropDownList();
		//print_r($data); die;
		$year_id = $this->createElement('select','academic_year_list')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setAttrib('required','required')
           
							->addMultiOptions(array('' => 'Select Academic Year'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($year_id);
        //To sort by department
        $Department_model = new Application_Model_Department();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$department = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            //->setAttrib('required','required')
							->addMultiOptions(array('' => 'Filter by Department'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($department);
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
        //End        
        $stu_fname = $this->createElement('text', 'stu_fname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_fname);

        $stu_lname = $this->createElement('text', 'stu_aadhar')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->removeDecorator("htmlTag");
        $this->addElement($stu_lname);
        
        $stu_regNo = $this->createElement('text', 'reg_no')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->removeDecorator("htmlTag");
        $this->addElement($stu_regNo);
        $stu_examRoll = $this->createElement('text', 'exam_roll')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->removeDecorator("htmlTag");
        $this->addElement($stu_examRoll);
        
        $blood_group = $this->createElement('text', 'blood_group')
                ->setAttrib('maxlength', '3')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))                
                ->removeDecorator("htmlTag");
        $this->addElement($blood_group);
        
        $gender = $this->createElement('select', 'gender')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
            '1' => 'Female',
            '2' => 'Transgender'));
        $this->addElement($gender);
       
        $participant_username = $this->createElement('text', 'participant_username')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($participant_username);
      
        
        
        
         $participant_pword = $this->createElement('password', 'participant_pword')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($participant_pword);
        
          $confirm_password = $this->createElement('password', 'confirm_password')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                  //->addValidator($validator)
                ->removeDecorator("htmlTag");
        $this->addElement($confirm_password);
        
                $date_box =  $this->getDropDownList($this->year_of_passing);
              //  print_r($date_box);exit;
                 $year_of_passing = $this->createElement('select', 'passing_year')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))

                //->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
               ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($date_box);
        $this->addElement($year_of_passing);

        $stu_nationality = $this->createElement('text', 'stu_nationality')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_nationality);

        $stu_email_id = $this->createElement('text', 'stu_email_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_email_id);

        $stu_dob = $this->createElement('text', 'stu_dob')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_dob);

        $present_addr = $this->createElement('textarea', 'present_addr')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($present_addr);

        $premanent_addr = $this->createElement('textarea', 'premanent_addr')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($premanent_addr);

        $father_fname = $this->createElement('text', 'father_fname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($father_fname);

        $stu_doA = $this->createElement('text', 'stu_doA')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_doA);

        $father_mobileno = $this->createElement('text', 'father_mobileno')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($father_mobileno);


        $mother_fname = $this->createElement('text', 'mother_fname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($mother_fname);

        

        $Academic_model = new Application_Model_Academic();
        $data = $Academic_model->getDropDownList();
        //print_r($data); die;
        $academic_id = $this->createElement('select', 'academic_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($academic_id);

       $admTerms =  new Application_Model_Declaredterms();
        $data = $admTerms->getDropDownList();
           $adm_term = $this->createElement('select', 'adm_sem')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                   ->addMultiOptions($data);
                   $this->addElement($adm_term);
       $leavingTerms =  new Application_Model_Declaredterms();
        $data = $leavingTerms->getDropDownList();
           $leaving_term = $this->createElement('select', 'leaving_sem')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                   ->addMultiOptions($data);
                   $this->addElement($leaving_term);
                   
        $is_promoted = $this->createElement('select', 'is_promoted')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(// '' => 'Select',
            '1' => 'Yes',
            '2' => 'No'
                    ));
        $this->addElement($is_promoted);   
        
        $is_course_completed = $this->createElement('select', 'is_course_completed')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(// '' => 'Select',
            '1' => 'Yes',
            '2' => 'No'
                    ));
        $this->addElement($is_course_completed);   
        
        $is_fee_paid = $this->createElement('select', 'is_fee_paid')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(// '' => 'Select',
            '1' => 'Yes',
            '2' => 'No'
                    ));
        $this->addElement($is_fee_paid);  
        $uni_exam = $this->createElement('text', 'name_of_university_exam')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($uni_exam);
        
        $exam_name = $this->createElement('text', 'name_of_exam')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($exam_name);
        
        $result = $this->createElement('text', 'result_of_exam')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($result);
        $result = $this->createElement('text', 'session_for_cert')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($result);
        
        $stu_id = $this->createElement('text', 'stu_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_id);

        $stu_status = $this->createElement('select', 'stu_status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(// '' => 'Select',
            '1' => 'Continue',
            '2' => 'Discontinue',
            '3' => 'T.C.',
            '4' => 'Alumni',
            '5' => 'Left'));
        $this->addElement($stu_status);


        $effective_date = $this->createElement('text', 'effective_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($effective_date);
        
        

        $roll_no = $this->createElement('text', 'roll_no')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
               
                ->removeDecorator("htmlTag");
        $this->addElement($roll_no);
        
        
        
                        $ge_model = new Application_Model_Ge();
                        $data = $ge_model->getDropDownList($aecc_id);
                        $ge_id = $this->createElement('select','ge')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
                            ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                             $this->addElement($ge_id);
        
        
        $aecc_model = new Application_Model_Aeccge();
		$data = $aecc_model->getDropDownList($aecc_id);
		//print_r($data); die;
		$aecc_id = $this->createElement('select','aecc')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            //->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($aecc_id);
		
        
        
    }
    
public function getDropDownList($year_of_passing=''){
$data = array();
$start_date = $year_of_passing;
//print_r($start_date);exit;
$year = substr($start_date, strlen($start_date)-4,4);

$years = $this->date_diff2($start_date,date('d-m-Y'));
for($i=0;$i<=$years; $i++){
$data[(int)$year+$i] = (int)$year+$i;
}
return $data;
}



function date_diff2($date1 = '', $date2 = '') {


    $diff = abs(strtotime($date2) - strtotime($date1));

    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff ) / (60 * 60 * 24));
    return (int) $years;
}

}

?>