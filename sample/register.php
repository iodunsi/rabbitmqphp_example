<?php
require_once('/home/paa39/git/IT490-Project/rabbitMQLib.inc');

header("Content-Type: application/json");

ini_set("log_errors", 1);
ini_set("error_log", "/var/log/php_errors.log");

session_start(); // ✅ Maintain session across requests

// ✅ Reuse RabbitMQ connection across session
class RabbitMQConnection {
    public static function getClient() {
        if (!isset($_SESSION['rabbitmq_client'])) {
            error_log("[RABBITMQ] Creating a NEW RabbitMQ connection (First request only)...");
            $_SESSION['rabbitmq_client'] = new rabbitMQClient("testRabbitMQ.ini", "testServer");
        } else {
            error_log("[RABBITMQ] Using EXISTING RabbitMQ connection...");
        }
        return $_SESSION['rabbitmq_client'];
    }
}

error_log("[REGISTER] Request received: " . json_encode($_POST));

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    error_log("[REGISTER] ERROR: Invalid request method.");
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

// ✅ Extract and validate input
$username = isset($_POST['username']) ? trim($_POST['username']) : null;
$password = isset($_POST['password']) ? trim($_POST['password']) : null;

if (empty($username) || empty($password)) {
    error_log("[REGISTER] ERROR: Missing username or password.");
    echo json_encode(["status" => "error", "message" => "Please enter both username and password"]);
    exit();
}

// ✅ Hash the password before sending to RabbitMQ
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
error_log("[REGISTER] Sending to RabbitMQ: Username='" . $username . "', Password='[HIDDEN]'");

// ✅ Prepare RabbitMQ request
$request = [
    "type" => "register",
    "username" => $username,
    "password" => $hashedPassword
];

try {
    // ✅ Get the persistent RabbitMQ connection (created once per session)
    $client = RabbitMQConnection::getClient();

    // ✅ Send request and get response
    $response = $client->send_request($request);

    error_log("[REGISTER] Received response from RabbitMQ: " . json_encode($response));

    // ✅ Handle registration response
    if (!isset($response['status'])) {
        echo json_encode(["status" => "error", "message" => "Unexpected response from authentication server"]);
        exit();
    }

    if ($response['status'] === "success") {
        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
        exit();
    } elseif ($response['status'] === "error") {
        echo json_encode(["status" => "error", "message" => $response['message']]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Unexpected response format"]);
        exit();
    }

} catch (Exception $e) {
    error_log("[REGISTER] ERROR: RabbitMQ Connection Failed - " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Error connecting to RabbitMQ"]);
    
    // ✅ Close connection on failure
    unset($_SESSION['rabbitmq_client']);
    exit();
}
?>
