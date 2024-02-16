<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_BackGradeAllocationItems extends Zend_Db_Table_Abstract {

    protected $_name = 'back_grade_allocation_items';
    protected $_id = 'grade_allocation_item_id';
    
    	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)	;			   
					  //->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
    
  
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)                      				   
					  //->where("$this->_name.status !=?", 0)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
    
    
    
	public function trashItems($grade_allocation_id) {



        $this->_db->delete($this->_name, "grade_allocation_id=$grade_allocation_id");



    }	
    
        public function getMarksDetailsWithCourse($tabl_id,$stu_id,$term_id){

            $select=$this->_db->select()

                      ->from($this->_name)

                      ->where("$this->_name.tabl_id=?", $tabl_id)	

                      ->where("$this->_name.student_id=?", $stu_id);

        $result=$this->getAdapter()

                      ->fetchAll($select);    

    //print_r($result);die;					  

        return $result;

    }

    
    
}