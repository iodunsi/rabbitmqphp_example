<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Setup RabbitMQ client
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

    // Create request array
    $request = array();
    $request['type'] = "register";
    $request['username'] = $username;
    $request['password'] = $password;

    // Send request to RabbitMQ
    $response = $client->send_request($request);

    if ($response['success']) {
        echo "User registered successfully!";
    } else {
        echo "Registration failed.";
    }
}
?>


<form method="post">
    Username: <input type="text" name="username">
    Password: <input type="password" name="password">
    <button type="submit">Register</button>
</form>

