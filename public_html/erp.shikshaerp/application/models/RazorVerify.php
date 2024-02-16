<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require  APPLICATION_PATH .'/public/razorpay/config.php';
require  APPLICATION_PATH . '/public/razorpay/razorpay-php/Razorpay.php';

class Application_Model_RazorVerify extends Razorpay\Api\Errors\SignatureVerificationError {
    
}


    
