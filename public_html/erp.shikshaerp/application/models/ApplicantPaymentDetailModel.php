<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for pwc entrance exam Payments Information.
 Date: 23 Dec. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_ApplicantPaymentDetailModel extends Zend_Db_Table_Abstract {

    public $_name = 'applicant_payement_details';
    protected $_id = 'id'; 
    //This function gets all promotion rule data
    
    public function getRecords(){       
        $select=$this->_db->select()
            ->from($this->_name,array('course',"count(distinct(record.form_id)) as total_applied",'sum(case when payment_status = 1 then 1 ELSE 0 END) as paid'))
            ->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = $this->_name.id",array())
            
                ->where("$this->_name.course!=?",'')
                ->group("$this->_name.course");
               // echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
       
    } 
    //Added By Kedar : 07 Oct 2020
    public function getRecordByYearId($yearId){
        $select=$this->_db->select()
            ->from($this->_name,array('course',"count(distinct(record.form_id)) as total_applied",'sum(case when payment_status = 1 then 1 ELSE 0 END) as paid'))
            ->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = $this->_name.id",array())
            
                ->where("$this->_name.course!=?",'')
                ->where("$this->_name.acad_year_id=?",$yearId)
                ->group("$this->_name.course");
              //echo $select;die;
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
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function getRecordByCouse($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name,array('max(roll_no) as roll_no1',"$this->_name.*",'max(payment_status) as payment_status1') )
            ->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = $this->_name.id",array())
            ->joinLeft(array('edu'=>'applicant_educational_details'),"edu.application_no = $this->_name.application_no",array('edu.*'))
            ->joinLeft(array('course'=>'applicant_course_details'),"course.application_no = $this->_name.application_no",array('course.degree_id','course.scrutiny_status'))
            ->joinLeft(array('personal'=>'applicant_personal_details'),"personal.application_no = $this->_name.application_no",array('personal.*'))
            ->where("$this->_name.course!=?",'')
            ->where("$this->_name.course=?", $id)
            ->where("$this->_name.payment_status=?", 1)
             ->where("record.f_code like ?", 'ok')
              ->group('form_id');
            
            $result=$this->getAdapter()
            ->fetchAll($select);    
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
            ->from(array($this->_name),
                array('roll_no as roll','applicant_name'))
           ->join(array("applicant_payment_record"),"applicant_payment_record.payment_id=$this->_name.id")
           ->join(array("applicant_course_details"),"applicant_course_details.application_no=$this->_name.application_no")
           ->join(array("entrance_exam_schedule"),"entrance_exam_schedule.department=applicant_course_details.course")
            ->where("md5($this->_name.application_no)=?", $conditions)
            ->where("applicant_payment_record.f_code=?", 'Ok');
        
            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo $select; die;
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
             //->order('id desc')
             //->limit(1);
            $result=$this->getAdapter()
            ->fetchAll($select);    
            
            //echo $select;die;
           // echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    
     public function payedApplicant($applicatonNumber){
        $select=$this->_db->select()
            ->from(array($this->_name),array('payment_status')) 
            ->where("$this->_name.payment_status=?", 1)
            ->where("$this->_name.application_no=?", $applicatonNumber);
             //->order('id desc')
             //->limit(1);
            $result=$this->getAdapter()
            ->fetchRow($select);    
            
            //echo $select;die;
           // echo"<pre>";print_r($result);exit;	  
        return $result;
    }
     public function check_for_status($applicatonNumber){
        $select=$this->_db->select()
            ->from(array($this->_name))   
                ->join(array("applicant_course_details"),"applicant_course_details.application_no=$this->_name.application_no")
                ->join(array("entrance_exam_schedule"),"entrance_exam_schedule.department=applicant_course_details.course")
            ->where("md5($this->_name.application_no)=?", $applicatonNumber);
            // ->order("$this->_name.id desc")
            // ->limit(1);
            $result=$this->getAdapter()
            ->fetchAll($select);    
            
            //echo $select;die;
           // echo"<pre>";print_r($result);exit;	  
        return $result;
    }
    //All course by payment
    public function getAllUgCourseCount($year_id=''){       
        $select=$this->_db->select();
            $select->from(array($this->_name),array('course','count(applicant_payement_details.course) as total_count'));  

            $select->joinleft(array("department_type"),"department_type.id=$this->_name.course",array("department_type","session_id"));
            
            $select->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = applicant_payement_details.id",array());
            
            
            $select->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = $this->_name.course",array("max_seat"));
            $select->where("applicant_payement_details.payment_status=?", 1);
            if(!empty($year_id)){
                $select->where("applicant_payement_details.acad_year_id=?", $year_id);
            }
            $select->where("seat.core_course=?",0);
            $select->where("seat.generic_elective=?",0);
            $select->where("record.f_code=?", Ok);
            $select->where("department_type.degree_id=?",1);
            $select->group(array('applicant_payement_details.course'));
            $select->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    //For Principal Interface Date:25 May 2020
    //All course by payment
    
    public function getdocumentVerifiedRecordByCouse($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name,array('max(roll_no) as roll_no1',"$this->_name.*",'max(payment_status) as payment_status1') )
            ->join(array('record'=>'applicant_payment_record'),"record.payment_id = $this->_name.id",array())
         
            ->join(array('documents'=>'applicant_documents_followup'),"documents.application_no = $this->_name.application_no")
            ->where("$this->_name.course!=?",'')
            ->where("documents.certificate_list!=?",'')
            ->where("$this->_name.course=?", $id)
            ->where("$this->_name.payment_status=?", 1)
            ->where("record.f_code like ?", 'Ok')
            ->group('record.form_id');
            
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function getapprovedRecordByCourse($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name,array('max(roll_no) as roll_no1',"$this->_name.*",'max(payment_status) as payment_status1') )
            ->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = $this->_name.id",array())
            
            ->joinLeft(array('documents'=>'applicant_documents_followup'),"documents.application_no = $this->_name.application_no")
            ->joinLeft(array('personals'=>'applicant_personal_details'),"personals.application_no = $this->_name.application_no",array('father_name','dob_date'))
            ->joinLeft(array('pay_mode'=>'applicant_paymode_details'),"pay_mode.application_no = $this->_name.application_no",array('pay_mode1'))
            ->where("$this->_name.course!=?",'')
            ->where("documents.principal_status =?",1)
            ->where("$this->_name.course=?", $id)
             ->where("record.f_code like ?", 'ok')
                ->group('record.form_id');
            
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function getSlipGeneratedRecordByCourse($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name,array('max(roll_no) as roll_no1',"$this->_name.*",'max(payment_status) as payment_status1') )
            ->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = $this->_name.id",array())
            
            ->joinLeft(array('documents'=>'applicant_documents_followup'),"documents.application_no = $this->_name.application_no")
            ->joinLeft(array('personals'=>'applicant_personal_details'),"personals.application_no = $this->_name.application_no",array('father_name','dob_date'))
            ->joinLeft(array('pay_mode'=>'applicant_paymode_details'),"pay_mode.application_no = $this->_name.application_no",array('pay_mode1','pay_mode2'))
            ->where("$this->_name.course!=?",'')
            ->where("$this->_name.payment_status=?", 1)
            ->where("documents.principal_status =?",1)
            ->where("documents.fee_slip =?",1)
            ->where("$this->_name.course=?", $id)
            ->where("record.f_code like ?", 'ok')
            ->group('record.form_id');
            
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //For paid applicants details after payment interface
    public function getPaidRecordByCourse($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name,array('max(roll_no) as roll_no1',"$this->_name.*",'max(payment_status) as payment_status1') )
            ->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = $this->_name.id",array())
            
            ->joinLeft(array('paymode'=>'applicant_paymode_details'),"paymode.application_no = $this->_name.application_no",array('class_roll','pay_mode2','amount1','unique_id1','date_time1'))
             ->joinLeft(array('edu'=>'applicant_educational_details'),"edu.application_no = $this->_name.application_no",array('edu.*'))
             ->joinLeft(array('course'=>'applicant_course_details'),"course.application_no = $this->_name.application_no",array('course.*'))
            ->joinLeft(array('personal'=>'applicant_personal_details'),"personal.application_no = $this->_name.application_no",array('personal.*'))
            ->where("$this->_name.course!=?",'')
            ->where("$this->_name.payment_status=?", 1)
            ->where("paymode.class_roll !=?",'')
           ->where("$this->_name.course=?", $id)
             ->where("record.f_code like ?", 'ok')
                ->group('record.form_id');
            
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //End
    public function getAllPgCourseCount($year_id=''){       
        $select=$this->_db->select();
            $select->from(array($this->_name),array('course','count(applicant_payement_details.course) as total_count'));   

            $select->joinleft(array("department_type"),"department_type.id=$this->_name.course",array("department_type","session_id"));
           
            $select->joinLeft(array('record'=>'applicant_payment_record'),"record.payment_id = applicant_payement_details.id",array());
            $select->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = $this->_name.course",array("max_seat"));
            
            $select->where("applicant_payement_details.payment_status=?", 1);
            if(!empty($year_id)){
                $select->where("applicant_payement_details.acad_year_id=?", $year_id);
            }
            $select->where("seat.core_course=?",0);
            $select->where("seat.generic_elective=?",0);
            $select->where("record.f_code=?", Ok);
            $select->where("department_type.degree_id !=?",1);
            $select->group(array('applicant_payement_details.course'));
            $select->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    //ENd 
    
}

?>
