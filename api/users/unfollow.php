<?php
require_once '../../config/Database.php';
require_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$body = json_decode(file_get_contents('php://input'));

echo $user->unfollow(
  $body->userId, 
  $body->followingId, 
);