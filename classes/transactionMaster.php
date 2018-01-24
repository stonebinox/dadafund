<?php
/*----------------------------------
Author: Anoop Santhanam
Date created: 18-1-18 11:06
Last modified: 18-1-18 11:06
Comments: Main class file for
transaction_master table.
-----------------------------------*/
class transactionMaster extends userMaster
{
    public $app=NULL;
    private $transaction_id=NULL;
    public $transactionValid=false;
    function __construct($transactionID=NULL)
    {
        $this->app=$GLOBALS['app'];
        if($transactionID!=NULL)
        {
            $this->transaction_id=secure($transactionID);
            $this->transactionValid=$this->verifyTransaction();
        }
    }
    function verifyTransaction()
    {
        if($this->transaction_id!=NULL)
        {
            $app=$this->app;
            $transactionID=$this->transaction_id;
            $tm="SELECT user_master_iduser_master FROM transaction_master WHERE stat='1' AND idtransaction_master='$transactionID'";
            $tm=$app['db']->fetchAssoc($tm);
            if(validate($tm))
            {
                $userID=$tm['user_master_iduser_master'];
                userMaster::__construct($userID);
                if($this->userValid)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    function addTransaction($email,$amount=0)
    {
        $email=secure($email);
        $userID=userMaster::getUserIDFromEmail($email);
        if(is_numeric($userID))
        {
            $amount=secure($amount);
            if((validate($amount))&&(is_numeric($amount))&&($amount>=0))
            {
                $app=$this->app;
                $nextValue=intval($amount+1);
                $balance=$nextValue-$amount;
                $in="INSERT INTO transaction_master (timestamp,user_master_iduser_master,original_amount,amount_difference) VALUES (NOW(),'$userID','$amount','$balance')";
                $in=$app['db']->executeQuery($in);
                $tm="SELECT idtransaction_master FROM transaction_master WHERE stat='1' AND user_master_iduser_master='$userID' ORDER BY idtransaction_master DESC LIMIT 1";
                $tm=$app['db']->fetchAssoc($tm);
                $transactionID=$tm['idtransaction_master'];
                return $transactionID;
            }   
            else
            {
                return "INVALID_TRANSACTION_AMOUNT";
            }
        }
        else
        {
            return "INVALID_USER_EMAIL";
        }
    }
}
?>