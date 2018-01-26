<?php
/*-----------------------------
Author: Anoop Santhanam
Date Created: 25/1/18 18:38
Last modified: 25/1/18 18:38
Comments: Main class file for
partner_master table.
------------------------------*/
class partnerMaster extends userMaster
{
    public $app=NULL;
    public $partnerValid=false;
    private $partner_id=NULL;
    function __construct($partnerID=NULL)
    {
        $this->app=$GLOBALS['app'];
        if($partnerID!=NULL)
        {
            $this->partner_id=secure($partnerID);
            $this->partnerValid=$this->verifyPartner();
        }
    }
    function verifyPartner()
    {
        if($this->partner_id!=NULL)
        {
            $app=$this->app;
            $partnerID=$this->partner_id;
            $pm="SELECT idpartner_master FROM partner_master WHERE stat='1' AND idpartner_master='$partnerID'";
            $pm=$app['db']->fetchAssoc($pm);
            if(validate($pm))
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
    function getPartner()
    {
        if($this->partnerValid)
        {
            $app=$this->app;
            $partnerID=$this->partner_id;
            $pm="SELECT * FROM partner_master WHERE stat='1' AND idpartner_master='$partnerID'";
            $pm=$app['db']->fetchAssoc($pm);
            if(validate($pm))
            {
                return $pm;
            }
            else
            {
                return "INVALID_PARTNER_ID";
            }
        }
        else
        {
            return "INVALID_PARTNER_ID";
        }
    }
}
?>