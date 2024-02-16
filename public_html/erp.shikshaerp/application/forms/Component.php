<?php

class Application_Form_Component extends Zend_Form {

    public function init() {


        $component = $this->createElement('text', 'component')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
           ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($component);

        $status = $this->createElement('select', 'status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->addMultioptions(array( '0' => 'Active', '1' => 'Inactive'))
                ->removeDecorator('htmlTag');
        $this->addElement($status);
    }

}
