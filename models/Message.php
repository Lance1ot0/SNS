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

    $query = "SELECT * FROM $this->messages_table WHERE user_from_id = :user_one_id AND user_to_id = :user_two_id OR user_from_id = :user_two_id AND user_to_id = :user_one_id ORDER BY published_at ASC";

    $stmt = $this->conn->prepare($query);

    $stmt->execute([
      ':user_one_id' => $user_one_id,
      ':user_two_id' => $user_two_id,
    ]);

    return json_encode([
      'conversation' => $stmt->fetchAll(PDO::FETCH_ASSOC),
      'success' => true
    ]);

  }
}