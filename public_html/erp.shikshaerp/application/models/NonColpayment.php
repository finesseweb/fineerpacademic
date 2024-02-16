<?php
/* 
    Author: Raushan Kumar
    Date: 25  Oct. 2019
    Summary: This model is made for exam Form Submit Record
*/
class Application_Model_NonColpayment extends Zend_Db_Table_Abstract {

    public $_name = 'ugnon_payment_details';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
    public function getRecordbyfid($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("ugnon"=>"ugnon_form_submission"),"ugnon.id =$this->_name.payment_id")
                ->where("md5($this->_name.u_id)=?", $id)
                ->where("$this->_name.status=?", 1)
                ->where("$this->_name.f_code !=?", 'F')
                ->where("$this->_name.mmp_txn !=?",'')
                ->where("$this->_name.mmp_txn !=?",0)
                ->where("ugnon.payment_status =?",1)
                 //->where("ugnon.status =?",0)
                ->order("$this->_name.id  DESC");
              
        $result = $this->getAdapter()
                ->fetchRow($select);
         //echo $select ; die;
        return $result;
    }
    
    
    
    
    public function getPayRecords($id){
          $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.u_id=?", $id)
                ->order("id desc");
              
        $result = $this->getAdapter()
                ->fetchAll($select);
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
