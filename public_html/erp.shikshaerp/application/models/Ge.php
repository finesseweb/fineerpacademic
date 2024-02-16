<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Model_Ge extends Zend_Db_Table_Abstract
{
    public $_name = 'master_ge';
    protected $_id = 'ge_id';
   
    
    	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name) 
                      ->joinleft(array('department'),"department.id=$this->_name.department",array('department'))
                      ->joinleft(array('degree_info'),"degree_info.id=$this->_name.degree_id",array('degree'))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
    	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }

    
    
    
    
        public function getDropDownList(){
            $select = $this->_db->select()
            ->from($this->_name, array('ge_id','general_elective_name',))				
                    ->where("status =?",0)
                    ->order('general_elective_name  ASC');
            $result = $this->getAdapter()->fetchAll($select);
            $data = array();
            foreach ($result as $val) {

                $data[$val['ge_id']] = $val['general_elective_name'];
            }
            return $data;
        }
        public function getDropDownListForcourseGroup(){
            $select = $this->_db->select()
            ->from($this->_name, array('ge_id','general_elective_name',))				
                    ->where("status =?",0)
                    ->order('general_elective_name  ASC');
            $result = $this->getAdapter()->fetchAll($select);
            $data = array();
            foreach ($result as $val) {

                $data[$val['ge_id']] = $val['general_elective_name'];
            }
            return $data;
    }
    
    
}
