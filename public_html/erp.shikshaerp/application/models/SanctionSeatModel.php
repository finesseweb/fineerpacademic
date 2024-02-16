<?php
class Application_Model_SanctionSeatModel extends Zend_Db_Table_Abstract {



    public $_name = 'sanctioned_seat';

    protected $_id = 'id';



    public function getRecordById($id) {

        $select = $this->_db->select()

                ->from($this->_name)

                ->where("$this->_name.$this->_id =?", $id);

               // ->where("status=?",0);

        $result = $this->getAdapter()

                ->fetchRow($select);

        return $result;

    }
    public function getRecords() {

        $select = $this->_db->select()

                ->from($this->_name)
                ->joinLeft(array("dept_type"=>"department_type"),"dept_type.id=$this->_name.course",array('department_type'))
                ->joinLeft(array("ge"=>"master_ge"),"ge.ge_id=$this->_name.generic_elective",array('general_elective_name'))
                ->joinLeft(array("am"=>"academic_master"),"am.academic_year_id=$this->_name.core_course",array('department'))
                ->order("$this->_name.course")
                //->where("$this->_name.$this->_id =?", $id);

                ->where("sanctioned_seat.status=?",0);
            
        $result = $this->getAdapter()->fetchAll($select);
         //echo"<pre>";print_r($result);exit;
         foreach ($result as $key => $value) {
            //echo '<pre>';print_r($value['department']); 
             if($value['department']){
                $select = $this->_db->select()
                ->from('department',array('department'))
                ->where("department.id=?", $value['department']);
                    //echo $select;
                $result1 = $this->getAdapter()
                ->fetchAll($select); 
        
                $result[$key]['department_name'] = $result1[0]['department'];
             }
         }
        

        return $result;
    }
    
    public function checkExistedEntry($dept,$core_course='',$ge_id=''){
        //echo '<pre>'; print_r($core_course);exit;
        if(!empty($dept) && $core_course ==0 && !empty($ge_id)){
            
            $select = $this->_db->select()
                ->from($this->_name ,array('generic_elective'))
                ->where("$this->_name.course =?", $dept)
                ->where("$this->_name.core_course =?", $core_course)
                ->where("$this->_name.generic_elective =?", $ge_id);
            $result = $this->getAdapter()
                ->fetchRow($select);
                //echo $select;
               return $result;
        }elseif (!empty ($dept) && !empty ($core_course)) {
            $select = $this->_db->select()
                ->from($this->_name, array('core_course'))
                ->where("$this->_name.course =?", $dept)
                ->where("$this->_name.core_course=?", $core_course)
                ->where("$this->_name.generic_elective =?", 0);
                //echo $select;
            $result = $this->getAdapter()
                ->fetchRow($select);
               return $result;
        }else{
            $select = $this->_db->select()
                ->from($this->_name, array('course'))
                ->where("$this->_name.course =?", $dept)
                ->where("$this->_name.core_course=?", 0)
                ->where("$this->_name.generic_elective =?", 0);
            $result = $this->getAdapter()
                ->fetchRow($select);
               //echo $select;
               return $result;
        }
        
    }
    //For Ge count
    public function getRecordByindividualGenericElectiveSeatCount($course_id,$ge_id,$field_name = "generic_elective")
    {       
        $select=$this->_db->select()
            ->from($this->_name,array("max_seat") )
               
            ->where("$this->_name.course=?",$course_id)
            ->where("$this->_name.core_course=?",0)
            ->where("$this->_name.$field_name=?",$ge_id );
            // ->where("record.f_code like ?", 'ok');
            //echo $select;die;
            $result=$this->getAdapter()
            ->fetchRow($select);    
  		//echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //End
    public function checkExistedData($form_id) {
        $select=$this->_db->select();
        $select->from(array('applicant_documents_followup'),
            array('form_id','certificate_list'));
        $select->where("applicant_documents_followup.form_id=?",$form_id);
         //echo $select;die;
        $result=$this->getAdapter()
        ->fetchRow($select); 
        
         //  echo"<pre>";print_r($result);die;	  
        return $result;
    }
    public function insertDocuments($docArray,$form_id,$app_id,$course_id){
        //echo '<pre>';print_r(implode(',',$docArray));exit;
        $stackData=implode(',',$docArray);
        $dataArray=array(
            'form_id' =>$form_id,
            'application_no' =>$app_id,
            'course' =>$course_id,
            'certificate_list' =>$stackData,
            'submit_date' =>date("Y-m-d")
        );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->insert('applicant_documents_followup',$dataArray);
        return $query;
    }    
    public function updateDocuments($docArray,$form_id,$app_id){
        //echo '<pre>';print_r(implode(',',$docArray));exit;
        $stackData=implode(',',$docArray);
        $where = array(
                'form_id = ?' => $form_id
            );
        $dataArray=array(
            'certificate_list' =>$stackData,
            'application_no' =>$app_id,
            'update_date' =>date("Y-m-d")
        );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('applicant_documents_followup',$dataArray,$where);
        return $query;  
       
    } 
    public function updatePrincipalStatus($form_id,$buttonValue){
        
        $where = array(
                'form_id = ?' => $form_id
            );
        $dataArray=array(
            'principal_status' =>$buttonValue
            
        );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('applicant_documents_followup',$dataArray,$where);
        return $query;  
       
    }
    
    public function getRecordByPrincipalStatus($status_filter,$course){
        $select=$this->_db->select()
            ->from('applicant_documents_followup')	 
->join(array("applicant_payement_details"),"applicant_payement_details.application_no=applicant_documents_followup.application_no") 
            //->where("$this->_name.application_no=?", $application_id);
            ->where("applicant_payement_details.payment_status=?", 1)
            ->where("applicant_documents_followup.principal_status=?", $status_filter) 
            ->where("applicant_documents_followup.course=?", $course);	 
            $result=$this->getAdapter()
            ->fetchAll($select);  
            //echo $select;die;
            //echo"<pre>";print_r($result);die;	  
        return $result;
        
    }
    public function getRecordByAccountStatus($status_filter,$course){
        $select=$this->_db->select()
            ->from('applicant_documents_followup')	 
->join(array("applicant_payement_details"),"applicant_payement_details.application_no=applicant_documents_followup.application_no") 
            ->joinLeft(array('personals'=>'applicant_personal_details'),"personals.application_no = applicant_documents_followup.application_no",array('father_name','dob_date'))
            //->where("$this->_name.application_no=?", $application_id);
            ->where("applicant_payement_details.payment_status=?", 1)
            ->where("applicant_documents_followup.principal_status=?", 1)	
            ->where("applicant_documents_followup.fee_slip=?", $status_filter)	
            ->where("applicant_documents_followup.course=?", $course);	 
            $result=$this->getAdapter()
            ->fetchAll($select);  
           //echo $select;die;
        //echo"<pre>";print_r($result);die;	  
        return $result;
        
    }
    
    
    public function getAllScrutinizedUgCourseCount(){       
        $select=$this->_db->select()
            ->from('applicant_documents_followup',array('course','count(form_id) as total_count'))   

            ->joinleft(array("department_type"),"department_type.id=applicant_documents_followup.course",array("department_type","session_id"))
            
            ->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_documents_followup.course",array("max_seat"))
            
            ->where("seat.core_course=?",0)
            ->where("seat.generic_elective=?",0)
            ->where("department_type.degree_id=?",1)
            ->group('applicant_documents_followup.course')
            ->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    
     public function getAllScrutinizedPgCourseCount(){       
        $select=$this->_db->select()
            ->from('applicant_documents_followup',array('course','count(form_id) as total_count'))   

            ->joinleft(array("department_type"),"department_type.id=applicant_documents_followup.course",array("department_type","session_id"))
            
            ->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_documents_followup.course",array("max_seat"))
            
            ->where("seat.core_course=?",0)
            ->where("seat.generic_elective=?",0)
            ->group('applicant_documents_followup.course')
            ->where("department_type.degree_id !=?",1)
            ->order(array('department_type.degree_id'));
           
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
   
    public function getAllApprovedUgCourseCount($yearId){
        $select=$this->_db->select();
            $select->from('applicant_documents_followup',array('course','count(form_id) as total_count'));  

            $select->joinleft(array("department_type"),"department_type.id=applicant_documents_followup.course",array("department_type","session_id"));
            
            $select->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_documents_followup.course",array("max_seat"));
            
            $select->where("seat.core_course=?",0);
            $select->where("seat.generic_elective=?",0);
            $select->where("department_type.degree_id=?",1);
            $select->where('applicant_documents_followup.principal_status=?',1);
            if(!empty($yearId)){
            $select->where('applicant_documents_followup.acad_year_id=?',$yearId);
            }
            $select->group('applicant_documents_followup.course');
            $select->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    public function getAllApprovedPgCourseCount($yearId){
        $select=$this->_db->select();
            $select->from('applicant_documents_followup',array('course','count(form_id) as total_count'));   

            $select->joinleft(array("department_type"),"department_type.id=applicant_documents_followup.course",array("department_type","session_id"));
            
            $select->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_documents_followup.course",array("max_seat"));
            
            $select->where("seat.core_course=?",0);
            $select->where("seat.generic_elective=?",0);
            $select->where("department_type.degree_id !=?",1);
            $select->where('applicant_documents_followup.principal_status=?',1);
            $select->where('applicant_documents_followup.acad_year_id=?',$yearId);
            $select->group('applicant_documents_followup.course');
            $select->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    //All payslip generated students
    public function getSlipGeneratedUgCourseCount($yearId){
        $select=$this->_db->select();
            $select->from('applicant_documents_followup',array('course','count(form_id) as total_count'));   

            $select->joinleft(array("department_type"),"department_type.id=applicant_documents_followup.course",array("department_type","session_id"));
            
            $select->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_documents_followup.course",array("max_seat"));
            
            $select->where("seat.core_course=?",0);
            $select->where("seat.generic_elective=?",0);
            $select->where("department_type.degree_id=?",1);
            $select->where('applicant_documents_followup.principal_status=?',1);
            $select->where('applicant_documents_followup.acad_year_id=?',$yearId);
            $select->where('applicant_documents_followup.fee_slip=?',1);
            $select->group('applicant_documents_followup.course');
            $select->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    
    public function getSlipGeneratedPgCourseCount($yearId){
        $select=$this->_db->select();
            $select->from('applicant_documents_followup',array('course','count(form_id) as total_count'));   

            $select->joinleft(array("department_type"),"department_type.id=applicant_documents_followup.course",array("department_type","session_id"));
            
            $select->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_documents_followup.course",array("max_seat"));
            
            $select->where("seat.core_course=?",0);
            $select->where("seat.generic_elective=?",0);
            $select->where("department_type.degree_id !=?",1);
            $select->where('applicant_documents_followup.principal_status=?',1);
            $select->where('applicant_documents_followup.acad_year_id=?',$yearId);
            $select->where('applicant_documents_followup.fee_slip=?',1);
            $select->group('applicant_documents_followup.course');
            $select->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    
    //All applicant details function
    public function getAllPaidUgCourseCount(){
        $select=$this->_db->select()
            ->from('applicant_documents_followup',array('course','count(form_id) as total_count'))   

            ->joinleft(array("department_type"),"department_type.id=applicant_documents_followup.course",array("department_type","session_id"))
            
            ->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_documents_followup.course",array("max_seat"))
            
            ->where("seat.core_course=?",0)
            ->where("seat.generic_elective=?",0)
            ->where("department_type.degree_id=?",1)
            ->where('applicant_documents_followup.fee_status=?',1)
            ->group('applicant_documents_followup.course')
            ->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    //For Entrance Report
   public function getAllFinalUgCourseCount($yearId){
        $select=$this->_db->select();
            $select->from('applicant_paymode_details',array('course','count(form_id) as total_count'," count(case pay_mode2 when '1' then 1 else null end) as'Online'","count(case pay_mode2 when '2' then 1 else null end) as'DD'","count(case pay_mode2 when '3' then 1 else null end) as'NEFT'","count(case pay_mode2 when '4' then 1 else null end) as'RTGS'"));  

           $select->joinleft(array("department_type"),"department_type.id=applicant_paymode_details.course",array("department_type","session_id"));
            
            $select->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_paymode_details.course",array("max_seat"));
            
            
            $select->where("seat.core_course=?",0);
            $select->where("seat.generic_elective=?",0);
            $select->where("department_type.degree_id=?",1);
            $select->where('applicant_paymode_details.acad_year_id=?',$yearId);
            $select->group('applicant_paymode_details.course');
            $select->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    
    public function getAllPaidPgCourseCount(){
        $select=$this->_db->select()
            ->from('applicant_documents_followup',array('course','count(form_id) as total_count'))   

            ->joinleft(array("department_type"),"department_type.id=applicant_documents_followup.course",array("department_type","session_id"))
            
            ->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_documents_followup.course",array("max_seat"))
            
            ->where("seat.core_course=?",0)
            ->where("seat.generic_elective=?",0)
            ->where("department_type.degree_id !=?",1)
            ->where('applicant_documents_followup.fee_slip=?',1)
            ->group('applicant_documents_followup.course')
            ->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
           // echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    public function getAllFinalPgCourseCount($yearId){
        $select=$this->_db->select()
           ->from('applicant_paymode_details',array('course','count(form_id) as total_count'," count(case pay_mode2 when '1' then 1 else null end) as'Online'","count(case pay_mode2 when '2' then 1 else null end) as'DD'","count(case pay_mode2 when '3' then 1 else null end) as'NEFT'","count(case pay_mode2 when '4' then 1 else null end) as'RTGS'"))  
            ->joinleft(array("department_type"),"department_type.id=applicant_paymode_details.course",array("department_type","session_id"))
            
            ->joinleft(array('seat'=>'sanctioned_seat'),"seat.course = applicant_paymode_details.course",array("max_seat"))
            
            ->where("seat.core_course=?",0)
            ->where("seat.generic_elective=?",0)
            ->where("department_type.degree_id !=?",1)
            ->where('applicant_paymode_details.acad_year_id=?',$yearId)
            ->group('applicant_paymode_details.course')
            ->order(array('department_type.degree_id'));
            $result=$this->getAdapter()
            ->fetchAll($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
   
    //End
    public function updateFeeSlipStatus($form_id,$buttonValue){
        
        $where = array(
                'form_id = ?' => $form_id
            );
        $dataArray=array(
            'fee_slip' =>$buttonValue
            
        );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('applicant_documents_followup',$dataArray,$where);
        //echo $query;die;
        return $query;  
       
    }
    public function checkData($form_id,$fund_type){
        $select=$this->_db->select()
            ->from('applicant_fee_account',array('slip_account_type','id'))   

            
            ->where('applicant_fee_account.form_id=?',$form_id)
            ->where('applicant_fee_account.slip_account_type=?',$fund_type);
            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo $select; die;
            //echo"<pre>";print_r($result);exit;
            return $result;
    }
    
    
        public function checkFeeSlpGenerated($form_id,$semester){
        $select=$this->_db->select()
            ->from('applicant_fee_account',array('group_concat(slip_account_type) as fund_type'))   
->join(array("applicant_documents_followup"),"applicant_documents_followup.form_id=applicant_fee_account.form_id")
            
            ->where('applicant_fee_account.form_id=?',$form_id)
            ->where('applicant_documents_followup.fee_slip=?',1)
            ->where('applicant_fee_account.cmn_terms=?',$semester);
        // echo $select; die;
            $result=$this->getAdapter()
            ->fetchRow($select);    
            //echo $select; die;
       //  echo"<pre>";print_r($result);exit;
            return $result;
    }
    
    
    public function updateFeeSlip($fund_type,$update_id,$feeData){
        
        $where = array(
                'id = ?' => $update_id
            );
        $dataArray=array(
            'account1'=>$feeData['account_name1'],
            'total_fee1'=>$feeData['totalfee1'],
            'slip_account_type' =>$fund_type
            
        );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('applicant_fee_account',$dataArray,$where);
       // echo $query;die;
        return $query;  
       
    }
    public function insertFeeSlip($form_id,$fund_type,$feeData){
        
        //echo '<pre>'; print_r($feeData);exit;
        $dataArray=array(
           
            'form_id' =>$form_id,
            'cmn_terms' =>'t1',
            'account1'=>$feeData['account_name1'],
            'total_fee1'=>$feeData['totalfee1'],
            'slip_account_type' =>$fund_type
            
        );
       
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->insert('applicant_fee_account',$dataArray);
       //echo $query;die;
        return $query;  
       
    }
    //For pay mode details
    public function checkExistedPayModeData($form_id) {
        $select=$this->_db->select();
        $select->from(array('applicant_paymode_details'),
            array('form_id'));
        $select->where("applicant_paymode_details.form_id=?",$form_id);
         //echo $select;die;
        $result=$this->getAdapter()
        ->fetchRow($select); 
        
         //  echo"<pre>";print_r($result);die;	  
        return $result;
    }
    //Students details
    public function getStudentDetails($app_id){
        
        $select=$this->_db->select();
        $select->from(array('applicant_paymode_details'),
            array('form_id','class_roll'));
        $select->joinLeft(array('course'=>'applicant_course_details'),"course.application_no = applicant_paymode_details.application_no",array('degree_id','course','core_course1','ge1','aecc1'));
        $select->joinLeft(array('personals'=>'applicant_personal_details'),"personals.application_no = applicant_paymode_details.application_no",array('personals.*'));
        $select->joinLeft(array('edu'=>'applicant_educational_details'),"edu.application_no = applicant_paymode_details.application_no",array('caste','caste_category','photo'));
        $select->where("applicant_paymode_details.application_no=?",$app_id);
        //echo $select;die;
        $result=$this->getAdapter()
        ->fetchRow($select); 
        
           //echo"<pre>";print_r($result);die;	  
        return $result; 
    }
    public function inserttPayDetails($docArray,$form_id,$course_id,$classRoll){
       // echo '<pre>';print_r($docArray);exit;
        $stackData=$docArray[0];
        if(isset($_SESSION['admin_login']['admin_login']))
            $empl_id=$_SESSION['admin_login']['admin_login']->user_id;
        $dataArray=array(
            'form_id'=>$form_id,
            'course'=>$course_id,
            'class_roll'=>$classRoll,
            'application_no'=>!empty($stackData['application_no'])?$stackData['application_no']:'0',
            //'application_no'=>!empty($stackData['application_no'])?$stackData['application_no']:'0',
            'account_name1' =>!empty($stackData['account_name1'])?$stackData['account_name1']:'0',
            'account_name2' =>!empty($stackData['account_name2'])?$stackData['account_name2']:'0',
            'amount1' =>!empty($stackData['amount1'])?$stackData['amount1']:'0',
            'amount2' =>!empty($stackData['amount2'])?$stackData['amount2']:'0',
            'unique_id1' =>!empty($stackData['unique_id1'])?$stackData['unique_id1']:'0',
            'unique_id2' =>!empty($stackData['unique_id2'])?$stackData['unique_id2']:'0',
            'date_time1' =>!empty($stackData['date_time1'])?$stackData['date_time1']:'0',
            'date2' =>!empty($stackData['date2'])?$stackData['date2']:'0',
            'bank_id1' =>!empty($stackData['bank_id1'])?$stackData['bank_id1']:'0',
            'bank_id2' =>!empty($stackData['bank_id2'])?$stackData['bank_id2']:'0',
            'pay_mode1' =>!empty($stackData['pay_mode1'])?$stackData['pay_mode1']:'0',
            'pay_mode2' =>!empty($stackData['pay_mode2'])?$stackData['pay_mode2']:'0',
            'submit_date' =>date("Y-m-d"),
            'ip_address'=>$_SERVER['REMOTE_ADDR'],
            'user_id'=>$empl_id
        );
       // echo '<pre>';print_r($dataArray);exit;
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->insert('applicant_paymode_details',$dataArray);
         // echo '<pre>';print_r($query);exit;
        return $query;
    }
    public function updateScrutinyStatus($form_id){
       // echo '<pre>';print_r($docArray[0]);exit;
         $where = array(
                'form_id = ?' => $form_id
            );
         $data= array(
            'scrutiny_status'=>1
         );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('applicant_course_details',$data,$where);
        return $query;
    }
    public function updatePayDetails($docArray,$form_id){
        
        $stackData=$docArray[0];
        //echo '<pre>';print_r($stackData);exit;
        if(isset($_SESSION['admin_login']['admin_login']))
            $empl_id=$_SESSION['admin_login']['admin_login']->user_id;
        $where = array(
                'form_id = ?' => $form_id
            );
        $dataArray=array(
            'application_no'=>trim(!empty($stackData['application_no'])?$stackData['application_no']:'0'),
            'account_name1' =>trim(!empty($stackData['account_name1'])?$stackData['account_name1']:'0'),
            'account_name2' =>!empty($stackData['account_name2'])?$stackData['account_name2']:'0',
            'amount1' =>trim(!empty($stackData['amount1'])?$stackData['amount1']:'0'),
            'amount2' =>!empty($stackData['amount2'])?$stackData['amount2']:'0',
            'unique_id1' =>trim(!empty($stackData['unique_id1'])?$stackData['unique_id1']:'0'),
            'unique_id2' =>!empty($stackData['unique_id2'])?$stackData['unique_id2']:'0',
            'date_time1' =>!empty($stackData['date_time1'])?$stackData['date_time1']:'0',
            'date2' =>!empty($stackData['date2'])?$stackData['date2']:'0',
            'bank_id1' =>!empty($stackData['bank_id1'])?$stackData['bank_id1']:'0',
            'bank_id2' =>!empty($stackData['bank_id2'])?$stackData['bank_id2']:'0',
            'pay_mode1' =>!empty($stackData['pay_mode1'])?$stackData['pay_mode1']:'0',
            'pay_mode2' =>!empty($stackData['pay_mode2'])?$stackData['pay_mode2']:'0',
            'update_date' =>date("Y-m-d"),
            'ip_address'=>$_SERVER['REMOTE_ADDR'],
            'user_id'=>$empl_id
        );
        //echo '<pre>';print_r($dataArray);exit;
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query=$this->_db->update('applicant_paymode_details',$dataArray,$where);
        return $query;  
       
    } 
    public function checkCourseForRoll($course_id){
        $select=$this->_db->select();
        $select->from(array('applicant_paymode_details'),
          
               array('class_roll'=>'max(class_roll) as roll_no'))
            ->where("applicant_paymode_details.course=?", $course_id);			   

            $result=$this->getAdapter()
            ->fetchRow($select);  
            //echo $select; die;
  		//echo"<pre>";print_r($result);exit;	  
        return $result;
    }
        
}
?>