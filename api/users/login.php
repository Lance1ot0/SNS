<?php
require_once '../../config/Database.php';
require_once '../../models/User.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->connect();

$user = new User($db);

$body = json_decode(file_get_contents('php://input'));

echo $user->login($body->email, $body->password);