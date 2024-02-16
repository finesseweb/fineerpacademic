<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Coursefee extends Zend_Db_Table_Abstract
{
    public $_name = 'course_fee';
    protected $_id = 'id';
  
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
	
	public function getRecordByDepartment($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.department=?", $id)				   
	              ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	
    
    public function getColByDepartment($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.department=?", $id)
                 ->where("$this->_name.exam_type=?", 1)	
		 ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
     public function getNonColByDepartment($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.department=?", $id)
                 ->where("$this->_name.exam_type=?", 2)	
		 ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
 public function getExamdatecolRecord($acad,$cmnterm) {
        $select = $this->_db->select()
            ->from($this->_name)
            ->join(array("acad"=>"academic_master"),"acad.department=$this->_name.department",array())
            ->where("$this->_name.status =?", 0)
            ->where("$this->_name.exam_type =?", 1)
            ->where("acad.academic_year_id =?", $acad)
            ->where("$this->_name.cmn_terms =?" ,$cmnterm);
        $result = $this->getAdapter()
            ->fetchRow($select);
        
        //echo $select; die();
        return $result;
    }
    
    
    public function getExamdateNoncolRecord($acad,$cmnterm) {
        $select = $this->_db->select()
            ->from($this->_name)
            ->join(array("acad"=>"academic_master"),"acad.department=$this->_name.department",array())
            ->where("$this->_name.status =?", 0)
            ->where("$this->_name.exam_type =?", 2)
            ->where("acad.academic_year_id =?", $acad)
            ->where("$this->_name.cmn_terms =?" ,$cmnterm);
        $result = $this->getAdapter()
            ->fetchRow($select);
        
        //echo $select; die();
        return $result;
    }
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
            ->from($this->_name)
            ->joinleft(array("term"=>"declared_terms"),"term.term_des=$this->_name.cmn_terms",array("term_name"))
            ->joinleft(array("degree_info"),"degree_info.id=$this->_name.degree_id",array("degree"))
            ->joinleft(array("department"),"department.id=$this->_name.department",array("department"))
            ->where("$this->_name.status !=?", 2)
            ->order("$this->_name.$this->_id desc");
            $result=$this->getAdapter()
            ->fetchAll($select);       
        return $result;
    }
public function getSemRecord()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                  ->joinleft(array("term"=>"term_master"),"term.cmn_terms=$this->_name.cmn_terms",array("term_description"))
                    ->where("$this->_name.status =?", 0)
                    ->where("$this->_name.exam_type =?", 1)
                      ->group("$this->_name.cmn_terms");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
public function getNonSemRecord()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                  ->joinleft(array("term"=>"term_master"),"term.cmn_terms=$this->_name.cmn_terms",array("term_description"))
                    ->where("$this->_name.status =?", 0)
                    ->where("$this->_name.exam_type =?", 2)
                      ->group("$this->_name.cmn_terms");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
    
public function isRecordExists($batch, $term, $paper)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)                      				   
					  ->where("$this->_name.status !=?", 2)
					  ->where("$this->_name.batch =?", $batch)
					  ->where("$this->_name.term =?", $term)
					  ->where("$this->_name.paper =?", $paper);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return count($result);
    }
    
	public function getFee($term, $batch, $paper)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('fee'))                      				   
					  ->where("$this->_name.status !=?", 2)
					  ->where("$this->_name.batch =?", $batch)
					  ->where("$this->_name.term =?", $term)
					  ->where("$this->_name.paper =?", $paper);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result['fee'];
    }
    
    
    
    public function getEndFee($academic)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('examFee'))  
                      ->joinleft(array("acad"=>"academic_master"),"acad.department=$this->_name.department",array())
                 
		      ->where("acad.academic_year_id =?", $academic);
		    
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result['examFee'];
    }
    
     public function getEnddate($sem,$academic)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('feeForm_start_date','feeForm_end_date','feeForm_extended_date'))  
                      ->joinleft(array("acad"=>"academic_master"),"acad.department=$this->_name.department")
                      ->where("$this->_name.cmn_terms =?", $sem)
                       ->where("$this->_name.exam_type =?", 1)
                     ->where("acad.academic_year_id =?", $academic);
		    
        $result=$this->getAdapter()
                      ->fetchRow($select); 
        ///echo $select;die();
        return $result;
    }
    
      public function getNonColEnddate($sem,$academic)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('feeForm_start_date','feeForm_end_date','feeForm_extended_date'))  
                      ->joinleft(array("acad"=>"academic_master"),"acad.department=$this->_name.department")
                      ->where("$this->_name.cmn_terms =?", $sem)
                      ->where("$this->_name.exam_type =?", 2)
                     ->where("acad.academic_year_id =?", $academic);
		    
        $result=$this->getAdapter()
                      ->fetchRow($select); 
        ///echo $select;die();
        return $result;
    }
    
    
     public function getNonEnddate($sem)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('feeForm_start_date','feeForm_end_date','feeForm_extended_date'))  
                      ->where("$this->_name.cmn_terms =?", $sem)
                      ->where("$this->_name.exam_type =?", 2);
                     
		    
        $result=$this->getAdapter()
                      ->fetchRow($select); 
        ///echo $select;die();
        return $result;
    }
    
    
    
    
}