<?php
namespace Banking;

abstract class Bank
{
    private $bankName;

    public function __construct($bankName)
    {
       $this->bankName = $bankName;
    }

    /*get the name of bank */
    abstract public function getBankName();
    
    /*get the account ownner list */
    abstract public function accountOwnerList($request);

    /*get the account ownner details */
    abstract public function accountDetails($request);

    /*get the account ownner deposit */
    abstract public function accountDeposit($request);

    /*account withdrawal */
    abstract public function accountWithdrawal($request);

    /* Account balance transfer */
    abstract public function accountBalanceTransfer($request);
    
    /*account remove soft delete */
    abstract public function removeAccount($request);

    /*account type with owner name */
    abstract public function accountTypeWithOwnerName($request);
}

?>
