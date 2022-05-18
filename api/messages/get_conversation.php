<?php
require_once '../../config/Database.php';
require_once '../../models/Message.php';
require_once '../../models/User.php';

session_start();

$database = new Database();
$db = $database->connect();

$user = new User($db);
$message = new Message($db, $user);

$body = json_decode(file_get_contents('php://input'));

$conversation = $message->get_conversation(
  $body->userOneId,
  $body->userTwoId
);

echo $conversation;