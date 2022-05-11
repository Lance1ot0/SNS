<?php
require_once 'config/Database.php';
require_once 'models/User.php';

require_once 'routers/user.php';

date_default_timezone_set('Europe/Paris');

$database = new Database();
$db = $database->connect();

$user = new User($db);

echo phpinfo();
