<?php
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testQueue");

$request = [
    "type" => "send_email",
    "to" => "test@example.com",
    "subject" => "Welcome!",
    "message" => "Welcome, testuser2! Thank you for registering."
];

$response = $client->send_request($request);

echo "Email Test Response: ";
print_r($response);
?>
