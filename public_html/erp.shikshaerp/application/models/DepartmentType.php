<?php

class Application_Model_DepartmentType extends Zend_Db_Table_Abstract {

    public $_name = 'department_type';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id =?", $id);
               // ->where("status=?",0);
        $result = $this->getAdapter()
                ->fetchRow($select);
        //$result['batch_id'] = $this->academic($id);
        return $result;
    }

    
    public function getDepartment($department)
    {
       
          $select = $this->_db->select()
                ->from($this->_name)
                ->where('department_type = ?',$department);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getRecordByDegreeId($degree)
    {
       
          $select = $this->_db->select()
                ->from($this->_name)
                ->where('degree_id = ?',$degree);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRecords() {

        $select = $this->_db->select()
                ->from($this->_name);
                
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getAllUgStream() {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('degree_id = ?',1);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getAllPgStream() {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('degree_id != ?',1);
                
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
        public function getActiveRecords() {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('status=?',1);
            
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    	
    	public function getDepartmentType(){
        $select = $this->_db->select()
		->from('department_type')			
	
                ->order('department_type  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        //echo $select;die;
        $data = array();
        foreach ($result as $val) {
			
			$data[$val['id']] = $val['department_type'];
        }
        return $data;
    }
    
     	public function getDropDownList(){
        $select = $this->_db->select()
		->from($this->_name, array('id','department_type',))				
				->where("status =?",0)
                ->order('department_type  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        //echo $select;die;
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[$val['id']] = $val['department_type'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//echo "<pre>";print_r($data);
        }
        return $data;
    }
    //Added bY Kedar
    public function getIndividualDepartmentType($edit_id,$session_id='') {
        $select = $this->_db->select()
                ->from($this->_name, array('id','department_type','degree_id'));
                $select->where('id = ?',$edit_id);
                if(!empty($session_id))
                $select->where('session_id = ?',$session_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
   public function getAcademicDdetails($dept_type){
        $select = $this->_db->select()
                ->from($this->_name, array('id','session_id'))
                ->where('id = ?',$dept_type);
        $result = $this->getAdapter()
                ->fetchRow($select);
      
        if($result){
                $select = $this->_db->select()
                ->from("department",array("id"))
                ->where("department.department_type =?",$result['id']);

                $result1 = $this->getAdapter()->fetchRow($select);
               
        }  
        
        if($result1){
                $select = $this->_db->select()
                ->from("academic_master",array("academic_year_id"))
                ->where("academic_master.session =?",$result['session_id'])
                ->where("academic_master.department =?",$result1['id']);

                $result2 = $this->getAdapter()->fetchRow($select);
              
        }  
        $result['academic_id']=$result2['academic_year_id'];
        
        return $result;
       
   } 
   //For teacher department
    public function getTeacherDepartment($inserData){
        $select = $this->_db->select()
                ->from('empl_dept', array('id','empl_id'))
                ->where('empl_id = ?',$inserData['empl_id'])
                ->where('dept_id = ?',$inserData['dept_id']);
        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        return $result;
   }
   public function insertTeacherDept($data){
       $query = Zend_Db_Table_Abstract::getDefaultAdapter();
       $query=$this->_db->insert('empl_dept',$data);
       //echo $query;die;
        return $query;  
   }
    public function updateEmplDept($data,$id){
        //echo '<pre>'; print_r($data);exit;
        $where = array(
                'id = ?' => $id
            );
       
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('empl_dept',$data,$where);
        return $query;  
    }
    public function getEmplDeptRecord($id){
        $select = $this->_db->select()
                ->from('empl_dept')
                ->where('id = ?',$id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        return $result;
   }
    public function getEmplDeptRecords(){
        $select = $this->_db->select()
                ->from('empl_dept')
                ->joinleft(array("dept"=>"department"),"dept.id=empl_dept.dept_id",array("department"))
                ->where('empl_dept.status = ?',0);
                //->joinleft(array("fa_users"=>"fa_kv_empl_info"),"fa_users.empl_id=empl_dept.empl_id",array("empl_firstname"));
        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo $select;die;
        return $result;
   }
    public function getDegreeOnStream($stream){
        $select = $this->_db->select()
                ->from($this->_name,array('degree_id'))
                ->where('id = ?',$stream);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
}