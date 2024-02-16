<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for Promotion rule.
 Date: 25 Sept. 2019
*/
class Application_Model_SemesterRule extends Zend_Db_Table_Abstract {

    public $_name = 'promotion_master';
    protected $_id = 'id'; 
    //This function gets all promotion rule data
    public function getRecords(){       
        $select=$this->_db->select()
            ->from($this->_name)
            ->join(array("term"=>"declared_terms" ),"term.term_des=$this->_name.semester",array("term_name"))
            ->join(array("terms"=>"declared_terms"),"terms.term_des=$this->_name.nextSem",array("term_name as next_sem"))
            ->join(array("degree"=>"degree_info"),"degree.id=$this->_name.degree_id",array("degree as degree_name"))
            ->where("$this->_name.status !=?",2);
            $result=$this->getAdapter()
            ->fetchAll($select);  
            //echo"<pre>";print_r($result);die;
        return $result;
    } 
    public function getRecordById($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name)
            ->where("$this->_name.id=?", $id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
  			  
        return $result;
    }
    public function checkRow($degree_id='',$cmn_term=''){       
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("$this->_name.degree_id=?", $degree_id)	
            ->where("$this->_name.semester=?", $cmn_term);	
            $result=$this->getAdapter()
            ->fetchAll($select);  
            //echo $select;die;
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    

    
    
}
?>
