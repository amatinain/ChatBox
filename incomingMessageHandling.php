<?php

require('path/Twilio/autoload.php');

use Twilio\Rest\Client;

// Your Account Sid and Auth Token from twilio.com/user/account
$sid = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';
$token = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';
$serviceSid = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';

$fromUserName = $_REQUEST['From'];
$messageBody = $_REQUEST['Body'];
$agentName = 'agent1';
$client = new Client($sid, $token);
$channelName = $agentName . '_' . $fromUserName;

$service = $client->chat->services($serviceSid);

$setUser = checkForUserExistance($service, $fromUserName);
$setChannel = checkForChannelExistance($service, $channelName, $fromUserName);
$setMember = checkForMemberExistance($service, $setChannel, $agentName, $fromUserName);
-
sendMessageToChannel($service, $fromUserName, $messageBody, $setChannel);

//create a function that will check the existance of the cahnnel in the chat instace
function checkForChannelExistance($srv, $chnlName, $frmUsrNm)
    {
        //set the channel paremeter to null
        $channel = null;
        //try to search the channel by name
        try{    
            $channel = $srv->channels($chnlName)->fetch();
            }
        catch(Exception $e)
            {
                error_log('No such channel, procesding to channel');
            }
        //if the channle is not found, meaning the $channel value is still null, then create a new channel
        //with the name of the agent+the From value
        if($channel == null)
        {
            error_log('Creating channel' . $chnlName); 
            $channel = $srv->channels->create(
            array(
            'friendlyName' => 'Agent conversation with' . $frmUsrNm,
            'uniqueName' => $chnlName));
        }
        error_log($channel->sid);
        return $channel->sid;
    }
//create a function that will check with a member of the user or agent exist in the channel
function checkForMemberExistance($srv, $chnlSid, $mem, $frmUsrNm)
    {
            
            //create two values that are set to false
            $agentExistInChannel = false;
            $userExistInChannel = false;
            //get the member list of the channel in question
            $memberList = $srv->channels($chnlSid)->members->read();
            
            //loop through all the members in the channel
            foreach($memberList as $checkFormember)
                {
                    
                    //check if the member exist, then change the value we have created before to true
                    error_log($mem . " " . $checkFormember->identity);
                    if($checkFormember->identity == $mem)
                    {
                        $agentExistInChannel = true;
                        $member = $checkFormember;
                    }
                    elseif($checkFormember->identity == $frmUsrNm)
                    {
                        $userExistInChannel = true;
                        $member = $checkFormember;
                    }
                    
                    //if the agent or the user doesn't exist
                    if($agentExistInChannel == false || $userExistInChannel == false)
                    {
                        
                        //try to add the agent as member
                        try
                        {   
                        error_log('Try to add Agent to channel');
                        $member = $srv->channels($chnlSid)->members->create($mem);
                        }
                        catch(Exception $e)
                        {
                        error_log('agent already exist in the channel');    
                        $e->getMessage();  
                        }
                        //try to add the user as member
                        try
                        {    
                        error_log('Try to add User to channel');
                        $member = $srv->channels($chnlSid)->members->create($frmUsrNm);
                        }
                        catch(Exception $e)
                        {
                        error_log('user already exist in the channel');    
                        $e->getMessage();  
                        }
                    }    
             
                }
            //then, if the values in question are still false, create a new member in the channel
            
            return $member;
    }

//create a function to check for user 'From' existnace in the chat instance, in oder to add it as a member and use it as a From value
function checkForUserExistance($srv,$user)
    {   
    
            
        $activeUser = null;
        try{
            $activeUser = $srv->users($user)->fetch();
        }
        catch(Exception $e)
            {
            error_log('No such user as ' . $user);    
            }
        if($activeUser == null)
            {
            error_log('Creating user: ' . $user); 
            $user = $srv->users->create($user);
            }
            return $activeUser;
    }

//create a function that will send the messaes
function sendMessageToChannel($srv, $id, $msg, $chnlsid)
    {
       error_log("sending out message");
       $srv->channels($chnlsid)->messages->create($msg,array('From' => $id)); 
    }

//generate TwiML response
?>

<Response></Response>