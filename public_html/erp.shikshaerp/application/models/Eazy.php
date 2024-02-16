<?php

class Application_Model_Eazy extends Zend_Db_Table_Abstract 
{
    public $merchant_id;
    public $encryption_key;
    public $sub_merchant_id;
    public $reference_no;
    public $paymode;
    public $return_url;
    public $name;
    public $phone_no ;
    public $email;
    public $form_no;
    public $department;
    public $idr;

    const DEFAULT_BASE_URL = 'https://eazypay.icicibank.com/EazyPG?';

    public function __construct()
    {
        $this->merchant_id              =    '302278';
        $this->encryption_key           =    '3043170922705000';
        $this->sub_merchant_id          =    '45';
        $this->merchant_reference_no    =    '456538';
        $this->paymode                  =    '9';
        $this->return_url               =    'https://pwcadmissions.in/academic/application/semfeeresponse-print/?id=';
    }

    public function getPaymentUrl($amount, $reference_no, $optionalField=null)
    {
        $mandatoryField   =    $this->getMandatoryField($amount, $reference_no);
        $optionalField    =    $this->getOptionalField($optionalField);
        $amount           =    $this->getAmount($amount);
        $reference_no     =    $this->getReferenceNo($reference_no);

        $paymentUrl = $this->generatePaymentUrl($mandatoryField, $optionalField, $amount, $reference_no);
        return $paymentUrl;
    }

    protected function generatePaymentUrl($mandatoryField, $optionalField, $amount, $reference_no)
    {
       
        $encryptedUrl = self::DEFAULT_BASE_URL."merchantid=".$this->merchant_id."&mandatory fields=".$mandatoryField."&optional fields=".$optionalField."&returnurl=".$this->getReturnUrl()."&Reference No=".$reference_no."&submerchantid=".$this->getSubMerchantId()."&transaction amount=".$amount."&paymode=".$this->getPaymode();

        return $encryptedUrl;
    }

    protected function getMandatoryField($amount, $reference_no)
    {
     //   echo "<pre>";print_r($reference_no.'|'.$this->sub_merchant_id.'|'.$amount.'|'.$this->name.'|'.$this->phone_no.'|'.$this->email.'|'.$this->form_no.'|'.$this->department);die;
        return $this->getEncryptValue($reference_no.'|'.$this->sub_merchant_id.'|'.$amount.'|'.$this->name.'|'.$this->phone_no.'|'.$this->email.'|'.$this->form_no.'|'.$this->department);
    }

    // optional field must be seperated with | eg. (20|20|20|20)
    protected function getOptionalField($optionalField=null)
    {
        if (!is_null($optionalField)) {
            return $this->getEncryptValue($optionalField);
        }
        return null;
    }

    protected function getAmount($amount)
    {
        return $this->getEncryptValue($amount);
    }

    protected function getReturnUrl()
    {
        return $this->getEncryptValue($this->return_url);
    }

    protected function getReferenceNo($reference_no)
    {
        return $this->getEncryptValue($reference_no);
    }

    protected function getSubMerchantId()
    {
        return $this->getEncryptValue($this->sub_merchant_id);
    }

    protected function getPaymode()
    {
        return $this->getEncryptValue($this->paymode);
    }

    // use @ to avoid php warning php 

    protected function getEncryptValue($plaintext)
    {
//return $plaintext;
        $key = $this->encryption_key;
        $cipher = "aes-128-ecb";
    if (in_array($cipher, openssl_get_cipher_methods())){
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv);
        return $ciphertext;
    }
}
   
    
}

