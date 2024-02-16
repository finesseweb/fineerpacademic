<?php
/* 
    Author: Kedar Kumar
    Date: 21 Oct. 2019
    Summary: This model is made for exam schedule
*/
class Application_Model_ExamScheduleModel extends Zend_Db_Table_Abstract {

    public $_name = 'exam_schedule';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
     public function getRecordBysession($id ='' ,$batch_id ='') {
         
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("acad"=>"academic_master"),"acad.session=$this->_name.session_id")
                ->where("acad.academic_year_id=?", $batch_id)
                ->where("$this->_name.session_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchAll($select);
       
        return $result;
    }
  
    public function getRecords() {

         $select=$this->_db->select()
            ->from($this->_name)
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=$this->_name.cmn_terms",array("term_name"))
            ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
            ->joinleft(array("course_cat"=>"course_category_master"),"course_cat.cc_id=$this->_name.cc_id",array("cc_name"))
            ->joinleft(array("degree_info"),"degree_info.id=$this->_name.degree_id",array("degree"))
            
            
            //->where("$this->_name.attend_status !=?",1)
            ->where("$this->_name.status !=?",2);
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchAll($select);  
            //echo"<pre>";print_r($result);die;
        return $result;
    }
    public function getDurationFromList(){
        $select = $this->_db->select()
		->from('duration', array('id','duration_from'))				
				->where("status =?",0)
                ->order('id  DESC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[date('h:i a ',strtotime($val['duration_from']))] = date('h:i a ',strtotime($val['duration_from']));
		
        }
        return $data;
    }
    public function getDurationToList(){
        $select = $this->_db->select()
          
		->from('duration', array('id','duration_to'))				
				->where("status =?",0)
                ->order('id  DESC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			//date('h:i:s a m/d/Y', strtotime($date));
			$data[date('h:i a ',strtotime($val['duration_to']))] = date('h:i a ',strtotime($val['duration_to']));
		
        }
        return $data;
    }
    //To check already exists data in exam_schedule table
    public function getDataExists($course_id='',$exam_date=''){
       
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('course_id = ?',$course_id)
            ->where('exam_date = ?',$exam_date);
        //echo $select;die;
        $result = $this->getAdapter()
            ->fetchRow($select);
            //echo"<pre>";print_r($result);
        return $result; 
        
    }
    
    
    public function getDropDownList(){
        $select = $this->_db->select()
		->from($this->_name, array('id','exam_date'))				
				->where("status =?",0)
                ->order('id  DESC')
                 ->group("exam_date");
        $result = $this->getAdapter()->fetchAll($select);
        //echo"<pre>";print_r($result);die;
       $data = array();
		
        foreach ($result as $val) {
			//print_r($val);die;
			$data[$val['exam_date']] = $val['exam_date'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
        
        
      
    }
    
    public function getcourseById($id){
        $select = $this->_db->select()
                ->from($this->_name , array('course_id'))
                ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
                ->where("$this->_name.exam_date=?", $id);
              
        $result = $this->getAdapter()
                ->fetchAll($select);
         //echo"<pre>";print_r($result);die;
        return $result;
    }
    
    
      public function getDateByCourseId($id){
        $select = $this->_db->select()
                ->from($this->_name , array('exam_date'))
                               ->where("$this->_name.course_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchAll($select);
         //echo"<pre>";print_r($result);die;
        return $result[0]['exam_date'];
    }
    
}
