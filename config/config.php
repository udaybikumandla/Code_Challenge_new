<?php
define('BANK_NAME', 'HDFC');
include 'class/Constants.php';
include 'class/Message.php';
include 'class/Account.php';

/* Use for below headers for api call */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
header('Access-Control-Max-Age: 3600');
header(
    'Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
);
$request = json_decode(file_get_contents('php://input'), true);
?>
