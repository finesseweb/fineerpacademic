<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for pwc entrance exam Payments Information.
 Date: 23 Dec. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_ApplicationFeesSubmit extends Zend_Db_Table_Abstract {

    public $_name = 'applicant_payment_record';
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
    public function getRecordbyUid($form_id){       
        $select=$this->_db->select()
            ->from($this->_name)
            ->where("$this->_name.form_id=?", $form_id);			   

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
            
            //echo"<pre>";print_r($result);exit;	  
        return $result;
    }
   
    //To get filled form Filled data 
   
    public function insertPayMode($application_no,$payMode){
         
        if($application_no){
            
            $where = array(
                    "md5(application_no) = ?" => $application_no
                );
                $data = array(
                    'payment_mode' => $payMode
                );
            $query = Zend_Db_Table_Abstract::getDefaultAdapter();
            $query=$this->_db->update('applicant_course_details',$data,$where);
           return $query;
        }
    }
    public function checkCourseForRoll($course){
        $select=$this->_db->select()
            ->from(array($this->_name),
               array('roll_no'=>'max(roll_no) as roll_no','payment_status'))
            ->where("$this->_name.course=?", $course);			   

            $result=$this->getAdapter()
            ->fetchRow($select);  
            //echo $select; die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function checkRow($form_id){
        $select=$this->_db->select()
            ->from(array($this->_name),
               array('form_id'=>'form_id as form_id'))
            ->where("$this->_name.form_id=?", $form_id);		   

            $result=$this->getAdapter()
            ->fetchRow($select);  
            //echo $select; die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function getChallan($application_no=''){
        $select=$this->_db->select();
            $select->from(array($this->_name),
                array('challan_no'=>'max(challan_no) as challan_no'));
            if($application_no)	{
                 $select ->where("md5($this->_name.application_no)=?", $application_no);	
            }	   
	 
            $result=$this->getAdapter()
            ->fetchRow($select);  
            //echo $select; die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function checkRow2($form_id){
        $select=$this->_db->select()
            ->from(array($this->_name),
               array('form_id'=>'form_id as form_id'))
            ->where("$this->_name.form_id=?", $form_id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);  
            //echo $select; die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function check_payment_status($applicatonNumber){
        $select=$this->_db->select()
            ->from(array($this->_name),array('payment_status'))   
            ->where("$this->_name.application_no=?", $applicatonNumber);		
            $result=$this->getAdapter()
            ->fetchRow($select);    
            
            //echo $select;die;
            //echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    
}

?>
