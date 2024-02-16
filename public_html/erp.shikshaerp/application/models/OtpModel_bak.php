<?php

class Application_Model_OtpModel extends Zend_Db_Table_Abstract {

    public $_name = 'otp_manage';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    public function getVerifyotp($id,$otp) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.u_id=?", $id)
                 ->where("$this->_name.otp=?", $otp);
              
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }


    public function getRecords() {

        $select = $this->_db->select()
                ->from($this->_name);
             
     
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    
    
    public function getRecordsbyuid($id) {

        $select = $this->_db->select()
                ->from($this->_name)
          ->where("$this->_name.u_id=?", $id);
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    
    
  
    
}
