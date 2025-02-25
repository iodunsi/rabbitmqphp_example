<?php
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$request = array();
$request['type'] = "login";
$request['username'] = "testuser";
$request['password'] = "testpassword";
$response = $client->send_request($request);

print_r($response);
?>
