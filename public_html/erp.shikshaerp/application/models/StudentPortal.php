<?php

/**

 * @Framework Zend Framework

 * @Powered By TIS 

 * @category   ERP Product

 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.

 * (http://www.techintegrasolutions.com)

 * 	Author Divakar

 */
class Application_Model_StudentPortal extends Zend_Db_Table_Abstract {

    public $_name = 'erp_student_information';
    protected $_id = 'student_id';
    private $_flashMessenger = null;

    //get details by record for edit

    public function getStudenInfo($id) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.student_id =?", $id);

        $result = $this->getAdapter()
                ->fetchRow($select);

        //  echo "<pre>";  print_r($result);exit;

        return $result;
    }

    public function getStudenInfoByU1($id) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.stu_id =?", $id)
                ->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        //  echo "<pre>";  print_r($result);exit;

        return $result;
    }
    
    public function getStudenInfoByU2($id) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status !=?", 2)
                ->where("md5($this->_name.stu_id) =?", $id)
                ->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        //  echo "<pre>";  print_r($result);exit;

        return $result;
    }

    public function getStudenInfoByStuIdForSemFee($id,$sem='') {

        $select = $this->_db->select()
                ->from($this->_name);
                $select->joinleft(array("am" => "academic_master"), "am.academic_year_id= $this->_name.academic_id", array('short_code', 'department', 'session'));
                if($sem){
                    $select->joinleft(array("tm" => "term_master"), "tm.academic_year_id= $this->_name.academic_id", array('term_id'));
                    $select->where("tm.cmn_terms =?", $sem);
                }
                $select->where("$this->_name.status !=?", 2);
                $select->where("$this->_name.stu_id =?", $id);
                $select->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        //  echo "<pre>";  print_r($result);exit;

        return $result;
    }

    public function getStudenInfoByU($id) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status !=?", 2)
                ->where("md5($this->_name.stu_id) =?", $id)
                ->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        //  echo "<pre>";  print_r($result);exit;

        return $result;
    }
    //Added by Kedar :  Date:01 JULY 2020
    public function getStudenInfoByFormId($id){
        $term1 = 't1';
        $select=$this->_db->select()

            ->from($this->_name)
            ->where("$this->_name.status !=?", 2)

            ->where("$this->_name.stu_id =?",$id)
            ->group("erp_student_information.roll_no");

            $result=$this->getAdapter()

                ->fetchRow($select); 
            
            
            if ($result) {

            $select = $this->_db->select()
                    ->from($this->_name);

            $select->join(array("am" => "academic_master"), "am.academic_year_id= $this->_name.academic_id", array('batch_code', 'department', 'session'));
            if ($result['leaving_sem'] != $term1)
                $select->join(array("tr_items" => "tabulation_report_items"), "tr_items.student_id=$this->_name.student_id", array('sum(total_grade_point) as total_grade', 'sum(total_credit_point) as total_credit', 'sum(fail_in_ct_ids) as failed_count', 'max(tabl_id) as tr_id'));
            //$select->from('academic_master',array('batch_code','department','session'));
            if ($result['leaving_sem'] != $term1) {

                $select->where("tr_items.student_id=?", $result['student_id']);
                $select->group("tr_items.student_id");
            }
            $select->where("am.academic_year_id=?", $result['academic_id']);
            //echo $select;die;
            $result1 = $this->getAdapter()
                    ->fetchRow($select);
            }
            
            if($result1){
                $select = $this->_db->select()
                    ->from('session_info',array('session'))
                    ->where("session_info.id=?", $result1['session']);

                $result2 = $this->getAdapter()
                    ->fetchRow($select);
            }         
            if (!empty($result['leaving_sem']) && $result['leaving_sem'] != $term1) {
                //echo '<pre>'; print_r('Sm'); exit;
                $select = $this->_db->select()
                    ->from('tabulation_report',array('added_date'))
                    ->where("tabulation_report.tabl_id=?", $result1['tr_id']);

                $result3 = $this->getAdapter()
                    ->fetchRow($select);
            }         

            
               //echo $select;die;
            $split_date= explode('-',$result3['added_date']);
            $result['last_exam_date']=$split_date[0];
            $result['failed_count']=$result1['failed_count'];
            $result['total_grade']=$result1['total_grade'];
            $result['total_credit']=$result1['total_credit'];
            $result['batch']=$result1['batch_code'];
            $result['session'] =$result2['session'];
            //echo "<pre>";  print_r($result);exit;
        return $result;
    
    }
    
   
    //Added by Kedar :  Date:15 Feb 2021
    public function getStudenInfoByFormIdForPassoutCert($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("pass_stu" => "pass_out_students"), "pass_stu.stu_id=$this->_name.student_id", array('pass_out_no','publish_date'))
                
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.stu_id =?", $id)
                ->group("erp_student_information.roll_no");
            
        $result = $this->getAdapter()
                ->fetchRow($select);

        $term1 = 't1';
       
        if ($result) {

            $select = $this->_db->select()
                    ->from($this->_name);

            $select->join(array("am" => "academic_master"), "am.academic_year_id= $this->_name.academic_id", array('batch_code', 'department', 'session'));
            if ($result['leaving_sem'] != $term1)
                $select->join(array("tr_items" => "tabulation_report_items"), "tr_items.student_id=$this->_name.student_id", array('sum(total_grade_point) as total_grade', 'sum(total_credit_point) as total_credit', 'sum(fail_in_ct_ids) as failed_count', 'max(tabl_id) as tr_id'));
            //$select->from('academic_master',array('batch_code','department','session'));
            if ($result['leaving_sem'] != $term1) {

                $select->where("tr_items.student_id=?", $result['student_id']);
                $select->group("tr_items.student_id");
            }
            $select->where("am.academic_year_id=?", $result['academic_id']);
            //echo $select;die;
            $result1 = $this->getAdapter()
                    ->fetchRow($select);
        }
        //echo '<pre>'; print_r($result1); exit;
        if ($result1) {
            $select = $this->_db->select()
                    ->from('session_info', array('session','id'))
                    ->where("session_info.id=?", $result1['session']);

            $result2 = $this->getAdapter()
                    ->fetchRow($select);
        }

        if (isset($result['leaving_sem']) && $result['leaving_sem'] != $term1) {
            
            $select = $this->_db->select()
                    ->from('tabulation_report', array('added_date'))
                    ->where("tabulation_report.tabl_id=?", $result1['tr_id']);

            $result3 = $this->getAdapter()
                    ->fetchRow($select);
        }
        if ($result1) {
            $select = $this->_db->select()
                    ->from('department', array('degree_id'))
                    ->where("department.id=?", $result1['department']);

            $result4 = $this->getAdapter()
                    ->fetchRow($select);
        }
        if ($result1) {
            $select = $this->_db->select()
                    ->from('examination_dates', array('exam_date'))
                    ->where("examination_dates.academic_id=?", $result['academic_id'])
                    ->where("examination_dates.cmn_terms=?", $result['leaving_sem']);
            $result5 = $this->getAdapter()
                    ->fetchRow($select);
        }

        //echo $select;die;
        $split_date = explode('-', $result3['added_date']);
        $result['last_exam_date'] = $split_date[0];
        $result['failed_count'] = $result1['failed_count'];
        $result['total_grade'] = $result1['total_grade'];
        $result['total_credit'] = $result1['total_credit'];
        $result['batch'] = $result1['batch_code'];
        $result['session'] = $result2['session'];
        $result['session_id'] = $result2['id'];
        $result['degree'] = $result4['degree_id'];
        $result['exam_date'] = !empty($result5['exam_date']) ? $result5['exam_date'] : 'N/A';
        //echo "<pre>";  print_r($result);exit;
        return $result;
    }

    public function getDepartmentType($acad_id) {
        $select = $this->_db->select()
                ->from('academic_master', array('department'))
                ->where("academic_master.academic_year_id=?", $acad_id);

        $result = $this->getAdapter()
                ->fetchRow($select);
        if ($result) {
            $select = $this->_db->select()
                    ->from('department', array('debt_group'))
                    ->where("department.id=?", $result['department']);

            $result1 = $this->getAdapter()
                    ->fetchRow($select);
        }
        $result['stream'] = $result1['debt_group'];

        //echo "<pre>";  print_r($result);exit;
        return $result;
    }

    public function getTcNumber($stream) {
        $select = $this->_db->select()
                ->from($this->_name, array('max(tc_number) as tcNumber'))
                ->where("$this->_name.stream=?", $stream);

        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        return $result;
    }

    public function getStudenInfoByFormIdpromoted($id, $term_id = '') {
        $select = $this->_db->select()
                ->from($this->_name)
                //->joinLeft(array("session"=>"session_info"),"session.id=$this->_name.session",array('session'))
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.stu_id =?", $id)
                ->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchRow($select);
        if ($term_id) {
            if ($result) {
                $currentTerm = $term_id;
                if ($term_id != 't1') {
                    $term_id_arr = explode('t', $term_id);
                    $term_id = ((int) $term_id_arr[1]) - 1;
                    $term_id = 't' . $term_id;
                }

                $select = $this->_db->select();

                $select->from($this->_name, array('concat(stu_id,"-",stu_fname) as name', "$this->_name.*"));


                $select->join(array('tab_items' => 'tabulation_report_items'), "tab_items.student_id = $this->_name.student_id");
                $select->join(array('tab_report' => 'tabulation_report'), "tab_report.tabl_id = tab_items.tabl_id");
                $select->join(array('term' => 'term_master'), "term.academic_year_id = $this->_name.academic_id and term.term_id = tab_report.term_id");
                $select->join(array('payment' => 'exam_form_submission'), "payment.term_id = term.term_id and payment.student_id = $this->_name.student_id");
                $select->where("$this->_name.academic_id in(?)", $result['academic_id']);
                if ($currentTerm != 't1') {
                    $select->where("term.cmn_terms =?", $term_id);
                    $select->where("tab_items.final_remarks != ?", 'F');
                }
                $select->where("$this->_name.stu_id =?", $id);
                $select->where("$this->_name.status !=?", 2);
                $select->where("payment.status =?", 1);
                $select->where("payment.payment_status =?", 1);
                $select->group("$this->_name.student_id");
                $select->order("$this->_name.exam_roll");
                //echo $select;die;
                $result1 = $this->getAdapter()
                        ->fetchRow($select);
            }
        }
        //echo '<pre>'; print_r($result1);exit;
        if ($result) {

            $select = $this->_db->select()
                    ->from('academic_master', array('batch_code', 'department', 'session'))
                    ->where("academic_master.academic_year_id=?", $result['academic_id']);

            $result2 = $this->getAdapter()
                    ->fetchRow($select);
        }
        if ($result2) {
            $select = $this->_db->select()
                    ->from('session_info', array('session', 'id'))
                    ->where("session_info.id=?", $result2['session']);

            $result3 = $this->getAdapter()
                    ->fetchRow($select);
        }


        //echo $select;die;
        if (!empty($result1)) {
            $result['batch'] = $result2['batch_code'];
            $result['session'] = $result3['session'];

            //echo "<pre>";  print_r($result);exit;
            return $result;
        }
    }

    public function getStudentsInfoByAcademicId($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.tc_status =?", 1)
                ->where("$this->_name.academic_id =?", $id)
                ->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo $select;die;
        // echo "<pre>";  print_r($result);exit;

        return $result;
    }

    public function getStudentsInfoByAcademicIdForCollegiatePassOut($academic_id,$termId,$maxCountCheck) {
        $select = $this->_db->select()
                ->from('exam_form_submission')

                ->join(array("tr_items" => "tabulation_report_items"), "tr_items.student_id=exam_form_submission.student_id", array('final_remarks','student_id'))
                
                ->join(array("erp_student" => "erp_student_information"), "erp_student.student_id=tr_items.student_id", array('stu_fname','stu_lname','stu_id'))
                ->where("exam_form_submission.academic_year_id =?", $academic_id)
                ->where("exam_form_submission.term_id =?", $termId)
                ->where("tr_items.sgpa !=?", 0.0)
                ->order("erp_student.stu_fname")
                ->group("tr_items.student_id")
                ->having("count(tr_items.student_id)=$maxCountCheck");

        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo $select;die;
        //echo "<pre>";  print_r($result1);  exit;
        return $result;
    }
    public function getStudentsInfoByAcademicIdForNonCollegiatePassOut($academic_id,$termId,$maxCountCheck) {
        $select = $this->_db->select()
                ->from('ugnon_form_submission')

                ->join(array("tr_items" => "tabulation_report_items"), "tr_items.student_id=ugnon_form_submission.student_id", array('final_remarks','student_id'))
                
                ->join(array("erp_student" => "erp_student_information"), "erp_student.student_id=tr_items.student_id", array('stu_fname','stu_lname','stu_id'))
                ->where("ugnon_form_submission.academic_year_id =?", $academic_id)
                ->where("ugnon_form_submission.term_id =?", $termId)
                ->where("tr_items.sgpa !=?", 0.0)
                ->order("erp_student.stu_fname")
                ->group("tr_items.student_id")
                ->having("count(tr_items.student_id)=$maxCountCheck");

        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo $select;die;
        //echo "<pre>";  print_r($result);  exit;
        return $result;
    }
    public function getStudentList($studentList){
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.student_id in(?)", $studentList)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.exam_roll");
        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo $select;die;
        //  echo "<pre>";  print_r($result);exit;

        return $result;
        //->where("acad.academic_year_id in(?)", explode(',', $academic_id)); 
        
    }
    //END

    public function getStudenacademicDetails($id) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.stu_id =?", $id)
                ->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchRow($select);


        return $result;
    }

    public function getelectivestudentDetailsBack($academic_id = '', $term_id = '', $pay = false) {

        $select = $this->_db->select();
        $select->from(array("student" => $this->_name), array("student.*"));

        $select->joinLeft(array("acad" => "academic_master"), "acad.academic_year_id=student.academic_id", array("short_code as academic_year"));
        if ($pay) {
            $select->join(array("payment_ug" => "ugnon_form_submission"), "payment_ug.student_id=student.student_id", array());
        }
        $select->where("acad.academic_year_id in(?)", explode(',', $academic_id));

        $select->where("student.stu_status =?", 1);
        if ($pay) {
            $select->where("payment_ug.payment_status =?", 1);
            $select->where("payment_ug.term_id =?", explode('t', $term_id)[1]);
        }

        $select->order("student.exam_roll");
        $select->group("student.student_id");
        $result = $this->getAdapter()
                ->fetchAll($select);

        if ($pay) {
            if (!count($result))
                return $this->getelectivestudentDetailsBackpg($academic_id, $term_id, $pay);
        }

        return $result;
    }

    public function getelectivestudentDetailsBackpg($academic_id = '', $term_id = '', $pay = false) {


        $select = $this->_db->select();
        $select->from(array("student" => $this->_name), array("student.*"));
        $select->joinLeft(array("acad" => "academic_master"), "acad.academic_year_id=student.academic_id", array("short_code as academic_year"));
        if ($pay) {
            $select->join(array("payment_pg" => "pg_non_form_data"), "payment_pg.student_id=student.student_id", array());
        }
        $select->where("acad.academic_year_id in(?)", explode(',', $academic_id));
        $select->where("student.stu_status =?", 1);
        if ($pay) {
            $select->where("payment_pg.payment_status =?", 1);
            $select->where("payment_pg.term_id =?", explode('t', $term_id)[1]);
        }
//             
        $select->order("student.exam_roll");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    //Get Students with t.c fee collection status: Kedar : Date: 06 Nov 2020
    public function getStudentacademicDetailsForTc($id, $feeType) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("tc_fee_collection"), "tc_fee_collection.stu_id=$this->_name.stu_id", array("status as pay_status", "fee_type"))
                ->where("$this->_name.status !=?", 2)
                ->where("tc_fee_collection.status =?", 1)
                ->where("tc_fee_collection.fee_type =?", $feeType)
                ->where("$this->_name.stu_id =?", $id)
                ->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        //echo "<pre>";  print_r($result);exit;
        return $result;
    }

    //End Date : 06 Nov 2020

    public function getPermotedStudenacademicDetails($stu_id, $academic_id, $term_id) {


        $currentTerm = $term_id;
        if ($term_id != 't1') {
            $term_id_arr = explode('t', $term_id);
            $term_id = ((int) $term_id_arr[1]) - 1;
            $term_id = 't' . $term_id;
        }

        $select = $this->_db->select();

        $select->from($this->_name, array('concat(stu_id,"-",stu_fname) as name', "$this->_name.*"));


        $select->join(array('tab_items' => 'tabulation_report_items'), "tab_items.student_id = $this->_name.student_id");
        $select->join(array('tab_report' => 'tabulation_report'), "tab_report.tabl_id = tab_items.tabl_id");
        $select->join(array('term' => 'term_master'), "term.academic_year_id = $this->_name.academic_id and term.term_id = tab_report.term_id");
        $select->join(array('payment' => 'exam_form_submission'), "payment.term_id = term.term_id and payment.student_id = $this->_name.student_id");
        $select->where("$this->_name.academic_id in(?)", $academic_id);
        if ($currentTerm != 't1') {
            $select->where("term.cmn_terms =?", $term_id);
            $select->where("tab_items.final_remarks != ?", 'F');
        }
        $select->where("$this->_name.stu_id =?", $stu_id);
        $select->where("$this->_name.status !=?", 2);
        $select->where("payment.status =?", 1);
        $select->where("payment.payment_status =?", 1);
        $select->group("$this->_name.student_id");
        $select->order("$this->_name.exam_roll");
//echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result;
    }

    public function getStudenInfoByU_ID($id) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("attend_info" => "attendance_info"), "attend_info.u_id=$this->_name.stu_id", array('batch'))
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.stu_id =?", $id)
                ->group("erp_student_information.roll_no");

        $result = $this->getAdapter()
                ->fetchRow($select);
        //echo $select;die;
        //  echo "<pre>";  print_r($result);exit;

        return $result;
    }

    public function getStudentM($uid) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id =?", $uid);

        $result = $this->getAdapter()
                ->fetchRow($select);

        // echo "<pre>";  print_r($result);exit;

        return $result;
    }

    public function getStudenFullInfo($id) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array('session as session_id'))
                ->joinleft(array("dept" => "session_info"), "dept.id=academic.session", array('session'))

                // ->joinleft(array("erp"=>"erp_elective_selection_items"),"erp.students_id=$this->_name.student_id",array())
                ->where("$this->_name.status !=?", 2)
                ->where("md5($this->_name.stu_id) =?", $id)
                ->limit(1);

        $result = $this->getAdapter()
                ->fetchRow($select);

        //  echo "<pre>";  print_r($result);exit;

        return $result;
    }

    public function getRecord($id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("academic_master"), "academic_master.academic_year_id=erp_student_information.academic_id", array("session","department"))
                ->join(array("tr_items" => "tabulation_report_items"), "tr_items.student_id=$this->_name.student_id", array('sum(total_grade_point) as total_grade', 'sum(total_credit_point) as total_credit', 'sum(fail_in_ct_ids) as failed_count', 'max(tabl_id) as tr_id'))
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2);

        //echo $select;die;

        $result = $this->getAdapter()
                ->fetchRow($select);

        if ($result) {
            $select = $this->_db->select()
                    ->from('department', array('degree_id'))
                    ->where("department.id=?", $result['department']);

            $result1 = $this->getAdapter()
                    ->fetchRow($select);
        }

        $username = $this->getUserName($id);

        $result['participant_username'] = $username['participant_username'];

        $result['participant_Alumni'] = $username['participant_Alumni'];

        $result['secondary_mail'] = $username['participant_email'];



        $result['linked_in'] = $username['linked_in'];
        $result['degree_id'] = $result1['degree_id'];
        //echo '<pre>';print_r($result);exit;

        return $result;
    }

    //Added by kedar 19 Nov 2019

    public function getRecordbyUid($u_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('student_id,stu_id,exam_roll,stu_email_id,roll_no,stu_mobileno, CONCAT(stu_fname," ",stu_lname) AS studentName,concat(father_fname," ",father_lname) As FatherName'))
                ->where("$this->_name.stu_id=?", $u_id)
                ->where("$this->_name.status !=?", 2);

        //echo $select;die;

        $result = $this->getAdapter()
                ->fetchRow($select);





        //echo"<pre>"; print_r($result);exit;

        return $result;
    }

    public function UpdateMobileRecordbyId($s_id = '', $stu_mobile = '', $father_mobile = '', $student_email = '') {

        $data = array(
            'stu_mobileno' => $stu_mobile,
            'father_mobileno' => $father_mobile,
            'stu_email_id' => $student_email
        );

        $where = array(
            'student_id = ?' => $s_id
        );

        $query = Zend_Db_Table_Abstract::getDefaultAdapter();

        $query->update('erp_student_information', $data, $where);

        //echo $query;exit;
        //return $DB;
    }

    //End

    public function getUserName($id) {

        $select = $this->_db->select()
                ->from('participants_login', array('participant_username', 'participant_Alumni', 'linked_in', 'participant_email'))
                ->where("participants_login.$this->_id=?", $id)
                ->where("participants_login.participant_Active !=?", 2);

        //echo $select;die;

        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result;
    }

    public function getUserName1($id) {

        $select = $this->_db->select()
                ->from('erp_student_information', array('filename'))
                ->where("erp_student_information.$this->_id=?", $id);

        //echo $select;die;

        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result['filename'];
    }

    public function getImage($id) {

        $select = $this->_db->select()
                ->from('erp_student_information')
                ->where("md5(erp_student_information.stu_id)=?", $id);

        //echo $select;die;

        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result;
    }

    public function getRecordsById($id) {



        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id")
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.student_id =?", $id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function checkRecords($id) {

        $select = $this->_db->select()
                ->from('participants_login')
                ->where("participants_login.student_id =?", $id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return count($result);
    }

    public function checkAlumni($id) {

        $select = $this->_db->select()
                ->from('erp_alumni_table')
                ->where("erp_alumni_table.student_id =?", $id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return count($result);
    }

    public function getAlumniDetail($student_id) {

        $select = $this->_db->select()
                ->from(array('alumni' => 'erp_alumni_table'))
                ->joinleft(array("participant" => "participants_login"), "participant.student_id=alumni.student_id", array("participant_pword"))
                ->where("alumni.student_id =?", $student_id);



        $result = $this->getAdapter()
                ->fetchRow($select);



        return $result;
    }

    public function getstudentsbyacademics($academic_id, $term_id, $pay = false, $attendance = false) {
        $term_model = new Application_Model_TermMaster();
        $termpay = $term_model->getTermRecordsbycmnelective(explode(',',$academic_id), $term_id);
        $currentTerm = $term_id;
        if ($term_id != 't1') {
            $term_id_arr = explode('t', $term_id);
            $term_id = ((int) $term_id_arr[1]) - 1;
            $term_id = 't' . $term_id;
        }

        $select = $this->_db->select();

        $select->from($this->_name, array('concat(stu_id,"-",stu_fname) as name', "$this->_name.*"));

        if ($term_id != 't1') {
            $select->join(array('tab_items' => 'tabulation_report_items'), "tab_items.student_id = $this->_name.student_id", array());
            $select->join(array('tab_report' => 'tabulation_report'), "tab_report.tabl_id = tab_items.tabl_id", array());

            $select->join(array('term' => 'term_master'), "term.academic_year_id = $this->_name.academic_id and term.term_id = tab_report.term_id", array());
        }
        if ($pay) {
            $select->join(array("payment" => "exam_form_submission"), "payment.student_id=$this->_name.student_id", array());
        }

        $select->where("$this->_name.academic_id in(?)", $academic_id);
        if ($attendance) {
            $select->joinLeft(array("semester_wise_attendance_report"), "semester_wise_attendance_report.u_id = $this->_name.stu_id", array("component_paper", "max(attend_status) as attend_status"));
        }
        if ($currentTerm != 't1') {
            $select->where("term.cmn_terms = ?", "$term_id");
            $select->where("tab_items.final_remarks != ?", 'F');
        }
        if ($attendance) {
            $select->where("semester_wise_attendance_report.cmn_terms =?", $currentTerm);
            $select->where("semester_wise_attendance_report.course_id =?", 0);
        }
        $select->where("$this->_name.status !=?", 2);
        $select->where("$this->_name.stu_status =?", 1);
        if ($pay) {
            $select->where("payment.payment_status =?", 1);
            $select->where("payment.term_id in (?)", explode(',',$termpay));
        }
        $select->group("$this->_name.student_id");

        $select->order("$this->_name.exam_roll");
       
        if ($attendance) {
            $select1 = $this->_db->select();
            $select1->from($select);
            $select1->where("attend_status=?", 0);
            $result = $this->getAdapter()
                    ->fetchAll($select1);
        } else {
            $result = $this->getAdapter()
                    ->fetchAll($select);
        }

        return $result;
    }

    //Get all records

    public function getRecords() {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("short_code AS academic_year"))
                ->joinleft(array("terms" => "term_master"), "terms.term_id=$this->_name.terms_id", array("term_name"))
                ->where("$this->_name.academic_id !=?", 0)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id DESC");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    //Get all records

    public function getRecordsfordetails() {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("short_code AS academic_year"))
                ->joinleft(array("terms" => "term_master"), "terms.term_id=$this->_name.terms_id", array("term_name"))
                ->where("$this->_name.academic_id !=?", 0)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id DESC");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    // Add By kedar 23 Sept. 2019

    public function getRecordsBySession($session_id = '', $department_id = '',$flag='') {

        $select = $this->_db->select();

        $select->from($this->_name);

        $select->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("short_code AS academic_year"));
//=====comented
         $select->joinleft(array("credits" => "academic_credits"), 
            "credits.academic_id=$this->_name.academic_id", array("credit_number"));

        $select->where("$this->_name.academic_id !=?", 0);

        $select->where("$this->_name.status !=?", 2);

        if (!empty($session_id)) {

            $select->where("academic.session =? ", $session_id);

            $select->where("academic.department =? ", $department_id);
            if($flag == A)
            $select->where("$this->_name.stu_status =?", 1);
        }

        $select->order("$this->_name.$this->_id DESC");

        //echo $select;die;

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getDropDownList() {

        $select = $this->_db->select()
                ->from($this->_name, array('student_id', 'stu_fname'))
                ->where("$this->_name.status!=?", 2)
                ->order('student_id  ASC');

        $result = $this->getAdapter()->fetchAll($select);

        $data = array();



        foreach ($result as $val) {



            $data[$val['student_id']] = $val['stu_fname'];
        }

        return $data;
    }

    public function getstudents($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students', 'erp_student_information.student_id', 'father_fname', 'roll_no', 'exam_roll', 'stu_id', 'stu_status'))
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getstudentsByStuStatus($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students', 'erp_student_information.student_id', 'father_fname', 'roll_no', 'exam_roll', 'stu_id', 'stu_status'))
                ->where("$this->_name.academic_id=?", $academic_id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getstudents1($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_id," ",erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->where("$this->_name.academic_id in(?)", $academic_id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getStudentsSortByName($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.stu_fname ASC");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getstudentsyearwise($academic_id, $year_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.year=?", $year_id)
                ->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getstudentsdetails($academic_id, $term_id = '', $attend = false, $pay = false) {

        $term_model = new Application_Model_TermMaster();
        $term_id1 = $term_model->getTermRecordsbycmn($academic_id, $term_id)['term_id'];

        $currentTerm = $term_id;
        if ($term_id != 't1') {
            $term_id_arr = explode('t', $term_id);
            $term_id = ((int) $term_id_arr[1]) - 1;
            $term_id = 't' . $term_id;
        }

        $select = $this->_db->select();

        $select->from($this->_name, array('CONCAT(erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students', 'erp_student_information.student_id', 'erp_student_information.stu_id', 'erp_student_information.roll_no', 'erp_student_information.reg_no', 'erp_student_information.exam_roll', 'concat(erp_student_information.father_fname," ",erp_student_information.father_lname) as father_name'));
        if ($attend) {
            $select->join(array("semester_wise_attendance_report"), " semester_wise_attendance_report.u_id = erp_student_information.stu_id", array("component_paper", "attend_status", "u_id"));
        }
        $select->joinLeft(array("academic_master"), "academic_master.academic_year_id=$this->_name.academic_id", array("short_code"));
        if ($pay) {
            $select->join(array("payment_ug" => "exam_form_submission"), "payment_ug.student_id=$this->_name.student_id", array());
        }

        $select->where("$this->_name.academic_id in(?)", $academic_id);
        if ($currentTerm != 't1') {
            $select->join(array('tab_items' => 'tabulation_report_items'), "tab_items.student_id = $this->_name.student_id");
            $select->join(array('tab_report' => 'tabulation_report'), "tab_report.tabl_id = tab_items.tabl_id");
            $select->join(array('term' => 'term_master'), "term.academic_year_id = $this->_name.academic_id and term.term_id = tab_report.term_id");
            $select->where("term.cmn_terms =?", $term_id);
            $select->where("tab_items.final_remarks != ?", 'F');
        }
        if ($attend) {
            $select->where("semester_wise_attendance_report.u_id is Not null");
            $select->where("semester_wise_attendance_report.cmn_terms =?", $currentTerm);
        }
        $select->where("$this->_name.status !=?", 2);
        if ($pay) {
            $select->where("payment_ug.payment_status =?", 1);
            $select->where("payment_ug.term_id =?", $term_id1);
        }
        $select->group("$this->_name.student_id");
        $select->order("$this->_name.exam_roll");
        //   echo $select ; die;
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getstudentsdetailattend($academic_id, $term_id = '', $tab_Offset = true) {


        //echo '<pre>'; print_r($tab_Offset);exit;
        $currentTerm = $term_id;
        if ($term_id != 't1') {
            $term_id_arr = explode('t', $term_id);
            $term_id = ((int) $term_id_arr[1]) - 1;
            $term_id = 't' . $term_id;
        }

        $select = $this->_db->select();

        $select->from($this->_name, array('CONCAT(erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students', 'erp_student_information.student_id', 'erp_student_information.stu_id', 'erp_student_information.roll_no', 'erp_student_information.reg_no', 'erp_student_information.exam_roll', 'concat(erp_student_information.father_fname," ",erp_student_information.father_lname) as father_name'));

        $select->joinLeft(array("academic_master"), "academic_master.academic_year_id=$this->_name.academic_id", array("short_code"));


        $select->where("$this->_name.academic_id in(?)", $academic_id);
        if ($tab_Offset == 1) {
            if ($currentTerm != 't1') {
                $select->join(array('tab_items' => 'tabulation_report_items'), "tab_items.student_id = $this->_name.student_id");
                $select->join(array('tab_report' => 'tabulation_report'), "tab_report.tabl_id = tab_items.tabl_id");
                $select->join(array('term' => 'term_master'), "term.academic_year_id = $this->_name.academic_id and term.term_id = tab_report.term_id");
                $select->where("term.cmn_terms =?", $term_id);
                $select->where("tab_items.final_remarks != ?", 'F');
            }
        }

        $select->where("$this->_name.stu_status =?", 1);
        $select->group("$this->_name.student_id");
        $select->order("$this->_name.exam_roll");
        // echo $select ; die;
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getstudentsdetailsFinal($academic_id, $term_count = 6 ) {



   

        $select = $this->_db->select();

        $select->from($this->_name, array('CONCAT(erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students', 'erp_student_information.student_id', 'erp_student_information.stu_id', 'erp_student_information.roll_no', 'erp_student_information.reg_no', 'erp_student_information.exam_roll', 'concat(erp_student_information.father_fname," ",erp_student_information.father_lname) as father_name'));
        $select->join(array('tab_items' => 'tabulation_report_items'), "tab_items.student_id = $this->_name.student_id");
        $select->join(array('tab_report' => 'tabulation_report'), "tab_report.tabl_id = tab_items.tabl_id");
        $select->join(array("academic_master"), "academic_master.academic_year_id=$this->_name.academic_id", array("short_code"));
        
        

        $select->where("$this->_name.academic_id = ?", $academic_id);
        $select->where("tab_report.academic_id = ?", $academic_id);
        $select->where("tab_report.flag like (?)", 'R');
//        $select->where("$this->_name.status !=?", 2);
//        $select->where("$this->_name.stu_status !=?", 3);
        $select->group("tab_items.student_id");
        $select->having("count(*)= ?",$term_count);
        $select->order("$this->_name.exam_roll");
//        echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getstudentsdetailsWithAttendence($academic_id, $cmn_terms = '', $course, $ge_id, $limti = 100, $offset = 0, $attend = false, $pay = false) {
        $term_model = new Application_Model_TermMaster();
        $term_id1 = $term_model->getTermRecordsbycmn($academic_id, $cmn_terms)['term_id'];
        $select = $this->_db->select();

        $select->from($this->_name, array('CONCAT(erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students', 'erp_student_information.student_id', 'erp_student_information.stu_id', 'erp_student_information.roll_no', 'erp_student_information.reg_no', 'erp_student_information.exam_roll', 'concat(erp_student_information.father_fname," ",erp_student_information.father_lname) as father_name'));
        if ($attend == 1) {
            $select->join(array("semester_wise_attendance_report"), " semester_wise_attendance_report.u_id = erp_student_information.stu_id", array("component_paper", "attend_status"));
        }
        if ($pay) {
            $select->join(array("payment_ug" => "exam_form_submission"), "payment_ug.student_id=$this->_name.student_id", array());
        }
        $select->join(array("academic_master"), "academic_master.academic_year_id=$this->_name.academic_id", array("short_code"));
        $select->where("$this->_name.academic_id=?", $academic_id);
        if ($attend == 1) {
            if (!empty($ge_id)) {
                $select->where("semester_wise_attendance_report.ge_id = ?", $ge_id);
                $select->where("semester_wise_attendance_report.course_id = ?", $course);
            } else {
                $select->where("semester_wise_attendance_report.ge_id = 0");
                $select->where("semester_wise_attendance_report.course_id = 0");
            }

            $select->where("semester_wise_attendance_report.cmn_terms =?", $cmn_terms);
        }
        if ($pay) {
            $select->where("payment_ug.payment_status =?", 1);
            $select->where("payment_ug.term_id=?", $term_id1);
        }
        $select->where("$this->_name.status !=?", 2);
        $select->group("erp_student_information.stu_id");
        $select->order('erp_student_information.exam_roll');
        // echo  $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getstudentsdetails1($academic_id, $ct_id) {

        $select = $this->_db->select()
                ->from('core_course_master', array('academic_year_id'))

                //->joinLeft(array("core_course_master"),"core_course_master.academic_year_id=$this->_name.academic_id",array("cc_id"))
                //->where("core_course_master.academic_year_id=?", 1)
                ->distinct()
                ->where("core_course_master.cc_id=?", 1);



        //->where("$this->_name.status !=?", 2);
        // echo $select;die;

        $result = $this->getAdapter()
                ->fetchAll($select);

        //echo "<pre>"; print_r($result);die;

        return $result;
    }

    public function getstudentsdetailsForFirstTerm($academic_id) {

        $structID = $this->structID($academic_id);

        if ($structID != 0) {

            $select = $this->_db->select()
                    ->from("erp_fee_structure_items")
                    ->where('structure_id =?', $structID);

            $due_dates = $this->getAdapter()
                    ->fetchAll($select);

            $t1_date = date("Y-m-d", strtotime($due_dates[0]['t1_date']));

            $t2_date = date("Y-m-d", strtotime($due_dates[0]['t2_date']));

            $t3_date = date("Y-m-d", strtotime($due_dates[0]['t3_date']));

            $t4_date = date("Y-m-d", strtotime($due_dates[0]['t4_date']));

            $t5_date = date("Y-m-d", strtotime($due_dates[0]['t5_date']));

            $term_id = $this->getTermId($structID['structure_id'], 1);

            $category = $this->getFeeCategory();

            $total_fee_in_that_term = 0;

            foreach ($category as $key_category => $value) {

                $total_fee_in_that_term += $this->getFee($structID['structure_id'], 1, $value['category_id'])[0]['total'];
            }

            $service_fee = $this->getFee($structID['structure_id'], 1, 2);

            $otherAnnualCharges = $this->getFee($structID['structure_id'], 1, 3);

            $tuition_fee = abs($total_fee_in_that_term - ((int) $service_fee[0]['total'] + (int) $otherAnnualCharges[0]['total']));

            $result = array(
                'gpa' => 0.0,
                'fee' => $total_fee_in_that_term,
                'service_fee' => $service_fee[0]['total'],
                'other_annual_charges' => $otherAnnualCharges[0]['total'],
                'tuition_fee' => $tuition_fee,
                'fee_discount' => 0,
                'total_fee' => $total_fee_in_that_term,
                'batch_id' => $academic_id,
                'term_id' => $term_id,
                't1_date' => $t1_date,
                't2_date' => $t2_date,
                't3_date' => $t3_date,
                't4_date' => $t4_date,
                't5_date' => $t5_date
            );

            return $result;
        } else
            return 0;
    }

    public function getFeeCategory() {



        $select = $this->_db->select()
                ->from('erp_fee_category_master', array('category_id'))
                ->where("status !=?", 2);

        // echo $select; exit;

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getTermId($structure_id, $terms) {

        $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('terms_id as term_id'))
                ->where("structure_id =?", $structure_id)
                ->where("terms =?", (INT) $terms);

        // echo $select; exit;

        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result['term_id'];
    }

    public function getFee($structure_id, $term_id, $category_id) {

        $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('sum(fees) as total'))
                ->where("structure_id =?", $structure_id)
                ->where("category_id =?", $category_id)
                ->where("terms =?", (INT) $term_id);

        // echo $select; exit;

        $result = $this->getAdapter()
                ->fetchAll($select);



        return $result;
    }

    public function getstudentsdetailsByTerm_id($academic_id, $term_id, array $pre_index_details) {

        $x = $this->myfunc($term_id);

        //print($academic_id." ". $term_id);exit;

        $terms_count = strlen($x['term_name']);

        $term_val = substr($x['term_name'], $terms_count - 1);

        // echo "<pre>";print_r($term_id);exit;

        $structID = $this->structID($academic_id);

        if ($structID != 0) {



            $select = $this->_db->select()
                    ->from(array("student" => $this->_name), array('sum(fees) as sum', 'CONCAT(stu_fname,' . " " . ' stu_lname) as students', 'stu_id as participants_id', 'student_id'))
                    ->join(array("ref_master" => "course_grade_after_penalties_items"), "ref_master.student_id=student.student_id")
                    ->join(array("structure" => "erp_fee_structure_master"), "structure.academic_id=student.academic_id")
                    ->join(array("due_date" => "erp_fee_structure_term_items"), "due_date.structure_id=structure.structure_id")
                    ->join(array("due_date_real" => "erp_fee_structure_items"), "due_date_real.structure_id=structure.structure_id")



                    // ->join(array("scholarship" => "scholarship_management"),"scholarship.term_id = student.academic_id" )
                    ->where("student.academic_id = ?", $academic_id)
                    ->where("ref_master.term_id = ?", $term_id)

                    // ->where('scholarship.term_id = ?', $term_id)
                    ->where("due_date.structure_id =?", $structID['structure_id'])
                    ->where("due_date.category_id =?", 1)
                    ->where("due_date.terms_id =?", $term_id)
                    ->group("student.stu_id")
                    ->where("student.status !=?", 2);

            //echo $select ; exit;



            $result = $this->getAdapter()
                    ->fetchAll($select);



            if (count($result) != 0) {

                $i = 0;

                $total_fee_in_that_term = $this->getTotalFee($structID['structure_id'], $term_id);

                $service_fee = $this->getServiceFee($structID['structure_id'], $term_id);

                $x = array();

                $otherAnnualCharges = $this->getOtherAnnualCharges($structID['structure_id'], $term_id);

                foreach ($result as $key => $value) {



                    $gpa_percent = $this->getPercentage($result[$i]['student_id'], $pre_index_details, $academic_id);



                    $result[$i]['total_fee'] = $total_fee_in_that_term[0]['total'];

                    $result[$i]['sum1'] = $service_fee;

                    $result[$i]['sum2'] = $otherAnnualCharges;



                    if (count($gpa_percent) > 0) {



                        if ($pre_index_details['c_type'] != 'el') {

                            //  print_r($gpa_percent);exit;

                            $result[$i]['scholarship_percent'] = $gpa_percent[0]['fee'];

                            $result[$i]['cgpa'] = $gpa_percent[0]['cgpa'];
                        } else {

                            $result[$i]['scholarship_percent'] = $gpa_percent[0]['fee'];

                            $result[$i]['cgpa'] = $gpa_percent[0]['cgpa'];
                        }

                        $total_fee = $this->getCalculatedFee($result[$i]['sum'], $result[$i]['scholarship_percent'], $total_fee_in_that_term[0]['total']);



                        $result[$i]['calculated_fee'] = $total_fee;
                    } else {

                        //  $x[$i] = "hello";

                        $result[$i]['scholarship_percent'] = "0";

                        $result[$i]['calculated_fee'] = $total_fee_in_that_term[0]['total'];
                    }

                    $i++;
                }
            } else {

                $result = 3;
            }
        } else {

            $result = 0;
        }

// echo "<pre>";print_r($result);exit;

        return $result;
    }

    public function getTotalFee($structure_id, $term_id) {

        $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('sum(fees) as total'))
                ->where("structure_id =?", $structure_id)
                ->where("terms_id =?", (INT) $term_id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getPercentage($final_grade, array $pre_index_details, $batch) {



        if ($pre_index_details['c_type'] != 'el') {

            $select = $this->_db->select()
                    ->from(array('gr' => 'course_grade_after_penalties_items'), array('gr.term_id', 'gr.student_id', 'gr.item_id', 'gr.cgpa', '(SELECT sch.scholarship_fee_wavier FROM scholarship_management sch WHERE sch.status =0 AND batch_id =' . $batch . ' AND gr.cgpa BETWEEN sch.gpa_from AND sch.gpa_to) as fee'))
                    ->where('gr.student_id = ?', $final_grade)
                    ->where('gr.term_id = ?', $pre_index_details['id']);

            // echo $select;exit;

            $result = $this->getAdapter()
                    ->fetchAll($select);

            //  print_r($result);exit;
        } else {

            //print_r($pre_index_details['id']);exit;

            $select = $this->_db->select()
                    ->from(array('gr' => 'experiential_grade_allocation_items'), array('gr.student_id', 'gr.grade_allocation_item_id', 'gr.cgpa', '(SELECT sch.scholarship_fee_wavier FROM scholarship_management sch WHERE sch.status =0 AND batch_id =' . $batch . ' AND gr.cgpa BETWEEN sch.gpa_from AND sch.gpa_to) as fee'))
                    ->join(array("ref_master" => "experiential_grade_allocation_master"), "ref_master.grade_id=gr.grade_allocation_id")
                    ->where('ref_master.course_id = ?', $pre_index_details['id'])
                    ->where('student_id = ?', $final_grade);

            //echo $select; exit;

            $result = $this->getAdapter()
                    ->fetchAll($select);
        }

        // echo "<pre>";print_r($result);echo "</pre>";exit;

        return $result;
    }

    public function getLastId() {

        $select = $this->_db->select()
                ->from('scholarship_management', array('max(id) as last_id'));

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result[0]['last_id'];
    }

    public function checkLastValue($last_id, $gpa_from) {
        
    }

    public function getServiceFee($structure_id, $term_id) {

        //print($term_id);exit;

        $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('sum(fees) as sum1'))
                ->where("structure_id =?", $structure_id)
                ->where("category_id =?", 2)
                ->where("terms_id =?", (INT) $term_id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getOtherAnnualCharges($structure_id, $term_id) {

        $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('sum(fees) as sum2'))
                ->where("structure_id =?", $structure_id)
                ->where("category_id =?", 3)
                ->where("terms_id =?", (INT) $term_id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getCalculatedFee($sum, $relief_percent_in_fee, $total_fee) {

        $other_term_fee = $total_fee - $sum;

        $scholarship_fee = $other_term_fee + ($sum - ($sum / 100) * $relief_percent_in_fee);

        return $scholarship_fee;
    }

    public function myfunc($term_id) {



        $select = $this->_db->select()
                ->from("term_master", 'term_name')
                ->where("term_id =?", $term_id);



        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result[0];
    }

    public function structID($id) {



        $select = $this->_db->select()
                ->from("erp_fee_structure_master", 'structure_id')
                ->where("academic_id =?", $id);



        $result = $this->getAdapter()
                ->fetchAll($select);

        //print_r($result);exit; 

        if (count($result) == 0) {

            return 0;
        } else {

            return $result[0];
        }
    }

    public function getStuIds() {

        $select = $this->_db->select()
                ->from($this->_name, 'student_id')
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id DESC");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getStudentNames($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchAll($select);

        $data = array();

        foreach ($result as $k => $val) {

            $data[$val['student_id']] = $val['students'];
        }

        return $data;
    }

    public function getStudentDetails($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->join(array("exam" => "exam_form_submission"), "exam.u_id=erp_student_information.stu_id")
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("exam.payment_status=?", 1)
               // ->where("exam.status=?", 0)
                ->where("$this->_name.status !=?", 2)
                ->group("exam.u_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result);
        //die();
        $data = array();

        foreach ($result as $k => $val) {

            $data[$val['student_id']] = $val['students'];
        }

        return $data;
    }

    public function getNonugDetails($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->join(array("exam" => "ugnon_form_submission"), "exam.u_id=erp_student_information.stu_id")
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("exam.payment_status=?", 1)
                ->where("exam.status=?", 0)
                ->where("$this->_name.status !=?", 2)
                ->group("exam.u_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result);
        //die();
        $data = array();

        foreach ($result as $k => $val) {

            $data[$val['student_id']] = $val['students'];
        }

        return $data;
    }

    public function getNonpgDetails($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->join(array("exam" => "pg_non_form_data"), "exam.u_id=erp_student_information.stu_id")
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("exam.payment_status=?", 1)
                ->where("exam.status=?", 0)
                ->where("$this->_name.status !=?", 2)
                ->group("exam.u_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result);
        //die();
        $data = array();

        foreach ($result as $k => $val) {

            $data[$val['student_id']] = $val['students'];
        }

        return $data;
    }

    public function getStudentPCRecord($academic_id = '', $stu_id = '') {

        //print_r($branch_id); die;

        if (!empty($academic_id) || !empty($stu_id)) {

            $where = "";

            if (!empty($academic_id)) {

                $where .= " AND erp_student_information.academic_id = '$academic_id'";
            }

            if (!empty($stu_id)) {

                $where .= " AND erp_student_information.student_id = '$stu_id'";
            }



            $select = "SELECT `erp_student_information`.* FROM `erp_student_information` WHERE erp_student_information.status!=2 $where GROUP BY erp_student_information.student_id order by erp_student_information.exam_roll";
        }

        //echo $select; die;

        $result = $this->getAdapter()
                ->fetchAll($select);

        //print_r($result);die;

        return $result;
    }
 
    
    public function getStudentResult($academic_id = '' ,$count='') {

        //print_r($branch_id); die;

        if (!empty($academic_id)) {

            $where = "";

            if (!empty($academic_id)) {

                $where  = $academic_id;
            }
              if (!empty($count)) {

                $tot  = $count;
            }
 $select ="SELECT erp_student_information.stu_id, erp_student_information.stu_fname,tabulation_report.added_date,SUM(tabulation_report_items.sgpa) as total_sgpa, round(sum(tabulation_report_items.total_grade_point)/ sum(tabulation_report_items.total_credit_point),3) as cgpa FROM `tabulation_report` 
JOIN tabulation_report_items on tabulation_report_items.tabl_id = tabulation_report.tabl_id
join erp_student_information on erp_student_information.student_id = tabulation_report_items.student_id
where tabulation_report.academic_id = $where and tabulation_report_items.sgpa !=0 GROUP by tabulation_report_items.student_id HAVING count(*) = $tot
ORDER BY `cgpa` DESC";
           // $select = "SELECT `erp_student_information`.* FROM `erp_student_information` WHERE erp_student_information.status!=2 $where GROUP BY erp_student_information.student_id order by erp_student_information.exam_roll";
        }

       // echo $select; die;

        $result = $this->getAdapter()
                ->fetchAll($select);

      //  print_r($result);die;

        return $result;
    }
    public function getStudentadmitRecord($academic_id = '', $stu_id = '',$term_id='') {

       // print_r($term_id); die;

        if (!empty($academic_id) || !empty($stu_id)) {

            $where = "";

            if (!empty($academic_id)) {

                $where .= " AND erp_student_information.academic_id = '$academic_id'";
            }

            if (!empty($stu_id)) {

                $where .= " AND erp_student_information.student_id = '$stu_id'";
            }

            $where .= " AND term_master.cmn_terms='$term_id' AND exam_form_submission.payment_status='1'";
           // $where .= " AND exam_form_submission.status='0' AND exam_form_submission.payment_status='1'";
            $select = "SELECT erp_student_information.* FROM `exam_form_submission` join erp_student_information on erp_student_information.stu_id = exam_form_submission.u_id join term_master on term_master.academic_year_id = exam_form_submission.academic_year_id $where GROUP BY u_id order by stu_fname asc";
        }

        //echo $select; die;

        $result = $this->getAdapter()
                ->fetchAll($select);

        //print_r($result);die;

        return $result;
    }

    public function getNonugadmitRecord($academic_id = '', $stu_id = '') {

        //print_r($branch_id); die;

        if (!empty($academic_id) || !empty($stu_id)) {

            $where = "";

            if (!empty($academic_id)) {

                $where .= " AND erp_student_information.academic_id = '$academic_id'";
            }

            if (!empty($stu_id)) {

                $where .= " AND erp_student_information.student_id = '$stu_id'";
            }

            $where .= " AND ugnon_form_submission.status='0' AND ugnon_form_submission.payment_status='1'";

            $select = "SELECT erp_student_information.* FROM `ugnon_form_submission` join erp_student_information on erp_student_information.student_id = ugnon_form_submission.student_id $where GROUP BY u_id order by stu_fname asc";
        }

        //echo $select; die;

        $result = $this->getAdapter()
                ->fetchAll($select);

        //print_r($result);die;

        return $result;
    }

    public function getNonpgadmitRecord($academic_id = '', $stu_id = '') {

        //print_r($branch_id); die;

        if (!empty($academic_id) || !empty($stu_id)) {

            $where = "";

            if (!empty($academic_id)) {

                $where .= " AND erp_student_information.academic_id = '$academic_id'";
            }

            if (!empty($stu_id)) {

                $where .= " AND erp_student_information.student_id = '$stu_id'";
            }

            $where .= " AND pg_non_form_data.status='0' AND pg_non_form_data.payment_status='1'";

            $select = "SELECT erp_student_information.* FROM `pg_non_form_data` join erp_student_information on erp_student_information.stu_id = pg_non_form_data.u_id $where GROUP BY u_id order by stu_fname asc";
        }

        //echo $select; die;

        $result = $this->getAdapter()
                ->fetchAll($select);

        //print_r($result);die;

        return $result;
    }

    public function getStudentsAcademicWise($academic_year_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('student_id', 'stu_id', "concat(stu_fname,' ',stu_lname) as stu_fname"))
                ->where("$this->_name.academic_id in (?)", $academic_year_id)
                ->where("$this->_name.status!=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['student_id']] = $val['stu_id'] . '-' . $val['stu_fname'];
        }
        return $data;
    }

    public function fetchDiscontinuedStudentDetailById($stu_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id=?", $stu_id)
                ->where("$this->_name.stu_status = ?", 2)
                ->where("$this->_name.status != ?", 2)
                ->order("student_id DESC")
                ->limit(1, 0);

        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result;
    }

    public function fetchDiscontinuedBatchesOfStudent($student_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id IN (SELECT stu_id FROM $this->_name WHERE student_id = ?)", $student_id)
                ->where("$this->_name.stu_status = ?", 2)
                ->where("$this->_name.status != ?", 2)
                ->order("$this->_name.student_id ASC");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function fetchAllBatchesOfStudent($student_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id IN (SELECT stu_id FROM $this->_name WHERE student_id = ?)", $student_id)
                ->where("$this->_name.status != ?", 2)
                ->order("$this->_name.student_id ASC");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getDistincetStudentsByBatchId($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('DISTINCT(erp_student_information.stu_id) as stu_id', 'CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    //To check existed exam roll no.

    public function getDataExists($reg_no, $exam_roll) {

        $select = $this->_db->select();

        $select->from($this->_name, array('reg_no', 'exam_roll'));

        if (!empty($reg_no) && !empty($exam_roll)) {

            $select->where('reg_no IN(?)', $reg_no);

            $select->orWhere('exam_roll IN(?)', $exam_roll);
        } else if ($reg_no) {

            $select->where('reg_no IN(?)', $reg_no);

            $select->orWhere('exam_roll IN(?)', $reg_no);
        } else if ($exam_roll) {

            $select->where('exam_roll IN(?)', $exam_roll);

            $select->orWhere('reg_no IN(?)', $exam_roll);
        } else {

            $result = 0;
        }

        //echo $select; die;

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    //For promotion documents follow up
    public function insertPromotionStudents($data) {

        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query = $this->_db->insert('promotion_documents_followup', $data);
        //echo $query;die;
        return $query;
    }

    public function updatePromotionStudents($form_id) {
        $where = array(
            'form_id = ?' => $form_id
        );
        $dataArray = array(
            'status' => 2
        );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query = $this->_db->update('promotion_documents_followup', $dataArray, $where);
        return $query;
    }

    public function updateBlockStatusPromotionStudents($form_id) {
        $where = array(
            'form_id = ?' => $form_id
        );
        $dataArray = array(
            'status' => 0
        );
        $query = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query = $this->_db->update('promotion_documents_followup', $dataArray, $where);
        return $query;
    }

    public function checkPromotionDocument($form_id) {
        $select = $this->_db->select();
        $select->from(array('promotion_documents_followup'), array('form_id', 'status', 'date'));
        $select->where("promotion_documents_followup.form_id=?", $form_id);
        //$select->where("promotion_documents_followup.status =?",2);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);

        //  echo"<pre>";print_r($result);die;	  
        return $result;
    }

    public function checkPromotionDocumentWithStatus($form_id) {
        $select = $this->_db->select();
        $select->from(array('promotion_documents_followup'), array('form_id', 'status', 'date'));
        $select->where("promotion_documents_followup.form_id=?", $form_id);
        $select->where("promotion_documents_followup.status =?", 2);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);

        //echo"<pre>";print_r($result);die;	  
        return $result;
    }

}
