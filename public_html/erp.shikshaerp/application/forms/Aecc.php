<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */class Application_Form_Aecc extends Zend_Form
{
	public function init()
	{
		
		$aecc_name = $this->createElement('text','aecc_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
				->setRequired(true)
				->setAttrib('required','required')
                        ->setRequired(true)
                ->removeDecorator("htmlTag");
                $this->addElement($aecc_name);
		
	
		$status = $this->createElement('select','status')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
                        ->setRequired(true)
							->removeDecorator("htmlTag")
							->addMultiOptions(array('0' => 'Active','2'=>'Inactive'));
                    $this->addElement($status);
		
	
	}
	
}

