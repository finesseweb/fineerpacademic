<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for pwc entrance exam Applicant Information for personal details.
 Date: 14 Jan. 2020
*/
//ini_set('display_errors', '1');
class Application_Model_ApplicantPersonalDetailModel extends Zend_Db_Table_Abstract {

    public $_name = 'applicant_personal_details';
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
    public function getsavedData($conditions){       
        $select=$this->_db->select()
            ->from($this->_name)   
            ->where("md5($this->_name.application_no)=?", $conditions);	 
            $result=$this->getAdapter()
            ->fetchRow($select);    
           //echo $select; die;
           // echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    
}

?>
