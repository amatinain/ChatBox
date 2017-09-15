<?php

$destinationNumber = $_REQUEST['destinationNumber'];
$messageBody = $_REQUEST['message'];

require('/path/Twilio/autoload.php');
use Twilio\Rest\Client;

$sid = 'XXXXXXXXXXXXXXXXXXXXX';
$token = 'XXXXXXXXXXXXXXXXXXXXX';
$client = new Client($sid, $token);

$client->messages->create(
    $destinationNumber,
    array(
        'from' => 'YourTwilioNumber',
        'body' => $messageBody));

?>