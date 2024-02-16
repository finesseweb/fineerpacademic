<?php/**  * @Framework Zend Framework * @Powered By TIS  * @category   ERP Product * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd. * (http://www.techintegrasolutions.com) *	Authors Kannan and Rajkumar */class Application_Model_FeeStructureTermItems extends Zend_Db_Table_Abstract{    public $_name = 'erp_fee_structure_term_items';    protected $_id = 'term_items_id';      //get details by record for edit	public function getRecord($id)  //company_id is main Company Id    {               $select=$this->_db->select()                      ->from($this->_name)                      ->where("$this->_id=?",$id);        $result=$this->getAdapter()                      ->fetchRow($select);  //print_r($result); die;					          return $result;    }			public function getRecords()    {               $select=$this->_db->select()                      ->from($this->_name);        $result=$this->getAdapter()                      ->fetchAll($select);   		//print_r($result);die;	          return $result;    }		//View purpose			public function trashItems($structure_id) {        $this->_db->delete($this->_name, "structure_id=$structure_id");    }		public function getItemRecords($structure_id)  //company_id is main Company Id    {               $select=$this->_db->select()                      ->from($this->_name)                      ->where("$this->_name.structure_id=?",$structure_id);        $result=$this->getAdapter()                      ->fetchAll($select);  		//print_r($result); die;					          return $result;    }         	public function getItemRecordsByTerm($structure_id,$term)  //company_id is main Company Id    {               $select=$this->_db->select()                      ->from($this->_name) ->where("$this->_name.terms_id = ?",$term)                      ->where("$this->_name.structure_id=?",$structure_id);                     // ->order(array("category_id asc","fee_heads_id asc"));        $result=$this->getAdapter()                      ->fetchAll($select);  		//print_r($result); die;					          return $result;    }            public function gettotalFee($structure_id,$term){         $select=$this->_db->select()                      ->from($this->_name,array("sum(fees) as fee","category_id"))                      ->where("$this->_name.terms_id = ?",$term)                      ->where("$this->_name.structure_id=?",$structure_id)                     // ->order(array("category_id asc","fee_heads_id asc"));                    ->group("category_id");                          $result=$this->getAdapter()                      ->fetchAll($select);  		//print_r($result); die;					          return $result;            }        	public function getFee($structure_id,$cmn_terms)  //company_id is main Company Id    {               $select=$this->_db->select()                      ->from($this->_name,array("sum(fees) as totalfee"))                      ->join(array('cat'=>"erp_fee_category_master"),"cat.category_id = $this->_name.category_id",array())                      ->join(array('acc'=>"account_master"),"acc.id = cat.fund_type",array("acc.acc_name"))                      ->where("$this->_name.structure_id=?",$structure_id)                      ->where("$this->_name.terms_id=?",$cmn_terms)                      ->group(array("cat.fund_type","$this->_name.terms_id"));                    //  echo $select; die;        $result=$this->getAdapter()                      ->fetchAll($select);  		//print_r($result); die;					          return $result;    }         	public function getFeeByaccname($structure_id,$cmn_terms,$acc_name)  //company_id is main Company Id    {               $select=$this->_db->select()                      ->from($this->_name,array("sum(fees) as totalfee"))                      ->join(array('cat'=>"erp_fee_category_master"),"cat.category_id = $this->_name.category_id",array())                      ->join(array('acc'=>"account_master"),"acc.id = cat.fund_type",array("acc.acc_name"))                      ->where("$this->_name.structure_id=?",$structure_id)                      ->where("$this->_name.terms_id=?",$cmn_terms)->where("acc.acc_name in (?)",$acc_name)                                            ->group(array("$this->_name.terms_id"));                    //  echo $select; die;        $result=$this->getAdapter()                      ->fetchAll($select);  		//print_r($result); die;					          return $result;    }             	public function getTotFee($structure_id,$cmn_terms)  //company_id is main Company Id    {               $select=$this->_db->select()                      ->from($this->_name,array("sum(fees) as totalfee"))                      ->join(array('cat'=>"erp_fee_category_master"),"cat.category_id = $this->_name.category_id",array())                      ->join(array('acc'=>"account_master"),"acc.id = cat.fund_type",array("acc.acc_name"))                      ->where("$this->_name.structure_id=?",$structure_id)                      ->where("$this->_name.terms_id=?",$cmn_terms)                      ->group(array("$this->_name.terms_id"));                    //  echo $select; die;        $result=$this->getAdapter()                      ->fetchAll($select);  		//print_r($result); die;					          return $result;    } 		public function getFeesRecords($structure_id,$cat_ids,$fhead_ids,$term_id)      {                           $select=$this->_db->select()                      ->from($this->_name)                      ->where("$this->_name.structure_id=?",$structure_id)					  ->where("$this->_name.category_id=?",$cat_ids)					  ->where("$this->_name.fee_heads_id=?",$fhead_ids)					  ->where("$this->_name.terms_id=?",$term_id);        $result=$this->getAdapter()                      ->fetchRow($select);  		//echo'<pre>';print_r($result); 					          return $result;    } 		public function getFeeTermTotals($structure_id,$cat_ids,$term_id)      {               $select=$this->_db->select()                      ->from($this->_name)                      ->where("$this->_name.structure_id=?",$structure_id)					  ->where("$this->_name.category_id=?",$cat_ids)					  ->where("$this->_name.terms_id=?",$term_id);        $result=$this->getAdapter()                      ->fetchRow($select);  		//echo'<pre>';print_r($result); 					          return $result;    }         public function getFeeTermTotal1($structure_id,$cat_ids,$term_id)      {               $select=$this->_db->select()                      ->from($this->_name,array('sum(fees) as total'))                      ->where("$this->_name.structure_id=?",$structure_id)		 ->where("$this->_name.category_id=?",$cat_ids)		 ->where("$this->_name.terms_id=?",$term_id);        $result = $this->getAdapter()                      ->fetchRow($select);         return $result['total'];    }         public function getAcademicId($id){                $select=$this->_db->select()                      ->from('erp_fee_structure_master', array('academic_id','department'))                      ->joinLeft(array("dept" => "department"),"dept.id = erp_fee_structure_master.department",array("degree_id"))                      ->where("structure_id=?",$id)                        ->where("erp_fee_structure_master.status !=?",2);        $result=$this->getAdapter()                      ->fetchRow($select);   					          return $result;                            }}?>