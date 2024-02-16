<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for pwc entrance exam Applicant Information.
 Date: 16 Dec. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_ApplicantCourseDetailModel extends Zend_Db_Table_Abstract {

    public $_name = 'applicant_course_details';
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
    public function getApplicationNumber($form_id)
    {       
        $select=$this->_db->select()
            ->from(array($this->_name))   
           ->joinleft(array("applicant_educational_details"),"applicant_educational_details.application_no=$this->_name.application_no",array('photo')) 
            ->where("$this->_name.form_id=?", $form_id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //Added by kedar for Entrance Report
    public function getRecordByAppID($application_id=''){       
        $select=$this->_db->select()
            ->from($this->_name,array('applicant_name','degree_id','course','phone','application_no','form_id'))	 
->joinleft(array("applicant_documents_followup"),"applicant_documents_followup.application_no=$this->_name.application_no",array('certificate_list')) 
            //->where("$this->_name.application_no=?", $application_id);	
            ->where("$this->_name.application_no=?", $application_id);	 
            $result=$this->getAdapter()
            ->fetchRow($select);  
            //echo $select;die;
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function getRecordByAppIDPaySlip($application_id=''){ 
        //echo"<pre>";print_r($application_id);die;	
        if($application_id){
        $select=$this->_db->select()
            ->from($this->_name,array('applicant_name','degree_id','course','phone','application_no','form_id as form_id'))	 
            ->joinleft(array("applicant_paymode_details"),"applicant_paymode_details.application_no=$this->_name.application_no",array('pay_mode1','pay_mode2','account_name1','account_name2','bank_id1','bank_id2','date_time1','date2','amount1','amount2','unique_id1','unique_id2')) 
            //->joinleft(array("semester_fee_collection"),"semester_fee_collection.stu_id=$this->_name.form_id",array('mmp_txn','date')) 
            ->joinleft(array("applicant_fee_account"),"applicant_fee_account.form_id=$this->_name.form_id",array('account1','total_fee1','account2','total_fee2')) 
           //	->where("semester_fee_collection.semester=?", 't1')	
               // ->where("semester_fee_collection.status=?", 1)
            ->where("$this->_name.application_no=?", $application_id);	 
            $result=$this->getAdapter()
            ->fetchAll($select); 
            //echo"<pre>";print_r($result);die;	
            //echo $select;die;
          if(empty($result)){
              $select=$this->_db->select()
              ->from($this->_name,array('applicant_name','degree_id','course','phone','application_no','form_id as form_id'))	 
            ->joinleft(array("applicant_paymode_details"),"applicant_paymode_details.application_no=$this->_name.application_no",array('pay_mode1','pay_mode2','account_name1','account_name2','bank_id1','bank_id2','date1','date2','amount1','amount2','unique_id1','unique_id2')) 
            
            ->joinleft(array("applicant_fee_account"),"applicant_fee_account.form_id=$this->_name.form_id",array('account1','total_fee1','account2','total_fee2')) 
            ->where("$this->_name.application_no=?", $application_id);	 
            $result=$this->getAdapter()
            ->fetchAll($select); 

            
               
          }
           //echo"<pre>";print_r($result);die;
        return $result;
        }
        echo 'pass';
    }
    //End
      public function getApplicationCourseDetails($form_id)
    {       
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("$this->_name.form_id=?",$form_id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);
            //echo $select;            die;
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
    public function getAllFormFilledData($application_no){
       
        $select=$this->_db->select()
            ->from(array($this->_name),array('degree_id as degree','course','session','core_course1','ge1','aecc1','core_course2','ge2','aecc2','comp_evs','form_id',))   
           ->joinleft(array("applicant_personal_details"),"applicant_personal_details.application_no=$this->_name.application_no") 
         
            ->joinleft(array("entrance_exam_schedule"),"entrance_exam_schedule.department=$this->_name.course") 
           ->joinleft(array("applicant_educational_details"),"applicant_educational_details.application_no=$this->_name.application_no") 
        
            ->where("md5($this->_name.application_no)=?", $application_no);	 
            $result=$this->getAdapter()
            ->fetchRow($select);    
       //echo $select; die;
      //echo"<pre>";print_r($result);exit;	
           
        if($result){
           $select = $this->_db->select()
                ->from('department_type',array('department_type','session'))
                ->where("department_type.id=?", $result['course']);

            $result1 = $this->getAdapter()
                ->fetchRow($select);

            $select = $this->_db->select()
            ->from("academic_master",array("department"))
            ->where("academic_master.academic_year_id =?",$result['core_course1']);

            $result2 = $this->getAdapter()->fetchRow($select);
            if($result2){
                $select = $this->_db->select()
                ->from("department",array("department"))
                ->where("department.id =?",$result2['department']);

                $result3 = $this->getAdapter()->fetchRow($select);
               
            }    
            $select = $this->_db->select()
            ->from("academic_master",array("department"))
            ->where("academic_master.academic_year_id =?",$result['core_course2']);

            $result4 = $this->getAdapter()->fetchRow($select);
            if($result4){
                $select = $this->_db->select()
                ->from("department",array("department"))
                ->where("department.id =?",$result4['department']);

                $result5 = $this->getAdapter()->fetchRow($select);

            }
            $select = $this->_db->select()
            ->from("master_ge",array("general_elective_name"))
            ->where("master_ge.ge_id =?",$result['ge1']);

            $result6 = $this->getAdapter()->fetchRow($select);

            $select = $this->_db->select()
            ->from("master_ge",array("general_elective_name"))
            ->where("master_ge.ge_id =?",$result['ge2']);

            $result7 = $this->getAdapter()->fetchRow($select);
            
            $result['course_name']=$result1['department_type'];
            $result['session'] =$result1['session'];
            $result['corecourse1'] =$result3['department'];
            $result['corecourse2'] =$result5['department'];
            $result['ge1'] =$result6['general_elective_name'];
            $result['ge2'] =$result7['general_elective_name'];
            return $result;
        }
    }
    
    //End
    public function generateFormId($application_no){
         
        $select=$this->_db->select()
        ->from(array($this->_name),array('id'))   
           
       ->where("md5($this->_name.application_no)=?", $application_no);	 
            $result=$this->getAdapter()
         ->fetchRow($select);    
       //echo $select; die;
      
           
        if($result){
            $form_id = 'F-2020-' .+$result['id'];
            $where = array(
                    "md5(application_no) = ?" => $application_no
                );
                $data = array(
                    'form_id' => $form_id,
                    'form_status' => 'okey'
                );
            $query = Zend_Db_Table_Abstract::getDefaultAdapter();
            $query=$this->_db->update('applicant_course_details',$data,$where);
            echo $query; die;
           return $form_id;
        }
    }
    //To check Form Status Date:12 Feb 2020
    public function check_form_status($applicatonNumber){
        $select=$this->_db->select()
            ->from(array($this->_name),array('form_status'))   
            ->where("$this->_name.application_no=?", $applicatonNumber);	
            $result=$this->getAdapter()
            ->fetchRow($select);    
            
            //echo $select;die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    public function insertPayMode($application_no,$payMode){
         
        if($application_no){
            
            $where = array(
                    'application_no = ?' => $application_no
                );
                $data = array(
                    'payment_mode' => $payMode
                );
            $query = Zend_Db_Table_Abstract::getDefaultAdapter();
            $query=$this->_db->update('applicant_course_details',$data,$where);
           return $query;
        }
    }
     public function getRecordByAppNo($application_id=''){       
        $select=$this->_db->select()
            ->from($this->_name,array('applicant_name','degree_id','course','phone','application_no'))	 
            ->joinleft(array("applicant_personal_details"),"applicant_personal_details.application_no=$this->_name.application_no") 
            //->where("$this->_name.application_no=?", $application_id);	
            ->where("md5($this->_name.application_no)=?", $application_id);	 
            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    
         public function getRecordByCourse($core_course_id=''){       
        $select=$this->_db->select()
            ->from($this->_name,array('applicant_name as stu_fname','degree_id','course','phone','application_no as reg_no','form_id as stu_id'))	 
            ->join(array('payment'=>'applicant_payement_details'),"payment.application_no = $this->_name.application_no",array('roll_no as exam_roll'))
            ->join(array('edu'=>'applicant_educational_details'),"edu.application_no = $this->_name.application_no",array('photo as filename'))
            //->where("$this->_name.application_no=?", $application_id);	
           
            ->where("payment.payment_status = ?", 1) 
            ->where("($this->_name.core_course1 in (?)", $core_course_id)
            
              ->orwhere("$this->_name.core_course2 in (?))", $core_course_id)
              ->group("$this->_name.form_id");
       // echo $select; die;
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    
    
    
    //Added by kedar for Entrance Report
    public function getRecordByindividualPgCourse($id,$field_name = "course")
    {       
        $select=$this->_db->select()
            ->from($this->_name,array("count(distinct($this->_name.form_id)) as total") )
                ->joinLeft(array('payment'=>'applicant_payement_details'),"payment.form_id = $this->_name.form_id",array())
            ->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = payment.id",array())
            ->joinLeft(array('edu'=>'applicant_educational_details'),"edu.application_no = $this->_name.application_no",array('photo'))
            ->joinLeft(array('paymode'=>'applicant_paymode_details'),"paymode.application_no = $this->_name.application_no",array('class_roll'))
            ->where("$this->_name.course!=?",'')
            ->where("$this->_name.$field_name=?", $id)
             ->where("payment.payment_status = ?", 1)
             ->where("record.f_code like ?", 'ok');
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    public function getRecordByindividualCourse($id,$field_name = "core_course1")
    {       
        $select=$this->_db->select()
            ->from('applicant_paymode_details')   
            ->joinleft(array('course'=>'applicant_course_details'),"course.application_no = applicant_paymode_details.application_no",array("count(distinct(course.form_id)) as total"," count(case course.aecc1 when 'English' then 1 else null end) as'English'","count(case course.aecc1 when 'Hindi' then 1 else null end) as'Hindi'") )
            
            ->joinLeft(array('edu'=>'applicant_educational_details'),"edu.application_no = applicant_paymode_details.application_no",array("count(case edu.caste_category when 'General' then 1 else null end) as'General'","count(case edu.caste_category when 'General' then 1 else null end) as'General'","count(case edu.caste_category when 'BC-1(EBC)' then 1 else null end) as'BC-1(EBC)'","count(case edu.caste_category when 'BC-2(OBC)' then 1 else null end) as'BC-2(OBC)'","count(case edu.caste_category when 'EWS' then 1 else null end) as'EWS'","count(case edu.caste_category when 'SC' then 1 else null end) as'SC'","count(case edu.caste_category when 'ST' then 1 else null end) as'ST'","count(case edu.caste_category when '0' then 1 else null end) as'zero'"))
            
            
            ->joinleft(array('seat'=>'sanctioned_seat'),"seat.core_course = course.core_course1",array("max_seat"))
           
            ->where("seat.generic_elective=?",0)
            ->where("course.course!=?",'')
            ->where("course.$field_name=?", $id);
            //echo $select;
            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    public function getRecordByindividualGenericElective($id,$ge_id,$field_name = "ge1"){       
        $select=$this->_db->select()
            ->from('applicant_paymode_details')   
             ->joinleft(array('course'=>'applicant_course_details'),"course.application_no = applicant_paymode_details.application_no",array("count(distinct(course.form_id)) as total") )
                ->joinLeft(array('payment'=>'applicant_payement_details'),"payment.form_id = course.form_id",array())
            ->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = payment.id",array())
            
            ->where("payment.payment_status =?", 1)
            ->where("course.course!=?",'')
            ->where("course.core_course1 IN(?)", explode(',',$id))
            ->where("course.$field_name=?",$ge_id )
             ->where("record.f_code like ?", 'ok');
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //End
    
}

?>
