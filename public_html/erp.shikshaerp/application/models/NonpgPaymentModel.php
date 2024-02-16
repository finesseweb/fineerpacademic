<?php

class Application_Model_NonpgPaymentModel extends Zend_Db_Table_Abstract {

    public $_name = 'pgnon_payment_detail';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
              
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
    
    
    public function getStuRecords($uid) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("md5($this->_name.stu_id)=?", $uid);
        $result = $this->getAdapter()
                ->fetchAll($select);
         // echo $result; die();
        return $result;
    }
    
    
    public function getRecordbyfid($uid) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.u_id=?", $uid);
        $result = $this->getAdapter()
                ->fetchRow($select);
//echo $select; exit;
        return $result;
    }
    
    
    public function getpayRecordbyfid($uid) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("examfee"=>"pg_non_form_data"),"examfee.id =$this->_name.payment_id")
                ->where("md5($this->_name.u_id)=?", $uid)
                ->where("$this->_name.status=?", 1)
                ->where("$this->_name.mmp_txn !=?",'')
                ->where("$this->_name.mmp_txn !=?",0)
                ->where("$this->_name.f_code=?", 'Ok')
                ->where("examfee.payment_status =?",1)
                ->where("examfee.status =?",0)
                ->order("$this->_name.id  DESC");
        $result = $this->getAdapter()
                ->fetchRow($select);
//echo $select; exit;
        return $result;
    }
    
    
  
    
}
