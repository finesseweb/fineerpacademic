<?php

/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class Application_Model_TermMaster extends Zend_Db_Table_Abstract {

    public $_name = 'term_master';
    protected $_id = 'term_id';

    //get details by record for edit
    public function getMaxVersion($dates){
        $select = $this->_db->select()
                ->from('batch_scheduler',array('max(publish) as version'))
                ->where("date=?", $dates);

        $result = $this->getAdapter()
                ->fetchRow($select);
        
        return $result;
    }
    
    
    public function getPenalties($course_id,$term_id){
        
          $select = $this->_db->select()
                ->from('course_grade_after_penalties_items',array("student_id,academic_grades,FIND_IN_SET($course_id, academic_courses) As courses_position" ))
                  ->where('term_id =?',$term_id)
            ->where("academic_courses LIKE (?)", "%$course_id%");
       // echo   $select;exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
   public function studentName($stu_id){
        $select = $this->_db->select()
                ->from('erp_student_information', array('concat(stu_fname, stu_lname) As stu_name'));
                 $result = $this->getAdapter()
                ->fetchAll($select);
        return $result[0]['stu_name'];
   }
    
    
       public function getStartAndEndDate($tern_id,$batch_id) {
        $select = $this->_db->select()
                ->from($this->_name,array('start_date','end_date'))
                ->where("academic_year_id=?", $batch_id)
                ->where("term_id=?", $tern_id)
                ->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
     public function getRecordByCmnTerms($id,$session_id='',$ge_id = '',$course_id = '') {
         
        $select = $this->_db->select();
                //echo $session_id;
                $select->from($this->_name,array('term_id','year_id','academic_year_id'));
                $select->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_year_id", array("short_code","from_date", "to_date"));
                
                  if(empty($ge_id) && !empty($course_id)){
                        $select->joinleft(array("core" => "core_course_master"), "core.term_id=$this->_name.term_id", array(""));
                        $select->where("course_id=?", $course_id);
                        
                  }
                if(!empty($session_id)){
                $select->where("academic.session=?", $session_id);
                $select->where("$this->_name.status !=?", 2);
                $select->where("$this->_name.cmn_terms =?", $id);
                }
        $result = $this->getAdapter()
                ->fetchAll($select);
       //echo "<pre>";print_r($result);exit;
        //echo $select; die;
        return $result;
    }
    
     public function getRecordByYearId($id) {
        
       $select = $this->_db->select()
                ->from($this->_name,array('academic_year_id'))
                ->where("year_id=?", $id)
                ->where("$this->_name.status !=?", 2);
       $result = $this->getAdapter()
                ->fetchAll($select); 
       return $result;
    }
    
    
    
    
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
     public function getRecordbycmm($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.cmn_terms =?", $id)
                ->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchRow($select);
                  //echo'<pre>';print_r($result);die;
        return $result;
    }
    
 
    
    
    public function getYearId($id) {
        $select = $this->_db->select('year_id')
                ->from($this->_name)
                ->where("$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['year_id'];
    }

    public function getRecordByAcademicId($academic_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("academic_year_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.cmn_terms");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getRecordByAcadCmnterms($reqData) {
        $select = $this->_db->select()
                ->from($this->_name,array('term_id'))
                ->where("academic_year_id=?", $reqData['acad_id'])
                ->where("cmn_terms=?", $reqData['term'])
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.cmn_terms");
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    //Get all records
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_year_id", array("short_code","from_date", "to_date"))
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_id DESC")
                ->group("$this->_id");
        //  ->group("$this->_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getValidateTermName($term_id) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                ->where("$this->_name.term_name =?", $term_id)
                ->where("$this->_name.status!=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    public function isTermExist($term_id,$academic_year_id,$year_id) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                ->where("$this->_name.year_id =?", $year_id)
                ->where("$this->_name.cmn_terms =?", $term_id)
                ->where("$this->_name.academic_year_id =?", $academic_year_id)
                ->where("$this->_name.status!=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['term_id'];
    }
    
    public function getValidateTermNameById($term_id) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                ->where("$this->_name.term_id =?", $term_id)
                ->where("$this->_name.status!=?", 2);
       
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getTerm($term) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                ->where("$this->_name.term_name =?", $term)
                ->where("$this->_name.status!=?", 2);
       //echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
      public function getTermName($term) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                ->where("$this->_name.term_id =?", $term)
                ->where("$this->_name.status!=?", 2);
       //echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }


    public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('term_id', 'term_description as term_name'))
                ->where("$this->_name.status!=?", 2)
                ->order('term_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        //echo'<pre>';print_r($result);die;
        $data = array();
        foreach ($result as $k => $vals) {

            $data[$vals['term_id']] = $vals['term_name'];
        }

        return $data;
    }
    public function getDropDownListAcademic($ac_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('term_id', 'term_description as term_name'))
                ->where("$this->_name.status!=?", 2)
                ->where("$this->_name.academic_year_id =?", $ac_id)
                ->order('term_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        //echo'<pre>';print_r($result);die;
        $data = array();
        foreach ($result as $k => $vals) {

            $data[$vals['term_id']] = $vals['term_name'];
        }

        return $data;
    }

    public function getTermDropDownList($academic_year_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('term_id', 'term_description as term_name'))
                ->where("$this->_name.status!=?", 2)
                ->where("$this->_name.academic_year_id =?", $academic_year_id)
                ->order('term_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        //echo'<pre>';print_r($result);die;
        $data = array();
        foreach ($result as $k => $vals) {

            $data[$vals['term_id']] = $vals['term_name'];
        }

        return $data;
    }
    //Developed by: Kedar 25 Sept. 2019
    public function getNextTerm($semester_id) {
        $data='';
        $select = $this->_db->select()
                ->from('declared_terms', array('term_name'))
                ->where("declared_terms.status!=?", 2)
                ->where("declared_terms.id =?", $semester_id+1)
                ->order('id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data= $result[0]['term_name'];
        return $data;
    }
    //End

    public function getGradeTermName($academic_year_id) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                ->joinleft(array("core" => "core_course_master"), "core.term_id=$this->_name.term_id", array("course_id"))
                ->joinleft(array("course" => "course_master"), "course.course_id=core.course_id", array("course_name"))
                ->where("$this->_name.academic_year_id =?", $academic_year_id)
                ->where("$this->_name.status!=?", 2);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getBatchTerms($academic_year_id) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                //->joinleft(array("core"=>"core_course_master"),"core.term_id=$this->_name.term_id",array("course_id"))
                //->joinleft(array("course"=>"course_master"),"course.course_id=core.course_id",array("course_name"))
                ->where("$this->_name.academic_year_id =?", $academic_year_id)
                ->where("$this->_name.year_id =?", 2)
                ->where("$this->_name.status!=?", 2);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);

        $data = array();
        foreach ($result as $k => $vals) {

            $data[$vals['term_id']] = $vals['term_name'];
        }

        return $data;
    }

    public function getCoreCourseTerms($academic_year_id) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                //->joinleft(array("core"=>"core_course_master"),"core.term_id=$this->_name.term_id",array("course_id"))
                //->joinleft(array("course"=>"course_master"),"course.course_id=core.course_id",array("course_name"))
                ->where("$this->_name.academic_year_id =?", $academic_year_id)
                //->where("$this->_name.year_id =?", 1)
                ->where("$this->_name.status!=?", 2);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);

        $data = array();
        foreach ($result as $k => $vals) {

            $data[$vals['term_id']] = $vals['term_name'];
        }

        return $data;
    }

    public function getTermRecords($academic_year_id, $term_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id);

        $result = $this->getAdapter()
                ->fetchAll($select);
//print_r($result); die();
        return $result;
    }
     public function getTermRecordsbycmn($academic_year_id, $term_id) {

        $select = $this->_db->select()
                ->from($this->_name,array('term_id','term_name','term_description'))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.cmn_terms=?", $term_id );
            //echo $select; die();
        $result = $this->getAdapter()
                ->fetchRow($select);
        
        return $result;
      }
      
        public function getTermRecordsbycmnelective($academic_year_id, $term_id) {

        $select = $this->_db->select()
                ->from($this->_name,array("GROUP_CONCAT(term_id) as term_id"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id in (?)", $academic_year_id)
                ->where("$this->_name.cmn_terms=?", $term_id )
                ->group("$this->_name.cmn_terms");
    
        $result = $this->getAdapter()
                ->fetchRow($select);
        
        return $result['term_id'];
      }
    
      
      
     public function getTermRecordsbycmn1($academic_year_id, $term_id) {

        $select = $this->_db->select()
                ->from($this->_name,array('term_id','year_id'))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.cmn_terms=?", $term_id );
           //  echo $select; die();
        $result = $this->getAdapter()
                ->fetchRow($select);
        
        return $result;
      }
      
      
       
    public function getAcademicTermIdAction($academic_year_id, $term_id){
        
          $select = $this->_db->select()
                ->from($this->_name,array('term_name'))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.cmn_terms>?", $term_id );
        $result = $this->getAdapter()
                ->fetchRow($select);
        
        return $result['term_name'];
    }

    public function getTermRecordsByYear($academic_year_id, $year_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.year_id=?", $year_id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    
    public function getNonTermRecordsByYear($academic_year_id, $year_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("nonug"=>"non_collegiate_data"),"nonug.term_id=$this->_name.cmn_terms")
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.year_id=?", $year_id)
                ->where("nonug.status=?", 0)
                ->group("nonug.term_id");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    
       public function getNonpgTermRecordsByYear($academic_year_id, $year_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("nonpg"=>"non_collegiate_stu_data"),"nonpg.term_id=$this->_name.cmn_terms")
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.year_id=?", $year_id)
                ->where("nonpg.status=?", 0)
                ->group("nonpg.term_id");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    public function getTermRecordsByTerm($academic_year_id, $term_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id);
           // echo $select;exit;
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }


    
    
    
    public function getCoreCourse($term_id,$academic_year_id,$ge_id){
        
        
                

        $select = $this->_db->select();
                $select->from($this->_name, array('academic_year_id', 'term_id', 'tot_no_of_credits'));
                $select->joinLeft(array("core" => "core_course_master"), "core.term_id=$this->_name.term_id", array("credit_id"));
                $select->joinleft(array("credit" => "credit_master"), "credit.credit_id = core.credit_id", array("sum(distinct credit_value) as cre_value"));
               $select ->where("$this->_name.academic_year_id=?", $academic_year_id);
               
              //  if($this->getCoreCourse($term_id, $academic_year_id, $ge_id) == 1){
                 $select->where("core.ge_id !=?", 0);
                //}
                $select->where("$this->_name.term_id=?", $term_id);
                $select->where("$this->_name.status!=?", 2);
                $select->group(array("$this->_name.term_id"));
              //  echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
        
        
    }
    
        public function getTermCredits($academic_year_id, $term_id,$ge_id =0) {
            

                $ge_id = empty($ge_id)?0:$ge_id;

                $select = $this->_db->select();
                $select->from($this->_name, array('academic_year_id', 'term_id', 'tot_no_of_credits'));
                $select->joinLeft(array("core" => "core_course_master"), "core.term_id=$this->_name.term_id", array("credit_id"));
                $select->joinleft(array("credit" => "credit_master"), "credit.credit_id = core.credit_id", array("sum(credit_value) as cre_value"));
                $select ->where("$this->_name.academic_year_id=?", $academic_year_id);
               
               
   
        
               $select->where("core.ge_id =?", 0);
                $select->where("$this->_name.term_id=?", $term_id);
                $select->where("$this->_name.status!=?", 2);
                $select->group("$this->_name.term_id");
        $result = $this->getAdapter()
                ->fetchRow($select);
        
         $cre_res = $this->getCoreCourse($term_id,$academic_year_id,$ge_id);
         $cre_res = empty($cre_res)?0:$cre_res;
        if(empty($result)){
          $result = $cre_res; 
          $result = $result == 0?$this->checkCreditData($academic_year_id, $term_id,$ge_id):$result;
          return $result;
        }
        
       if($ge_id == 0)
        $result['cre_value']+= $cre_res['cre_value'];
        
        return $result;
 
    }
    
    
    
    
    public function checkCreditData($academic_year_id, $term_id,$ge_id =0){
     
       
          $ge_id = empty($ge_id)?0:$ge_id;

                $select = $this->_db->select();
                $select->from($this->_name, array('academic_year_id', 'term_id', 'tot_no_of_credits'));
                $select->joinLeft(array("core" => "core_course_master"), "core.term_id=$this->_name.term_id", array("credit_id"));
                $select->joinleft(array("credit" => "credit_master"), "credit.credit_id = core.credit_id", array("sum(credit_value) as cre_value"));
                $select ->where("$this->_name.academic_year_id=?", $academic_year_id);
              //  if($this->getCoreCourse($term_id, $academic_year_id, $ge_id) == 1){
                //}
                $select->where("$this->_name.term_id=?", $term_id);
                $select->where("$this->_name.status!=?", 2);
                $select->group("$this->_name.term_id");
        $result = $this->getAdapter()
                ->fetchRow($select);
        
      //  echo "<pre>";print_r($result);exit;
        
        return $result;
        
        
    }
    
    
    

    public function getRemainingTermCredits($academic_year_id, $term_id, $ccl_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('academic_year_id', 'term_id', 'tot_no_of_credits'))
                ->joinLeft(array("core" => "core_course_master"), "core.term_id=$this->_name.term_id", array("credit_id", "ccl_id"))
                ->joinleft(array("credit" => "credit_master"), "credit.credit_id=core.credit_id", array("sum(credit_value) as cre_value"))
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.status!=?", 2)
                ->where("core.ccl_id!=?", $ccl_id)
                ->group("$this->_name.term_id");

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getYearTerms($year_id, $academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array("term_name", "term_id"))
                ->where("$this->_name.year_id =?", $year_id)
                ->where("$this->_name.academic_year_id =?", $academic_id)
                ->where("$this->_name.status!=?", 2);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);

        $data = array();
        foreach ($result as $k => $vals) {

            $data[$vals['term_id']] = $vals['term_name'];
        }

        return $data;
    }

    public function getTerms($academic_year_id, $year_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.year_id=?", $year_id)
                ->where("$this->_name.status!=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getTotalAllotedCredits($academic_year_id, $term_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('electives_credits'))
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.status != ?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);


        return $result;
    }

    public function getSecondYearTerms() {
        $select = $this->_db->select()
                ->from($this->_name, array('term_id', 'term_name'))
                ->where("$this->_name.year_id=?", 2)
                ->where("$this->_name.status!=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);

        $data = array();
        foreach ($result as $val) {
            $data[$val['term_id']] = $val['term_name'];
        }
        return $data;
    }

    public function getAcademicTerms($academic_year_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('term_id', 'term_name'))
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.year_id=?", 2)
                ->where("$this->_name.status != 2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['term_id']] = $val['term_name'];
        }
        return $data;
    }
    public function getAcademicMinTerms($academic_year_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('min(term_id) as term_id','term_name'))
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                //->where("$this->_name.year_id=?", 2)
                ->where("$this->_name.status != 2");
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['term_id'];
    }
    public function getAcademicMinTerms1($academic_year_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('term_id','term_description as term_name'))
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                //->where("$this->_name.year_id=?", 2)
                ->where("$this->_name.status != 2")
                ->order("$this->_name.term_id ASC");
        $result = $this->getAdapter()
                ->fetchAll($select);
           // echo $select;die;
        return $result[0]['term_name'];
    }

    public function getElectiveCredit($academic_year_id, $term_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('electives_credits'))
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getElectivesTermCredits($academic_year_id, $term_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.status!=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    //BY Ramesh
    public function getlastdateRecord($academic_id) {
        $select = "SELECT * FROM term_master  Where  academic_year_id='$academic_id' and status !=2  order by term_id  DESC";

        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;
        return $result;
    }

//Sailaja
    public function getPreviousidRecord($academic_id, $previous_id) {
        $id = $previous_id - 1;
        $select = "SELECT * FROM term_master Where academic_year_id='$academic_id' and term_id = '$id' and status != 2 order  by term_id DESC";

        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result;
    }

    /**
     * Returns Array of Terms of All requested batches in array
     * @param Array $batch_list Array of batch ids
     * @param INT $year First Year or Second Year
     */
    public function getTermsByBatchesYear($batch_list, $year) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.academic_year_id IN (?)", $batch_list)
                ->where("$this->_name.year_id=?", $year)
                ->where("$this->_name.status != ?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);


        return $result;
    }

    public function getTermOnDate($my_date) {
        $select = $this->_db->select()
                ->from($this->_name, array('academic_year_id', 'term_id', 'start_date', 'end_date','term_name'))
                ->where("$this->_name.status != ?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
       
      for($i=0;$i<count($result); $i++){
        $result[$i]['academic_name'] = $this->academicDetails($result[$i]['academic_year_id']);
        
      }
        return $result;
    }
    
     public function getTermOnAcademicId($batch) {
        
        $select = $this->_db->select()
                ->from($this->_name, array('academic_year_id', 'term_id', 'start_date', 'end_date','term_name'))
                ->where("academic_year_id = ?",$batch)
                ->where("$this->_name.status != ?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
       
      for($i=0;$i<count($result); $i++){
        $result[$i]['academic_name'] = $this->academicDetails($result[$i]['academic_year_id']);
        
      }
        return $result;
    }
    
    
       public function getTermOnDat1($term_id,$batch_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('academic_year_id', 'term_id', 'start_date', 'end_date','term_name','cmn_terms'))
                ->where("$this->_name.term_id =?",$term_id)
                ->where("$this->_name.academic_year_id =?",$batch_id)
                ->where("$this->_name.status != ?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
       
      for($i=0;$i<count($result); $i++){
        $result[$i]['academic_name'] = $this->academicDetails($result[$i]['academic_year_id'])['short_code'];
        $result[$i]['session'] = $this->academicDetails($result[$i]['academic_year_id'])['session'];
        
      }
        return $result;
    }
    
    
    
    public function academicDetails($academic_id){
       
           $select = $this->_db->select()
                ->from('academic_master', array('short_code','session'))
                   ->where('academic_year_id = ?', $academic_id);
                    $result = $this->getAdapter()
                ->fetchRow($select);
                    return $result;
    }    
    public function academicDetail($academic_id){
       
           $select = $this->_db->select()
                ->from('academic_master', array('short_code','from_date','to_date'))
                   ->where('academic_year_id = ?', $academic_id);
                    $result = $this->getAdapter()
                ->fetchAll($select);
                    return $result;
    }

    public function getCourses($academic_id, $term_id) {
        $select = $this->_db->select()
                ->from('core_course_master', array('course_id'))
                ->where('core_course_master.academic_year_id = ?', $academic_id)
                ->where('core_course_master.term_id = ?', $term_id)
                ->where("core_course_master.status != ?", 2);
        //echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getCourseName($course_id) {
        $select = $this->_db->select()
                ->from('course_master', array('course_code', 'course_name'))
                ->where('course_master.course_id = ?', $course_id)
                ->where("course_master.status != ?", 2);
        // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result[0];
    }

    public function getCourseCount($course_id, $start_date, $end_date, $batch_id, $term_id) {
        $day_num = $this->date_diff2($start_date, $end_date);
        $main = array();
       
        for($k=0; $k<count($course_id); $k++){
        for ($j = 0; $j < (int) $day_num; $j++) {
            $All_courses_date_arr = array();
            $inc_date = date("d-m-Y", strtotime($start_date . ' + ' . $j . ' days'));
            for ($i = 1; $i <= 5; $i++) {
                $select = $this->_db->select()
                        ->from('batch_scheduler')
                        ->where("batch=?", $batch_id)
                        ->where("term_id =?", $term_id)
                        ->where("date = ?", $inc_date)
                        ->where("class_" . $i . " =?", $course_id[$k]['course_id']);
                        //echo $select;exit;
                $result = $this->getAdapter()
                        ->fetchAll($select);
               
                // $main[$j][$i] =count($result);
                if (count($result) > 0){
                   
                    (int)$main[$course_id[$k]['course_id']]+= 1;
                    
                }
            }
          }
        }
        
      return $main;
        
    }
    
    
      public function getCourseCount1($course_id, $start_date, $end_date, $batch_id, $term_id,$version) {
       
        $day_num = $this->date_diff2($start_date, $end_date);
     
    
        for($k=0; $k<count($course_id); $k++){
        for ($j = 0; $j < (int) $day_num+1; $j++) {
            $All_courses_date_arr = array();
            $inc_date = date("d-m-Y", strtotime($start_date . ' + ' . $j . ' days'));
           
            for ($i = 1; $i <= 5; $i++) {
                $select = $this->_db->select()
                        ->from('batch_scheduler')
                        ->where("batch=?", $batch_id)
                        ->where("term_id =?", $term_id)
                        ->where("date = ?", $inc_date)
                        ->where("class_" . $i . " =?", $course_id[$k]['course_id'])
                        ->where('publish = ?', (float)$version);
                     //  echo $select;exit;
                $result = $this->getAdapter()
                        ->fetchAll($select);
               
                // $main[$j][$i] =count($result);
                if (count($result) > 0){
                   
                    (int)$main[$course_id[$k]['course_id']]+= 1;
                    
                }
            }
          }
        }
       // print_r($main);exit;
      return $main;
        
    }
    
    
    
      public function getCourseReport($course_id,$batch_id, $term_id,$section,$version='') {
                $select = $this->_db->select()
                        ->from('course_report',array('course_count'))
                        ->where("batch_id=?", $batch_id)
                        ->where("course_code=?", $course_id)
                        ->where("section=?",$section)
                        ->where("term_id =?", $term_id);
                     //  echo $select;exit;
                $result = $this->getAdapter()
                        ->fetchRow($select);
               
                // $main[$j][$i] =count($result);
              
       
       // print_r($main);exit;
      return $result['course_count'];
        
    }
      public function getCourseReport1($course_id,$batch_id, $term_id,$version) {
                $select = $this->_db->select()
                        ->from('course_report',array('sum(course_count) as course_count'))
                        ->where("batch_id=?", $batch_id)
                        ->where("course_code=?", $course_id)
                        ->where("term_id =?", $term_id);
                     //  echo $select;exit;
                $result = $this->getAdapter()
                        ->fetchRow($select);
               
                // $main[$j][$i] =count($result);
              
       
       // print_r($main);exit;
      return $result['course_count'];
        
    }
    
    
    

    public function date_diff2($date1 = '', $date2 = '') {


        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff ) / (60 * 60 * 24));
        return (int) $days;
    }
    
    
    
    
   public function getRecordsBycmnTerms($id){
       
       $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.cmn_terms=?", $id)
                ->where("$this->_name.status != ?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);


        return $result;
    
       
   }
    
    

}

?>