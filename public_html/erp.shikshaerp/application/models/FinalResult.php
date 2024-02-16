<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Model_FinalResult extends Zend_Db_Table_Abstract {
    
       public $_name = 'final_result';
       protected $_id = 'id';

    //Get all records
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
               ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
     public function getRecordByAcdemic($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.academic_year_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
    public function getTopRecordByAcdemic($id,$limit='') {
        $select = $this->_db->select()
                ->from($this->_name)
                 ->join(array("acad"=>"academic_master"),"acad.academic_year_id=$this->_name.academic_year_id")
		
                ->where("$this->_name.academic_year_id=?", $id)
                ->order('total_cgpa  desc')
                ->limit($limit);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
      public function getAllRecordByAcdemic($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                 ->join(array("acad"=>"academic_master"),"acad.academic_year_id=$this->_name.academic_year_id")
		 ->where("$this->_name.academic_year_id=?", $id)
               ->group("$this->_name.academic_year_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('id', 'name',))
             
                ->order('id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['id']] = $val['name'];
        }
        return $data;
    }
}

