<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for pwc entrance exam Applicant Information.
 Date: 16 Dec. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_ApplicantRegisterationModel extends Zend_Db_Table_Abstract {

    public $_name = 'applicant_registration';
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
    public function getRecordByAppNo($application_id=''){       
        $select=$this->_db->select()
            ->from($this->_name,array('applicant_name','email_id','phone_number','password','application_no as application_no'))	   
            ->where("md5($this->_name.application_no)=?", $application_id);	
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function checkRow($conditions='',$email_id=''){       
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("$this->_name.phone=?", $conditions)	
            ->where("$this->_name.email_id=?", $email_id);	
            $result=$this->getAdapter()
            ->fetchAll($select);    
            
            //echo $select;die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function checkRow1($email_id=''){       
        $select=$this->_db->select()
            ->from(array($this->_name))	
            ->where("$this->_name.email_id=?", $email_id);	
            //->where("$this->_name.status=?", 1);
            $result=$this->getAdapter()
            ->fetchRow($select);    
            
            //echo $select;die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function checkRowfor_email_exist($email_id=''){       
        $select=$this->_db->select()
            ->from(array($this->_name))	
            ->where("$this->_name.email_id=?", $email_id)	
            ->where("$this->_name.status=?", 1);
            $result=$this->getAdapter()
            ->fetchRow($select);    
            
            //echo $select;die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function checkLogin($loginData){       
        $select=$this->_db->select()
            ->from(array($this->_name))	
            ->where("$this->_name.application_no=?", $loginData['application_no'])	
             
            ->where("$this->_name.password=?", $loginData['password']);	
            $result=$this->getAdapter()
            ->fetchRow($select);    
            
            //echo $select;die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function checkEmail($emailData){       
        $select=$this->_db->select()
            ->from(array($this->_name))	
            ->where("$this->_name.email_id=?", $emailData['email_id']);	
            $result=$this->getAdapter()
            ->fetchRow($select);    
            
            //echo $select;die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
  
    public function updateInsertData($data,$conditions,$email_id=''){       
            $where = array(
                'phone = ?' => $conditions,
                'email_id = ?' => $email_id
            );
                
            $query = Zend_Db_Table_Abstract::getDefaultAdapter();
            $query=$this->_db->update('applicant_registration',$data,$where);
            return $query;
    }
    public function updateOtp($otpData,$conditions,$email_id){       
            $where = array(
                'phone = ?' => $conditions,
                'email_id = ?' => $email_id
            );
                $data = array(
                    'otp' =>$otpData
            );
            $query = Zend_Db_Table_Abstract::getDefaultAdapter();
            $query=$this->_db->update('applicant_registration',$data,$where);
            return $query;
    }
    public function validateOtp($phone_no,$email_id,$inputOtp){
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("$this->_name.phone=?", $phone_no)
            ->where("$this->_name.email_id=?", $email_id)
            ->where("$this->_name.otp=?", $inputOtp);	
            $result=$this->getAdapter()
            ->fetchAll($select);    
            
            //echo $select;die;
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
  
}

?>
