<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Model_Year extends Zend_Db_Table_Abstract {
    
       public $_name = 'year_master';
    protected $_id = 'id';

    //Get all records
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('id', 'name',))
                ->where("status =?", 0)
                ->order('id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['id']] = $val['name'];
        }
        return $data;
    }
}

