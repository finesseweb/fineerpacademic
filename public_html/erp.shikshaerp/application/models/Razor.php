<?php
require  APPLICATION_PATH . '/public/razorpay/razorpay-php/Razorpay.php'; 

class Application_Model_Razor extends \Razorpay\Api\Api {
   public function __construct($key, $secret)
    {
       parent::__construct($key, $secret);
    }    
}


    

?>