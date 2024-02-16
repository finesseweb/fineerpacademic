<?php
/** 
 * @Framework Zend Framework
 * @category   ERP Product
 *	Authors     Kedar Kumar
 */
class Application_Model_AcademicYear extends Zend_Db_Table_Abstract
{
    public $_name = 'academic_year';
    protected $_id = 'year_id';
  
    //get details by record for edit
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)			   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
    public function getRecordById($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);    
        //echo $select;die;
        return $result;
    }
    public function getAcademicYear($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);    
        //echo $select;die;
        return $result;
    }
    public function getAcadYearId(){
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.active_year=?", 1)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);    
        //echo $select;die;
        return $result['year_id'];
    }
    public function getRecordByIds($values)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('department'))
                      ->where("$this->_name.$this->_id IN(?)",  explode(',',$values))
                      //->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);   
        //echo $select;die;
        return $result;
    }
    public function getDropDownList(){
        $select = $this->_db->select()
		->from($this->_name, array('year_id','academic_year','active_year'))				
				->where("$this->_name.status!=?",2)
                ->order('year_id  DESC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
			//echo"<pre>";print_r($val);exit;
			$data[$val['year_id']] = $val['academic_year'];
			
            
        }
        return $data;
    }
	
	

}
?>