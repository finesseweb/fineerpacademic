<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for pwc entrance exam Applicant Information.
 Date: 16 Dec. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_ApplicantLoginModel extends Zend_Db_Table_Abstract {

    public $_name = 'applicant_login_details';
    protected $_id = 'id'; 
    //This function gets all promotion rule data
    public function getRecords(){       
       
       
    } 
    public function getRecordById($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name)
            ->where("$this->_name.id=?", $id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function checkRow($conditions){       
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("$this->_name.applicant_id=?", $conditions);	
            $result=$this->getAdapter()
            ->fetchAll($select);    
            
            //echo"<pre>";print_r($result);exit;	  
        return $result;
    }
  
    
   
    
  
}

?>
