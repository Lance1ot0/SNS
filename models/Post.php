<?php

class Post
{
  private $conn;
  private $posts_table = 'posts';
  private $_user;

  public function __construct($db, $user)
  {
    $this->conn = $db;
    $this->_user = $user;
  }

  public function get_all()
  {
    $query = "SELECT * FROM $this->posts_table";

    $stmt = $this->conn->prepare($query);

    $stmt->execute();

    $posts = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $post) {
      $author = $this->_user->get_single($post['user_id']);

      array_push($posts, [
        'post' => $post,
        'author' => $author['user']
      ]);
    }

    return $posts;
  }

  public function get_single($id)
  {
    ['post_exists' => $post_exists, 'stmt' => $stmt] = $this->is_existing($id);

    if ($post_exists) {
      $post = $stmt->fetch(PDO::FETCH_ASSOC);

      return $post;
    } else {
      return json_encode(["message" => "The post doesn't exist."]);
    }
  }

  public function get_all_from_user($id)
  {
    ['user_exists' => $user_exists] = $this->_user->is_existing($id);

    if (!$user_exists) {
      return json_encode(["message" => "The user doesn't exist."]);
    }

    $query = "SELECT * FROM $this->posts_table WHERE user_id = :user_id";

    $stmt = $this->conn->prepare($query);

    $stmt->execute([
      ':user_id' => $id
    ]);

    $posts = [];
    $author = $this->_user->get_single($id);

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $post) {
      array_push($posts, $post);
    }

    return [
      'posts' => $posts,
      'author' => $author['user']
    ];
  }

  public function create($id, $content)
  {
    $query = "INSERT INTO $this->posts_table SET
      content = :content,
      user_id = :user_id
    ";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':content' => htmlentities($content),
        ':user_id' => $id,
      ]);

      return json_encode([
        'message' => 'The post has successfully been created.',
        'success' => true
      ]);
    } catch (Exception $e) {
      return json_encode(['message' => $e->getMessage()]);
    }
  }

  public function delete($id)
  {
    ['post_exists' => $post_exists, 'stmt' => $stmt] = $this->is_existing($id);

    if (!$post_exists) {
      return json_encode([
        'message' => "The post does not exist."
      ]);
    }

    $query = "DELETE FROM $this->posts_table WHERE id = :id";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':id' => $id
      ]);

      return json_encode([
        'message' => 'The post has been successfully deleted.',
        'success' => true
      ]);
    } catch (Exception $e) {
      return json_encode(['message' => $e->getMessage()]);
    }
  }

  public function is_existing($id)
  {
    $query = "SELECT * FROM $this->posts_table WHERE id = :id";

    $stmt = $this->conn->prepare($query);

    $stmt->execute([
      ':id' => $id
    ]);

    $post_exists = $stmt->rowCount() == 1;

    return [
      'post_exists' => $post_exists,
      'stmt' => $stmt
    ];
  }
}
