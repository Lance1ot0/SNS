<?php

require_once '../../config/Database.php';
require_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$user_id;
$uploads_folder_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/users/';

if (isset($_POST['userId'])) {
  $user_id = $_POST['userId'];
  
  if ($_FILES['file']['name'] != "") {
    $file = $_FILES['file']['name'];
    $path = pathinfo($file);
    $ext = $path['extension'];
    $temp_name = $_FILES['file']['tmp_name'];
    $path_filename_ext = "$uploads_folder_path$user_id.$ext";
    
    try {
      move_uploaded_file($temp_name, $path_filename_ext);
      
      ['user' => $user_data] = $user->get_single($user_id);
      
      $user->update_profile_picture($user_id, "$user_id.$ext");

      echo json_encode([
        'message' => 'The file has been successfully uploaded.',
        'success' => true
      ]);
    } catch (Exception $e) {
      echo json_encode(['message' => $e->getMessage()]);
    }
  }
}