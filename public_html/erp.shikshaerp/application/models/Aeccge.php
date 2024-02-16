<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Aeccge extends Zend_Db_Table_Abstract {

    public $_name = 'master_aeccge';
    protected $_id = 'aeccge_id';

    //Get all records
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name,array('aeccge_id', 'department', 'ge_id', 'aecc_id', 'status', 'degree_id as degree'))
                ->joinleft(array("dept"=>"department"),"dept.id=$this->_name.department",array('department as department_name'))
                ->joinleft(array("aecc"=>"master_aecc"),"aecc.aecc_id=$this->_name.aecc_id")
		->joinLeft(array("ge"=>"master_ge"),"ge.ge_id=$this->_name.ge_id")
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
            //echo $select;die;
            //echo '<pre>'; print_r($result);exit;
        return $result;
    }

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2);
       // echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
    
    public function getRecordByGe($id){
            
                   $select = $this->_db->select()
                      ->from('core_course_master')
                      ->where("core_course_master.ge_id=?", $id)
                      ->joinLeft(array("course"=>"course_master"),"course.course_id=core_course_master.course_id",array('course_id','course_name'))
                      ->where("core_course_master.status !=?", 2);
            $result = $this->getAdapter()
                      ->fetchAll($select);
           return $result;
    
    }
    
    
    public function getDropDownList($id){
        
            $select = $this->_db->select()
                      ->from('core_course_master')
                      ->where("core_course_master.ge_id=?", $id)
                      ->joinLeft(array("course"=>"course_master"),"course.course_id=core_course_master.course_id",array('course_id','course_name'))
                      ->where("core_course_master.status !=?", 2);
           $result = $this->getAdapter()
                      ->fetchAll($select);
           
           
           $data = array();
          foreach ($result as $val) {
            $data[$val['course_id']] = $val['course_name'];
        }
        return $data;
    }
    
    public function getRecordByDepartment($id,$academic_id,$term_id = '',$ge_id = ''){
        $select = $this->_db->select();
            $select->from($this->_name);
            $select ->joinLeft(array("ge"=>"master_ge"),"ge.ge_id=$this->_name.ge_id");
            $select->joinLeft(array("core"=>"core_course_master"),"core.ge_id=$this->_name.ge_id");
            $select->where("$this->_name.department=?", $id);
            $select->where("core.academic_year_id=?", $academic_id);               
            if($term_id)
            $select->where("core.term_id=?", $term_id);
            if($ge_id)
            $select->where("core.cc_id=?", $ge_id);
            //$select->group(array("core.cc_id"));  
            $select->where("$this->_name.status !=?", 2);
            //echo $select;die;  
            $result = $this->getAdapter()
            ->fetchAll($select);        
        return $result;
    }
    
    //==================for not aecc ================= in addmission ======//
    
     public function getRecordByDepartment2($id,$academic_id,$term_id = '',$ge_id = ''){
        $select = $this->_db->select();
                $select->from($this->_name);
               $select ->joinLeft(array("ge"=>"master_ge"),"ge.ge_id=$this->_name.ge_id");
                $select->joinLeft(array("core"=>"core_course_master"),"core.ge_id=$this->_name.ge_id");
                $select->where("$this->_name.department=?", $id);
                $select->where("core.academic_year_id=?", $academic_id);
                $select->where("ge.ge_id !=?", 5);
                if($term_id)
                $select->where("core.term_id=?", $term_id);
                
                if($ge_id)
                $select->where("core.cc_id=?", $ge_id);
                
               // $select->group(array("core.cc_id"));
               
                $select->where("$this->_name.status !=?", 2);
               // echo select;die;
             
        $result = $this->getAdapter()
                ->fetchAll($select);
                 
        return $result;
    }
    public function getRecordByDepartment1($id,$degree_id = '') {
        $select = $this->_db->select();
               $select->from($this->_name);
                $select->joinLeft(array("ge"=>"master_ge"),"ge.ge_id=$this->_name.ge_id");
                $select->where("$this->_name.department=?", $id);
                if(!empty($degree_id)){
                    $select->where("$this->_name.degree_id=?", $degree_id);
                }
                $select->group("$this->_name.ge_id");
                $select->where("$this->_name.status !=?", 2);
             
                
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    //Added by kedar to get all ge
     public function getRecordByDepartment3($id,$academic_id,$term_id = '',$ge_id = ''){
        //echo '<pre>'; print_r(explode(',',$id));
         
        $select = $this->_db->select();
                $select->from($this->_name);
                $select ->joinLeft(array("ge"=>"master_ge"),"ge.ge_id=$this->_name.ge_id");
                $select->joinLeft(array("core"=>"core_course_master"),"core.ge_id=$this->_name.ge_id");
                
                //$select->where('status_id IN(?)', $data);
                
                $select->where("$this->_name.department IN(?)",  explode(',',$id));
                $select->where("core.academic_year_id IN(?)",explode(',',$academic_id));
                $select->where("ge.ge_id !=?", 5);
                if($term_id)
                $select->where("core.term_id=?", $term_id);
                
                if($ge_id)
                $select->where("core.cc_id=?", $ge_id);
                
               // $select->group(array("core.cc_id"));
               
                $select->where("$this->_name.status !=?", 2);
                $select->group("ge.ge_id");
                // echo $select;die;
             
        $result = $this->getAdapter()
                ->fetchAll($select);
                 
        return $result;
    }
    //end
}