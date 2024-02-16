<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Revalstatus extends Zend_Db_Table_Abstract
{
    public $_name = 'reval_status';
    protected $_id = 'id';
    
    
    
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
    	public function getRecordby($tbl_id,$student_id,$flag = 'R')
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array("DATE_FORMAT(updated_date, '%d-%m-%Y') as added_date"))
                      ->joinLeft(array("tr"=>"tabulation_report"),"tr.tabl_id=$this->_name.tabl_id",array("term_id"))
                      ->where("$this->_name.tabl_id=?", $tbl_id)
                      ->where("$this->_name.student_id=?", $student_id)
                      ->where("$this->_name.flag=?", $flag)
                      ->order(array("$this->_name.id desc"));
					  //->where("$this->_name.status !=?", 2);
                      //echo $select;die;
        $result=$this->getAdapter()
                      ->fetchRow($select); 
        
        return $result;
    }
    
    
    
}

