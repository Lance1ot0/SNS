<?php
require_once '../../config/Database.php';
require_once '../../models/Post.php';
require_once '../../models/User.php';

session_start();

$database = new Database();
$db = $database->connect();

$user = new User($db);
$post = new Post($db, $user);

$body = json_decode(file_get_contents('php://input'));

$user_data = $_SESSION['user'];

echo $post->create(
  $user_data['id'], 
  $body->content, 
);