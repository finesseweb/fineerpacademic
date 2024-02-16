<?php

class Application_Model_TransactionResponse extends Zend_Db_Table_Abstract {

    private $respHashKey = "";

    /**
     * @return string
     */
    public function getRespHashKey()
    {
        return $this->respHashKey;
    }

    /**
     * @param string $respHashKey
     */
    public function setRespHashKey($respHashKey)
    {
        $this->respHashKey = $respHashKey;
    }



    public function validateResponse($responseParams)
    {
        $str = $responseParams["mmp_txn"].$responseParams["mer_txn"].$responseParams["f_code"].$responseParams["prod"].$responseParams["discriminator"].$responseParams["amt"].$responseParams["bank_txn"].$responseParams["udf1"].$responseParams["udf2"].$responseParams["udf3"].$responseParams["udf4"].$responseParams["udf5"];
        $signature =  hash_hmac("sha512",$str,$this->respHashKey,false);
        if($signature == $responseParams["signature"]){
            return true;
        } else {
            return false;
        }

    }
}
