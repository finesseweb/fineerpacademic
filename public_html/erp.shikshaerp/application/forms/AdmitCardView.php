<?php

class Application_Form_AdmitCardView extends Zend_Form {
 

    public function init() {
        
       
       

     /// raushan   
        $year = $this->createElement('text', 'year')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($year);

        $session = $this->createElement('text', 'session')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($session);


        $semester = $this->createElement('select', 'semester')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required') 
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        
                 $this->addElement($semester);


     $stu_fname = $this->createElement('text', 'stu_fname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_fname);
        
        $stu_dob = $this->createElement('text', 'stu_dob')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
               ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_dob);


        $father_fname = $this->createElement('text', 'father_fname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                 ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($father_fname);


         $registration_no = $this->createElement('text', 'registration_no')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                  ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($registration_no);
        
        
        $reg_no = $this->createElement('text', 'reg_no')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                  ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($reg_no);


        $examination = $this->createElement('text', 'examination')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                 ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($examination);



        $exam_roll = $this->createElement('text', 'exam_roll')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($exam_roll);


         $college = $this->createElement('text', 'college')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                 ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($college);


        $core_course = $this->createElement('text', 'core_course')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                 ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($core_course);
        

        $core_paper = $this->createElement('text', 'core_paper')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($core_paper);


        $dse = $this->createElement('text', 'dse')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
               ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($dse);

         $dse_paper = $this->createElement('text', 'dse_paper')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                  ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($dse_paper);


        $ge = $this->createElement('text', 'ge')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                 ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($ge);

         $ge_paper = $this->createElement('text', 'ge_paper')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
              ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
         
        $this->addElement($ge_paper);

        // $aecc = $this->createElement('checkbox', 'aecc')
        //         ->removeDecorator('label')->setAttrib('class', array('form-control'))
        //         ->setAttrib('required', 'required')
        //         ->setAttrib('required', 'true')
        //         ->removeDecorator("htmlTag");
        // $this->addElement($aecc);


        $sec = $this->createElement('text', 'sec')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($sec);


         $sec_paper = $this->createElement('text', 'sec_paper')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                 ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($sec_paper);


        $comm_exam = $this->createElement('text', 'comm_exam')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                 ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($comm_exam);


        $exam_center = $this->createElement('text', 'exam_center')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setAttrib('readonly','readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($exam_center);
        
         
        
        
        
    }
    

}

?>
