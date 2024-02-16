<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for Monthly Attendance.
 Date: 25 Sept. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_MonthlyAttendanceLineModel extends Zend_Db_Table_Abstract {

    public $_name = 'attendance_info_master';
    protected $_id = 'id'; 
    //This function gets all promotion rule data
    public function getRecords(){       
        $select=$this->_db->select()
            ->from($this->_name)
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=$this->_name.cmn_terms",array("term_name"))
            ->joinleft(array("department"),"department.id=$this->_name.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
            ->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=$this->_name.cc_id",array("cc_name"))
            
            
            ->join(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('CONCAT(students.stu_fname," ",students.stu_lname) AS studentName'))
             ->where("$this->_name.attend_status !=?",1)
             ->where("$this->_name.status !=?",2);
            $result=$this->getAdapter()
            ->fetchAll($select);  
            //echo"<pre>";print_r($result);die;
        return $result;
    } 
    public function getRecordById($id)
    {    
        //echo"<pre>";print_r($id);die;
        $select=$this->_db->select()
            ->from($this->_name)
            ->join(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('CONCAT(students.stu_fname," ",students.stu_lname) AS students','stu_id','exam_roll','reg_no','roll_no'))
            ->where("$this->_name.id=?", $id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//  echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //to get class conducted count 
    public function   get_conducted_class_data($session='',$course_id='',$term_id='',$coreCourse='',$hons_id=''){
        //echo "<pre>";print_r($coreCourse);exit;
        $cc_id_InArray = ["1", "7", "11","16","17","18"];
        $result='';
        $select=$this->_db->select();
            $select->from(array($this->_name),
                   array('conducted_class'=>'SUM(conducted_class)'));
            if(in_array($coreCourse, $cc_id_InArray)){
               $select->where("$this->_name.cc_id=?", $coreCourse);
               $select->where("$this->_name.department=?", $hons_id);
               $select->group('u_id');
                $select->limit(1);
                //echo $select;die;
                $result=$this->getAdapter()
                ->fetchRow($select);    
                //echo "<pre>"; print_r($result);die;
                 return $result;
            }else{
                $data = 1;
                //return $data;
                echo '';
            }
            if(!empty($course_id)){
                $select->where("$this->_name.cmn_terms=?", $term_id);		   
                $select->where("$this->_name.session=?", $session);			   
                $select->where("$this->_name.course_id=?", $course_id);
                
                $select->group('u_id');
                $select->limit(1);
                //echo $select;die;
                $result=$this->getAdapter()
                ->fetchRow($select);    
                //echo "<pre>"; print_r($result);die;
                return $result;
            }
            		   
           
    }
    //End
     public function getUniqueRecords($course_id='',$hons_id=''){       
        $select=$this->_db->select();
             //->distinct()
           $select->from(array($this->_name),
               array('u_id'=>'DISTINCT(u_id) as u_id'));
            $select ->where("$this->_name.attend_status !=?",1);
            if(!empty($course_id)){
                $select->where("$this->_name.course_id =?",$course_id);
            }
            if(!empty($hons_id)){
                $select->where("$this->_name.department =?",$hons_id);
            }
            
            $select->where("$this->_name.status !=?",2);
            //echo $select; die;
            $result=$this->getAdapter()
            ->fetchAll($select);  
            //echo"<pre>";print_r($result);die;
        return $result;
    } 
    //To get attended class data
    public function get_attended_class_count($values,$course_id='',$coreCourse='',$hons_id=''){
        //echo "<pre>";print_r($hons_id);exit;
         $cc_id_InArray = ["1", "7", "11","16","17","18"];
         $select=$this->_db->select();
            
            $select->from(array($this->_name),
                    array('attended_class'=>'SUM(attended_class)','attend_remarks','attend_status','percent'));

            $select->joinLeft(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('CONCAT(students.stu_fname," ",students.stu_lname) AS studentName','CONCAT(students.father_fname," ",students.father_lname) AS fatherName','stu_id'));
            
            if(in_array($coreCourse, $cc_id_InArray)){
               $select->where("$this->_name.cc_id=?", $coreCourse);
               $select->where("$this->_name.department=?", $hons_id);
               $select->where("$this->_name.u_id=?", $values);
            }else{
               //return $result = '';die;
            }
            if(!empty($course_id)){
           
                $select->where("$this->_name.u_id=?", $values);			   
                $select->where("$this->_name.course_id=?", $course_id);
            }
            
            $select1 = $this->_db->select();
            $select1->from($select);
            $select1->where("stu_id is not null");
            $result=$this->getAdapter()
            ->fetchRow($select1);    
            
            //echo '<pre>';print_r($result);
            	 
        return $result;     
    }
    public function get_existed_attendance_data($term_id,$course_id='',$effective_date,$hons_id=''){
        //echo '<pre>'; print_r($effective_date);exit;
        $start_date = date_create($effective_date);
        $effective_month = date_format($start_date,"Y-m"); 
                                                    
        //echo $dateAr;
        $selectedDate = dateAr[2] . '-' . dateAr[1];
        $select = $this->_db->select();
            $select->from($this->_name);
            if(!empty($hons_id)){
                 $select->where('department = ?',$hons_id);
            }
            if(!empty($course_id)){
               $select->where('course_id = ?',$course_id); 
            }
            
            $select->where('cmn_terms = ?',$term_id);
            $select->where('DATE_FORMAT(effective_date,"%Y-%m") = ?',$effective_month);
        //echo $select;die;
        $result = $this->getAdapter()
           
            ->fetchRow($select);
            //echo $select;
           // echo"<pre>";print_r($result); exit;
        return $result;
    }
    
    
    
    
    
    public function getAttendanceOnMonthRange($academic,array $term_arr, $from, $to){
            $from_arr = explode('-',$from);
            $from = $from_arr[1].'-'.$from_arr[0];
            $to_arr = explode('-',$to);
            $to = $to_arr[1].'-'.$to_arr[0];
            
             $select = $this->_db->select();
            $select->from($this->_name);
            $select->where('batch = ?',$academic);
            $select->where('cmn_terms in (?)',$term_arr);
            $select->where('DATE_FORMAT(effective_date,"%Y-%m") >= ?',$from);
            $select->where('DATE_FORMAT(effective_date,"%Y-%m") <= ?',$to);
            $select->order(array('cmn_terms','u_id','effective_date'));
            $result = $this->getAdapter()
            ->fetchAll($select);
         return $result;
    }
    public function distinctDate($academic,array $term_arr, $from, $to){
            $from_arr = explode('-',$from);
            $from = $from_arr[1].'-'.$from_arr[0];
            $to_arr = explode('-',$to);
            $to = $to_arr[1].'-'.$to_arr[0];
            $select = $this->_db->select();
            $select->from($this->_name,array('DISTINCT(Date_format(`effective_date`,"%m")) as month','theory','practical'));
            $select->where('batch = ?',$academic);
            $select->where('cmn_terms in (?)',$term_arr);
            $select->where('DATE_FORMAT(effective_date,"%Y-%m") >= ?',$from);
            $select->where('DATE_FORMAT(effective_date,"%Y-%m") <= ?',$to);
            $result = $this->getAdapter()
            ->fetchAll($select);
         return $result;
    }
    public function getAttendanceOnMonthRange1($academic,array $term_arr, $from, $to){
            $from_arr = explode('-',$from);
            $from = $from_arr[1].'-'.$from_arr[0];
            $to_arr = explode('-',$to);
            $to = $to_arr[1].'-'.$to_arr[0];
            
             $select = $this->_db->select();
            $select->from($this->_name,array('u_id'));
            $select->where('batch = ?',$academic);
            $select->where('cmn_terms in (?)',$term_arr);
            $select->where('DATE_FORMAT(effective_date,"%Y-%m") >= ?',$from);
            $select->where('DATE_FORMAT(effective_date,"%Y-%m") <= ?',$to);
            $select->group(array('u_id'));
            $result = $this->getAdapter()
            ->fetchAll($select);
         return $result;
    }
    
    
    //Filter record by Course Group
    public function getRecordsByCourseGroup($session_id='',$course_group='',$degree_id='',$search_date='',$department=''){
        
        $start_date = date_create($search_date);
        
        $effective_month = date_format($start_date,"Y-m"); 
        //echo '<pre>'; print_r($effective_month); exit;
        $select=$this->_db->select();
            $select->from($this->_name);
            $select->joinleft(array("term"=>"declared_terms"),"term.term_des=$this->_name.cmn_terms",array("term_name"));
            $select->joinleft(array("department"),"department.id=$this->_name.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"));
            $select->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=$this->_name.cc_id",array("cc_name"));
            
            
            $select->join(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('CONCAT(students.stu_fname," ",students.stu_lname) AS studentName','roll_no'));
            $select->where("$this->_name.session =?",$session_id);
            if(!empty($course_group)){
                $select->where("$this->_name.course_id =?",$course_group);
            }
            if(!empty($department)){
                $select->where("$this->_name.department =?",$department);
            }
            $select->where("$this->_name.degree_id =?",$degree_id);
            $select->where('DATE_FORMAT(attendance_info.effective_date,"%Y-%m") = ?',$effective_month);
            $select->where("$this->_name.status !=?",2);
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);
        return $result;
    }
}
?>
