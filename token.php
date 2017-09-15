<?php
require_once('/Users/amatinian/Documents/t_php_m/Twilio/autoload.php');
header('Content-Type: application/json');



// An identifier for your app - can be anything you'd like
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\IpMessagingGrant;

// Required for all Twilio access tokens
$twilioAccountSid = 'XXXXXXXXXXXXXXXXXXXXXXXX';
$twilioApiKey = 'XXXXXXXXXXXXXXXXXXXXXXXX';
$twilioApiSecret = 'XXXXXXXXXXXXXXXXXXXXXXXX';

// Required for IP messaging grant
$ipmServiceSid = 'IS5ea45e33ea5743bfb7825ad2b83aad16';
// An identifier for your app - can be anything you'd like
$appName = 'TwilioChatDemo';
// choose a random username for the connecting user
$identity = $_REQUEST['userName'];
// A device ID should be passed as a query string parameter to this script
$deviceId = $_REQUEST['deviceId'];
$endpointId = $appName . ':' . $identity . ':' . $deviceId;

// Create access token, which we will serialize and send to the client
$token = new AccessToken(
    $twilioAccountSid,
    $twilioApiKey,
    $twilioApiSecret,
    3600,
    $identity
);

// Create IP Messaging grant
$ipmGrant = new IpMessagingGrant();
$ipmGrant->setServiceSid($ipmServiceSid);
$ipmGrant->setEndpointId($endpointId);

// Add grant to token
$token->addGrant($ipmGrant);

// render token to string
$data =  $token->toJWT();
echo json_encode($data);
error_log($token->toJWT());