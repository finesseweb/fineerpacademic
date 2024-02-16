<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ElectiveSelection extends Zend_Db_Table_Abstract
{
    public $_name = 'erp_elective_selection';
    protected $_id = 'elective_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.elective_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	public function isExist($academic_id,$term_id,$student_id,$course_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('elective_id'))
                ->joinLeft(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array())
                      ->where("$this->_name.academic_year_id =?", $academic_id)				   
                      ->where("$this->_name.term_id =?", $term_id)				   
                      ->where("$this->_name.student_id =?", $student_id)				   
                      ->where("elective_items.electives =?", $course_id)				   
                      ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);   
       
        return $result['elective_id'];
    }
	
	public function isExist1($academic_id,$term_id,$student_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('elective_id'))
                      ->where("$this->_name.academic_year_id =?", $academic_id)				   
                      ->where("$this->_name.term_id =?", $term_id)				   
                      ->where("$this->_name.student_id =?", $student_id)				   
                      ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result['elective_id'];
    }
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name) 
                     ->join(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array())
                     ->join(array("course"=>"course_master"),"course.course_id=elective_items.electives",array("course_id","course_code"))
                     
                
					->join(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					->join(array("am"=>"academic_master"),"am.academic_year_id=$this->_name.academic_year_id",array("short_code"))
					  ->where("$this->_name.status !=?", 2)
                        ->group(array("term.cmn_terms","elective_items.electives"))
					  ->limit("10") 
					  ->order("$this->_name.$this->_id DESC");	 
//        echo $select;die;
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	public function getRecordsByAcademicId($batch_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name) 
                     ->join(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array())
                     ->join(array("course"=>"course_master"),"course.course_id=elective_items.electives",array("course_id","course_code"))
                     
                
					->join(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					->join(array("am"=>"academic_master"),"am.academic_year_id=$this->_name.academic_year_id",array("short_code"))
					  ->where("$this->_name.academic_year_id =?", $batch_id)
					  ->where("$this->_name.status !=?", 2)
                        ->group(array("term.cmn_terms","elective_items.electives"))
					  ->order("$this->_name.$this->_id DESC");	 
//        echo $select;die;
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }

	public function getacademicRecords($academic)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id=?", $academic)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	public function getElectives($academic_year_id)
	{
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array("GROUP_CONCAT(terms) as term_id","GROUP_CONCAT(elective_name) as elev_names","GROUP_CONCAT(students_id) as stu_ids","GROUP_CONCAT(electives) as tot_electives","terms","electives"))
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->group("elective_items.terms")
					->group("elective_items.electives")
					->where("$this->_name.status != 2");
					
					
				$result = $this->getAdapter()
						->fetchAll($select);
						
			return $result;			

	}
	
	//DashBoard
	
	
	public function getElectivesDashboard()
	{
		$select = $this->_db->select()
					->from($this->_name)
					//->joinLeft(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array("GROUP_CONCAT(terms) as term_id","GROUP_CONCAT(elective_name) as elev_names","GROUP_CONCAT(students_id) as stu_ids","GROUP_CONCAT(electives) as tot_electives","terms","electives"))
					->joinLeft(array("electives_items"=>"erp_elective_selection_items"),"electives_items.elective_id=$this->_name.elective_id",array("electives"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=electives_items.electives",array("course_name"))	
					->group("electives_items.electives")
					->where("$this->_name.status != 2");
				$result = $this->getAdapter()
						->fetchAll($select);
						
				//print_r($result);die;		
							
			return $result;			
					
					
		
		
	}
	public function getCountForElective($elective_id){
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_items"=>"erp_elective_selection_items"),"electives_items.elective_id=$this->_name.elective_id",array("count(electives) as elective_count","electives"))
					->where("electives_items.electives=?",$elective_id);
					//echo $select; die;
				$result = $this->getAdapter()
						->fetchRow($select);
						
				return $result;		
	}
	//DashBoard Ending
	
	public function getStudentsForElective($academic_year_id,$elective_id,$term_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("elective_selection_items"=>"erp_elective_selection_items"),"elective_selection_items.elective_id=$this->_name.elective_id",array("students_id as student_id","electives","terms"))
					->joinLeft(array("student_information"=>"erp_student_information"),"student_information.student_id=elective_selection_items.students_id",array("CONCAT(stu_fname,stu_lname) as students","stu_id"))
                                  ->where("$this->_name.status != 2")
				   ->where("$this->_name.academic_year_id=?",$academic_year_id)
				   ->where("elective_selection_items.electives=?",$elective_id)
				   ->where("elective_selection_items.terms=?",$term_id);
				 $result = $this->getAdapter()
					->fetchAll($select);
		return $result;						
	}
        
 public function getStudentsForElectiveByCourse($academic_year_id,$course,$term_id,$field_name = 'electives',$session='',$ge_id='',$join_attend = false,$pay=false,$tab_offset=true){
     //echo '<pre>'; print_r($tab_offset);exit;
     $term_model = new Application_Model_TermMaster();
     $term_id1 = $term_model->getRecord($term_id)['cmn_terms'];
             $currentTerm=$term_id1;
            if($term_id1 != 't1'){
            $term_id_arr = explode('t',$term_id1);
            $term_id1 = ((int)$term_id_arr[1])-1;
            $term_id1 = 't'.$term_id1;
            }

	$select = $this->_db->select();
		$select->from($this->_name);
		$select->joinLeft(array("elective_selection_items"=>"erp_elective_selection_items"),"elective_selection_items.elective_id=$this->_name.elective_id",array("students_id as student_id","electives as course_id","aecc"));
                $select->joinLeft(array("student_information"=>"erp_student_information"),"student_information.student_id=elective_selection_items.students_id",array("CONCAT(stu_fname,' ',stu_lname) as students","stu_id","CONCAT(father_fname,father_lname) as father_name","reg_no","exam_roll","roll_no","stu_status"));
                $select->joinLeft(array("academic_master"),"academic_master.academic_year_id=student_information.academic_id",array("short_code"));
                if($tab_offset == 1){
                    if($currentTerm !='t1'){
                        $select->join(array('tab_items'=>'tabulation_report_items'),"tab_items.student_id = $this->_name.student_id");
                        $select->join(array('tab_report'=>'tabulation_report'),"tab_report.tabl_id = tab_items.tabl_id");
                    }
                }
                $select->join(array('term'=>'term_master'),"term.academic_year_id = $this->_name.academic_year_id");
                   if($pay){
             $select->join(array("payment_ug"=>"exam_form_submission"),"payment_ug.student_id=$this->_name.student_id",array());
            }
                if($join_attend && $currentTerm !='t2'){
                $select->joinLeft(array("semester_wise_attendance_report")," semester_wise_attendance_report.u_id = student_information.stu_id",array("component_paper","attend_status"));
                //$select->where("student_information.stu_status != 3" );
                $select->where("semester_wise_attendance_report.ge_id = ?",$ge_id );
                $select->where("semester_wise_attendance_report.course_id = ?",$course );
                }
                 if($pay){
             $select->where("payment_ug.payment_status =?", 1);
            $select->where("payment_ug.term_id =?", $term_id);
             }
            if($tab_offset == 1){
                 if($currentTerm !='t1'){                                               
                    $select->where("term.cmn_terms =?", $term_id1);
                    $select->where("tab_items.final_remarks != ?", 'F');
                }
            }
		$select->where("$this->_name.status != 2");
        $select->where("student_information.stu_status = 1" );
		$select->where("$this->_name.academic_year_id=?",$academic_year_id);
		
		$select->where("elective_selection_items.$field_name=?",$course);
		$select->where("elective_selection_items.terms=?",$term_id);
		
             
        if(!empty($session)){
            $select->where("academic_master.session=?",$session);
        }
        	$select->group("students_id");
		$select->order("exam_roll");
       // echo $select;die;
		$result = $this->getAdapter()
		->fetchAll($select);
			//echo $select;die;		
		return $result;	
	}
        
        
public function getStudentsForElectiveByBackCourse($academic_year_id,$course,$term_id,$field_name = 'electives',$session='',$pay=false){	
	$select = $this->_db->select();
		$select->from($this->_name);
		$select->joinLeft(array("elective_selection_items"=>"back_selection_items"),"elective_selection_items.elective_id=$this->_name.elective_id",array("students_id as student_id","electives as course_id","aecc"));
                $select->joinLeft(array("student_information"=>"erp_student_information"),"student_information.student_id=elective_selection_items.students_id",array("CONCAT(stu_fname,' ',stu_lname) as students","stu_id","CONCAT(father_fname,father_lname) as father_name","reg_no","exam_roll"));
                $select->joinLeft(array("academic_master"),"academic_master.academic_year_id=student_information.academic_id",array("short_code"));
                $select->joinLeft(array("term_master"),"term_master.term_id=$this->_name.term_id and term_master.academic_year_id = academic_master.academic_year_id");
                 if($pay){
             $select->join(array("payment_ug"=>"ugnon_form_submission"),"payment_ug.student_id=$this->_name.student_id",array());
                 }
		$select->where("$this->_name.status != 2");
                $select->where("student_information.stu_status = 1" );
	
		$select->where("elective_selection_items.$field_name=?",$course);
		$select->where("term_master.cmn_terms=?",$term_id);
        if(!empty($session)){
            $select->where("academic_master.session=?",$session);
        }else{	$select->where("$this->_name.academic_year_id=?",$academic_year_id);}
        if($pay){
             $select->where("payment_ug.payment_status =?", 1);
            $select->where("payment_ug.term_id in (?)", explode('t',$term_id));
        }
		$select->order("exam_roll");
	//	echo $select;die;
		$result = $this->getAdapter()
		->fetchAll($select);	
            
            if(!count($result))
          return $this->getStudentsForElectiveByBackCoursePg($academic_year_id,$course,$term_id,$field_name,$session,$pay);
        
		return $result;	
	}
        
        function getStudentsForElectiveByBackCoursePg($academic_year_id,$course,$term_id,$field_name = 'electives',$session='',$pay=""){	
	$select = $this->_db->select();
		$select->from($this->_name);
		$select->joinLeft(array("elective_selection_items"=>"back_selection_items"),"elective_selection_items.elective_id=$this->_name.elective_id",array("students_id as student_id","electives as course_id","aecc"));
                $select->joinLeft(array("student_information"=>"erp_student_information"),"student_information.student_id=elective_selection_items.students_id",array("CONCAT(stu_fname,' ',stu_lname) as students","stu_id","CONCAT(father_fname,father_lname) as father_name","reg_no","exam_roll"));
                $select->joinLeft(array("academic_master"),"academic_master.academic_year_id=student_information.academic_id",array("short_code"));
                $select->joinLeft(array("term_master"),"term_master.term_id=$this->_name.term_id and term_master.academic_year_id = academic_master.academic_year_id");
                 if($pay){
             $select->join(array("payment_pg"=>"pg_non_form_data"),"payment_pg.student_id=$this->_name.student_id",array());
                 }
		$select->where("$this->_name.status != 2");
                $select->where("student_information.stu_status = 1" );
		$select->where("elective_selection_items.$field_name=?",$course);
		$select->where("term_master.cmn_terms=?",$term_id);
        if(!empty($session)){
            $select->where("academic_master.session=?",$session);
        }else{	$select->where("$this->_name.academic_year_id=?",$academic_year_id);}  
        if($pay){
             $select->where("payment_pg.payment_status =?", 1);
            $select->where("payment_pg.term_id in (?)", explode('t',$term_id));
        }
		$select->order("exam_roll");
// 		echo $select;exit;
		$result = $this->getAdapter()
		->fetchAll($select);		
		return $result;	
	}
        
        
	public function getStudentsForElectiveByCourse1($academic_year_id,$course,$term_id,$field_name = 'electives',$session=''){
					
		
		
	$select = $this->_db->select();

		$select->from($this->_name);
		$select->joinLeft(array("elective_selection_items"=>"erp_elective_selection_items"),"elective_selection_items.elective_id=$this->_name.elective_id",array("students_id as student_id","electives as course_id","aecc"));
                $select->joinLeft(array("allocamaster"=>"grade_allocation_master"),"allocamaster.course_id=elective_selection_items.electives",array(""));
		$select->joinLeft(array("allocaItem"=>"grade_allocation_items"),"allocaItem.student_id=$this->_name.student_id",array(""));
		
                $select->joinLeft(array("academic_master"),"academic_master.academic_year_id=$this->_name.academic_year_id",array("short_code"));
		$select->where("$this->_name.status != 2");
		$select->where("$this->_name.academic_year_id=?",$academic_year_id);
		$select->where("allocamaster.academic_id=?",$academic_year_id);
		$select->where("allocamaster.term_id=?",$academic_year_id);
		$select->where("allocamaster.course_id=?",$course);
                
		$select->where("elective_selection_items.$field_name=?",$course);
		$select->where("$this->_name.term_id=?",$term_id);
        if(!empty($session)){
            $select->where("academic_master.session=?",$session);
        }
           // $select->group('students_id') ;
        //echo $select;die;
		$result = $this->getAdapter()
		->fetchAll($select);
					
		return $result;	
	}
	public function getStudentsForElectiveByAcademicTerm($academic_year_id,$term_id,$course_arr){
					
		
		
	$select = $this->_db->select();
		$select->from($this->_name);
		$select->joinLeft(array("elective_selection_items"=>"erp_elective_selection_items"),"elective_selection_items.elective_id=$this->_name.elective_id",array("students_id as student_id","electives as course_id","aecc"));
                $select->joinLeft(array("student_information"=>"erp_student_information"),"student_information.student_id=elective_selection_items.students_id",array("CONCAT(stu_fname,' ',stu_lname) as students","stu_id","CONCAT(father_fname,father_lname) as father_name","reg_no","exam_roll"));
                $select->joinLeft(array("academic_master"),"academic_master.academic_year_id=student_information.academic_id",array("short_code"));
		$select->where("$this->_name.status != 2");
                $select->where("student_information.stu_status = 1");
		$select->where("$this->_name.academic_year_id=?",$academic_year_id);
                
                
		$select->where("elective_selection_items.terms=?",$term_id);
		$select->order("exam_roll");

		/* Comment: By Kedar 18 May 2019 ->from($this->_name)
		->joinLeft(array("elective_selection_items"=>"erp_elective_selection_items"),"elective_selection_items.elective_id=$this->_name.elective_id",array("students_id as student_id",))
		->joinLeft(array("student_information"=>"erp_student_information"),"student_information.student_id=elective_selection_items.students_id",array("CONCAT(stu_fname,stu_lname) as students","stu_id","CONCAT(father_fname,father_lname) as father_name","reg_no","exam_roll"))
		->where("$this->_name.status != 2")
		->where("$this->_name.academic_year_id=?",$academic_year_id)
		->where("elective_selection_items.$field_name=?",$course)
		->where("elective_selection_items.terms=?",$term_id);*/
                
             //   echo $select."<br><br>";
             
		$result = $this->getAdapter()
		->fetchAll($select);
					
		return $result;	
	}
	
	
	public function getValidAcademicRecord($academic_year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id =?", $academic_year_id);
					  //->where("$this->_name.year_id =?", $year_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
	public function getStudentSelectedElectives($academic_year_id,$term_id,$student_id){
		
		$select = $this->_db->select()
				->from($this->_name)
				->joinLeft(array('selection_items'=>'erp_elective_selection_items'),"selection_items.elective_id=$this->_name.elective_id",array('electives'))
				->where("$this->_name.academic_year_id=?",$academic_year_id)
				->where("$this->_name.term_id=?",$term_id)
				->where("$this->_name.student_id=?",$student_id)
				->where("$this->_name.status != ?",2);
	$result = $this->getAdapter()
             ->fetchAll($select);
     return $result;			 
		
	}

	public function getStudentSelectedElectivesOnGeId($academic_year_id,$term_id,$student_id,$ge_id,$course_id,$paper = 'R'){
		
		$select = $this->_db->select()
				->from(array('selection_items'=>'erp_elective_selection_items'),array('electives'))
			//	->joinLeft(array('selection_items'=>'erp_elective_selection_items'),"selection_items.elective_id=$this->_name.elective_id",array('electives'))
			//	->where("$this->_name.academic_year_id=?",$academic_year_id)
				->where("selection_items.terms=?",$term_id)
				->where("selection_items.students_id=?",$student_id)
				//->where("$this->_name.flag=?",$paper)
				->where("selection_items.ge_id=?",$ge_id)
				->where("selection_items.electives=?",$course_id);
				// ->where("$this->_name.status != ?",2);   

	$result = $this->getAdapter()
             ->fetchRow($select);
     return $result['electives'];			 
		
	}
	public function getStudentSelectedElectivesOnBackGeId($academic_year_id,$term_id,$student_id,$ge_id,$course_id,$paper = 'B'){
		
		$select = $this->_db->select()
				->from($this->_name)
				->joinLeft(array('selection_items'=>'back_selection_items'),"selection_items.elective_id=$this->_name.elective_id",array('electives'))
				->where("$this->_name.academic_year_id=?",$academic_year_id)
				->where("selection_items.terms=?",$term_id)
				->where("$this->_name.student_id=?",$student_id)
                                //->where("$this->_name.flag=?",$paper)
				->where("selection_items.ge_id=?",$ge_id)
				->where("selection_items.electives=?",$course_id)
			//	->where("selection_items.fail_status=?",1)
				->where("$this->_name.status != ?",2);      
               // echo $select."<br><br><br>";
	$result = $this->getAdapter()
             ->fetchRow($select);
     return $result['electives'];			 
		
	}
	public function getStudentSelectedStudents($academic_year_id,$term_id,$student_id){
		
		$select = $this->_db->select()
				->from($this->_name)
				->joinLeft(array('selection_items'=>'back_selection_items'),"selection_items.elective_id=$this->_name.elective_id",array('electives'))
				->where("$this->_name.academic_year_id=?",$academic_year_id)
				->where("selection_items.terms=?",$term_id)
				->where("selection_items.students_id=?",$student_id)
				//->where("selection_items.fail_status=?",1)
				->where("$this->_name.status != ?",2);      
	$result = $this->getAdapter()
             ->fetchRow($select);
     return $result['electives'];			 
		
	}

	public function getStudentSelectedElectivesId($academic_year_id,$term_id,$student_id,$aecc_id=''){
		
		$select = $this->_db->select()
				->from($this->_name,array('elective_id'))
				->where("$this->_name.academic_year_id=?",$academic_year_id)
				->where("$this->_name.term_id=?",$term_id)
				->where("$this->_name.student_id=?",$student_id)
				//->where("$this->_name.ge_id=?",$aecc_id)
				->where("$this->_name.status != ?",2);
	$result = $this->getAdapter()
             ->fetchRow($select);
     return $result['elective_id'];			 
		
	}
	
	public function getValidStudentsRecord($academic_year_id,$student_id,$term_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id =?", $academic_year_id)
					  ->where("$this->_name.student_id =?", $student_id)
					  ->where("$this->_name.term_id =?", $term_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
    public function getSelectedElectives($elective_increment_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array('selection_items'=>'erp_elective_selection_items'),"selection_items.elective_id=$this->_name.elective_id",array("electives"))
					->where("$this->_name.elective_id=?",$elective_increment_id)
					->where("$this->_name.status!=2");
		$result = $this->getAdapter()
				->fetchAll($select);
				
				
		return $result;			
		
		
	}
        
        
        	public function getElectivesByTerm($academic_year_id,$term_id)
	{
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array("GROUP_CONCAT(terms) as term_id","GROUP_CONCAT(elective_name) as elev_names","GROUP_CONCAT(students_id) as stu_ids","GROUP_CONCAT(electives) as tot_electives","terms","electives","credit_value"))
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.term_id=?",$term_id)
                        		->group("elective_items.terms")
					->group("elective_items.electives")
					->where("$this->_name.status != 2");		
				//echo $select ; exit;	
				$result = $this->getAdapter()
						->fetchAll($select);
						
			return $result;			
					
					
		
		
	}
        
        
        
}
?>