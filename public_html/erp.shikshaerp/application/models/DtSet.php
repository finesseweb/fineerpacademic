<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_DtSet extends Zend_Db_Table_Abstract
{
    public $_name = 'dtablesetting';
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
    
    public function getSettings($url){
        $pos = strpos($url, 'type');
                if($pos){
                    $url = substr($url, 0, $pos-1);
                }
      $select=$this->_db->select()
                      ->from($this->_name,array('settings'))
                      ->where("$this->_name.url like (?)", "%{$url}%")				   
		      ->where("$this->_name.status !=?", 2);
		    //  echo $select; die;
        $result=$this->getAdapter()
                      ->fetchRow($select);  
        return $result['settings'];
        
        
    }
    
    
      public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('id', 'url',))
                ->where("status =?", 0)
                ->order('id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['id']] = $val['url'];
        }
        return $data;
    }
    
    
  

}
?>