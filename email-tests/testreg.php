<?php
require_once('rabbitMQLib.inc');
require_once('sample/connect.php');


$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$request = [
    "type" => "register",
    "username" => "testuser2",
    "email" => "test@example.com",
    "password" => password_hash("password123", PASSWORD_DEFAULT)
];

// Send test registration request(hopefully this works)
$response = $client->send_request($request);

echo "Test Registration Response: ";
print_r($response);
?>
