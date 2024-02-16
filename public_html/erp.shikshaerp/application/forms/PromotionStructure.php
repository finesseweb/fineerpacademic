<?php

class Application_Form_PromotionStructure extends Zend_Form {

    public function init() {
      
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
        
        
            $declaredTerms =  new Application_Model_Declaredterms();
        $data = $declaredTerms->getDropDownListSemester();
           $term = $this->createElement('select', 'semester')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($term);
        
        $nextSem = $this->createElement('select', 'nextSem')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setRegisterInArrayValidator(false)
                ->setAttrib('readonly', 'readonly')
                ->removeDecorator("htmlTag");
                
        $this->addElement($nextSem);


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
        $credit_value = $this->createElement('text', 'credit_score')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($credit_value);

        $no_papers = $this->createElement('select', 'semester_paper_count')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control' ,'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select',
            '0' => '0',
             '1' => '1',        
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            '10' => '10',
            '11' => '11',
            '12' => '12',       
            '13' => '13',
            '14' => '14',
            '18' => '18'));
        $this->addElement($no_papers);

        $appeared_paper = $this->createElement('select', 'appeared_paper')
              ->removeDecorator('label')
                ->setAttrib('class', array('form-control' ,'chosen-select'))
                ->setAttrib('required', 'required')->setRequired(true)
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
                      '1' => '1'));
        $this->addElement($appeared_paper);
        $promotion_dropdown = new Application_Model_Component();
        $drop = $promotion_dropdown->getDropDownList2();
        $ese_paper =  $this->createElement('select', 'component_paper')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                  ->setAttrib('multiple', 'multiple')
                ->removeDecorator("htmlTag")
                ->addMultiOptions($drop);
        $this->addElement($ese_paper);
    }
}
