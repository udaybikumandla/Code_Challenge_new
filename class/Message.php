<?php
namespace Messages;

class Message{

    public function ApiTypeInvaliedMsg()
    {
        return "API type invalied.";
    }
    public function AccountOwnerNameExitsMsg(){
        return "Account owner name already exits.";
    }
    public function AccountNumberExitsMsg(){
        return "Account number already exits.";
    }
    public function MobileNumberExitsMsg(){
        return "Mobile Number already exits.";
    }

    public function AccountSuccessMsg()
    {
        return "Account created successfully";
    }
    public function NoAccount()
    {
        return "There is no account.";
    }
    public function AccountUpdateSuccess()
    {
        return "Account Updated Successfully.";
    }
    public function BankOwenerList()
    {
        return "Bank Account Owner List.";
    }
    public function NoAccounts()
    {
        return "There is no accouts list.";
    }
    public function BankOwenerDetails()
    {
        return "Bank account owner details.";
    }
    public function DepositeSuccess()
    {
        return "Amount Deposited successfully.";
    }

    public function InsufficientMsg()
    {
        return "You have insufficient balance.";
    }

    public function IndividualWithdrawlLimitMSG()
    {
        return "Individual accounts have a withdrawal limits of 500 dollers";
    }

    public function WithdrawalScussessMSG()
    {
        return "Amount withdrawal successfully.";
    }

    public function TransactionTypeInvaliedMSG()
    {
        return "Transaction type invalied.";
    }

    public function FromAccountNotExitMsg()
    {
        return "From account not exits or not activated.";
    }

    public function ToAccountNotExitMsg()
    {
        return "To account not exits or not activated.";
    }

    public function TrasferredSuccessMsg()
    {
        return "Amount transferred successfully.";
    }

    public function OwnerNotActiveMsg()
    {
        return "This Accoout owner id in-active.";
    }

    public function OwnerNameWithType()
    {
        return "Bank account owner name with account types.";
    }

    public function AccountTypeMsg()
    {
        return "Please provide bank account type.";
    }

    public function subAccountTypeMsg()
    {
        return "Please provide bank sub account type.";
    }

    public function OwnerNameMSg()
    {
        return "Please provide owner name.";
    }

    public function OwnerNameValidationMsg()
    {
        return "Owner name should be only alphabets and whitespace allowed.";
    }

    public function  MobileNumberMsg()
    {
        return "Please provide mobile number.";
    }

    public function AddressMsg()
    {
        return "Please provide address.";
    }

    public function OwnerIdMsg()
    {
        return "Please provide ownerId.";
    }

    public function transactionTypeMsg()
    {
         return "Please provide transactionType.";
    }
    public function transactionTypInvaliedMsg()
    {
        return "TransactionType invalied.";
    }
    public function ammountMsg()
    {
        return "Please provide amount.";
    }
    public function transactionModeMsg()
    {
        return "Please provide tarnsactionMode.";
    }

    public function accountRemovedSuccessMsg()
    {
        return "Owner account has been removed.";
    }

    public function  toOwnerIdMsg()
    {
        return "Please provide toOwnerid.";
    }

    public function toAccountNumber()
    {
        return "Please provide toaccountno.";
    }

    public function accountNumberMsg()
    {
      return "Please provide account no.";   
    }
}
?>
