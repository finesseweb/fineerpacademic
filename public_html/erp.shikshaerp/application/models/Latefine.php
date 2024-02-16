<?php



class Application_Model_Latefine extends Zend_Db_Table_Abstract {



    public $_name = 'late_fine';

    protected $_id = 'id';


 public function getRecord($id) {

        $select = $this->_db->select()

                ->from($this->_name)

                ->where("$this->_name.$this->_id =?", $id);

             

        $result = $this->getAdapter()

                ->fetchRow($select);

        return $result;

    }

    





    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;

    }

    public function isFine($stu_id,$academic_id,$term){
          $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id =?", $stu_id)
                ->where("$this->_name.term =?", $term)
                  ->where("$this->_name.f_code like?", 'ok')
                ->where("$this->_name.academic_id =?", $academic_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;

    }
    

}

