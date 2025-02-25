<?php
error_reporting(E_ALL);
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $username = $_POST["username"];
    $password = $_POST["password"];

    // RabbitMQ client
    //$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

    // Request array
    $request = array();
    $request['type'] = "login";
    $request['username'] = $username;
    $request['password'] = $password;

    
    $response = $client->send_request($request);

    if ($response['success']) {
        $_SESSION["username"] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid credentials.";
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="icon" href="path/to/favicon.ico">
    <style>

html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

      body {
        background: linear-gradient(to right, #6dd5fa, #2980b9);
        color: #000;
        flex: 1; 
      }

      .footer {
        background-color: #fff;
        color: #333;
        padding: 10px 20px;
        font-size: 0.9rem;
        text-align: center;
        margin-top: auto; 
      }
    </style>
    <title>IT 490 Project</title>
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #43cea2;">
      <div class="container">
        <a class="navbar-brand text-white font-weight-bold" href="#" style="font-size: 1.5rem;">IT 490 Project</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link text-white" href="index.html">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="register.php">Register</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="dashboard.php">Dashboard</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

<form method="post">
    Username: <input type="text" name="username">
    Password: <input type="password" name="password">
    <button type="submit">Login</button>
</form>

<footer class="footer">
      Copyright © 2024 Isaiah Odunsi, Xavier Ruiz, Akinkunmi Sonubi  | <a href="#">Terms of Service</a> | <a href="#">Privacy Policy</a>
    </footer>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  </body>
</html>
