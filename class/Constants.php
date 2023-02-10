<?php
namespace AllConstants;

Class Constants{

    protected const ACCOUNT_TYPE_CHECKING = 'Checking';
    protected const ACCOUNT_TYPE_INVESTMENT = 'investment';
    protected const SUB_ACCOUNT_TYPE_INDIVIDUAL ='individual';
    protected const SUB_ACCOUNT_TYPE_CORPORATE ='corporate';
    
    public function AccounttypeChecking() {
        return self:: ACCOUNT_TYPE_CHECKING;
    } 

    public function AccounttypeInvestment() {
        return self:: ACCOUNT_TYPE_INVESTMENT;
    } 

    public function SubAccounttypeIndividual(){
        return self:: SUB_ACCOUNT_TYPE_INDIVIDUAL;
    }

    public function subAccounttypecorporate(){
        return self:: SUB_ACCOUNT_TYPE_CORPORATE;
    }

}
