<?php

namespace Accounts;
require_once('config/config.php');
require_once 'Bank.php';
use \Exception;
use Banking\bank as Bank;
use AllConstants\Constants as Constant;
use Messages\Message as Message;
session_start();
class Account extends Bank
{
    public $bankId;
    public $bankAccountTypeId;
    public $bankSubAccountTypeId;
    private $bankName;
    
    public function __construct($bankName)
    {
        parent::__construct($bankName);
        
    }
    public function getBankName()
    {
        return $this->bankName;
    }
    public function createAccount($request)
    {   
        try {
           $error = Account::insertValidation($request);
            if ($error['status'] == 1) {
                $acountNumberStart = 50100;
                $randumNumber = rand(10000, 99999);
                $acountNumber = $acountNumberStart . '' . $randumNumber;
                $balance = 0;
                $balance = sprintf('%01.2f', $balance);
                $accountsData = [];
                $result = [];
                if (
                    !empty($_SESSION['create_account']) &&
                    isset($_SESSION['create_account'])
                ) {
                    foreach ($_SESSION['create_account'] as $accountData) {
                        if (in_array($request['ownerName'], $accountData)) {
                            throw new Exception(Message::ACCOUNT_OWNER_NAME_EXITS);
                        }
                        if (in_array($acountNumber, $accountData)) {
                            throw new Exception(Message::ACCOUNT_OWNER_NAME_EXITS);
                        }
                        if (in_array($request['mobile'], $accountData)) {
                            throw new Exception(Message::MOBILE_NUMBER_EXITS);
                        }
                    }
                }
                if (
                    isset($_SESSION['create_account']) &&
                    !empty($_SESSION['create_account'])
                ) {
                    if (count($_SESSION['create_account']) > 0) {
                        $last_list = end($_SESSION['create_account']);
                        $id = $last_list['id'] + 1;
                        $request['id'] = $id;
                    } else {
                        $request['id'] = '1';
                    }
                } else {
                    $request['id'] = '1';
                    $_SESSION['create_account'] = [];
                }
                 if ($request['accountSubType'] != '') {
                    $subAccount_type =  $request['accountSubType'];
                } else {
                    $subAccount_type = 'NULL';
                }
                $result['id'] = $request['id'];
                $result['bank_name'] = BANK_NAME;
                $result['accountType'] = $request['accountType'];
                $result['account_sub_type'] =  $subAccount_type;
                $result['account_number'] = $acountNumber;
                $result['account_owner_name'] = $request['ownerName'];
                $result['account_balance'] = $balance;
                $result['mobile'] = $request['mobile'];
                $result['address'] = $request['address'];
                $result['account_status'] = 'Active';
                $_SESSION['create_account'][$request['id']] = $result;
                $data = [
                    'status' => 'success',
                    'data' => $result,
                    'message' => Message::ACCOUNT_SUCCESS_MSG,
                ];
                echo json_encode($data);
            } else {
                throw new Exception($error['message']);
            }
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            echo json_encode($data);
        }
    }

    public function UpdateAccount($request)
    {
        try {
            $error = Account::UpdateAccountValidation($request);
            if ($error['status'] == 1) {
                $ownerId = $request['ownerId'];
                $accountType = $request['accountType'];
                $accountSubType = $request['accountSubType'];
                $ownerName = $request['ownerName'];
                $mobile = $request['mobile'];
                $address = $request['address'];
                if (isset($_SESSION['create_account'])) {
                    $accountsData = $_SESSION['create_account'];
                } else {
                    $accountsData = [];
                }
                if (count($accountsData) > 0) {
                    $result = [];
                    foreach ($accountsData as $key => $accounts) {
                      
                        if ( $ownerId == $accounts['id'] && $accounts['account_status'] == 'Active') {
                           
                            $accountsData[$key]['accountType'] = $accountType;
                            $accountsData[$key]['account_sub_type'] = $accountSubType;
                            $accountsData[$key]['account_owner_name'] = $ownerName;
                            $accountsData[$key]['mobile'] = $mobile;
                            $accountsData[$key]['address'] = $address;
                            $_SESSION['create_account'] = $accountsData;

                            if($accounts['accountType']==Constant::ACCOUNT_TYPE_CHECKING)
                            {
                                $accountType = Constant::ACCOUNT_TYPE_CHECKING;
                            }
                            elseif($accounts['accountType']== Constant::ACCOUNT_TYPE_INVESTMENT)
                            {
                                $accountType = Constant::ACCOUNT_TYPE_INVESTMENT;
                            }
                           
                            if ($accounts['account_sub_type'] != ''&&  $accounts['account_sub_type']!='NULL' ) {
                                if($accounts['account_sub_type']== Constant::SUB_ACCOUNT_TYPE_INDIVIDUAL){
                                    $account_sub_type =  Constant::SUB_ACCOUNT_TYPE_INDIVIDUAL;
                                }
                                if($accounts['account_sub_type']== Constant::SUB_ACCOUNT_TYPE_CORPORATE){
                                    $account_sub_type =  Constant::SUB_ACCOUNT_TYPE_CORPORATE;
                                }
                                
                            } else {
                                $account_sub_type = 'NULL';
                            }
                            $result['bank_name'] = BANK_NAME;
                            $result['accountType'] = $accountType;
                           $result['subAccountType'] = $accountSubType;
                            $result['accountOwenrId'] = $ownerId;
                            $result['accountOwenName'] = $ownerName;
                            $result['account_no'] = $accounts['account_number'];
                            $result['availableBalance'] =
                                $accounts['account_balance'];
                            $result['mobile'] = $mobile;
                            $result['address'] = $address;
                            $result['accountStatus'] =
                            $accounts['account_status'];
                        }
                    }
                } else {
                    $data = [
                        'status' => 'success',
                        'data' => $accountsData,
                        'message' => Message::NO_ACCOUNT_MSG,
                    ];
                }
               
                $data = [
                   
                    'status' => 'success',
                    'data' => $result,
                    'message' => Message::ACCOUNT_UPDATE_SUCCESS,
                ];
                echo json_encode($data);
            } else {
                throw new Exception($error['message']);
            }
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            echo json_encode($data);
        }
    }

    /*Account owners list */
    public function accountOwnerList($request)
    {
        try {
           if (
                !empty($_SESSION['create_account']) &&
                isset($_SESSION['create_account'])
            ) { 
                $accountsData = $_SESSION['create_account'];
                $allAccounts = [];
                if (count($accountsData) > 0) {
                    $accountList = [];
                    $result = [];
                    foreach ($accountsData as $key => $accounts) {
                        
                        if($accounts['accountType']== Constant::ACCOUNT_TYPE_CHECKING)
                        {
                            $accountType = Constant::ACCOUNT_TYPE_CHECKING;
                        }
                        elseif($accounts['accountType']== Constant::ACCOUNT_TYPE_INVESTMENT)
                        {
                            $accountType = Constant::ACCOUNT_TYPE_INVESTMENT;
                        }
                       
                        if ($accounts['account_sub_type'] != ''&&  $accounts['account_sub_type']!='NULL' ) {
                            if($accounts['account_sub_type']== Constant::SUB_ACCOUNT_TYPE_INDIVIDUAL){
                                $account_sub_type =  Constant::SUB_ACCOUNT_TYPE_INDIVIDUAL;
                            }
                            if($accounts['account_sub_type']== Constant::SUB_ACCOUNT_TYPE_CORPORATE){
                                $account_sub_type =  Constant::SUB_ACCOUNT_TYPE_CORPORATE;
                            }
                            
                        } else {
                            $account_sub_type = 'NULL';
                        }
                        
                        $result['bank_name'] = BANK_NAME;
                        $result['account_Type'] = $accountType;
                        $result['account_sub_type'] = $account_sub_type;
                        $result['accountOwnerId'] = $accounts['id'];
                        $result['account_no'] = $accounts['account_number'];
                        $result['account_name'] =
                            $accounts['account_owner_name'];
                        $result['available balance'] =
                            $accounts['account_balance'];
                        $result['mobile'] = $accounts['mobile'];
                        $result['address'] = $accounts['address'];
                        $result['account_status'] = $accounts['account_status'];
                        $allAccounts[] = $result;
                    }
                    $data = [
                        'status' => 'success',
                        'data' => $allAccounts,
                        'message' => Message::BANK_OWNER_LIST,
                    ];
                    echo json_encode($data);
                }
            } else {
                throw new Exception(Message::NO_ACCOUNTS_MSG);
            }
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            echo json_encode($data);
        }
    }

    /*Account details using ownerID and accout number */

    public function accountDetails($request)
    {
        try {
             if ($request['ownerId'] == '') {
                throw new Exception(Message::OWNER_ID_MSG);
            }
            if ($request['accountNumber'] == '') {
                throw new Exception(Message::ACCOUNT_NUMBER_MSG);
            }
            $accountNumber = $request['accountNumber'];
            $owenerId = $request['ownerId'];
            $accounts = $_SESSION['create_account'];
            $result = [];
            foreach ($accounts as $key => $accountData) {
               
                if (
                    $accountData['account_number'] == $accountNumber &&
                    $accountData['id'] == $owenerId &&
                    $accountData['account_status'] == 'Active'
                ) {
                    if($accountData['accountType']== Constant::ACCOUNT_TYPE_CHECKING)
                    {
                        $accountType = Constant::ACCOUNT_TYPE_CHECKING;
                    }
                    elseif($accountData['accountType']== Constant::ACCOUNT_TYPE_INVESTMENT)
                    {
                        $accountType = Constant::ACCOUNT_TYPE_INVESTMENT;
                    }
                   
                    if ($accountData['account_sub_type'] != ''&&  $accountData['account_sub_type']!='NULL' ) {
                        if($accountData['account_sub_type']== Constant::SUB_ACCOUNT_TYPE_INDIVIDUAL){
                            $account_sub_type =  Constant::SUB_ACCOUNT_TYPE_INDIVIDUAL;
                        }
                        if($accountData['account_sub_type']== Constant::SUB_ACCOUNT_TYPE_CORPORATE){
                            $account_sub_type =  Constant::SUB_ACCOUNT_TYPE_CORPORATE;
                        }
                        
                    } else {
                        $account_sub_type = 'NULL';
                    }

                    $result['bank_name'] = BANK_NAME;
                    $result['accountType'] = $accountType;
                    $result['subAccountType'] = $account_sub_type;
                    $result['accountOwnerID'] = $accountData['id'];
                    $result['account_no'] = $accountData['account_number'];
                    $result['ownerName'] = $accountData['account_owner_name'];
                    $result['accountBalance'] = $accountData['account_balance'];
                    $result['mobile'] = $accountData['mobile'];
                    $result['address'] = $accountData['address'];
                    $result['account_status'] = $accountData['account_status'];
                }
            }
            $data = [
                'status' => 'success',
                'data' => $result,
                'message' => Message::BANK_OWNER_DETAILS,
            ];
            echo json_encode($data);
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            echo json_encode($data);
        }
    }

    /* Deposit money to account */

    public function accountDeposit($request)
    {
        try {
            $error = Account::DepositValidation($request);
            if ($error['status'] == 1) {
                $ownerID = $request['ownerId'];
                $transactionType = $request['transactionType'];
                $amount = $request['amount'];
                $owenerAccount = [];
                $accountsData = $_SESSION['create_account'];
                if (!empty($accountsData)) {
                    foreach ($accountsData as $key => $account) {
                        if (
                            $ownerID == $account['id'] &&
                            $account['account_status'] == 'Active'
                        ) {
                            $available_balance = $account['account_balance'];
                            $tatalAmount = $available_balance + $amount;
                            $tatalAmount = sprintf('%01.2f', $tatalAmount);
                            $accountsData[$key][
                                'account_balance'
                            ] = $tatalAmount;
                        }
                    }
                    $_SESSION['create_account'] = $accountsData;
                    $data = [
                        'status' => 'success',
                        'data' => [],
                        'message' => Message::DEPOSITE_SUCCESS_MSG,
                    ];
                    echo json_encode($data);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'data' => [],
                        'message' => Message::NO_ACCOUNT_MSG,
                    ]);
                }
            } else {
                throw new Exception($error['message']);
            }
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            echo json_encode($data);
        }
    }

    /*Account withdrawal */

    public function accountWithdrawal($request)
    {
        try {
            $error = Account::WithdrawalValidations($request);
            if ($error['status'] == 1) {
                $ownerID = $request['ownerId'];
                $amount = $request['amount'];
                $transactionType = $request['transactionType'];
                $tarnsactionMode = $request['tarnsactionMode'];
                $account_no = $request['account_no'];
                $AllAccountsData = $_SESSION['create_account'];
                if (!empty($AllAccountsData)) {
                    foreach ($AllAccountsData as $key => $accountData) {
                       
                        if (
                            $ownerID == $accountData['id'] &&
                            $accountData['account_status'] == 'Active'
                        ) {
                            
                            $available_balance = $accountData['account_balance'];
                            $account_sub_type =  $accountData['account_sub_type'];

                            $totalAmount = $available_balance - $amount;
                            $totalAmount = sprintf('%01.2f', $totalAmount);
                            

                            if ($accountData['account_sub_type'] != '' &&  $accountData['account_sub_type']!='NULL' ) {
                                if($accountData['account_sub_type']== Constant::SUB_ACCOUNT_TYPE_INDIVIDUAL){
                                    $subAccount_type =  Constant::SUB_ACCOUNT_TYPE_INDIVIDUAL;
                                }
                                if($accountData['account_sub_type']== Constant::SUB_ACCOUNT_TYPE_CORPORATE){
                                    $subAccount_type =  Constant::SUB_ACCOUNT_TYPE_CORPORATE;
                                }
                                
                            } else {
                                $subAccount_type = 'NULL';
                            }
                           
                            if ($available_balance < $amount) {
                                throw new Exception(Message::INSUFFICIENT_MSG);
                            } elseif (
                                $subAccount_type == 'individual' &&
                                $amount > 500
                            ) {
                                throw new Exception(Message::INDIVIDUAL_WITHDRAW_LIMIT_MSG);
                            }
                            $AllAccountsData[$key][
                                'account_balance'
                            ] = $totalAmount;
                        }
                    }
                    $_SESSION['create_account'] = $AllAccountsData;
                    $data = [
                        'status' => 'success',
                        'data' => [],
                        'message' => Message::WITHDRAWAL_SUCCESS_MSG,
                    ];
                    echo json_encode($data);
                }
            } else {
                throw new Exception($error['message']);
            }
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            echo json_encode($data);
        }
    }

    /* */
    public function accountBalanceTransfer($request)
    {
        try {
            $error = Account::balancetransferValidation($request);
            if ($error['status'] == 1) {
                $ownerID = $request['ownerId'];
                $amount = $request['amount'];
                $transactionType = $request['transactionType'];
                $tarnsactionMode = $request['tarnsactionMode'];
                $account_no = $request['account_no'];
                $toOwnerid = $request['toOwnerid'];
                $toaAcountNo = $request['toaccountno'];
                $accountsData = $_SESSION['create_account'];
                if ($transactionType != 'Transfer') {
                    throw new Exception(Message::TRANSACTION_TYPE_INVALIED);
                }
                $fromAccount = [];
                $toAccount = [];

                if (!empty($accountsData)) {
                    foreach ($accountsData as $key => $account) {
                      
                        if (
                            $ownerID == $account['id'] &&
                            $account['account_status'] == 'Active'
                        ) {
                            $fromAccount['account_balance'] = $account['account_balance'];
                            $fromAccount['accountSubType']  = $account['account_sub_type'];
                        }
                        if (
                            $toOwnerid == $account['id'] &&
                            $account['account_status'] == 'Active'
                        ) {
                            $toAccount['account_balance'] =  $account['account_balance'];
                            $toAccount['accountSubType'] = $account['account_sub_type'];
                        }
                    }
                } else {
                    $data = [
                        'status' => 'success',
                        'data' => [],
                        'message' => Message::NO_ACCOUNT_MSG,
                    ];
                    echo json_encode($data);
                }
                if (empty($fromAccount)) {
                    throw new Exception(
                        Message::FROM_ACCOUNT_NOT_EXITS_MSG
                    );
                }
                if (empty($toAccount)) {
                    throw new Exception(
                       Message::TO_ACCOUNT_NOT_EXITS_MSG
                    );
                }
                if ($fromAccount['account_balance'] < $amount) {
                    throw new Exception(Message::INSUFFICIENT_MSG);
                } else {
                    $fromAccountTotal =
                        $fromAccount['account_balance'] - $amount;
                    $toAccountTotal = $toAccount['account_balance'] + $amount;
                    $fromAccountTotal = sprintf('%01.2f', $fromAccountTotal);
                    $toAccountTotal = sprintf('%01.2f', $toAccountTotal);
                    foreach ($accountsData as $key => $account) {
                        if ($ownerID == $account['id']) {
                            $accountsData[$key][
                                'account_balance'
                            ] = $fromAccountTotal;
                        }
                        if ($toOwnerid == $account['id']) {
                            $accountsData[$key][
                                'account_balance'
                            ] = $toAccountTotal;
                        }
                    }
                    $_SESSION['create_account'] = $accountsData;
                    $data = [
                        'status' => 'success',
                        'data' => [],
                        'message' => Message::TRANSFER_SUCCSS_MSG,
                    ];
                    echo json_encode($data);
                }
            } else {
                throw new Exception($error['message']);
            }
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            echo json_encode($data);
        }
    }

    /* Account type with owner name */

    public function accountTypeWithOwnerName($request)
    {
        try {
            $message = $this->message();
            if (isset($_SESSION['create_account'])) {
                $accountsData = $_SESSION['create_account'];
            } else {
                $accountsData = [];
            }
            $ownerID = $request['ownerId'];
            if (count($accountsData) > 0) {
                $result = [];
                foreach ($accountsData as $key => $accountdata) {
                  
                    if (
                        $accountdata['id'] == $ownerID &&
                        $accountdata['account_status'] == 'Active'
                    ) {
                        $result['accountType'] = $accountdata['accountType'];
                        $result['subAccountType'] =
                            $accountdata['account_sub_type'];
                        $result['ownerName'] =
                            $accountdata['account_owner_name'];
                        $result['accountStatus'] =
                            $accountdata['account_status'];
                    }
                    if (
                        $accountdata['id'] == $ownerID &&
                        $accountdata['account_status'] == 'In-Active'
                    ) {
                     
                        throw new Exception(Message::OWNER_NOT_ACTIVE_MSG);
                        break;

                    }
                }
                $data = [
                    'status' => 'success',
                    'data' => $result,
                    'message' => Message::OWNER_NAME_WITH_TYPE,
                ];
            } else {
                $data = [
                    'status' => 'success',
                    'data' => $accountsData,
                    'message' => Message::NO_ACCOUNTS_MSG,
                ];
            }
            echo json_encode($data);
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            echo json_encode($data);
        }
    }
    /* Input filed validation */
    public static function validationRequired($inputField)
    {
        if (isset($inputField) && !empty($inputField)) {
            return true;
        } else {
            return false;
        }
    }

    /* Create account validation */
    public function insertValidation($data)
    {
        $return = [];
        if (Account::validationRequired($data['accountType']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::ACCOUNT_TYPE_MSG;
        } elseif (
            Account::validationRequired($data['accountSubType']) == false
        ) {
            $return['status'] = 'false';
            $return['message'] = Message::SUB_ACCOUNT_TYPE_MSG;
        } elseif (Account::validationRequired($data['ownerName']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::OWNER_NAME_MSG;
        } elseif (!preg_match('/^([a-zA-Z ]*)$/', $data['ownerName'])) {
            $return['status'] = 'false';
            $return['message'] = Message::OWNER_NAME_VALIDATION_MSG;
        } elseif (Account::validationRequired($data['mobile']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::MOBILE_NUMBER_MSG;
        } elseif (Account::validationRequired($data['address']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::ADDRESS_MSG;
        } else {
            $return['status'] = true;
        }
        return $return;
    }

    /* Deposit validation */
    public function DepositValidation($data)
    {
        $return = [];
        if (Account::validationRequired($data['ownerId']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::OWNER_ID_MSG;
        } elseif (
            Account::validationRequired($data['transactionType']) == false
        ) {
            $return['status'] = 'false';
            $return['message'] = Message::TRANSACTION_TYPE_MSG;
        } elseif ($data['transactionType'] != 'Deposit') {
            $return['status'] = 'false';
            $return['message'] = Message::TRANSACTION_TYPE_INVALIED;
        } elseif (Account::validationRequired($data['amount']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::AMMOUNT_MSG;
        } elseif (
            Account::validationRequired($data['tarnsactionMode']) == false
        ) {
            $return['status'] = 'false';
            $return['message'] = Message::TRANSACTION_MODE_MSG;
        } else {
            $return['status'] = true;
        }
        return $return;
    }

    /* Update account validation */
    public function UpdateAccountValidation($data)
    {
        $return = [];
        if (Account::validationRequired($data['accountType']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::ACCOUNT_TYPE_MSG;
        } elseif (
            Account::validationRequired($data['accountSubType']) == false
        ) {
            $return['status'] = 'false';
            $return['message'] = Message::SUB_ACCOUNT_TYPE_MSG;
        } elseif (Account::validationRequired($data['ownerId']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::OWNER_ID_MSG;
        } elseif (Account::validationRequired($data['ownerName']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::OWNER_NAME_MSG;
        } elseif (!preg_match('/^([a-zA-Z ]*)$/', $data['ownerName'])) {
            $return['status'] = 'false';
            $return['message'] = Message::OWNER_NAME_VALIDATION_MSG;
        } elseif (Account::validationRequired($data['mobile']) == false) {
            $return['status'] = 'false';
            $return['message'] =  Message::MOBILE_NUMBER_MSG;
        } elseif (Account::validationRequired($data['address']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::ADDRESS_MSG;
        } else {
            $return['status'] = true;
        }
        return $return;
    }

    /* withdrawal validation */
    public function WithdrawalValidations($data)
    {
        $return = [];
        if (Account::validationRequired($data['ownerId']) == false) {
            $return['status'] = false;
            $return['message'] = Message::OWNER_ID_MSG;
        } elseif (Account::validationRequired($data['account_no']) == false) {
            $return['status'] = false;
            $return['message'] = Message::ACCOUNT_NUMBER_MSG;
        } elseif (
            Account::validationRequired($data['transactionType']) == false
        ) {
            $return['status'] = false;
            $return['message'] = Message::TRANSACTION_TYPE_MSG;
        } elseif ($data['transactionType'] != 'withdrawal') {
            $return['status'] = false;
            $return['message'] = Message::TRANSACTION_TYPE_INVALIED;
        } elseif (
            Account::validationRequired($data['tarnsactionMode']) == false
        ) {
            $return['status'] = false;
            $return['message'] = Message::TRANSACTION_MODE_MSG;
        } elseif (Account::validationRequired($data['amount']) == false) {
            $return['status'] = false;
            $return['message'] = Message::AMMOUNT_MSG;
        } else {
            $return['status'] = true;
        }
        return $return;
    }

    /* Account remove soft delete */

    public function removeAccount($request)
    {
        $owner_id = $request['owner_id'];
        $accounts = $_SESSION['create_account'];
        if (count($accounts) > 0) {
            foreach ($accounts as $key => $accountData) {
                if ($owner_id == $accountData['id']) {
                    $accounts[$key]['account_status'] = 'In-Active';
                }
            }
            $_SESSION['create_account'] = $accounts;
            $data = [
                'status' => 'success',
                'data' => [],
                'message' => Message::ACCOUNT_REMOVED_SUCCESS_MSG,
            ];
            echo json_encode($data);
        } else {
            throw new Exception(Message::NO_ACCOUNT_MSG);
        }
    }

    /* balance transfer validations */
    public function balancetransferValidation($data)
    {
        $return = [];
        if (Account::validationRequired($data['ownerId']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::OWNER_ID_MSG;
        } elseif (Account::validationRequired($data['account_no']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::ACCOUNT_NUMBER_MSG;
        } elseif (
            Account::validationRequired($data['transactionType']) == false
        ) {
            $return['status'] = 'false';
            $return['message'] =  Message::TRANSACTION_TYPE_MSG;
        } elseif (
            Account::validationRequired($data['tarnsactionMode']) == false
        ) {
            $return['status'] = 'false';
            $return['message'] = Message::TRANSACTION_MODE_MSG;
        } elseif (Account::validationRequired($data['amount']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::AMMOUNT_MSG;
        } elseif (Account::validationRequired($data['toOwnerid']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::TO_OWNER_ID_MSG;
        } elseif (Account::validationRequired($data['toaccountno']) == false) {
            $return['status'] = 'false';
            $return['message'] = Message::TO_ACCOUNT_NUMBER;
        } else {
            $return['status'] = true;
        }
        return $return;
    }
}

?>
