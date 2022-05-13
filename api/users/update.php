<?php
require_once '../../config/Database.php';
require_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

session_start();


$body = json_decode(file_get_contents('php://input'));

['user' => $user_data] = $user->get_single($body->userId);

echo $user->update_profile(
  $user_data['id'],
  $body->firstname, 
  $body->lastname, 
  $body->bio
);