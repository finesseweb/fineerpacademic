<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserLog
 *
 * @author ash0a
 */


class Application_Model_UserLog extends Zend_Db_Table_Abstract
{
    public $_name = 'user_log';
    protected $_id = 'id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)	;			   
					 
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
    public function getRecordByemplId($id,$unique_id,$sess_vlue='',$ip='')
    {       
        $select=$this->_db->select()
            ->from($this->_name);
            $select->where("$this->_name.empl_id =?", $id);
            $select->where("$this->_name.unique_id =?", $unique_id);
            //$select->where("$this->_name.sess_vlue =?", $sess_vlue);
            if(!empty($ip))
            $select->where("$this->_name.ip =?", $ip);
            //$select->where("$this->_name.sess_vlue !=?", 0);
            $select->where("$this->_name.status =?", 1);
            $select->order("id desc");
            //echo $select;die;
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
   
}
?>
