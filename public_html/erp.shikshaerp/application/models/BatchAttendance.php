<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for Promotion rule.
 Date: 25 Sept. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_BatchAttendance extends Zend_Db_Table_Abstract {

    public $_name = 'attendance_info';
    protected $_id = 'id'; 
    //This function gets all promotion rule data
    public function getRecords(){       
        $select=$this->_db->select()
            ->from("attendance_info_master", array('effective_date','id'))
            
            ->joinleft(array("ai"=>"attendance_info"),"ai.attendance_master_id=attendance_info_master.id",array("Group_Concat(distinct(ai.batch)) as batch"))
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=attendance_info_master.cmn_terms",array("term_name"))
            ->joinleft(array("department"),"department.id=attendance_info_master.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=attendance_info_master.course_id",array("course_name"))
            ->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=attendance_info_master.cc_id",array("cc_name"))
             ->where("attendance_info_master.status !=?",2)
            ->order("attendance_info_master.id desc")
             ->group(array('ai.attendance_master_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);
            //echo"<pre>";print_r($result);die;
        return $result;
    } 
    public function getRecordsByEmplId($empl_id){       
        $select=$this->_db->select()
            ->from("attendance_info_master", array('effective_date','id'))
            
            ->joinleft(array("ai"=>"attendance_info"),"ai.attendance_master_id=attendance_info_master.id",array("Group_Concat(distinct(ai.batch)) as batch"))
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=attendance_info_master.cmn_terms",array("term_name"))
            ->joinleft(array("department"),"department.id=attendance_info_master.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=attendance_info_master.course_id",array("course_name"))
            ->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=attendance_info_master.cc_id",array("cc_name"))
             ->where("attendance_info_master.employee_id =?",$empl_id)
             ->where("attendance_info_master.status !=?",2)
            ->order("attendance_info_master.id desc")
             ->group(array('ai.attendance_master_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);
        //echo $select; die;
            //echo"<pre>";print_r($result);die;
        return $result;
    } 
    
        public function getRecordsByCourse($session,$cmn_terms,$course_id,$cc_id,$stu_id){       
        $select=$this->_db->select()
            ->from("attendance_info_master",array("sum(conducted_class) as total_class"))
            
            ->join(array("ai"=>"attendance_info"),"ai.attendance_master_id=attendance_info_master.id",array("sum(attended_class) as total_attended"))
           
             ->where("attendance_info_master.session =?",$session)
               ->where("attendance_info_master.cmn_terms =?",$cmn_terms)
                 ->where("attendance_info_master.cc_id =?",$cc_id)
                   ->where("attendance_info_master.course_id =?",$course_id)
                   ->where("ai.u_id =?",$stu_id)
             ->where("attendance_info_master.status !=?",2)
            ->order("attendance_info_master.id desc")
             ->group(array('ai.u_id'));
          // echo $select; die;
            $result=$this->getAdapter()
            ->fetchRow($select);
        //echo $select; die;
            //echo"<pre>";print_r($result);die;
        return $result;
    } 
    
        public function getRecordsBydepartment($session,$cmn_terms,$department,$cc_id,$stu_id){       
        $select=$this->_db->select()
            ->from("attendance_info_master", array("sum(conducted_class) as total_class"))
            
            ->join(array("ai"=>"attendance_info"),"ai.attendance_master_id=attendance_info_master.id",array("sum(attended_class) as total_attended"))
            
             ->where("attendance_info_master.session =?",$session)
               ->where("attendance_info_master.cmn_terms =?",$cmn_terms)
                 ->where("attendance_info_master.cc_id =?",$cc_id)
                   ->where("attendance_info_master.department =?",$department)
                   ->where("ai.u_id =?",$stu_id)
             ->where("attendance_info_master.status !=?",2)
            ->order("attendance_info_master.id desc")
             ->group(array('ai.u_id'));
             //  echo $select; die;
            $result=$this->getAdapter()
            ->fetchRow($select);
        //echo $select; die;
            //echo"<pre>";print_r($result);die;
        return $result;
    } 
    
    public function getRecordById($id)
    {    
        //echo"<pre>";print_r($id);die;
        $select=$this->_db->select()
            ->from($this->_name)
            ->join(array("am"=>"attendance_info_master"),"am.id=$this->_name.attendance_master_id",array('am.academic_year','am.session','am.cmn_terms','am.cc_id','am.ge_id','am.department','am.course_id','am.degree_id','am.department_id','am.employee_id','am.theory','am.practical','am.conducted_class','am.effective_date'))
            
            ->join(array("students"=>"erp_student_information"),"students.stu_id=$this->_name.u_id",array('students.stu_fname AS students','stu_id','exam_roll','reg_no','roll_no'))
            ->where("md5($this->_name.attendance_master_id)=?", $id);			   
          //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);    
           //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //to get class conducted count 
    public function   get_conducted_class_data($session='',$course_id='',$term_id='',$coreCourse='',$hons_id=''){
        //echo "<pre>";print_r($coreCourse);exit;
        //$cc_id_InArray = ["1", "7", "11","16","17","18"];
        $result='';
        $select=$this->_db->select();
            $select->from(array('attendance_info_master'),
                array('conducted_class'=>'SUM(conducted_class)'));

                if(!empty($hons_id)){
                    $select->where("attendance_info_master.session=?", $session);
                    $select->where("attendance_info_master.cc_id=?", $coreCourse);
                    $select->where("attendance_info_master.cmn_terms=?", $term_id);
                    $select->where("attendance_info_master.department=?", $hons_id);
                    
                    //$select->group('batch');
                    $select->limit(1);
                     //echo $select;die;
                    $result=$this->getAdapter()
                    ->fetchRow($select);    
                    //echo "<pre>"; print_r($result);die;
                    return $result;
                }
                
         
            
            if(!empty($course_id)){
                
                $select->where("attendance_info_master.session=?", $session);
                $select->where("attendance_info_master.cmn_terms=?", $term_id);		   
                	
                $select->where("attendance_info_master.cc_id=?", $coreCourse);
                $select->where("attendance_info_master.course_id=?", $course_id);
                
                //$select->group('batch');
                $select->limit(1);
                //echo $select;die;
                $result=$this->getAdapter()
                ->fetchRow($select);    
                //echo "<pre>"; print_r($result);die;
                return $result;
            }
            		   
           
    }
    //End
    public function getMasterIds($course_id='',$hons_id='',$term='',$coreCourse=''){  
         
        $select1= $this->_db->select();
        $select1->from('attendance_info_master',array('id'));
           
        
            if(!empty($course_id)){
                $select1->where("attendance_info_master.course_id =?",$course_id);
                $select1->where("attendance_info_master.cmn_terms =?",$term);
				$select1->where("attendance_info_master.cc_id =?",$coreCourse);
            }
            if(!empty($hons_id)){
                $select1->where("attendance_info_master.department =?",$hons_id);
                $select1->where("attendance_info_master.cmn_terms =?",$term);
				$select1->where("attendance_info_master.cc_id =?",$coreCourse);
            }
          //  echo $select1; die;
            $result1=$this->getAdapter()
            ->fetchAll($select1); 
           //echo $select1;exit;
         return $result1;
    }  

    public function getUniqueStudents($masterIds) {       
        $select=$this->_db->select();
            $select->from(array($this->_name),
            array('u_id'=>'DISTINCT(u_id) as u_id'));
            //$select ->where("$this->_name.attend_status !=?",1);
            if(!empty($masterIds)){
                $select->where('attendance_master_id IN (?)', $masterIds);
            }    //To get attended class data

            $result=$this->getAdapter()
            ->fetchAll($select);  
        return $result;
    } 
    //To get attended class data
    public function get_attended_class_count($values,$course_id='',$coreCourse='',$hons_id='',$masterIds=''){
           
             //$master_id = $this->mergData($masterIds, count($masterIds));
        $master_id = $masterIds;
       // echo $master_id; die;
        $parts = explode(',', $master_id);

        $master_id = implode("''", $parts);
            
              
            $select=$this->_db->select();
            
            $select->from(array('attendance_info'),
                    array('attended_class'=>'SUM(attended_class)','attend_remarks'));

           $select->joinLeft(array("students"=>"erp_student_information"),"students.stu_id=attendance_info.u_id",array('stu_fname as studentName','father_fname as fatherName','stu_id','stu_status'));
              $select->joinLeft(array("attn_master"=>"attendance_info_master"),"attn_master.id=$this->_name.attendance_master_id",array("case when sum(attn_master.practical) > 0 then ((sum(attendance_info.component_2) / sum(attn_master.theory) * 100) + (sum(attendance_info.component_3) / sum(attn_master.practical) * 100)) / 2  ELSE sum(attendance_info.component_2) / sum(attn_master.theory) * 100  END as overall_percent"));
          
               $select->where("attendance_info.u_id=?", $values);
               $select->where("attendance_info.attendance_master_id in(?)",array_filter(explode("'",$master_id)));
                //echo $select ; die;
            $select1 = $this->_db->select();
            $select1->from($select);
            $select1->where("stu_id is not null");
            $result=$this->getAdapter()
            ->fetchRow($select1);  
            //echo '<pre>';print_r($result);
            	 
        return $result;     
    }
    public function get_existed_attendance_data($term_id,$course_id='',$effective_date,$hons_id='',$ge_id=''){
    
        $start_date = date_create($effective_date);
        $effective_month = date_format($start_date,"Y-m"); 
                                                    
       
        $selectedDate = dateAr[2] . '-' . dateAr[1];
        $select = $this->_db->select();
            $select->from('attendance_info_master');
            if(!empty($ge_id)){
               $select->where('department = ?',$hons_id); 
               $select->where('ge_id= ?',$ge_id); 
            }else {
               $select->where('department = ?',$hons_id);
                $select->where('ge_id = ?',0); 
            }
            
            if(!empty($course_id)){
               $select->where('course_id = ?',$course_id); 
            }
            
            $select->where('cmn_terms = ?',$term_id);
            $select->where('DATE_FORMAT(effective_date,"%Y-%m") = ?',$effective_month);
        //echo $select;die;
        $result = $this->getAdapter()
           
            ->fetchRow($select);
           // echo $select;die;
            // echo"<pre>";print_r($result); exit;
        return $result;
    }
    
    
    
    
    
    public function getAttendanceOnMonthRange($ids){
            
             $select = $this->_db->select();
            $select->from($this->_name);
              $select->join(array('attendance'=>'attendance_info_master'),"attendance.id = $this->_name.attendance_master_id",array('effective_date'));
              $select->where('attendance_master_id in(?)',$ids);
             $select->order('attendance_info.u_id ASC');
             $select->order("$this->_name.batch");
            $result = $this->getAdapter()
             
            ->fetchAll($select);
            // echo $select; die;
         return $result;
    }
    public function getAttendanceOnMonthRange2($ids){
            
             $select = $this->_db->select();
            $select->from($this->_name,array('u_id'));
            $select->where('attendance_master_id in(?)',$ids);
            $select->join(array('info'=>'erp_student_information'),"info.stu_id = $this->_name.u_id",array(''));
            
            $select->order("$this->_name.batch");
            $select->order('info.roll_no');
            $select->group(array("u_id"));
            $result = $this->getAdapter()
            ->fetchAll($select);
           // echo $select; die;
         return $result;
    }
    public function distinctDate($department='',$course_id='',$ge_id='',$cc_id='',$term_id, $from, $to){
            $from_arr = explode('-',$from);
            $from = $from_arr[1].'-'.$from_arr[0];
            $to_arr = explode('-',$to);
            $to = $to_arr[1].'-'.$to_arr[0];
            $select = $this->_db->select();
            $select->from('attendance_info_master',array('DISTINCT(Date_format(`effective_date`,"%m")) as month','theory','practical'));
            $select->join(array("info"=>"attendance_info"),"info.attendance_master_id=attendance_info_master.id",array("batch"));
            if(!empty($ge_id)){
               $select->where('attendance_info_master.department = ?',$department); 
               $select->where('attendance_info_master.cc_id = ?',$cc_id); 
               $select->where('attendance_info_master.ge_id = ?',$ge_id); 
            }else {
                $select->where('attendance_info_master.department = ?',$department); 
                $select->where('attendance_info_master.cc_id = ?',$cc_id); 
                $select->where('attendance_info_master.ge_id = ?',0); 
            }
            if(!empty($course_id)){
               $select->where('attendance_info_master.course_id = ?',$course_id); 
            }
            $select->where('attendance_info_master.cmn_terms in (?)',$term_id);
            $select->where('DATE_FORMAT(attendance_info_master.effective_date,"%Y-%m") >= ?',$from);
            $select->where('DATE_FORMAT(attendance_info_master.effective_date,"%Y-%m") <= ?',$to);
            $select->group('attendance_info_master.effective_date');
           
            $result = $this->getAdapter()
            ->fetchAll($select);
           // echo $select; die;
         return $result;
    }
    public function getAttendanceOnMonthRange1($department='',$course_id='',$ge_id='',$cc_id='',$term_id, $from, $to){
            //echo $department;exit;
            $from_arr = explode('-',$from);
            $from = $from_arr[1].'-'.$from_arr[0];
            $to_arr = explode('-',$to);
            $to = $to_arr[1].'-'.$to_arr[0];
            
            $select = $this->_db->select();
            $select->from('attendance_info_master',array('id'));
            $select->join(array("info"=>"attendance_info"),"info.attendance_master_id=attendance_info_master.id");
            if(!empty($ge_id)){
               $select->where('attendance_info_master.department = ?',$department); 
               $select->where('attendance_info_master.cc_id = ?',$cc_id);
               $select->where('attendance_info_master.ge_id = ?',$ge_id); 
            }else {
                $select->where('attendance_info_master.department = ?',$department); 
                $select->where('attendance_info_master.cc_id = ?',$cc_id); 
                $select->where('attendance_info_master.ge_id = ?',0); 
            }
            if(!empty($course_id)){
               $select->where('attendance_info_master.course_id = ?',$course_id); 
            }
            
            $select->where('attendance_info_master.cmn_terms in (?)',$term_id);
            
            $select->where('DATE_FORMAT(attendance_info_master.effective_date,"%Y-%m") >= ?',$from);
            $select->where('DATE_FORMAT(attendance_info_master.effective_date,"%Y-%m") <= ?',$to);
            $select->group("attendance_info_master.id");
            $result = $this->getAdapter()
               
            ->fetchAll($select);
            //echo $select; die;  
         return $result;
    }
    
    
    //Filter record by Course Group
    public function getRecordsByCourseGroup($session_id='',$course_group='',$degree_id='',$search_date='',$department='',$cmn_terms=''){
        
        $start_date = date_create($search_date);
        
        $effective_month = date_format($start_date,"Y-m"); 
        
        
        //echo '<pre>'; print_r($result1); exit;
        $select=$this->_db->select();
            $select->from('attendance_info_master');
            
             
             
            $select->joinleft(array("term"=>"declared_terms"),"term.term_des=attendance_info_master.cmn_terms",array("term_name"));
            $select->joinleft(array("department"),"department.id=attendance_info_master.department",array("department"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=attendance_info_master.course_id",array("course_name"));
            $select->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=attendance_info_master.cc_id",array("cc_name"));
            
            
           
            $select->where("attendance_info_master.session =?",$session_id);
            if(!empty($course_group)){
                $select->where("attendance_info_master.course_id =?",$course_group);
            }
            if(!empty($department)){
                $select->where("attendance_info_master.department =?",$department);
            }
            if(!empty($cmn_terms)){
                $select->where("attendance_info_master.cmn_terms =?",$cmn_terms);
            }
            $select->where("attendance_info_master.degree_id =?",$degree_id);
           
            
            $select->where('DATE_FORMAT(attendance_info_master.effective_date,"%Y-%m") = ?',$effective_month);
            $select->where("attendance_info_master.status !=?",2);
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);
        return $result;
    }
    //Month wise
	public function getMonthlyBatchRecords($session,$term_id,$degree_id,$effective_date,$cc_id,$ge_id='',$hons_id='',$course_id='',$empl_id){
		
		//$start_date = date_create($effective_date);
        $effective_month = $effective_date;
		
		 
		if(!empty($hons_id)){
			$select=$this->_db->select();
	
			$select->from("attendance_info_master", array('effective_date','id'));
			$select->joinleft(array("ai"=>"attendance_info"),"ai.attendance_master_id=attendance_info_master.id",array("Group_Concat(distinct(ai.batch)) as batch"));
			$select->joinleft(array("term"=>"declared_terms"),"term.term_des=attendance_info_master.cmn_terms",array("term_name"));
			$select->joinleft(array("department"),"department.id=attendance_info_master.department",array("department"));
			$select->joinleft(array("course"=>"course_master"),"course.course_id=attendance_info_master.course_id",array("course_name"));
			$select->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=attendance_info_master.cc_id",array("cc_name"));
			$select->where("attendance_info_master.status !=?",2);
			$select->group(array('ai.attendance_master_id'));
			
			$select->where("attendance_info_master.session =?",$session);
			$select->where("attendance_info_master.cmn_terms =?",$term_id);
			$select->where("attendance_info_master.degree_id =?",$degree_id);  
			$select->where("attendance_info_master.department =?",$hons_id);
			$select->where("attendance_info_master.cc_id =?",$cc_id);
            $select->where("attendance_info_master.employee_id =?",$empl_id);
			$select->where("attendance_info_master.ge_id =?",0);
			$select->where('DATE_FORMAT(effective_date,"%Y-%m") = ?',$effective_month);
			//echo $select;die;
				$result=$this->getAdapter()
				->fetchAll($select);
				//echo"<pre>";print_r($result);die;
			return $result;
			
		}
		if(!empty($course_id)){
			$select=$this->_db->select();
	
			$select->from("attendance_info_master", array('effective_date','id'));
			$select->joinleft(array("ai"=>"attendance_info"),"ai.attendance_master_id=attendance_info_master.id",array("Group_Concat(distinct(ai.batch)) as batch"));
			$select->joinleft(array("term"=>"declared_terms"),"term.term_des=attendance_info_master.cmn_terms",array("term_name"));
			$select->joinleft(array("department"),"department.id=attendance_info_master.department",array("department"));
			$select->joinleft(array("course"=>"course_master"),"course.course_id=attendance_info_master.course_id",array("course_name"));
			$select->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=attendance_info_master.cc_id",array("cc_name"));
			$select->where("attendance_info_master.status !=?",2);
			$select->group(array('ai.attendance_master_id'));
			
			$select->where("attendance_info_master.session =?",$session);
			$select->where("attendance_info_master.cmn_terms =?",$term_id);
			$select->where("attendance_info_master.degree_id =?",$degree_id);  
			$select->where("attendance_info_master.course_id =?",$course_id);
			$select->where("attendance_info_master.cc_id =?",$cc_id);
			$select->where("attendance_info_master.ge_id =?",$ge_id);
			$select->where("attendance_info_master.employee_id =?",$empl_id);
			$select->where("attendance_info_master.department =?",0);
			
			$select->where('DATE_FORMAT(effective_date,"%Y-%m") = ?',$effective_month);
			//echo $select;die;
				$result=$this->getAdapter()
				->fetchAll($select);
				//echo"<pre>";print_r($result);die;
			return $result;
			
		}
		
	}
    
    //Daily wise
    public function getDailyBatchRecords($session,$term_id,$degree_id,$effective_date,$empl_id=''){

            $date = explode('-',$effective_date);
            $monthYear= $date[1]."-".$date[0];            
			$select=$this->_db->select();
	
			$select->from("daily_attendance_master", array('effective_date','id','period','employee_id'));
			$select->joinleft(array("ai"=>"daily_attendance_info"),"ai.master_id=daily_attendance_master.id",array("Group_Concat(distinct(ai.batch)) as batch"));
			$select->joinleft(array("term"=>"declared_terms"),"term.term_des=daily_attendance_master.cmn_terms",array("term_name"));
			$select->joinleft(array("department"),"department.id=daily_attendance_master.department",array("department"));
			$select->joinleft(array("course"=>"course_master"),"course.course_id=daily_attendance_master.course_id",array("course_name"));
			$select->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=daily_attendance_master.cc_id",array("cc_name"));
			$select->where("daily_attendance_master.status !=?",2);
			$select->group(array('ai.master_id'));
            
            if(strpos($empl_id, 'EMP-F') !== false){
                $select->where("daily_attendance_master.employee_id =?",$empl_id);
            } 
               
            
			$select->where("daily_attendance_master.session =?",$session);
			$select->where("daily_attendance_master.cmn_terms =?",$term_id);
			$select->where("daily_attendance_master.degree_id =?",$degree_id); 
			$select->where('DATE_FORMAT(daily_attendance_master.effective_date,"%Y-%m") = ?',$monthYear);
			//echo $select;die;
				$result=$this->getAdapter()
				->fetchAll($select);
				//echo"<pre>";print_r($result);die;
			return $result;
    }
    //calculate total monthly attendance on the basis of daily attendance
    
    public function checkMonthlyAttendance($session,$term_id,$degree_id,$effective_date,$cc_id,$ge_id,$hons_id,$course_id,$employee_id){
        $date = explode('-',$effective_date);
        $monthYear= $date[1]."-".$date[0];  
		//echo '<pre>'; print_r($effective_date); exit;
		 
		if(!empty($hons_id)){
			$select=$this->_db->select();
	
			$select->from('attendance_info_master', array('effective_date'));
            
			$select->where("attendance_info_master.session =?",$session);
			$select->where("attendance_info_master.cmn_terms =?",$term_id);
			$select->where("attendance_info_master.degree_id =?",$degree_id);  
			$select->where("attendance_info_master.department =?",$hons_id);
			$select->where("attendance_info_master.cc_id =?",$cc_id);
			$select->where("attendance_info_master.ge_id =?",0);
			$select->where('DATE_FORMAT(attendance_info_master.effective_date,"%Y-%m") = ?',$monthYear);
			//echo $select;die;
				$result=$this->getAdapter()
				->fetchRow($select);
				//echo"<pre>";print_r($result);die;
			return $result;
			
		}
		if(!empty($course_id)){
			$select=$this->_db->select();
	
			$select->from('attendance_info_master', array('effective_date'));
           
			
			
			$select->where("attendance_info_master.session =?",$session);
			$select->where("attendance_info_master.cmn_terms =?",$term_id);
			$select->where("attendance_info_master.degree_id =?",$degree_id);  
			$select->where("attendance_info_master.course_id =?",$course_id);
			$select->where("attendance_info_master.cc_id =?",$cc_id);
			$select->where("attendance_info_master.ge_id =?",$ge_id);
			$select->where("attendance_info_master.department =?",0);
            if($course_id == 14)
			$select->where("attendance_info_master.employee_id =?",$employee_id);
			$select->where('DATE_FORMAT(attendance_info_master.effective_date,"%Y-%m") = ?',$monthYear);
			//echo $select;die;
				$result=$this->getAdapter()
				->fetchRow($select);
				//echo"<pre>";print_r($result);die;
			return $result;
			
		}  
    
    }
    public function calculateMonthlyAttendance($session,$term_id,$degree_id,$effective_date,$cc_id,$ge_id,$hons_id,$course_id){
        $date = explode('-',$effective_date);
        $monthYear= $date[1]."-".$date[0];  
		//echo '<pre>'; print_r($monthYear); exit;
		 
		if(!empty($hons_id)){
			$select=$this->_db->select();
	
			$select->from('daily_attendance_master', array('department'));
            
            $select->join(array("ai"=>"daily_attendance_info"),"ai.master_id=daily_attendance_master.id",array("Group_Concat(distinct(ai.batch)) as batch",'f_id','count(attend_status) as  total_class','count(case attend_status when 1 then 1 else null end) as present_online', 'count(case attend_status when 2 then 1 else null end) as absent_online'));
			
			//$select->joinleft(array("term"=>"declared_terms"),"term.term_des=daily_attendance_master.cmn_terms",array("term_name"));
             $select->joinLeft(array("students"=>"erp_student_information"),"students.stu_id=ai.f_id",array('stu_fname as studentName','father_fname as fatherName','stu_id','roll_no'));
			$select->joinleft(array("department"),"department.id=daily_attendance_master.department",array("department"));
			//$select->joinleft(array("course"=>"course_master"),"course.course_id=daily_attendance_master.course_id",array("course_name"));
			//$select->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=daily_attendance_master.cc_id",array("cc_name"));
			$select->where("daily_attendance_master.status !=?",2);
			$select->group(array('ai.f_id'));
			
			$select->where("daily_attendance_master.session =?",$session);
			$select->where("daily_attendance_master.cmn_terms =?",$term_id);
			$select->where("daily_attendance_master.degree_id =?",$degree_id);  
			$select->where("daily_attendance_master.department =?",$hons_id);
            if(!empty($ge_id)){
                $select->where("daily_attendance_master.ge_id =?",$ge_id);
            }else{
                $select->where("daily_attendance_master.ge_id =?",0);
            }
			$select->where("daily_attendance_master.cc_id =?",$cc_id);
			
			$select->where('DATE_FORMAT(daily_attendance_master.effective_date,"%Y-%m") = ?',$monthYear);
			//echo $select;die;
				$result=$this->getAdapter()
				->fetchAll($select);
				//echo"<pre>";print_r($result);die;
			return $result;
			
		}
		if(!empty($course_id)){
			$select=$this->_db->select();
	
			$select->from('daily_attendance_master', array('department'));
            
            $select->join(array("ai"=>"daily_attendance_info"),"ai.master_id=daily_attendance_master.id",array("Group_Concat(distinct(ai.batch)) as batch",'f_id','count(attend_status) as  total_class','count(case attend_status when 1 then 1 else null end) as present', 'count(case attend_status when 2 then 1 else null end) as absent'));
			$select->joinLeft(array("students"=>"erp_student_information"),"students.stu_id=ai.f_id",array('stu_fname as studentName','father_fname as fatherName','roll_no'));
			//$select->joinleft(array("term"=>"declared_terms"),"term.term_des=daily_attendance_master.cmn_terms",array("term_name"));
			$select->joinleft(array("department"),"department.id=daily_attendance_master.department",array("department"));
			//$select->joinleft(array("course"=>"course_master"),"course.course_id=daily_attendance_master.course_id",array("course_name"));
			//$select->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=daily_attendance_master.cc_id",array("cc_name"));
			
			$select->group(array('ai.f_id'));
			
			$select->where("daily_attendance_master.session =?",$session);
			$select->where("daily_attendance_master.cmn_terms =?",$term_id);
			$select->where("daily_attendance_master.degree_id =?",$degree_id); 
            //if(!empty($course_id))
			$select->where("daily_attendance_master.course_id =?",$course_id);
			$select->where("daily_attendance_master.cc_id =?",$cc_id);
			$select->where("daily_attendance_master.ge_id =?",$ge_id);
			$select->where("daily_attendance_master.department =?",0);
			
			$select->where('DATE_FORMAT(daily_attendance_master.effective_date,"%Y-%m") = ?',$monthYear);
			//echo $select;die;
				$result=$this->getAdapter()
				->fetchAll($select);
				//echo"<pre>";print_r($result);die;
			return $result;
			
		}  
    }
}

?>
