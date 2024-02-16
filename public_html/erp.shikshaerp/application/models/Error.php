<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Model_Error extends Zend_Db_Table_Abstract
{
     
  public function err($message){  
  throw new Exception($message);die;   
  }
    
}

