<?php
require_once '../../config/Database.php';
require_once '../../models/User.php';
require_once '../../utils/redirect.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$user->logout();

redirect('/login');