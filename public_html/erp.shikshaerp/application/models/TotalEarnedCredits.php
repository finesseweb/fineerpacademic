<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Model_TotalEarnedCredits extends Zend_Db_Table_Abstract
{
    public $_name = 'academic_credits';
    protected $_id = 'id';
   
    
    	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('credit_number',"id")) 
                    ->joinleft(array('academics'=>'academic_master'),"academics.academic_year_id=$this->_name.academic_id",array('short_code'))
                      ->joinleft(array('department'),"department.id=academics.department",array('department'))
                      ->joinleft(array('degree_info'),"degree_info.id=department.degree_id",array('degree'))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
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
    public function checkExistedData($session){
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.session=?", $session)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
    public function getRecordAcademic($id){
         $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
    
        public function getDropDownList(){
            $select = $this->_db->select()
            ->from($this->_name, array('ge_id','general_elective_name',))				
                    ->where("status =?",0)
                    ->order('general_elective_name  ASC');
            $result = $this->getAdapter()->fetchAll($select);
            $data = array();
            foreach ($result as $val) {

                $data[$val['ge_id']] = $val['general_elective_name'];
            }
            return $data;
        }
        public function getDropDownListForcourseGroup(){
            $select = $this->_db->select()
            ->from($this->_name, array('ge_id','general_elective_name',))				
                    ->where("status =?",0)
                    ->order('general_elective_name  ASC');
            $result = $this->getAdapter()->fetchAll($select);
            $data = array();
            foreach ($result as $val) {

                $data[$val['ge_id']] = $val['general_elective_name'];
            }
            return $data;
    }
    
    
}
