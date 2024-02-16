<?php

/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class Application_Model_GradeAllocation extends Zend_Db_Table_Abstract {

    public $_name = 'grade_allocation_master';
    protected $_id = 'grade_id';

    //get details by record for edit
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id =?", $id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;					  
        return $result;
    }

    public function getDistinctmonthofdeletedgrdes($academic_year_id, $term_id) {
        $select = $this->_db->select()
                ->from($this->_name, array())
                ->join(array("deleted_grade" => "deleted_grade_allocation_items"), "deleted_grade.grade_allocation_id=$this->_name.grade_id", array("DATE_FORMAT(deleted_grade.update_date,'%M %Y %d') as display", "DATE_FORMAT(deleted_grade.update_date,'%Y-%m-%d') as vald"))
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->group("DATE_FORMAT(deleted_grade.update_date,'%Y-%m-%d')");

        //->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchAll($select);



        return $result;
    }

    public function getGrouped($session, $faculty, $term, $course, $paper = 'R') {
        $select = $this->_db->select()
                ->from($this->_name, array("$this->_name.*", "GROUP_CONCAT($this->_name.grade_id) as grade_arr"))
                ->where("$this->_name.session=?", $session)
                ->where("$this->_name.cmn_terms=?", $term)
                ->where("$this->_name.course_id=?", $course)
                ->where("$this->_name.employee_id=?", $faculty)
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.flag =?", $paper)
                ->group("cmn_terms");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getGroupebyCourseid($session, $course, $paper_flg = 'R') {
        $select = $this->_db->select()
                ->from($this->_name, array('GROUP_CONCAT(department) as department'))
                ->where("$this->_name.session=?", $session)
                ->where("$this->_name.course_id=?", $course)
                ->where("$this->_name.flag=?", $paper_flg)
                ->where("$this->_name.status !=?", 2)
                ->group("cmn_terms");
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    //Get all records
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name, array())
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("short_code AS academic_year"))
                ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id", array("term_name"))
                ->joinLeft(array("course" => "course_master"), "course.course_id=$this->_name.course_id", array("course_name", "course_code"))
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRecordsByFacultyId($faculty_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("short_code AS academic_year"))
                ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id", array("term_name"))
                ->joinLeft(array("course" => "course_master"), "course.course_id=$this->_name.course_id", array("course_name", "course_code"))
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.employee_id =?", $faculty_id)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    //Get all records
    public function getRecordsNew($paper = 'R') {
        $select = $this->_db->select()
                ->from($this->_name, array('GROUP_CONCAT(grade_id) as arr_grade', "$this->_name.*"))
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("GROUP_CONCAT(short_code) AS academic_year"))
                ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id", array("term_name"))
                ->joinLeft(array("course" => "course_master"), "course.course_id=$this->_name.course_id", array("course_name", "course_code"))
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.flag =?", $paper)
                ->order("$this->_name.$this->_id DESC")
                ->group(array("$this->_name.session", "$this->_name.cmn_terms", "$this->_name.course_id", "$this->_name.employee_id"));
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRecordsByFacultyIdNew($faculty_id, $paper = 'R') {
        $select = $this->_db->select()
                ->from($this->_name, array('GROUP_CONCAT(grade_id) as arr_grade', "$this->_name.*"))
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("GROUP_CONCAT(short_code) AS academic_year"))
                ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id", array("term_name"))
                ->joinLeft(array("course" => "course_master"), "course.course_id=$this->_name.course_id", array("course_name", "course_code"))
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.flag =?", $paper)
                ->where("$this->_name.employee_id =?", $faculty_id)
                ->order("$this->_name.$this->_id DESC")
                ->group(array("$this->_name.session", "$this->_name.cmn_terms", "$this->_name.course_id"));
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    //View purpose


    public function getStudentRecords($grade_allocation_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "number_value"))
                ->joinLeft(array("student" => "erp_student_information"), "student.student_id=allocation_items.student_id", array("CONCAT(student.stu_fname,' ',student.stu_lname) AS student_name", "student.student_id", "student.stu_id", 'student.reg_no', 'student.exam_roll', 'concat(student.father_fname," ",student.father_lname) as father_name'))
                ->joinLeft(array("academic_master"), "academic_master.academic_year_id=student.academic_id", array("short_code"))
                ->where("$this->_name.status!=2")
                ->where("$this->_name.grade_id=?", $grade_allocation_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getStudentRecordsNew($grade_allocation_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "number_value"))
                ->joinLeft(array("student" => "erp_student_information"), "student.student_id=allocation_items.student_id", array("CONCAT(student.stu_fname,' ',student.stu_lname) AS student_name", "student.student_id", "student.stu_id", 'student.reg_no', 'student.exam_roll', 'concat(student.father_fname," ",student.father_lname) as father_name'))
                ->joinLeft(array("academic_master"), "academic_master.academic_year_id=student.academic_id", array("short_code"))
                ->where("$this->_name.status!=2")
                ->where("$this->_name.grade_id in (?)", explode(',', $grade_allocation_id));
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getStudentRecordsNewWithAttendance($grade_allocation_id, $course, $ge_id, $term = '', $limit = 100, $offset = 0) {
        //  echo $limit; die;
        $details = $this->getRecord($grade_allocation_id);

        $select = $this->_db->select();
        $select->from($this->_name);
        $select->join(array("allocation_items" => "grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "number_value"));
        $select->join(array("student" => "erp_student_information"), "student.student_id=allocation_items.student_id", array("CONCAT(student.stu_fname,' ',student.stu_lname) AS student_name", "student.student_id", "student.stu_id", 'student.reg_no', 'student.exam_roll', 'concat(student.father_fname," ",student.father_lname) as father_name'));
        $select->join(array("academic_master"), "academic_master.academic_year_id=student.academic_id", array("short_code"));
        //    $select->join(array("semester_wise_attendance_report")," semester_wise_attendance_report.u_id = student.stu_id",array("component_paper","attend_status"));
        //$select->where("semester_wise_attendance_report.attend_status = 0" );
        if (!empty($ge_id)) {
            //  $select->where("semester_wise_attendance_report.ge_id = ?",$ge_id );
            //  $select->where("semester_wise_attendance_report.course_id = ?",$course );
        } $select->where("$this->_name.status!=2");
        //  $select->where("semester_wise_attendance_report.session = ?",$details['session']);
        //	$select->where("semester_wise_attendance_report.cmn_terms = ?",$details['cmn_terms'] );

        $select->where("$this->_name.grade_id in (?)", explode(',', $grade_allocation_id));
        $select->order('student.exam_roll');
        $select->group("student.stu_id");
        //  echo $select ;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getStudentRecordsNewBack($grade_allocation_id, $pay = false, $term_id = '') {

        $select = $this->_db->select();
        $select->from($this->_name);
        $select->joinLeft(array("allocation_items" => "back_grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "number_value"));
        $select->joinLeft(array("student" => "erp_student_information"), "student.student_id=allocation_items.student_id", array("CONCAT(student.stu_fname,' ',student.stu_lname) AS student_name", "student.student_id", "student.stu_id", 'student.reg_no', 'student.exam_roll', 'concat(student.father_fname," ",student.father_lname) as father_name'));
        $select->joinLeft(array("academic_master"), "academic_master.academic_year_id=student.academic_id", array("short_code"));
        if ($pay) {
            $select->join(array("payment_ug" => "ugnon_form_submission"), "payment_ug.student_id=allocation_items.student_id", array());
        }
        $select->where("$this->_name.status!=2");
        $select->where("$this->_name.grade_id in (?)", explode(',', $grade_allocation_id));
        if ($pay) {
            $select->where("payment_ug.payment_status =?", 1);
            $select->where("payment_ug.term_id =?", explode('t', $term_id)[1]);
        } //echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        if (!count($result))
            return $this->getStudentRecordsNewBackPg($grade_allocation_id, $pay, $term_id);
        return $result;
    }

    public function getStudentRecordsNewBackPg($grade_allocation_id, $pay = false, $term_id = '') {

        $select = $this->_db->select();
        $select->from($this->_name);
        $select->joinLeft(array("allocation_items" => "back_grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "number_value"));
        $select->joinLeft(array("student" => "erp_student_information"), "student.student_id=allocation_items.student_id", array("CONCAT(student.stu_fname,' ',student.stu_lname) AS student_name", "student.student_id", "student.stu_id", 'student.reg_no', 'student.exam_roll', 'concat(student.father_fname," ",student.father_lname) as father_name'));
        $select->joinLeft(array("academic_master"), "academic_master.academic_year_id=student.academic_id", array("short_code"));
        if ($pay) {
            $select->join(array("payment_pg" => "pg_non_form_data"), "payment_pg.student_id=allocation_items.student_id", array());
        }
        $select->where("$this->_name.status!=2");
        $select->where("$this->_name.grade_id in (?)", explode(',', $grade_allocation_id));
        if ($pay) {
            $select->where("payment_pg.payment_status =?", 1);
            $select->where("payment_pg.term_id =?", explode('t', $term_id)[1]);
        }
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getGradeRecords($academic_year_id, $department_id, $employee_id, $term_id, $course_id, $student_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "component_id", "number_value"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.course_id=?", $course_id)
                //->where("allocation_items.component_id=?",$component_id)
                ->where("allocation_items.student_id=?", $student_id);

        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result;
    }

    public function getGradeRecordsOn($academic_year_id, $term_id, $course_id, $student_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "component_id", "number_value"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.flag=?", 'R')
                ->where("$this->_name.course_id=?", $course_id)
                //->where("allocation_items.component_id=?",$component_id)
                ->where("allocation_items.student_id=?", $student_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
        public function getGradeFinalRecordsOn($academic_year_id, $term_id, $course_id, $student_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "component_id", "sum_with_seperator(number_value) as number_value"))
                ->join(array("components"=>"evaluation_components_items_master"), "components.course_id = $this->_name.course_id",array("sum_with_seperator(GROUP_CONCAT(components.weightage)) as total_weightage"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.flag=?", 'R')
                ->where("$this->_name.course_id=?", $course_id)
                //->where("allocation_items.component_id=?",$component_id)
                ->where("allocation_items.student_id=?", $student_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
      public function getGradeDeletedFinalRecordsOn($academic_year_id, $term_id, $course_id, $student_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "deleted_grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "component_id", "sum_with_seperator(number_value) as number_value"))
                ->join(array("components"=>"evaluation_components_items_master"), "components.course_id = $this->_name.course_id",array("sum_with_seperator(GROUP_CONCAT(components.weightage)) as total_weightage"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.flag=?", 'R')
                ->where("$this->_name.course_id=?", $course_id)
                //->where("allocation_items.component_id=?",$component_id)
                ->where("allocation_items.student_id=?", $student_id);
        echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getdeletedGradeRecordson($academic_year_id, $term_id, $course_id, $student_id, $dte) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "deleted_grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "component_id", "number_value"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.flag=?", 'R')
                ->where("$this->_name.course_id=?", $course_id)
                ->where("DATE_FORMAT(allocation_items.update_date,'%Y-%m-%d') =?", $dte)
                ->where("allocation_items.student_id=?", $student_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getGradeRecordsOnBack($academic_year_id, $term_id, $course_id, $student_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "back_grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value", "component_id", "number_value"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.course_id=?", $course_id)
                ->where("$this->_name.flag=?", 'B')
                //->where("allocation_items.component_id=?",$component_id)
                ->where("allocation_items.student_id=?", $student_id);

        $result = $this->getAdapter()
                ->fetchRow($select);

        return $result;
    }

    public function isGradeAllocated($batch_id, $faculty_id, $term_id, $course_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.academic_id=?", $batch_id)
                ->where("$this->_name.employee_id=?", $faculty_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.course_id=?", $course_id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;					  
        if (is_array($result) && !empty($result)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function isGradeAllocated1($batch_id, $faculty_id, $term_id, $course_id, $allocated = 'R') {
        $select = $this->_db->select()
                ->from($this->_name, array('grade_id'))
                ->where("$this->_name.academic_id=?", $batch_id)
                ->where("$this->_name.employee_id=?", $faculty_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.course_id=?", $course_id)
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.flag =?", $allocated);
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;					  
        if (is_array($result) && !empty($result)) {
            return $result['grade_id'];
        } else {
            return FALSE;
        }
    }

    public function getRecordsByCourseGroup($session_id, $course_group, $degree_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('GROUP_CONCAT(grade_id) as arr_grade', "$this->_name.*"))
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("GROUP_CONCAT(short_code) AS academic_year"))
                ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id", array("term_name"))
                ->joinLeft(array("course" => "course_master"), "course.course_id=$this->_name.course_id", array("course_name", "course_code"))
                ->where("$this->_name.session =?", $session_id)
                ->where("$this->_name.ge_id =?", $course_group)
                ->where("$this->_name.degree_id =?", $degree_id)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id ASC")
                ->group(array("$this->_name.cmn_terms", "$this->_name.course_id"));
        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo $select;die;
        return $result;
    }
    public function getFailStudents($term_id,$acad_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("grade_items" => "grade_allocation_items"), "grade_items.grade_allocation_id=$this->_name.grade_id", array(" count('*') as failed_stu","grade_value"))
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.academic_id=?", $acad_id)
                ->where("$this->_name.flag like ?",'R')
                ->where("grade_items.grade_value  REGEXP ?",'(F|D|Ab|NA)')
                ->group("grade_items.student_id");
        //->where("$this->_name.status !=?", 2);
       // echo $select;die;
        $result = $this->getAdapter()
            ->fetchAll($select);

        //secho '<pre>';print_r($result);exit;

        return $result;
    }
    public function getPassStudents($term_id,$acad_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("grade_items" => "grade_allocation_items"), "grade_items.grade_allocation_id=$this->_name.grade_id", array(" count('*') as passed_stu",'grade_value'))
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.academic_id=?", $acad_id)
                ->where("$this->_name.flag like ?",'R')
                ->where("grade_items.grade_value NOT REGEXP ?",'(F|D|Ab|NA)')
                ->group("grade_items.student_id");
                

        //->where("$this->_name.status !=?", 2);
        
        $result = $this->getAdapter()
                ->fetchAll($select);
        echo $select;die;


        return $result;
    }
}

?>