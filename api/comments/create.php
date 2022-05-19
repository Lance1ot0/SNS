<?php
require_once '../../config/Database.php';
require_once '../../models/Comment.php';
require_once '../../models/User.php';

session_start();

$database = new Database();
$db = $database->connect();

$user = new User($db);
$comment = new Comment($db, $user);

$body = json_decode(file_get_contents('php://input'));

$user_data = $_SESSION['user'];

// echo $post->under_post(
    
//   );