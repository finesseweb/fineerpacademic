<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for pwc entrance exam Applicant Information for Educational details.
 Date: 15 Jan. 2020
*/
//ini_set('display_errors', '1');
class Application_Model_ApplicantEducationalDetailModel extends Zend_Db_Table_Abstract {

    public $_name = 'applicant_educational_details';
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
            ->from(array($this->_name))   
            ->where("md5($this->_name.application_no)=?", $conditions);	 
            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo $select;die;
            //echo"<pre>";print_r($result);	  
        return $result;
    }
    
    public function trashItems($deleteId='') {
            $condition = array(
                'application_no = ?' => $deleteId,
            );
            $n = $this->_db->delete('applicant_educational_details', $condition);
            
        } 
    }

?>
