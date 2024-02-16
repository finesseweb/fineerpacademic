<?php
/* 
    Author: Raushan Kumar
    Date: 25  Oct. 2019
    Summary: This model is made for exam Form Submit Records
*/
class Application_Model_FeesCollection extends Zend_Db_Table_Abstract {

    public $_name = 'semester_fee_collection';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
          //  echo $select; die;  
        $result = $this->getAdapter()
                ->fetchRow($select);
            
        return $result;
    }
    
    
    public function getRecordbyfid($id) {
        $select = $this->_db->select()
                ->from($this->_name)
              
                ->where("$this->_name.id=?", $id)
                ->where("$this->_name.status=?", 1)
                ->where("$this->_name.f_code !=?", 'F')
                ->where("$this->_name.mmp_txn !=?",'')
                ->where("$this->_name.mmp_txn !=?",0)
               
                ->order("$this->_name.id  DESC");
              
        $result = $this->getAdapter()
                ->fetchRow($select);
         //echo"<pre>";print_r($result);die;
        return $result;
    }
    
    
    
        public function getTRecordb($id) {
        $select = $this->_db->select()
                ->from($this->_name)
              
                ->where("$this->_name.stu_id=?", $id)
                ->where("$this->_name.mmp_txn !=?",'')
                ->where("$this->_name.mmp_txn !=?",0)
               
                ->order("$this->_name.id  DESC");
              
        $result = $this->getAdapter()
                ->fetchAll($select);
         //echo"<pre>";print_r($result);die;
        return $result;
    }
    
    
    public function getPayRecords($id,$sem){
          $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id=?", $id)
                ->where("$this->_name.semester=?", $sem)
                ->where("$this->_name.status=?", 1)
                ->order("id desc");
              
        $result = $this->getAdapter()
                ->fetchAll($select);
          //echo"<pre>";print_r($result);die;
        return $result;
    }
    
    
        public function getPayRecordsBytxn($id){
          $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.mmp_txn=?", $id)
                ->where("$this->_name.stu_id like ?", "%F-2020%")
                ->order("id desc");
              
        $result = $this->getAdapter()
                ->fetchRow($select);
          //echo"<pre>";print_r($result);die;
        return $result;
    }
    
       public function getPayRecordsByMerSingle($id,$txn=array(),$date){
          $select = $this->_db->select();
                $select->from($this->_name);
                $select->where("$this->_name.merchant_id=?", $id);
                if(count($txn)>0)
                $select->where("$this->_name.mmp_txn not in (?)", $txn);
                $select->where("$this->_name.f_code =?","E000" );
                $select->where("$this->_name.submit_date = ?", $date);
                $select->order("id Desc");
        $result = $this->getAdapter()
                ->fetchRow($select);
          //echo"<pre>";print_r($result);die;
        return $result;
    }
    //To get filter record : Kedar : 22 Oct 2020
       public function getPayRecordsByMerSingleForFilterData($to_date,$from_date,$dept,$sem){
        $start_date = explode('/',$to_date);
        $toDate = $start_date[2]."-".$start_date[1]."-".$start_date[0];         
        $end_date =explode('/',$from_date);
        $fromDate = $end_date[2]."-".$end_date[1]."-".$end_date[0]; 
        //echo '<pre>'; print_r($fromDate);exit;
          $select = $this->_db->select();
                $select->from($this->_name);
                $select->joinleft(array("department"),"department.id=$this->_name.department",array("department"));
                $select->joinleft(array("declared_terms"),"declared_terms.term_des=$this->_name.semester",array("term_name"));
                $select->where("$this->_name.f_code =?","E000" );
                $select->where("$this->_name.status =?",1 );
                if(!empty($dept))
                $select->where("$this->_name.department =?", $dept);
                if(!empty($sem))
                $select->where("$this->_name.semester =?",$sem );
                $select->where("$this->_name.submit_date <=?", $toDate);
                $select->where("$this->_name.submit_date >=?", $fromDate);
                $select->order("id ASC");
        $result = $this->getAdapter()
                ->fetchAll($select);
            //echo $select;die;
            //echo"<pre>";print_r($result);die;
        return $result;
    }
       public function getStudentFeeInstallmentRecords($dept,$sem){
          $select = $this->_db->select();
                $select->from($this->_name,array('name','stu_id','batch','fee','claas_roll','due_amount','exam_id','description','sum(fee) as paid_fees','count(description) as total_installment'));
                $select->joinleft(array("department"),"department.id=$this->_name.department",array("department"));
                $select->joinleft(array("declared_terms"),"declared_terms.term_des=$this->_name.semester",array("term_name"));
                //$select->where("$this->_name.f_code =?","E000" );
                $select->where("$this->_name.status =?",1 );
                if(!empty($dept))
                $select->where("$this->_name.department =?", $dept);
                 if(!empty($sem))
                $select->where("$this->_name.semester =?",$sem );
                $select->group("stu_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
            //echo $select;die;
          //echo"<pre>";print_r($result);die;
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
    
}
