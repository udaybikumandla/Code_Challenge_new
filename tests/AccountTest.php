<?php

class AccountTest extends \PHPUnit\Framework\TestCase
{
    /**
     * A basic AccountTest.
     *
     * @return void
     */
    public function ApiCall($data)
    {
        $client = new GuzzleHttp\Client();
        $response = $client->post(
            'http://localhost/bank-app-core/account.php',
            [
                'json' => $data,
            ]
        );
        json_decode($response->getBody(), true);
        return   $this->assertEquals(200, $response->getStatusCode());
       
    }

    public function test_create_account()
    {
        $inputData = [
            'api_type' => 'createaccount',
            'accountType' => 'investment',
            'accountSubType' => 'individual',
            'ownerName' => 'uday kiran',
            'mobile' => '9000749147',
            'address' => 'chepur near busstop 508256',
        ];
        $data = $this->ApiCall($inputData);
    }

    public function test_show_account_list()
    {
        $inputData = [
            'api_type' => 'accountowner',
        ];
        $this->ApiCall($inputData);
    }

    public function test_single_account_data()
    {
        $inputData = [
            'api_type' => 'accountdetails',
            'ownerId' => '2',
            'accountNumber' => '5010065181',
        ];
        $this->ApiCall($inputData);
    }

    public function test_remove_account()
    {
        $inputData = [
            'api_type' => 'removeAccount',
            'ownerId' => '2',
        ];
        $this->ApiCall($inputData);
    }

    public function test_account_deposit()
    {
        $inputData = [
            'api_type' => 'banktransaction',
            'ownerId' => '1',
            'transactionType' => 'Deposit',
            'amount' => '500',
            'tarnsactionMode' => 'CASH',
        ];
        $this->ApiCall($inputData);
    }

    public function test_update_account()
    {
        $inputData = [
            'api_type' => 'updateaccount',
            'ownerId' => '2',
            'accountType' => 'investment',
            'accountSubType' => 'individual',
            'ownerName' => 'uday kumar',
            'mobile' => '7013970373',
            'address' => 'hyd moulali',
        ];
        $this->ApiCall($inputData);
    }
    public function test_account_withdrawal()
    {
        $inputData = [
            'api_type' => 'withdrawaltransaction',
            'ownerId' => '1',
            'account_no' => '5010068000',
            'transactionType' => 'withdrawal',
            'tarnsactionMode' => 'CASH',
            'amount' => '501',
        ];
        $this->ApiCall($inputData);
    }

    public function test_transferAmount()
    {
        $inputData = [
            'api_type' => 'balancetransfer',
            'ownerId' => '1',
            'account_no' => '5010068000',
            'transactionType' => 'Transfer',
            'tarnsactionMode' => 'NEFT',
            'amount' => '50',
            'toOwnerid' => '2',
            'toaccountno' => '5010050869',
        ];
        $this->ApiCall($inputData);
    }

    public function test_account_type_with_name()
    {
        $inputData = [
            'api_type' => 'accountdetailschekcing',
            'ownerId' => '1',
        ];
        $this->ApiCall($inputData);
    }
}

?>
