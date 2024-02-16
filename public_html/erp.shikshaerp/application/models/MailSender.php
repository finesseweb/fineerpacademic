<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
require  APPLICATION_PATH . '/public/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class Application_Model_MailSender extends Zend_Db_Table_Abstract
{
     private $_mail;
     protected $_to;
     protected $_from;
     protected $_subject;
     protected $_message;

     public function __construct(array $to = array(), array $from = array(), string $subject, string $message) {
         $this->_mail =  new PHPMailer();
         $this->_to = $to;
         $this->_from = $from;
         $this->_subject = $subject;
         $this->_message = $message;
     }
     
     
     
    public function mail($username,$password){
         $this->_mail->SMTPDebug = 0;                     // Enable verbose debug output
                    $this->_mail->isSMTP(true);                                            // Send using SMTP
                    $this->_mail->Host  = 'localhost';
                    $this->_mail->SMTPOptions = array(
    'ssl' => [
        'verify_peer' => true,
        'verify_depth' => 3,
        'allow_self_signed' => true,
        'peer_name' => 'smtp.gmail.com',
        'cafile' => '/etc/ssl/ca_cert.pem',
    ],
);
                    $this->_mail->SMTPAuth = true;
                    $this->_mail->Username = $username;
                    $this->_mail->Password = $password;
                    $this->_mail->SMTPAutoTLS = false; 
                    $this->_mail->Priority  = 1;
                    $this->_mail->addCustomHeader("X-MSMail-Priority: High");
                    $this->_mail->addCustomHeader("Importance: High");
                    $this->_mail->SMTPSecure =ENCRYPTION_STARTTLS;
                    $this->_mail->Port = 587;                                   
                    $this->_mail->CharSet = "UTF-8";

                    //Recipients
                    $this->_mail->setFrom($this->_from[0], $this->_from[1]);              // Name is optional
                    $this->_mail->addAddress($this->_to[0]);

                  

                    // Content
                    $this->_mail->isHTML(true);                                  // Set email format to HTML
                    
                    $this->_mail->Subject = $this->_subject;
                   $this->_mail->MsgHTML($this->_message);  
                   return $this->_mail->send();
     }
     
     
     
     
 }

