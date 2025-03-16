<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

error_log("[EMAIL📧] Starting Email Consumer...\n", 3, "/var/log/mail.log");

// Email sending function
function sendEmail($to, $subject, $message) {
    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "Reply-To: support@yourdomain.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        error_log("[EMAIL✅] Email successfully sent to: $to\n", 3, "/var/log/mail.log");
        echo("[EMAIL📩] Email successfully sent to: $to:\n");
        return ["status" => "success"];
    } else {
        error_log("[EMAIL❌] Email sending failed for: $to\n", 3, "/var/log/mail.log");
        echo("[EMAIL📩] Email sending failed for: \n");

        return ["status" => "error"];
    }
}

// RabbitMQ Email Consumer
function processEmailRequest($request) {
    error_log("[EMAIL📩] Received email request: " . json_encode($request) . "\n", 3, "/var/log/mail.log");
    echo("[[EMAIL📩] Received email request:\n");
    if (!isset($request['to']) || !isset($request['subject']) || !isset($request['message'])) {
        error_log("[EMAIL⚠️] Invalid email request format\n", 3, "/var/log/mail.log");
        echo("[EMAIL⚠️] Invalid email request format\n");
        return ["status" => "error", "message" => "Invalid email request"];
    }

    return sendEmail($request['to'], $request['subject'], $request['message']);
}

// Start RabbitMQ Consumer
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
error_log("[EMAIL📧] Waiting for email message...\n", 3, "/var/log/mail.log");
echo("[EMAIL📧] Waiting for email message...\n");
$server->process_requests('processEmailRequest');
?>
