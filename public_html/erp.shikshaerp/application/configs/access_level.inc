<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



define('SS_ACAD_T',	111<<8);
define('SS_ACAD_M',	112<<8);
define('SS_ACAD_P',	113<<8);
define('SS_ACAD_FP',114<<8);
define('SS_ACAD_R',	115<<8);
define('SS_ACAD_A',	116<<8);
define('SS_ACAD_L',     117<<8);
define('SS_ACAD_SA',    118<<8);
define('SS_ACAD_EV',    119<<8);
define('SS_ACAD_PL',    120<<8);
define('SS_ACAD_ST',    126<<8);
define('SS_ACAD_AR',    127<<8);

global $security_sections,$security_areas;
   $security_sections = array(
        //==[ONLY FOR ACADEMIC] ==//
        SS_ACAD_T => ("Academic Transactions"),
        SS_ACAD_M => ("Academic Masters"),
        SS_ACAD_P => ("Academic Participants"),
        SS_ACAD_FP => ("Academic Faculty Portal"),
        SS_ACAD_R => ("Academic Reports"),
        SS_ACAD_A => ("Academic Attendance"),
        SS_ACAD_L => ("Academic LMS"),
        SS_ACAD_SA => ("Academic Seating Arrangment"),
        SS_ACAD_EV => ("Academic Events"),
        SS_ACAD_ST => ("Academic Settings"),
        SS_ACAD_AR=>("Academic Admission Report")
    );

$security_areas = array();

    $security_areas = array(//===[ACADEMIC SETUP]
              //masters
        'SA_ACAD_CLASS' => array(SS_ACAD_M | 1, ("Class")),
        'SA_ACAD_BATCH' => array(SS_ACAD_M | 2, ("Batch")),
        'SA_ACAD_DEPARTMENT' => array(SS_ACAD_M | 3, ("Department")),
        'SA_ACAD_COURSE_CAT' => array(SS_ACAD_M | 4, ("Course Category")),
        'SA_ACAD_COURSE_TYPE' => array(SS_ACAD_M | 5, ("Course Type")),
        'SA_ACAD_E_LEARN_C' => array(SS_ACAD_M | 6, ("Experential LEarning Component")),
        'SA_ACAD_FEE_CAT' => array(SS_ACAD_M | 7, ("Fee Cat")),
        'SA_ACAD_RATING' => array(SS_ACAD_M | 8, ("Rating")),
        'SA_ACAD_QUESTION' => array(SS_ACAD_M | 9, ("Question")),
        'SA_ACAD_CREDIT' => array(SS_ACAD_M | 10, ("Credit")),
        'SA_ACAD_FEE_HEADS' => array(SS_ACAD_M | 11, ("Fee Heads")),
        'SA_ACAD_SECTION' => array(SS_ACAD_M | 12, ("Section")),
        'SA_ACAD_ROOM' => array(SS_ACAD_M | 13, ("Room")),
        'SA_ACAD_TIME_TABLE' => array(SS_ACAD_M | 14, ("Time Table")),
        'SA_ACAD_COURSE' => array(SS_ACAD_M | 15, ("Course")),
        'SA_ACAD_CORE_COURSE' => array(SS_ACAD_M | 16, ("Core Course")),
        'SA_ACAD_EC_LEARN' => array(SS_ACAD_M | 17, ("Elective Course Learning")),
        'SA_ACAD_E_LEARN' => array(SS_ACAD_M | 18, ("Exprential Learning")),
        'SA_ACAD_REF_GRADE' => array(SS_ACAD_M | 19, ("Refrence Grade")),
        'SA_ACAD_PROG_DESIGN' => array(SS_ACAD_M | 20, ("Program Design")),
        'SA_ACAD_PROG_CAL_YEAR' => array(SS_ACAD_M | 21, ("Program Calendar Year")),
        'SA_ACAD_FEE_STRUCTURE' => array(SS_ACAD_M | 22, ("Fee Structure")),
        'SA_ACAD_SCHOLAR_SHIP' => array(SS_ACAD_M | 23, ("Scholar Ship")),
        'SA_ACAD_PART_SCHOLAR_SHIP' => array(SS_ACAD_M | 24, ("Participants Scholar ship")),
        'SA_ACAD_INTERACTIVE_LEARNING_SESSION' => array(SS_ACAD_M | 25, ("Interactive Learning Sessions")),
        'SA_ACAD_FEED_TEMPLATE' => array(SS_ACAD_M | 26, ("Feed BAck Templates")),
        'SA_ACAD_TERM_VIEW' => array(SS_ACAD_M | 27, ("Term View")),
        'SA_ACAD_CORE_COURSE_LEARN' => array(SS_ACAD_M | 28, ("Core Course Learn")),
        'SA_ACAD_E_LEARN_DESIGN_VIEW' => array(SS_ACAD_M | 29, ("Exprential Learn Desing View")),
        'SA_ACAD_PROG_CAL_VIEW' => array(SS_ACAD_M | 30, ("Program Calendar View")),
        'SA_ACAD_GLOBAL_SET' => array(SS_ACAD_M | 31, ("Global Settings")),
        'SA_ACAD_TERM_MASTER' => array(SS_ACAD_M | 32, ("Term Master")),
        'SA_ACAD_COURSE_FEE' => array(SS_ACAD_M | 33,('Exam Fee')),
        'SA_ACAD_ROOMMAP' => array(SS_ACAD_M | 34, ("Room Mapping")),
        'SA_ACAD_GE' => array(SS_ACAD_M | 35, ("Elective category")),
        'SA_ACAD_AECC_GE' => array(SS_ACAD_M | 36, ("Elective type")),
        'SA_ACAD_ACC' => array(SS_ACAD_M | 37, ("Account Master")),
        'SA_ACAD_TCHRDEPT' => array(SS_ACAD_M | 38, ("Teacher Department")),
        'SA_ACAD_PROM' => array(SS_ACAD_M | 39, ("Promotion Rules")),
        'SA_ACAD_ROUTINE' => array(SS_ACAD_M | 40, ("Add Routine")),
        'SA_ACAD_EXAMDATE' => array(SS_ACAD_M | 41, ("Exam Dates")),
        'SA_ACAD_EAR_CREDIT' => array(SS_ACAD_M | 42, ("Academics Credit")),
    
      //transaction
        'SA_ACAD_TO_DO_LIST' => array(SS_ACAD_T | 1, ("To Do List")),
        
        
        
     //faculty Portal);
        'SA_ACAD_FACULTY_ALLOTMENT' => array(SS_ACAD_FP | 1, ("Faculty Allotment")),
        'SA_ACAD_LEARNING_ALOTMENT' => array(SS_ACAD_FP | 2, ("Exprential Learning Allotment")),
        'SA_ACAD_EVALUATION_COMPONENT' => array(SS_ACAD_FP | 3, ("Evaluation Component")),
        'SA_ACAD_GRADE_ALLOCATION' => array(SS_ACAD_FP | 4, ("Grade Allocation")),
        
        'SA_ACAD_E_GRADE_ALLOCATION' => array(SS_ACAD_FP | 5, ("Exprential Grade Allocation")),
        'SA_ACAD_REVIEW_PUBLISH' => array(SS_ACAD_FP | 6, ("Review And Publish")),
        'SA_ACAD_LEARNING_PROJECT' => array(SS_ACAD_FP | 7, ("Exprential Learning Project")),
        'SA_ACAD_LEARN_PROJECT_ALLOCATION' => array(SS_ACAD_FP | 8, ("Exprential Learning Project Allocation ")),
        'SA_ACAD_NOTIFICATION' => array(SS_ACAD_FP | 9, ("Notification Push")),
        'SA_ACAD_ASSIGNMENT' => array(SS_ACAD_FP | 10, ("Participants Assignment")),
        'SA_ACAD_BACK_GRADE_ALLOCATION' => array(SS_ACAD_FP | 11, ("Back Grade Allocation")),
        'SA_ACAD_EARNED_CREDITS' => array(SS_ACAD_FP | 12, ("Earned Credits")),
                
     //participants
        'SA_ACAD_ENROLLMENT' => array(SS_ACAD_P | 1, ("Participants Enrollment")),
        'SA_ACAD_E_SELECTION' => array(SS_ACAD_P | 2, ("Elective Selection")),
        'SA_ACAD_APPLICATION_FORM' => array(SS_ACAD_P | 3, ("Application Form")),
        'SA_ACAD_STUDENT_DETAIL' => array(SS_ACAD_P | 4, ("Participants Details")),
        'SA_ACAD_STUD' => array(SS_ACAD_P | 5, ("Admission Student")),
        'SA_ACAD_PROMOTION' => array(SS_ACAD_P | 6, ("Promotion certification")),
        'SA_ACAD_TC' => array(SS_ACAD_P | 7, ("TC certification")),
        'SA_ACAD_CHAR' => array(SS_ACAD_P | 8, ("Character certification")),
        'SA_ACAD_SEMFEECOLL' => array(SS_ACAD_P | 9, ("Semester Fee Collection")),        
        'SA_ACAD_FORMFEECOLL' => array(SS_ACAD_P | 10, ("Form Fee Collection")),        
        'SA_ACAD_PASSOUT' => array(SS_ACAD_P | 11, ("PassOut certificate")),
                
    //Reports
        'SA_ACAD_GRADE_REPORT' => array(SS_ACAD_R | 1, ("Course Wise Grade Report")),
        'SA_ACAD_TRNC' => array(SS_ACAD_R | 8, ("Back Tabulation Register")),
        'SA_ACAD_TRC' => array(SS_ACAD_R | 9, ("Tabulation Register")),
        'SA_ACAD_PENALTIES_REPORT' => array(SS_ACAD_R | 2, ("Course Wise Penalties Report")),
        'SA_ACAD_AFTER_PENALTIES' => array(SS_ACAD_R | 3, ("Course Grade After Penalties")),
        'SA_ACAD_CGPA_GPA' => array(SS_ACAD_R | 4, ("Participants GPA/CGPA")),
        'SA_ACAD_TERM_GRADE_SHEET' => array(SS_ACAD_R | 5, ("Generate Term Wise Grade Sheet")),
        'SA_ACAD_BACK_REPORT' => array(SS_ACAD_R | 10, ("Generate NON Collegiate Term Wise Grade Sheet")),
        'SA_ACAD_FINAL_GRADE' => array(SS_ACAD_R | 6, ("Generate Final Grade")),
        'SA_ACAD_DIRECT_FINAL_GRADE' => array(SS_ACAD_R | 7, ("Direct Final Grade")),
        'SA_ACAD_STUDENTEXAMDETAILS' => array(SS_ACAD_R | 11, ("Exam Report")),
        'SA_ACAD_STUD_ADMITCARD' => array(SS_ACAD_R | 12, ("End Sem Admit Card")), 
		'SA_ACAD_NON_UG' => array(SS_ACAD_R | 13, ("NON UG ADMIT CARD")), 
		'SA_ACAD_NON_PG' => array(SS_ACAD_R | 14, ("NON PG ADMIT CARD")),
		'SA_ACAD_SHEET' => array(SS_ACAD_R | 20, ("Attendance sheet")),
		'SA_ACAD_FEE_INSDETAIL' => array(SS_ACAD_R | 21, ("Fee Details")),
		'SA_ACAD_TXN_DETAILS' => array(SS_ACAD_R | 22, ("Transaction Details")),
		'SA_ACAD_SEMFORMFEE' => array(SS_ACAD_R | 23, ("End Sem Form Payment")),
        'SA_ACAD_TRC_ALL' => array(SS_ACAD_R | 24, ("Accumulative Tabulation Register")),
        'SA_ACAD_PASSFAIL' => array(SS_ACAD_R | 25, ("Pass-Fail Report")),
                
    //Attendance
        'SA_ACAD_P_ATTENDANCE' => array(SS_ACAD_A | 1, ("Semester Wise Attendance")),

        'SA_ACAD_ATTENDANCE_REPORT' => array(SS_ACAD_A | 2, ("Attendance Report")),
        'SA_ACAD_M_ATTENDANCE' => array(SS_ACAD_A | 3, ("Monthly Attendance")),
        'SA_ACAD_D_ATTENDANCE' => array(SS_ACAD_A | 4, ("Daily Attendance")),
        'SA_ACAD_OFFLINE_ATTENDANCE' => array(SS_ACAD_A | 5, ("Monthly Attendance(Offline)")),
        'SA_ACAD_C_ATTENDANCE' => array(SS_ACAD_A | 6, ("Monthly Attendance(Online/Offline)")),
    
    //Admission Report
        'SA_ACAD_AR_DECLARERESULT' => array(SS_ACAD_AR | 1, ("Declare  Result")),
        'SA_ACAD_AR_DECLARELIST' => array(SS_ACAD_AR | 2, ("Declared  List")),
        'SA_ACAD_AR_ADMDOC' => array(SS_ACAD_AR | 3, ("Applicant  Document")),
		'SA_ACAD_AR_APPLIEDAPPLICANTS' => array(SS_ACAD_AR | 4, ("Applied Applicant Details")),
        'SA_ACAD_AR_SCRUTINY' => array(SS_ACAD_AR | 5, ("Scrutiny  Interface")),
        'SA_ACAD_AR_PRINCIPAL' => array(SS_ACAD_AR | 6, ("Principal  Interface")),
        'SA_ACAD_AR_ACCOUNT' => array(SS_ACAD_AR | 7, ("Account  Interface")),
        'SA_ACAD_AR_PAYMENT' => array(SS_ACAD_AR | 8, ("Payment  Interface")),
        'SA_ACAD_AR_ENTRANCEREPORT' => array(SS_ACAD_AR | 9, ("Entrance  Report")),
        'SA_ACAD_AR_APPLICANTDETAILS' => array(SS_ACAD_AR | 10, ("Applicant  Details")),
        'SA_ACAD_AR_ICARD' => array(SS_ACAD_AR | 11, ("I-Card  Details")),
        'SA_ACAD_AR_APPFORM' => array(SS_ACAD_AR | 12, ("Application  Form")),
     //LMS
        'SA_ACAD_BM' => array(SS_ACAD_L | 1, ("Book Managemnt")),
        'SA_ACAD_BH' => array(SS_ACAD_L | 2, ("Hold Book List")),
        'SA_ACAD_BE' => array(SS_ACAD_L | 3, ("Book Extension")),
		
    
    //SA_ACAD_ADDEVENT
        'SA_ACAD_ADDEVENT' => array(SS_ACAD_EV | 1,("Add Event")),
        'SA_ACAD_EVENTLIST' => array(SS_ACAD_EV | 2,("Event List")),
        'SA_ACAD_EVENTCATEGORY' => array(SS_ACAD_EV | 3,("Event Category")),
    
    //Seating Arrangment
        'SS_ACAD_OPERATION' => array(SS_ACAD_SA | 1,("Seating Plan")),
        'SS_ACAD_STUDENT' => array(SS_ACAD_SA | 2,("Add Student")),
        'SS_ACAD_DURATION' => array(SS_ACAD_SA | 3, ("Duration")),
        'SS_ACAD_EXAMBATCH' => array(SS_ACAD_SA | 4,("Exam Batch")),
        'SS_ACAD_SANSEAT' => array(SS_ACAD_SA | 5,("Sanction Seat")),
         
        
        //Settings
        'SA_ACAD_OTP_INFO' => array(SS_ACAD_ST | 1,("Manage OTP")),
        'SA_ACAD_NUMBER_UPDATE' => array(SS_ACAD_ST | 2,("Update Student Info")),
        'SA_ACAD_UPDATE_OFFLINE' => array(SS_ACAD_ST | 3,("Update Offline Applicant Info")),
        'SA_ACAD_ADMIN_SET' => array(SS_ACAD_ST | 4,("Users Authentication")),
        'SA_ACAD_RAZR' => array(SS_ACAD_ST | 5,("Razor")),
        'SA_ACAD_ADMIN_DOCSET' => array(SS_ACAD_ST | 6,("Unlock Doc Status")),     
                
    //-----------------------------Academic Access ----------// Acadmic Ends
    
                
                
            );

class accessLevel{
    
    
    public function __construct() {
        $this->setAccessLevel();
    }
    
    
        private function setAccessLevel(){ 
            global $security_areas;
        $k = $j = 0; //row colour counter
	     $ext = $sec = $m = -1;
        
	foreach($this->sort_areas($security_areas) as $area =>$parms ) {
            
		$newsec = ($parms[0]>>8)&0xff;
               
		$newext  = $parms[0]>>16; 
		define($area,$parms[0]);
		
	}  
        
     
 
    }
    
    private function sort_areas($areas)
{
	$old_order = 0;
	foreach($areas as $key => $area) {
		$areas[$key][] = $old_order++;
	}
	//uasort($areas,'comp_areas');
	return $areas;
}

public function setAccess($Action = '',$role_id = 'NA'){
//echo !in_array(constant($Action), $_SESSION['admin_login']['admin_login']->role_set); die;
    try{
    if(empty($Action)){
        throw new Exception("Action should not be empty in SetAccess Function");
    }
        else{
                if((!in_array(constant($Action), $_SESSION['admin_login']['admin_login']->role_set) && count($_SESSION['admin_login']['admin_login']->role_set) !=0 ) ){
                   
                    throw new Exception("Access Denied By System Administrator");
                }
        
            }
    }
    catch(Exception $e){
		 if($Action =='SA_ACAD_ADMIT_CARD')
                    return true;
      header("Location: /academic/batch-attendance/daily-attendance");
    
    
    }
    
}


    

}

?>