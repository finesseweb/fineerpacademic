<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_HRMUserModel extends Application_Model_GlobalDbModel
{
	    protected $_name = 'fa_users';
          
            
    function __construct() {
        parent::__construct('erp');
    }
	
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
                //->joinLeft(array("security"=>"fa_security_roles"),"security.id=fa_users.role_id")
                //->where("fa_users.user_id=?", $username)
                ->where("fa_users.inactive=0");

        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo '<pre>';print_r($result);die;
        return $result;
    }

    public function getInfo($email){
        $select = $this->_db->select()
				->from($this->_name)
				->where("email =?",$email);
				$result = $this->getAdapter()         
                                ->fetchRow($select);  
           //     echo $select; die;
                       return $result;
             
        
    }
}
?>