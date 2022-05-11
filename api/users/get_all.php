<?php
require_once '../../config/Database.php';
require_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);
 
echo $user->get_all();