<?php
/*
    Author : Kedar Kumar
    Date: 05 Jan 2021
 */
class Application_Model_FormValidation extends Zend_Db_Table_Abstract 
{
   
    function checkAlphabetsInput($data){
        $input= trim($data);
        if(preg_match("/^[a-zA-Z ]*$/", $data)){
            return $data;
            //echo 'Passed';die;
        }else{
            echo "<div style='text-align:center;position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);'>
     <img src='/academic/public/images/blackCatImages.jpg' width='100px' class='black_cat_img'> <br/>
     <b> Invalid Input Types.</b><br/><span style='color:green;'>you will be redirected in 3 sec...</span><div>";
            header( "refresh:3;" );die;
        }
        //echo '<pre>'; print_r($inpu);exit;
        //return true;
    }
    function checkNumberInput($data){
        $input= trim($data);
        if(preg_match("/^[0-9]*$/", $data)){
            return $data;
            //echo 'Passed';die;
        }else{
            echo "<div style='text-align:center;position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);'>
     <img src='/academic/public/images/blackCatImages.jpg' width='100px' class='black_cat_img'> <br/>
     <b> Invalid Input Types.</b><br/><span style='color:green;'>you will be redirected in 3 sec...</span><div>";
            header( "refresh:3;" );die;
        }
    }
    function checkValidInput($data){
        $input= trim($data);
        if(preg_match("/^[a-zA-Z0-9+-; :#@&\(\/)]*$/", $data)){
            return $data;
        }else{
           echo "<div style='text-align:center;position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);'>
     <img src='/academic/public/images/blackCatImages.jpg' width='100px' class='black_cat_img'> <br/>
     <b> Invalid Input Types.</b><br/><span style='color:green;'>you will be redirected in 3 sec...</span><div>";
            header( "refresh:3;" );die;
        }
    }
    function checkEmailInput($data){
        $input= trim($data);
        if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $data)){
            return $data;
        }else{
            
            echo "<div style='text-align:center;position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);'>
     <img src='/academic/public/images/blackCatImages.jpg' width='100px' class='black_cat_img'> <br/>
     <b> Invalid Email Id.</b><br/><span style='color:green;'>you will be redirected in 3 sec...</span><div>";
              header( "refresh:3;" );die;
        }
           
    }
    
}
?>