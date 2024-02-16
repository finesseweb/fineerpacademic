<?php

require_once 'TransactionResponse.php';
$transactionResponse = new TransactionResponse();
$transactionResponse->setRespHashKey("8b013258b7a4b89428");

if($transactionResponse->validateResponse($_POST)){
    echo "Transaction Processed <br/>";
    print_r($_POST);
} else {
    echo "Invalid Signature";
}


//print_r($_POST);
