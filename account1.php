<?php
session_start();
include 'config/Headers.php';
include 'class/Bank.php';
include 'class/Account.php';
include 'class/Helper.php';
$account = new \Accounts\Account();

try {
    if ($request['api_type'] == 'createaccount' && $request['api_type']) {
        $banks = $account->getBanks();
        $accountTypes = $account->accountTypes();
        $subAccountTypes = $account->subAccountTypes();
        $acountNumberStart = 50100;
        $randumNumber = rand(10000, 99999);
        $acountNumber = $acountNumberStart . '' . $randumNumber;
        $balance = 0;
        $balance = sprintf('%01.2f', $balance);
        $accountsData = [];
        $result = [];

        $error = $account->insertValidation($request);
        if (!empty($error) && $error['status'] == false) {
            throw new Exception($error['message']);
        }
        
        if (!empty($_SESSION['create_account']) && isset($_SESSION['create_account'])) {
            foreach ($_SESSION['create_account'] as $accountData) {
                if (in_array($request['ownerName'], $accountData)) {
                    throw new Exception('This account name already exits');
                }
                if (in_array($acountNumber, $accountData)) {
                    throw new Exception('Account Number already exits');
                }
                if (in_array($request['mobile'], $accountData)) {
                    throw new Exception('Mobile Number already exits');
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
        $bank_data = $account->getBankDataUsingId($request['bankId']);
        $account_type = $account->getAccountTypeByUsingId(
            $request['accountTypeId']
        );
        if ($request['accountSubTypeId'] != '') {
            $subAccount_type = $account->getSubAccountTypeByUsingId(
                $request['accountSubTypeId']
            );
            $subAccount_type = $subAccount_type['account_sub_type'];
        } else {
            $subAccount_type = 'NULL';
        }
        $result['id'] = $request['id'];
        $result['bankID'] = $request['bankId'];
        $result['bank_name'] = $bank_data['bank_name'];
        $result['accountTypeId'] = $request['accountTypeId'];
        $result['accountType'] = $account_type['type'];
        $result['accountSubTypeId'] = $request['accountSubTypeId'];
        $result['account_sub_type'] = $subAccount_type;
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
            'message' => 'Account created successfully',
        ];
        echo json_encode($data);
    } elseif ($request['api_type'] == 'accountowner' && $request['api_type']) {
        if (isset($request['bank_id']) && !empty($request['bank_id'])) {
            $reqBankId = $request['bank_id'];

            if (isset($_SESSION['create_account'])) {
                $accountsData = $_SESSION['create_account'];
                $allAccounts = [];
                if (count($accountsData) > 0) {
                    $accountList = [];
                    $result = [];
                    foreach ($accountsData as $key => $accounts) {
                        if (in_array($reqBankId, $accounts)) {
                            if (
                                $reqBankId == $accounts['bankID'] &&
                                $accounts['account_status'] == 'Active'
                            ) {
                                $bank_data = $account->getBankDataUsingId(
                                    $accounts['bankID']
                                );
                                $account_type = $account->getAccountTypeByUsingId(
                                    $accounts['accountTypeId']
                                );
                                if ($accounts['accountSubTypeId'] != '') {
                                    $subAccount_type = $account->getSubAccountTypeByUsingId(
                                        $accounts['accountSubTypeId']
                                    );
                                    $subAccount_type =
                                        $subAccount_type['account_sub_type'];
                                } else {
                                    $subAccount_type = 'NULL';
                                }

                                $result['bankId'] = $accounts['bankID'];
                                $result['bank_name'] = $bank_data['bank_name'];
                                $result['accountTypeId'] =
                                    $accounts['accountTypeId'];
                                $result['account_Type'] = $account_type['type'];
                                if ($accounts['accountSubTypeId'] != '') {
                                    $result['subAccountTypeId'] =
                                        $accounts['accountSubTypeId'];
                                } else {
                                    $result['subAccountTypeId'] = 'null';
                                }
                                if ($subAccount_type) {
                                    $result[
                                        'account_sub_type'
                                    ] = $subAccount_type;
                                } else {
                                    $result['account_sub_type'] = 'null';
                                }

                                $result['accountOwnerId'] = $accounts['id'];
                                $result['account_no'] =
                                    $accounts['account_number'];
                                $result['account_name'] =
                                    $accounts['account_owner_name'];
                                $result['available balance'] =
                                    $accounts['account_balance'];
                                $result['mobile'] = $accounts['mobile'];
                                $result['address'] = $accounts['address'];
                                $result['account_status'] =
                                    $accounts['account_status'];
                                $allAccounts[] = $result;
                            }
                        }
                    }
                }
                $data = [
                    'status' => 'success',
                    'data' => $allAccounts,
                    'message' => 'Bank Account Owner List',
                ];
                echo json_encode($data);
            } else {
                $accountsData = [];
                $data = [
                    'status' => 'success',
                    'data' => $accountsData,
                    'message' => 'Bank Account Owner List',
                ];
                echo json_encode($data);
            }
        } else {
            throw new Exception('Please pass bank_id.');
        }
    } elseif (
        $request['api_type'] == 'accountdetails' &&
        $request['api_type']
    ) {
        if ($request['ownerId'] != '' && !isset($request['ownerId'])) {
            throw new Exception('Please Pass ownerID.');
        }
        if (
            $request['accountNumber'] == '' &&
            !isset($request['accountNumber'])
        ) {
            throw new Exception('Please Pass accountNumber.');
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
                $bank_data = $account->getBankDataUsingId(
                    $accountData['bankID']
                );
                $account_type = $account->getAccountTypeByUsingId(
                    $accountData['accountTypeId']
                );
                if ($accountData['accountSubTypeId'] != '') {
                    $subAccount_type = $account->getSubAccountTypeByUsingId(
                        $accountData['accountSubTypeId']
                    );
                    $subAccount_type = $subAccount_type['account_sub_type'];
                } else {
                    $subAccount_type = 'NULL';
                }
                $result['bankId'] = $accountData['bankID'];
                $result['bank_name'] = $bank_data['bank_name'];
                $result['accountTypeId'] = $accountData['accountTypeId'];
                $result['accountType'] = $account_type['type'];
                $result['subAccountTypeId'] = $accountData['accountSubTypeId'];
                $result['subAccountType'] = $subAccount_type;
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
            'message' => 'Bank account owner details',
        ];
        echo json_encode($data);
    } elseif (
        $request['api_type'] == 'banktransaction' &&
        $request['api_type']
    ) {
        $error = $account->DepositValidation($request);
        if (!empty($error) && $error['status'] == false) {
            throw new Exception($error['message']);
        }
        $bankId = $request['bank_id'];
        $ownerID = $request['ownerId'];
        $transactionType = $request['transactionType'];
        $amount = $request['amount'];
        $owenerAccount = [];
        $accountsData = $_SESSION['create_account'];
        if (!empty($accountsData)) {
            foreach ($accountsData as $key => $account) {
                if (
                    $ownerID == $account['id'] &&
                    $bankId == $account['bankID'] &&
                    $account['account_status'] == 'Active'
                ) {
                    $available_balance = $account['account_balance'];
                    $tatalAmount = $available_balance + $amount;
                    $tatalAmount = sprintf('%01.2f', $tatalAmount);
                    $accountsData[$key]['account_balance'] = $tatalAmount;
                }
            }
            $_SESSION['create_account'] = $accountsData;
            $data = [
                'status' => 'success',
                'data' => [],
                'message' => 'Amount Deposited successfully.',
            ];
            echo json_encode($data);
        } else {
            return response()->json([
                'status' => 'failed',
                'data' => [],
                'message' => 'There is no account.',
            ]);
        }
    } elseif (
        $request['api_type'] == 'bankwithdrawaltransaction' &&
        $request['api_type']
    ) {
        $error = $account->WithdrawalValidations($request);
        if (!empty($error) && $error['status'] == false) {
            throw new Exception($error['message']);
        }
        $bankId = $request['bank_id'];
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
                    $bankId == $accountData['bankID'] &&
                    $accountData['account_status'] == 'Active'
                ) {
                    $available_balance = $accountData['account_balance'];
                    $accountSubTypeId = $accountData['accountSubTypeId'];

                    $totalAmount = $available_balance - $amount;
                    $totalAmount = sprintf('%01.2f', $totalAmount);
                    if ($accountData['accountSubTypeId'] != '') {
                        $subAccount_type = $account->getSubAccountTypeByUsingId(
                            $accountData['accountSubTypeId']
                        );
                        $subAccount_type = $subAccount_type['account_sub_type'];
                    } else {
                        $subAccount_type = 'NULL';
                    }
                    if ($available_balance < $amount) {
                        throw new Exception('You have insufficient balance');
                    } elseif (
                        $subAccount_type == 'individual' &&
                        $amount > 500
                    ) {
                        throw new Exception(
                            'Individual accounts have a withdrawal limits of 500 dollers'
                        );
                    }
                    $AllAccountsData[$key]['account_balance'] = $totalAmount;
                }
            }
            $_SESSION['create_account'] = $AllAccountsData;
            $data = [
                'status' => 'success',
                'data' => [],
                'message' => 'Amount withdrawal successfully.',
            ];
            echo json_encode($data);
        }
    } elseif ($request['api_type'] == 'removeAccount' && $request['api_type']) {
        $owner_id = $request['owner_id'];
        $accounts = $_SESSION['create_account'];

        foreach ($accounts as $key => $accountData) {
            if ($owner_id == $accountData['id']) {
                $accounts[$key]['account_status'] = 'In-Active';
            }
        }
        $_SESSION['create_account'] = $accounts;
        $data = [
            'status' => 'success',
            'data' => [],
            'message' => 'Owner account has been removed',
        ];
        echo json_encode($data);
    } elseif (
        $request['api_type'] == 'balancetransfer' &&
        $request['api_type']
    ) {
        $error = $account->balancetransferValidation($request);
        if (!empty($error) && $error['status'] == false) {
            throw new Exception($error['message']);
        }
        $bankId = $request['bank_id'];
        $ownerID = $request['ownerId'];
        $amount = $request['amount'];
        $transactionType = $request['transactionType'];
        $tarnsactionMode = $request['tarnsactionMode'];
        $account_no = $request['account_no'];
        $tobankid = $request['tobankid'];
        $toOwnerid = $request['toOwnerid'];
        $toaAcountNo = $request['toaccountno'];
        $accountsData = $_SESSION['create_account'];
        if ($transactionType != 'Transfer') {
            throw new Exception('Transaction type invalied');
        }

        $fromAccount = [];
        $toAccount = [];

        if (!empty($accountsData)) {
            foreach ($accountsData as $key => $account) {
                if (
                    $ownerID == $account['id'] &&
                    $bankId == $account['bankID'] &&
                    $account['account_status'] == 'Active'
                ) {
                    $fromAccount['account_balance'] =
                        $account['account_balance'];
                    $fromAccount['accountSubTypeId'] =
                        $account['accountSubTypeId'];
                }
                if (
                    $toOwnerid == $account['id'] &&
                    $tobankid == $account['bankID'] &&
                    $account['account_status'] == 'Active'
                ) {
                    $toAccount['account_balance'] = $account['account_balance'];
                    $toAccount['accountSubTypeId'] =
                        $account['accountSubTypeId'];
                }
            }
        } else {
            $data = [
                'status' => 'success',
                'data' => [],
                'message' => 'There is no account',
            ];
            echo json_encode($data);
        }
        if (empty($fromAccount)) {
            throw new Exception('From account not exits or not activated.');
        }
        if (empty($toAccount)) {
            throw new Exception('To account not exits or not activated');
        }
        if ($fromAccount['account_balance'] < $amount) {
            throw new Exception('You have insufficient balance');
        } else {
            $fromAccountTotal = $fromAccount['account_balance'] - $amount;
            $toAccountTotal = $toAccount['account_balance'] + $amount;
            $fromAccountTotal = sprintf('%01.2f', $fromAccountTotal);
            $toAccountTotal = sprintf('%01.2f', $toAccountTotal);
            foreach ($accountsData as $key => $account) {
                if (
                    $ownerID == $account['id'] &&
                    $bankId == $account['bankID']
                ) {
                    $accountsData[$key]['account_balance'] = $fromAccountTotal;
                }
                if (
                    $toOwnerid == $account['id'] &&
                    $tobankid == $account['bankID']
                ) {
                    $accountsData[$key]['account_balance'] = $toAccountTotal;
                }
            }
            $_SESSION['create_account'] = $accountsData;
            $data = [
                'status' => 'success',
                'data' => [],
                'message' => 'Amount transferred successfully',
            ];
            echo json_encode($data);
        }
    } elseif ($request['api_type'] == 'updateaccount' && $request['api_type']) {
        $error = $account->UpdateAccountValidation($request);
        if (!empty($error) && $error['status'] == false) {
            throw new Exception($error['message']);
        }
        $bankid = $request['bankid'];
        $ownerId = $request['ownerId'];
        $accountTypeId = $request['accountTypeId'];
        $accountSubTypeId = $request['accountSubTypeId'];
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
                if (
                    $bankid == $accounts['bankID'] &&
                    $ownerId == $accounts['id'] &&
                    $accounts['account_status'] == 'Active'
                ) {
                    $accountsData[$key]['accountTypeId'] = $accountTypeId;
                    $accountsData[$key]['accountSubTypeId'] = $accountSubTypeId;
                    $accountsData[$key]['account_owner_name'] = $ownerName;
                    $accountsData[$key]['mobile'] = $mobile;
                    $accountsData[$key]['address'] = $address;
                    $_SESSION['create_account'] = $accountsData;
                    $bank_data = $account->getBankDataUsingId($bankid);
                    $account_type = $account->getAccountTypeByUsingId(
                        $accountTypeId
                    );

                    if ($request['accountSubTypeId'] != '') {
                        $subAccount_type = $account->getSubAccountTypeByUsingId(
                            $accountSubTypeId
                        );
                    } else {
                        $subAccount_type = 'NULL';
                    }
                    $result['bankID'] = $bankid;
                    $result['bank_name'] = $bank_data['bank_name'];
                    $result['accountTypeId'] = $accountTypeId;
                    $result['accountType'] = $account_type['type'];
                    $result['subAccountTypeId'] = $request['accountSubTypeId'];
                    $result['subAccountType'] =
                        $subAccount_type['account_sub_type'];
                    $result['accountOwenrId'] = $ownerId;
                    $result['accountOwenName'] = $ownerName;
                    $result['account_no'] = $accounts['account_number'];
                    $result['availableBalance'] = $accounts['account_balance'];
                    $result['mobile'] = $mobile;
                    $result['address'] = $address;
                    $result['accountStatus'] = $accounts['account_status'];
                }
            }
        } else {
            $data = [
                'status' => 'success',
                'data' => $accountsData,
                'message' => 'There is no account.',
            ];
        }
        $data = [
            'status' => 'success',
            'data' => $result,
            'message' => 'Account Updated Successfully',
        ];
        echo json_encode($data);
    } elseif (
        $request['api_type'] == 'accountdetailschekcing' &&
        $request['api_type']
    ) {
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
                    $result['ownerName'] = $accountdata['account_owner_name'];
                    $result['accountStatus'] = $accountdata['account_status'];
                }
            }
            $data = [
                'status' => 'success',
                'data' => $result,
                'message' => 'Bank account owner name with account types.',
            ];
        } else {
            $data = [
                'status' => 'success',
                'data' => $accountsData,
                'message' => 'There is no accountys',
            ];
        }
        echo json_encode($data);
    } else {
        throw new Exception('API type invalied');
    }
} catch (\Exception $e) {
    $data = [
        'status' => 'failed',
        'message' => $e->getMessage(),
    ];
    echo json_encode($data);
}

?>
