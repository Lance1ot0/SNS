<?php
$route = strtok($_SERVER["REQUEST_URI"], '?');

switch ($route) {
  case '/':
    include 'pages/home.php';
    break;
  case '/profile':
    include 'pages/profile.php';
    break;
  case '/messages':
    include 'pages/messages.php';
    break;
}