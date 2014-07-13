<?php
header('Content-Type: application/json; charset=utf-8');

function send_push($token = null, $info, $order_id = 0, $orderStatus = 0){


// Adjust to your timezone
date_default_timezone_set('Europe/Rome');

// Report all PHP errors
error_reporting(-1);

// Using Autoload all classes are loaded on-demand
require_once 'ApnsPHP/Autoload.php';

// Instanciate a new ApnsPHP_Push object  
//$push = new ApnsPHP_Push(
//	ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
//	'server_certificates_bundle_sandbox.pem'
//);

$push = new ApnsPHP_Push(
	ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
	'ck.pem'
);

// Set the Root Certificate Autority to verify the Apple remote peer
//$push->setRootCertificationAuthority('entrust_root_certification_authority.pem');
$push->setProviderCertificatePassphrase('1234');

// Connect to the Apple Push Notification Service
$push->connect();

// Instantiate a new Message with a single recipient
$message = new ApnsPHP_Message_Custom($token);

// Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
// over a ApnsPHP_Message object retrieved with the getErrors() message.
$message->setCustomIdentifier("Message-Badge-13");

// Set badge icon to "3"
$message->setBadge(1);

// Set a simple welcome text
$message->setText($info);

// Play the default sound
//$message->setSound();

// Set a custom property
$message->setCustomProperty('order', $order_id);

// Set a custom property
$message->setCustomProperty('orderStatus', $orderStatus);

// Set the expiry value to 30 seconds
$message->setExpiry(30);

// Set the "View" button title.
//$message->setActionLocKey('Show me!');

// Add the message to the message queue
$push->add($message);

// Send all messages in the message queue
$push->send();

// Disconnect from the Apple Push Notification Service
$push->disconnect();

// Examine the error message container
$aErrorQueue = $push->getErrors();
if (!empty($aErrorQueue)) {
	var_dump($aErrorQueue);
}
}