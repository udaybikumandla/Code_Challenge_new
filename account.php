<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('config/config.php');

$account = new \Accounts\Account(BANK_NAME);
use Messages\Message as Message;
$bankName = $account->getBankName();

try {
    $message = new message();
if ($request['api_type'] == 'createaccount' && $request['api_type']) {
    $account->createAccount($request);
}
elseif ($request['api_type'] == 'updateaccount' && $request['api_type']) {
    $account->UpdateAccount($request);
}
elseif ($request['api_type'] == 'accountowner' && $request['api_type']) {
   
    $account->accountOwnerList($request);
}
elseif ($request['api_type'] == 'accountdetails' && $request['api_type']) {
   
    $account->accountDetails($request);
}
elseif ($request['api_type'] == 'banktransaction' && $request['api_type']){
    $account->accountDeposit($request);
}
elseif ($request['api_type'] == 'withdrawaltransaction' && $request['api_type']) {
    $account->accountWithdrawal($request);
}
elseif ($request['api_type'] == 'balancetransfer' && $request['api_type']) {
    $account->accountBalanceTransfer($request);
}
elseif ($request['api_type'] == 'removeAccount' && $request['api_type']) {
    $account->removeAccount($request);
}
elseif ( $request['api_type'] == 'accountdetailschekcing' && $request['api_type']) {
    $account->accountTypeWithOwnerName($request);
}
else {
    throw new Exception($message->ApiTypeInvaliedMsg());
}
}
catch (\Exception $e) {
    $data = [
        'status' => 'failed',
        'message' => $e->getMessage(),
    ];
    echo json_encode($data);
}


?>