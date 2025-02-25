#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function dbConnect() {
    $servername = "localhost";
    $username = "it490user";
    $password = "securepassword";
    $dbname = "it490";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function doLogin($username, $password) {
    $conn = dbConnect();
    
    $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            // Generate a session key
            $sessionKey = bin2hex(random_bytes(32));
            $stmt = $conn->prepare("UPDATE users SET session_key=? WHERE username=?");
            $stmt->bind_param("ss", $sessionKey, $username);
            $stmt->execute();
            return array("success" => true, "session_key" => $sessionKey);
        }
    }
    return array("success" => false);
}

function doRegister($username, $password) {
    $conn = dbConnect();
    
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    
    if ($stmt->execute()) {
        return array("success" => true);
    } else {
        return array("success" => false);
    }
}

function requestProcessor($request) {
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }
    
    switch ($request['type']) {
        case "login":
            return doLogin($request['username'], $request['password']);
        case "register":
            return doRegister($request['username'], $request['password']);
    }
    
    return array("returnCode" => '0', 'message' => "Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "Authentication Server Running...".PHP_EOL;
$server->process_requests('requestProcessor');
?>
