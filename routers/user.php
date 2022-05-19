<?php
$route = strtok($_SERVER["REQUEST_URI"], '?');

echo $_SERVER["REQUEST_URI"];

switch ($route) {
  case '/login':
    include 'pages/login.php';
    break;
  case '/signup':
    include 'pages/signup.php';
    break;
}