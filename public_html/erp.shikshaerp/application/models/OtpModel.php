<?php

class Application_Model_OtpModel extends Zend_Db_Table_Abstract {

    public $_name = 'otp_manage';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    public function getVerifyotp($id,$otp,$sem=NULL) {
        
        if(strlen($sem)>1){
            $term = explode('t',$sem);
            $sem = $term[1];
        }
       
        $select = $this->_db->select()
                 ->from('semester_fee_collection',array("min(due_amount) as dueFee"))
                //->joinLeft(array("sem"=>"semester_wise_attendance_report"),"sem.u_id=$this->_name.u_id")
               // ->join(array("sem"=>"semester_fee_collection"),"sem.stu_id =$this->_name.u_id",array("min(due_amount) as dueFee"))
                ->join(array("student" => "erp_student_information"),"student.stu_id = semester_fee_collection.stu_id",array("academic_id"))
                ->where("semester_fee_collection.stu_id=?", $id)
                // ->where("sem.cmn_terms=?", $sem)
                // ->where("sem.status=?", 0)
                ->where("semester_fee_collection.semester like '%?%'", (int)$sem)
                ->where("semester_fee_collection.f_code like 'ok' OR semester_fee_collection.f_code like 'E000'");
              //  ->where("sem.f_code like ?", $f);
                //->where("$this->_name.otp=?", $otp);
  //echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    public function getVerifyotp1($id,$otp) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.u_id=?", $id)
                 ->where("md5($this->_name.otp)=?", $otp);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
    public function getVerifyotpnon($id,$otp) {
        $select = $this->_db->select()
                ->from($this->_name)
                 ->join(array("student" => "erp_student_information"),"student.stu_id = $this->_name.u_id",array("academic_id"))
                ->where("$this->_name.u_id=?", $id);
                 //->where("$this->_name.otp=?", $otp);
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
    
    public function getRecordsbyotp() {

        $select = $this->_db->select()
                ->from($this->_name)
                  ->joinLeft(array("stu"=>"erp_student_information"),"stu.stu_id=$this->_name.u_id");
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    
    
    public function getRecordsbyuid($id) {

        $select = $this->_db->select()
                ->from($this->_name)
          ->where("$this->_name.u_id=?", $id);
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    
    //Added By Kedar 16 Nov 2019 for otpModel
  
    public function getRecordById($u_id='') {

       $select=$this->_db->select()
            ->from($this->_name)
            
            ->joinleft(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('students.exam_roll,students.roll_no, CONCAT(students.stu_fname," ",students.stu_lname) AS studentName,concat(students.father_fname," ",students.father_lname) As FatherName'))
            ->where("$this->_name.u_id =?",$u_id);
          
        //echo $select;die;
            $result=$this->getAdapter()
            ->fetchRow($select);
            //echo"<pre>";print_r($result);die;
        return $result;
    }
   //End 
  
    
}
