<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GradeSheet
 *
 * @author w10
 */
class Application_Model_FinalGradeNo extends Zend_Db_Table_Abstract {
    public $_name = 'final_grade_no';
    protected $_id = 'id';
  
    
      public function getGradeSheetNumber($batch_id, $student_dmi_id) {
         
        $select = $this->_db->select()
                ->from($this->_name,array('grade_no as sr_no'))                
                ->where("$this->_name.academic =?", $batch_id)
                ->where("$this->_name.student =?", $student_dmi_id);
       // echo $select; echo "<br />";
        $result = $this->getAdapter()
                ->fetchRow($select);
      if(is_array($result))
          return $result['sr_no'];
      else 
          return 0;
    }
    
    
}
