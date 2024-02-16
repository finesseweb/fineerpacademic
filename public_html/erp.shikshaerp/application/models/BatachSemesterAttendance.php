<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for Promotion rule.
 Date: 14 Oct. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_BatachSemesterAttendance extends Zend_Db_Table_Abstract {

    public $_name = 'semester_wise_attendance_report';
    protected $_id = 'id'; 
    //This function gets all promotion rule data
    public function getRecords(){       
        $select=$this->_db->select()
            ->from($this->_name)
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=$this->_name.cmn_terms",array("term_name"))
            ->joinleft(array("department"),"department.id=$this->_name.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
            ->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=$this->_name.cc_id",array("cc_name"))               //->join(array("academic"=>"academic_master" ),"academic.session=$this->_name.session",array("short_code"))
            ->joinleft(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('CONCAT(students.stu_fname," ",students.stu_lname) AS studentName'))
            // ->where("$this->_name.attend_status =?",2)
            ->where("$this->_name.status !=?",2);
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);
            //echo"<pre>";print_r($select);die;
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
    public function getRecordByUid($id,$term)
    {       
        $select=$this->_db->select()
            ->from($this->_name)
            ->where("$this->_name.cmn_terms=?", $term)		   
            ->where("$this->_name.u_id=?", $id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
     public function getRecordforadmit($id,$term)
    {       
        $select=$this->_db->select()
            ->from($this->_name)
            ->where("$this->_name.cmn_terms=?", $term)		   
            ->where("$this->_name.u_id=?", $id)
            ->where("$this->_name.status=?", 0);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
     public function getformpreview($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name)
           // ->where("$this->_name.cmn_terms=?", $term)		   
            ->where("$this->_name.u_id=?", $id)
            ->where("$this->_name.status=?", 0);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    
    
    public function getTermIdByUid($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name,array('department','cmn_terms'))	
            ->joinLeft(array("core"=>"core_course_master"),"core.course_id=$this->_name.course_id",array('term_id'))
            ->where("$this->_name.u_id=?", $id);   
            $result=$this->getAdapter()
            ->fetchRow($select);    	  
        return $result;
    }
    
    
    public function getTermIdByFid($id,$aid)
    {       
        $select=$this->_db->select()
            ->from($this->_name,array())	
            ->join(array("term"=>"term_master"),"term.cmn_terms=$this->_name.cmn_terms",array('term_id as cmn_terms','term_name'))
            ->where("$this->_name.u_id=?", $id)
               
             ->where("$this->_name.status=?", 0)
            ->where("term.academic_year_id=?", $aid)
            ->group("$this->_name.cmn_terms");
            $result=$this->getAdapter()
            ->fetchAll($select);    
  	//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    
    public function checkRecordByUid($id,$sem='')
    {       
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("md5($this->_name.u_id)=?", $id)
          
            ->where("$this->_name.cmn_terms=?", $sem)
           // ->where("$this->_name.status=?", 0)
            ->where("$this->_name.attend_status=?", 0);
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo $select ;die;	  
        return $result;
    }
     public function checkStuAllowedByUid($id,$sem='')
    {       
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("md5($this->_name.u_id)=?", $id)
          
            ->where("$this->_name.cmn_terms=?", $sem)
            ->where("$this->_name.status=?", 0);
         //   ->where("$this->_name.attend_status=?", 0);
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    
    
    public function checkAttendByUid($id,$cid='')
    {       
        $select=$this->_db->select()
            ->from(array($this->_name),array('attend_status'))   
            ->where("$this->_name.u_id=?", $id)
            ->where("$this->_name.course_id=?", $cid);
            $result=$this->getAdapter()
            ->fetchAll($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    public function checkCoreRecordBycid($sid ,$id,$cc_id='')
    {       
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("$this->_name.u_id=?", $sid)
            ->where("$this->_name.course_id=?", $id)
            ->where("$this->_name.cc_id=?", $cc_id)
            ->where("$this->_name.status=?", 0);	
            $result=$this->getAdapter()
            ->fetchAll($select);    
            
         //   echo $select;
  		//echo"<pre>";print_r($result);
              //  die;	  
        return $result;
    }
    public function checkRecordBycid($sid ,$id)
    {       
        $select=$this->_db->select()
            ->from(array($this->_name))   
            ->where("$this->_name.u_id=?", $sid)
            ->where("$this->_name.course_id=?", $id) 
            ->where("$this->_name.status=?", 0);	
            $result=$this->getAdapter()
            ->fetchAll($select);    
            
            //echo $select;
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    
    //to get class conducted count 
    public function get_conducted_class_data($academic_id='',$course_id='',$term_id=''){
        $select=$this->_db->select(conducted_class)
            //->from($this->_name)
            ->from(array($this->_name),
                    array('conducted_class'=>'SUM(DISTINCT conducted_class)'))
            ->where("$this->_name.cmn_terms=?", $term_id)		   
            ->where("$this->_name.academic_id=?", $academic_id)			   
            ->where("$this->_name.course_id=?", $course_id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
  			 //echo"<pre>";print_r($result);die;  
        return $result;
    }
    //End
     //To get attended class data
    public function get_attended_class_count($values){
         $select=$this->_db->select()
            
            ->from(array($this->_name),
                    array('attended_class'=>'SUM(DISTINCT attended_class)','attend_remarks','attend_status','overall_percent'))

            ->joinLeft(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('CONCAT(students.stu_fname," ",students.stu_lname) AS studentName','CONCAT(students.father_fname," ",students.father_lname) AS fatherName','stu_id','stu_status'))	   	   
            ->where("$this->_name.id=?", $values);			   
             echo $select;exit;
            $result=$this->getAdapter()
            ->fetchRow($select);    
  			 
        return $result;
    }
    
    //Filter record by session
    public function getRecordsBySession($session_id='',$course_group='',$degree_id=''){
        $select=$this->_db->select()
            ->from($this->_name)
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=$this->_name.cmn_terms",array("term_name"))
            ->joinleft(array("department"),"department.id=$this->_name.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
            ->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=$this->_name.cc_id",array("cc_name"))  //->join(array("academic"=>"academic_master" ),"academic.session=$this->_name.session",array("short_code"))
            ->joinleft(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('CONCAT(students.stu_fname," ",students.stu_lname) AS studentName'))
            ->where("$this->_name.session =?",$session_id)
            ->where("$this->_name.ge_id =?",$course_group)
            ->where("$this->_name.degree_id =?",$degree_id)
            ->where("$this->_name.status !=?",2);
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);
            //echo"<pre>";print_r($result);die;
        return $result;
    }  
    //Get Semester End Student after filter : Date:26 March 2020
    public function getEndSemesterRecords($session,$term_id,$degree_id,$cc_id,$ge_id='',$hons_id='',$course_id=''){
        $select=$this->_db->select();
		$select->from($this->_name);
		$select->joinleft(array("term"=>"declared_terms"),"term.term_des=$this->_name.cmn_terms",array("term_name"));
		$select->joinleft(array("department"),"department.id=$this->_name.department",array("department"));
		$select->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"));
		$select->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=$this->_name.cc_id",array("cc_name")); 
		$select->joinleft(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('stu_status,CONCAT(students.stu_fname," ",students.stu_lname) AS studentName'));
		$select->where("$this->_name.session =?",$session);
		$select->where("$this->_name.cmn_terms =?",$term_id);
		$select->where("$this->_name.degree_id =?",$degree_id);
		
		if(!empty($hons_id)){
			$select->where("$this->_name.department =?",$hons_id);
			$select->where("$this->_name.cc_id =?",$cc_id);
			//$select->where("$this->_name.ge_id =?",0);
			
		}
		if(!empty($course_id)){
			$select->where("$this->_name.course_id =?",$course_id);
			$select->where("$this->_name.cc_id =?",$cc_id);
			
			$select->where("$this->_name.department =?",0);
			
		}
        if(!empty($ge_id)){
            $select->where("$this->_name.ge_id =?",$ge_id);
        }
        
		//echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);
            //echo"<pre>";print_r($result);die;
        return $result;
    } 
    public function isexist($session,$term,$cc_id,$department='',$course_id='',$ge_id=''){
        $select=$this->_db->select();
		$select->from($this->_name);
		$select->where("$this->_name.session =?",$session);
		$select->where("$this->_name.cmn_terms =?",$term);
		
		if(!empty($department)){
			$select->where("$this->_name.department =?",$department);
			$select->where("$this->_name.cc_id =?",$cc_id);
			
		}
		if(!empty($ge_id)){
			$select->where("$this->_name.ge_id =?",$ge_id);
			
		}
		if(!empty($course_id)){
			$select->where("$this->_name.course_id =?",$course_id);
		
			
		}
		//echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);
            //echo"<pre>";print_r($result);die;
        return $result;
    }
}

?>
