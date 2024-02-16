<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for Monthly Attendance.
 Date: 25 Sept. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_DailyAttendanceModel extends Zend_Db_Table_Abstract {

    public $_name = 'daily_attendance_master';
    protected $_id = 'id'; 
    //This function gets all promotion rule data
    public function getRecords(){ 
        //echo"<pre>";print_r(date('Y-m'));die;
        $select=$this->_db->select()
            ->from("daily_attendance_master", array('effective_date','id','employee_id','period'))
            
            ->joinleft(array("ai"=>"daily_attendance_info"),"ai.master_id=daily_attendance_master.id",array("Group_Concat(distinct(ai.batch)) as batch"))
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=daily_attendance_master.cmn_terms",array("term_name"))
            ->joinleft(array("department"),"department.id=daily_attendance_master.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=daily_attendance_master.course_id",array("course_name"))
            ->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=daily_attendance_master.cc_id",array("cc_name"))
            ->where('DATE_FORMAT(daily_attendance_master.submit_date,"%Y-%m") = ?',date('Y-m'))
             ->group(array('ai.master_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);
            //echo $select;die;
            //echo"<pre>";print_r($result);die;
        return $result;
    
    } 
    //To get record by employee Id:
    public function getRecordsByEmplId($empl_id){       
        $select=$this->_db->select()
            ->from("daily_attendance_master", array('effective_date','id','employee_id','period'))
            
            ->joinleft(array("ai"=>"daily_attendance_info"),"ai.master_id=daily_attendance_master.id",array("Group_Concat(distinct(ai.batch)) as batch"))
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=daily_attendance_master.cmn_terms",array("term_name"))
            ->joinleft(array("department"),"department.id=daily_attendance_master.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=daily_attendance_master.course_id",array("course_name"))
            ->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=daily_attendance_master.cc_id",array("cc_name"))
            ->where("daily_attendance_master.employee_id =?",$empl_id)
            ->where("daily_attendance_master.submit_date =?",date('Y-m-d'))
            ->order(array('daily_attendance_master.effective_date'))
             ->group(array('ai.master_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);
            //echo $select;die;
            //echo"<pre>";print_r($result);die;
        return $result;
    
    } 
    //To insert data in daily attendance info
    public function insertDailyAttendance($attendanceInfoData){
        
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->insert('daily_attendance_info',$attendanceInfoData);
       //echo $query;die;
        return $query; 
    }
    public function checkInsertData($masterId){
        $select=$this->_db->select()
            ->from('daily_attendance_info')
            
            ->where("daily_attendance_info.master_id=?", $masterId);			   
          //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);    
           //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function dumpMasterData($masterId){
        $this->_db->delete("daily_attendance_master", "id = $masterId");
    }
    public function getRecordById($id)
    {    
        //echo"<pre>";print_r($id);die;
        $select=$this->_db->select()
            ->from('daily_attendance_info')
            ->joinleft(array("am"=>"daily_attendance_master"),"am.id=daily_attendance_info.master_id",array('am.session','am.cmn_terms','am.cc_id','am.ge_id','am.department','am.period','am.teacher_dept','am.course_id','am.degree_id','am.department_id','am.employee_id','am.effective_date','am.academic_year'))
            
            ->join(array("students"=>"erp_student_information"),"students.stu_id=daily_attendance_info.f_id",array('CONCAT(students.stu_fname," ",students.stu_lname) AS students','stu_id','exam_roll','reg_no','roll_no'))
            ->where("md5(daily_attendance_info.master_id)=?", $id);			   
          //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);    
           //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    public function  updateDailyAttendInfo($data,$form_id,$update_id){
       // echo '<pre>'; print_r($form_id);exit;
        $where = array(
                'f_id = ?' => $form_id,
                "md5(master_id) =?" => $update_id
            );
        
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('daily_attendance_info',$data,$where);
        return $query; 
    }
    public function getEmplDeptById($empl_id){
        $select=$this->_db->select()
            ->from('empl_dept',array('empl_id'))
            ->join(array("department"),"department.id=empl_dept.dept_id",array('id','department'))
            ->where("empl_id=?", $empl_id);			   
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);    
           //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //to check existed daily attendance data
    public function get_existed_daily_attendance_data($term_id,$session,$effective_date,$degree_id,$period,$teacher_dept,$employee_id){
   
        $select = $this->_db->select();
        $select->from('daily_attendance_master');
            
            $select->where('degree_id = ?',$degree_id);
            $select->where('session = ?',$session);
            $select->where('cmn_terms = ?',$term_id);
            $select->where('period = ?',$period);
            $select->where('teacher_dept = ?',$teacher_dept);
            $select->where('effective_date = ?',$effective_date);
            //$select->where('employee_id = ?',$employee_id);
        $result = $this->getAdapter()
           
            ->fetchRow($select);
           //echo $select;die;
            // echo"<pre>";print_r($result); exit;
        return $result;
    }
    
    
    //To delete daily attendance 11 Aug 2020
    public function getDailyAttendance($delete_id){
       $select = $this->_db->select();
        $select->from('daily_attendance_master',array('id'));
            
        $select->where("md5(id) = ?",$delete_id);
        $result = $this->getAdapter()
           
            ->fetchRow($select);
        return $result;
        
    }
    public function deleteDailyAttendance($delete_id){
        
        $this->_db->delete("daily_attendance_master", "id = $delete_id");
         
        $this->_db->delete("daily_attendance_info", "master_id = $delete_id");
    }
   
}

?>