<?php
/* 
 Author: Kedar Kumar
 Summary: This model is used for pwc entrance exam Applicant Information.
 Date: 16 Dec. 2019
*/
//ini_set('display_errors', '1');
class Application_Model_AnnounceResultModel extends Zend_Db_Table_Abstract {

    public $_name = 'announce_result_master';
    protected $_id = 'id'; 
    //This function gets all promotion rule data
    public function getRecords(){       
       
       
    } 
    public function getRecordById($id)
    {       
        $select=$this->_db->select()
            ->from($this->_name)
            ->where("$this->_name.id=?", $id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function insertResultItem($decalredStudentData){
        
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->insert('announce_result_items',$decalredStudentData);
       //echo $query;die;
        return $query; 
    }
    public function checkInsertData($id){
         $select = $this->_db->select();
        $select->from('announce_result_items');
            $select->where('master_id = ?',$id);
        $result = $this->getAdapter()
           
            ->fetchRow($select);
        return $result;
    }
    public function getAllDeclaredList(){
        $select=$this->_db->select()
            ->from($this->_name)		   
         ->joinleft(array("department_type"),"department_type.id=announce_result_master.department_type",array("department_type"));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function getAllDeclaredListItem($e_id){
        $select=$this->_db->select()
            ->from('announce_result_items')
             
         ->join(array("announce_result_master"),"announce_result_master.id=announce_result_items.master_id",array(""))
            ->joinleft(array("department_type"),"department_type.id=announce_result_master.department_type",array("department_type"))
         ->join(array("applicant_educational_details"),"applicant_educational_details.application_no= trim(announce_result_items.stu_id)",array("applicant_educational_details.applicant_name","applicant_educational_details.caste_category"))
         ->join(array("applicant_payement_details"),"applicant_payement_details.application_no= trim(announce_result_items.stu_id)",array("applicant_payement_details.roll_no"))
            
       ->where('applicant_payement_details.payment_status = ?',1)
       ->where('announce_result_items.master_id = ?',$e_id);
            $result=$this->getAdapter()
            ->fetchAll($select);  
           //echo $select;die;
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function checkCutofflistEntry($dept_id,$year_id,$list){
        $select=$this->_db->select()
            ->from($this->_name)
            ->where("$this->_name.department_type=?", $dept_id)			   
            ->where("$this->_name.acad_year_id=?", $year_id)			   
            ->where("$this->_name.cutoff_list=?", $list);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function getDeleteMasterId($delete_id){
         $select=$this->_db->select()
            ->from($this->_name,array('id'))
            ->where("$this->_name.id=?", $delete_id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function getDeleteId($delete_id){
         $select=$this->_db->select()
            ->from('announce_result_items',array('id'))
            ->where("announce_result_items.id=?", $delete_id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function deleteAnnouncedResults($delete_id){
        $this->_db->delete("announce_result_master", "id = $delete_id");
         
        $this->_db->delete("announce_result_items", "master_id = $delete_id");
    }
    public function deleteAnnounceItemResults($delete_id){
         
        $this->_db->delete("announce_result_items", "id = $delete_id");
    }
    public function  checkUploadedId($stu_id){
        $select=$this->_db->select()
           ->from('documents_for_admission',array('application_no'))
           ->where("trim(documents_for_admission.application_no)=?", $stu_id)	
          ->where("documents_for_admission.status =?" , 2);

           $result=$this->getAdapter()
           ->fetchRow($select);   
           //echo $select;die;
           //echo"<pre>";print_r($result);die;	  
        return $result;
    }

    public function validateStuId($stu_id){
        $select=$this->_db->select()
           ->from('announce_result_items',array('stu_id'))
           ->where("trim(announce_result_items.stu_id)=?", $stu_id);			   

           $result=$this->getAdapter()
           ->fetchRow($select);   
           //echo $select;die;
           //echo"<pre>";print_r($result);die;	  
       return $result;
    }
    public function studentDetails($app_id){
        $select=$this->_db->select()
            ->from('applicant_course_details')
            ->joinleft(array("department_type"),"department_type.id=applicant_course_details.course",array("department_type","id as dept_id"))
            
       ->where("md5(applicant_course_details.application_no) = ?",$app_id);
            $result=$this->getAdapter()
            ->fetchRow($select);  
           //echo $select;die;
           //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function documentDetails($app_id){
        $select=$this->_db->select()
            ->from('documents_for_admission')
                  
            ->where("md5(documents_for_admission.application_no) = ?",$app_id);
            $result=$this->getAdapter()
            ->fetchRow($select);  
           //echo $select;die;
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //To insert documents upload
    public function checkExistedEntry($a_id){
        $select=$this->_db->select()
            ->from('documents_for_admission')
            ->where("documents_for_admission.application_no=?", $a_id);
            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function insertDocuments($data){
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->insert('documents_for_admission',$data);
       //echo $query;die;
        return $query; 
    }
    public function  updateDocuments($data){
        $where = array(
                'application_no = ?' => $data['application_no']
            );
        
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('documents_for_admission',$data,$where);
        return $query; 
    }
    public function confirmFileUpload($a_id){
         $where = array(
                'application_no = ?' => $a_id,
            );
        $data= array(   
            'status' =>2
        );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('documents_for_admission',$data,$where);
        return $query; 
    }
    public function getAllApplicantDocumentList($degree_id,$dept_id){
        $select=$this->_db->select()
            ->from('documents_for_admission')
             ->joinleft(array("department_type"),"department_type.id=documents_for_admission.department",array("department_type"))    
             ->join(array("applicant_course_details"),"applicant_course_details.application_no= documents_for_admission.application_no",array("applicant_course_details.applicant_name","applicant_course_details.form_id"))
             ->join(array("applicant_payement_details"),"applicant_payement_details.application_no= documents_for_admission.application_no",array("applicant_payement_details.roll_no"))
            ->where("applicant_payement_details.payment_status = ?",1)
            ->where("documents_for_admission.degree_id = ?",$degree_id)
            ->where("documents_for_admission.department = ?",$dept_id);
            $result=$this->getAdapter()
            ->fetchAll($select);  
            //echo $select;die;
            //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //To unblock Doc Status
    public function getDetailByAppId($a_id){
        $select=$this->_db->select()
            ->from('documents_for_admission')
             ->joinleft(array("department_type"),"department_type.id=documents_for_admission.department",array("department_type"))    
             ->join(array("applicant_course_details"),"applicant_course_details.application_no= documents_for_admission.application_no",array("applicant_course_details.applicant_name","applicant_course_details.form_id"))
             ->join(array("applicant_payement_details"),"applicant_payement_details.application_no= documents_for_admission.application_no",array("applicant_payement_details.roll_no"))
            ->where("applicant_payement_details.payment_status = ?",1)
            ->where("documents_for_admission.application_no = ?",$a_id);
            $result=$this->getAdapter()
            ->fetchRow($select);  
            //echo $select;die;
           //echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function updateDocStatus($a_id){
        $where = array(
                'application_no = ?' => $a_id
            );
        $data= array(
            'status'=>0
        );
        
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('documents_for_admission',$data,$where);
        return $query; 
    
    }
}

?>