<?php
date_default_timezone_set('Asia/Calcutta');
$datenow = date("d/m/Y h:m:s");
$transactionDate = str_replace(" ", "%20", $datenow);

$transactionId = rand(1,1000000);

require_once 'TransactionRequest.php';

$transactionRequest = new TransactionRequest();

//Setting all values here
$transactionRequest->setMode("live");
$transactionRequest->setLogin(53243);
$transactionRequest->setPassword("Patna@1234");
$transactionRequest->setProductId("PRINCIPAL_PWC");
$transactionRequest->setAmount(1000);
$transactionRequest->setTransactionCurrency("INR");
$transactionRequest->setTransactionAmount(1000);
$transactionRequest->setReturnUrl("https://www.finessewebtech.com/paymentgw/response.php");
$transactionRequest->setClientCode(53243);
$transactionRequest->setTransactionId($transactionId);
$transactionRequest->setTransactionDate($transactionDate);
$transactionRequest->setCustomerName("Raushan");
$transactionRequest->setCustomerEmailId("arya0367@gmail.com");
$transactionRequest->setCustomerMobile("9999999999");
$transactionRequest->setCustomerBillingAddress("Mumbai");
$transactionRequest->setCustomerAccount("639827");
$transactionRequest->setReqHashKey("b338c08a991313f12c");


$url = $transactionRequest->getPGUrl();

header("Location: $url");