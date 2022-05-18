<?php

class Message {
  private $conn;
  private $messages_table = 'messages';
  private $_user;

  public function __construct($db, $user) {
    $this->conn = $db;
    $this->_user = $user;
  }

  public function get_all() {
    $query = "SELECT * FROM $this->messages_table";

    $stmt = $this->conn->prepare($query);

    $stmt->execute();

    $messages = [];

    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $message) {
      $user_from = $this->_user->get_single($message['user_from_id']);
      $user_to = $this->_user->get_single($message['user_to_id']);
      
      array_push($messages, [
        'message' => $message,
        'user_from' => $user_from['user'],
        'user_to' => $user_to['user']
      ]);
    }

    return $messages;
  }

  public function get_conversation($user_one_id, $user_two_id) {
    ['user_exists' => $user_one_exists] = $this->_user->is_existing($user_one_id);
    ['user_exists' => $user_two_exists] = $this->_user->is_existing($user_two_id);
    
    if (!($user_one_exists || !$user_two_exists)) {
      return json_encode(["message" => "The user 1 or user 2 doesn't exist."]);
    }
    
    $user_one = $this->_user->get_single($user_one_id);
    $user_two = $this->_user->get_single($user_two_id);

    $query = "SELECT * FROM $this->messages_table WHERE user_from_id = :user_one_id AND user_to_id = :user_two_id OR user_from_id = :user_two_id AND user_to_id = :user_one_id ORDER BY published_at ASC";

    $stmt = $this->conn->prepare($query);

    $stmt->execute([
      ':user_one_id' => $user_one_id,
      ':user_two_id' => $user_two_id,
    ]);

    return json_encode([
      'user_one' => $user_one,
      'user_two' => $user_two,
      'conversation' => $stmt->fetchAll(PDO::FETCH_ASSOC),
      'success' => true
    ]);

  }

  public function get_user_conversations_users($user_id) {
    ['user_exists' => $user_exists] = $this->_user->is_existing($user_id);

    if (!$user_exists) {
      return json_encode(["message" => "The user doesn't exist."]);
    }
    
    $query = "SELECT user_to_id FROM $this->messages_table WHERE user_from_id = :user_id";
    
    $stmt = $this->conn->prepare($query);
    
    $stmt->execute([
      ':user_id' => $user_id,
    ]);

    $users = [];

    foreach(array_unique($stmt->fetchAll(PDO::FETCH_ASSOC), SORT_REGULAR) as $user_to_id) {
      $user = $this->_user->get_single($user_to_id['user_to_id']);

      array_push($users, $user);
    }

    return json_encode([
      'users' => $users,
      'success' => true
    ]);  
  }

  public function send($user_from_id, $user_to_id, $content) {
    ['user_exists' => $user_from_exists] = $this->_user->is_existing($user_from_id);
    ['user_exists' => $user_to_exists] = $this->_user->is_existing($user_to_id);
    
    if (!($user_from_exists || !$user_to_exists)) {
      return json_encode(["message" => "The user 1 or user 2 doesn't exist."]);
    }

    $query = "INSERT INTO $this->messages_table SET user_from_id = :user_from_id, user_to_id = :user_to_id, content = :content";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':user_from_id' => $user_from_id,
        ':user_to_id' => $user_to_id,
        ':content' => $content
      ]);

      return json_encode([
        'message' => "The post a been successfully sent to user $user_to_id.",
        'success' => true
      ]);
    } catch (Exception $e) {
      return json_encode(['message' => $e->getMessage()]);
    }
  }

}