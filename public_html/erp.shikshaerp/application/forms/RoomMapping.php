<?php

class Application_Form_RoomMapping extends Zend_Form
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
			$status = $this->createElement('select','status')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select','0'=>'Active','1'=>'Inactive'))
							->removeDecorator('htmlTag');
				$this->addElement($status);
                               $department_master = new Application_Model_Department();
                               $department_lists = $department_master->getDropDownList(); 
			$status = $this->createElement('select','department_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
							->addMultioptions($department_lists)
							->removeDecorator('htmlTag');
				$this->addElement($status);
                               $room_master = new Application_Model_Room();
                               $room_lists = $room_master->getDropDownRoomList();
				$status = $this->createElement('select','room_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
							->addMultioptions($room_lists)
							->removeDecorator('htmlTag');
				$this->addElement($status);
        
	
	}
	
}