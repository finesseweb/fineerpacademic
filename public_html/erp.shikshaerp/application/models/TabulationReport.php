<?php

/* tabulation_report
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_TabulationReport extends Zend_Db_Table_Abstract
{
    public $_name = 'tabulation_report';
    protected $_id = 'tabl_id';
  

    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
    public function trashItems($tabl_id) {
        $this->_db->delete("tabulation_report_items", "tabl_id=$tabl_id");
    }
    public function trashBackItems($tabl_id) {
        $this->_db->delete("back_tabulation_report_items", "tabl_id=$tabl_id");
    }
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name) 
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	public function getRecordsAcademicTerm($academic_id,$term_id,$paper = 'R')
    {       
                        $select=$this->_db->select()
                        ->from($this->_name,array('tabl_id')) 
					  ->where("$this->_name.academic_id =?", $academic_id)
					  ->where("$this->_name.term_id =?", $term_id)
					  ->where("$this->_name.flag =?", $paper)
					  ->where("$this->_name.status !=?", 2);
                        $result=$this->getAdapter()
                        ->fetchRow($select);       
        return $result;
    }
    
    public function fetchStudentSgpa($academic_id,$term_id, $stu_id){
          $select=$this->_db->select()
                      ->from($this->_name,array('tabl_id','added_date')) 
                                    
                                    ->joinLeft(array('btabl_items'=>'back_tabulation_report_items'),"btabl_items.tabl_id = $this->_name.tabl_id",array("GROUP_CONCAT(btabl_items.tabl_id) as btbl_id"))
		->joinLeft(array('tabl_items'=>'tabulation_report_items'),"tabl_items.tabl_id = $this->_name.tabl_id")			 
                  ->where("$this->_name.academic_id =?", $academic_id)
					  ->where("$this->_name.term_id =?", $term_id)
					  
					  ->where("$this->_name.status !=?", 2)
                  ->where("(tabl_items.student_id =?", $stu_id)
                  ->orWhere("btabl_items.student_id =?)", $stu_id);
                                    $result=$this->getAdapter()
                      ->fetchRow($select);  
                  //echo $select;die;
        return $result;
    }
    public function fetchBackStudentSgpa($academic_id,$term_id, $stu_id){
          $select=$this->_db->select()
                      ->from($this->_name,array('tabl_id','added_date')) 
                                    ->joinLeft(array('tabl_items'=>'back_tabulation_report_items'),"tabl_items.tabl_id = $this->_name.tabl_id")
					  ->where("$this->_name.academic_id =?", $academic_id)
					  ->where("$this->_name.term_id =?", $term_id)
					  ->where("$this->_name.flag =?", 'B')
					  ->where("tabl_items.student_id =?", $stu_id)
					  ->where("$this->_name.status !=?", 2);
                                    $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
        
    }
    //Added by kedar to get term
    public function getTermIdByTablId($tablId)
    {       
        $select=$this->_db->select();
            $select ->from($this->_name,array('term_id','added_date')) ;
            
                $select->where("$this->_name.tabl_id =?", $tablId);
            
            $select->where("$this->_name.status !=?", 2);
        //echo $select;die;
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	public function getTablId($acad,$termId){
        $select=$this->_db->select();
            $select ->from($this->_name,array('tabl_id','added_date','flag')) ;
            
                $select->where("$this->_name.academic_id =?", $acad);
                $select->where("$this->_name.term_id =?", $termId);
            
            $select->where("$this->_name.status !=?", 2);
        //echo $select;die;
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }

}


