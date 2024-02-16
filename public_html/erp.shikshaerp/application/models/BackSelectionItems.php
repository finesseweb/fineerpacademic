//<?php
/**
 * Application_Model_ErpInventoryGrnItems
 *
 * @Framework Zend Framework
 * @Powered By TIS
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2014 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 */
class Application_Model_BackSelectionItems extends Zend_Db_Table_Abstract {

    protected $_name = 'back_selection_items';
    protected $_id = 'items_id';

    /**
     * Set Primary Key Id as a Parameter 
     *
     * @param string $id
     * @return single dimention array
     */
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_id=?", $id);
				//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
				
        return $result;
    }

    /**
     * Retrieve all Records
     *
     * @return Array
     */
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name);
				//->where("$this->_name.items_status !=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

	
   public function trashItems($elective_id='') {
        $this->_db->delete($this->_name, "elective_id = $elective_id");
    }
	
	public function getItemsRecords($elective_id) {
        $select = $this->_db->select()
                ->from($this->_name)
				 ->where("$this->_name.elective_id=?", $elective_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
	
        return $result;
    }
    
    
     public function getCoreCouseDetailByTermGeStudentAll($academic_year_id,$term_id,$ge_id){
        $select=$this->_db->select()
                      ->from($this->_name,array('course_id'))
                      ->where("$this->_name.academic_year_id=?", $academic_year_id)
                        ->where("$this->_name.term_id=?", $term_id)
                        ->where("$this->_name.ge_id=?", $ge_id)
                        ->where("$this->_name.status !=?", 2);
        //  echo $select;die;
        $result=$this->getAdapter()
                      ->fetchAll($select);   
        return $result;	

	}

        
  public function getCouseDetailByStudentId($academic_year_id,$term_id='',$ge_id){
        $select=$this->_db->select()
                      ->from($this->_name)
                ->join(array("select1" => "erp_elective_selection"), "select1.elective_id=$this->_name.elective_id",array())
                 ->where("select1.academic_year_id=?", $academic_year_id)
                        ->where("$this->_name.terms=?", $term_id)
                      ->where("$this->_name.students_id=?", $ge_id);
      //  echo $select;die;
        $result=$this->getAdapter()
                      ->fetchAll($select);   
        
        //print_r($result);
        //die();
        
        
        return $result;	

	}
    

    public function getTermIdByFid($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name,array("max(`terms`) as terms"))	
            ->joinLeft(array("term"=>"term_master"),"term.term_id=$this->_name.terms",array('term_name'))
            ->where("$this->_name.students_id=?", $id);
            $result=$this->getAdapter()
            ->fetchAll($select);    
  	//echo"<pre>";print_r($result);die;	  
        return $result;
    }

          public function getelectivestudentDetails1($academic_id='',$term_id='',$course_id='',$ge_id = 0,$pay=false)
    {  
    
        $select=$this->_db->select();
            $select->from($this->_name,array());	
            $select->joinLeft(array("term"=>"term_master"),"term.term_id=$this->_name.terms",array("term_name"));
            $select->joinLeft(array("student"=>"erp_student_information"),"student.student_id=$this->_name.students_id",array("student.*"));
            $select->joinLeft(array("acad"=>"academic_master"),"acad.academic_year_id=student.academic_id",array("short_code as academic_year"));
            if($pay){
             $select->join(array("payment_ug"=>"ugnon_form_submission"),"payment_ug.student_id=student.student_id",array());
            }
            $select->where("term.academic_year_id in(?)", explode(',',$academic_id));
            if($ge_id ==0){
            $select->where("$this->_name.ge_id=?", $ge_id);
            }
            else {
                $select->where("$this->_name.ge_id !=?", 0);
            }
             $select->where("$this->_name.fail_status =?", 0);
             $select->where("student.stu_status =?", 1);
             if($pay){
             $select->where("payment_ug.payment_status =?", 1);
              $select->where("payment_ug.term_id in (?)", explode(',',$term_id));
             }
            $select->where("term.term_id in (?)", explode(',',$term_id));
            $select->order("student.exam_roll");
            $select->where("$this->_name.electives=?", $course_id);
            $select->group("$this->_name.students_id");
            $result=$this->getAdapter()
            ->fetchAll($select); 
            
         if($pay){
            if(!count($result))
          return $this->getelectivestudentDetails2($academic_id,$term_id,$course_id,$ge_id,$pay );
         }
          
        return $result;
    }
    
              public function getelectivestudentDetails2($academic_id='',$term_id='',$course_id='',$ge_id = 0,$pay)
    {  
        
        
        $select=$this->_db->select();
            $select->from($this->_name,array());	
            $select->joinLeft(array("term"=>"term_master"),"term.term_id=$this->_name.terms",array("term_name"));
            $select->joinLeft(array("student"=>"erp_student_information"),"student.student_id=$this->_name.students_id",array("student.*"));
            $select->joinLeft(array("acad"=>"academic_master"),"acad.academic_year_id=student.academic_id",array("short_code as academic_year"));
             if($pay){
             $select->join(array("payment_pg"=>"pg_non_form_data"),"payment_pg.student_id=student.student_id",array());
             }
            $select->where("term.academic_year_id in(?)", explode(',',$academic_id));
            if($ge_id ==0){
            $select->where("$this->_name.ge_id=?", $ge_id);
            }
            else {
                $select->where("$this->_name.ge_id !=?", 0);
            }
             $select->where("$this->_name.fail_status =?", 0);
             $select->where("student.stu_status =?", 1);
              if($pay){
             $select->where("payment_pg.payment_status =?", 1);
              $select->where("payment_pg.term_id in (?)", explode(',',$term_id));
              }
            $select->where("term.term_id in (?)", explode(',',$term_id));
            $select->order("student.exam_roll");
            $select->where("$this->_name.electives=?", $course_id);
     
            $result=$this->getAdapter()
            ->fetchAll($select);    	  
        return $result;
    }
    
    public function getStuRecords($uid,$sem) {
                $select = $this->_db->select()

                ->from($this->_name)
                ->join(array("student"=>"erp_student_information"),"student.student_id=$this->_name.students_id",array("student.*"))
                 ->join(array("acad"=>"academic_master"),"acad.academic_year_id=student.academic_id",array("session","department"))
                 ->join(array("term"=>"term_master"),"term.term_id=$this->_name.terms",array())
                 ->where("md5(student.stu_id)=?", $uid)
                 ->where("term.cmn_terms=?", $sem)
                 ->where("$this->_name.fail_status=?", 0);
                $result = $this->getAdapter()

                ->fetchAll($select);

//echo $select; exit;

        return $result;

    }
    
    
    
     public function getStuSubRecords($uid,$sem) {
                $select = $this->_db->select()

                ->from($this->_name)
                ->join(array("student"=>"erp_student_information"),"student.student_id=$this->_name.students_id",array("student.*"))
                 ->join(array("acad"=>"academic_master"),"acad.academic_year_id=student.academic_id",array("session","department"))
                 ->join(array("term"=>"term_master"),"term.term_id=$this->_name.terms",array())
                 ->where("student.stu_id=?", $uid)
                 ->where("term.cmn_terms=?", $sem)
                 ->where("$this->_name.fail_status=?", 0);
                $result = $this->getAdapter()

                ->fetchAll($select);

//echo $select; exit;

        return $result;

    }
   public function getStuPreviewRecords($uid,$sem) {
                $select = $this->_db->select()

                ->from($this->_name)
                ->join(array("student"=>"erp_student_information"),"student.student_id=$this->_name.students_id",array("student.*"))
                 ->join(array("acad"=>"academic_master"),"acad.academic_year_id=student.academic_id",array("session","department"))
                 //->join(array("term"=>"term_master"),"term.term_id=$this->_name.terms",array())
                 ->where("student.stu_id=?", $uid)
                 ->where("$this->_name.terms=?", $sem)
                 ->where("$this->_name.fail_status=?", 0);
                $result = $this->getAdapter()

                ->fetchAll($select);

//echo $select; exit;

        return $result;

    } 
    
  public function getStudentForNonColRecords($academic_id,$sem){
      //  echo $pay; die();
            $select = $this->_db->select();
                
                $select->from('erp_student_information',array('stu_fname','stu_id','roll_no','exam_roll'));
              
                 $select->joinleft(array("back_selection_items"),"$this->_name.students_id=erp_student_information.student_id");
                 
               
              //  $select->joinleft(array("department"),"department.id=$this->_name.department",array("department"));
               // $select->joinleft(array("exam_form_submission"),"exam_form_submission.u_id=erp_student_information.stu_id",array("SUM(IF(exam_form_submission.payment_status = '1', fee, 0)) AS exampaid_fees"));
                $select->joinleft(array("term_master"),"term_master.term_id=$this->_name.terms");
               
                $select->where("erp_student_information.academic_id =?",$academic_id);
                $select->where("erp_student_information.stu_status =?",1);
                $select->where("term_master.cmn_terms =?",$sem);
                $select->where("$this->_name.fail_status =?",0);
                $select->group("erp_student_information.stu_id");
               
                //$select->limit("$this->_name.id",2);
        $result = $this->getAdapter()
                ->fetchAll($select);
          // echo $select;die;
          //  echo"<pre>";print_r($result);die;
        return $result;
    }   


    
    
    
	
}