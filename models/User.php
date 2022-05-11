<?php

class User {

  private $conn;
  private $users_table = 'users';
  private $followings_users_table = 'followings_users';

  public function __construct($db) {
    $this->conn = $db;
  }

  public function get_all() {
    $query = "SELECT * FROM $this->users_table";
  
    $stmt = $this->conn->prepare($query);

    $stmt->execute();

    return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function get_single($id) {
    ['user_exists' => $user_exists, 'stmt' => $stmt] = $this->is_existing($id);

    if ($user_exists) {
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      return json_encode($user);
    } else {
      return json_encode(["message" => "The user doesn't exist"]);
    }
  }

  public function create(
    $firstname,
    $lastname,
    $email,
    $password,
    $profile_picture,
    $banner,
    $is_active,
  ) {
    $query = "INSERT INTO $this->users_table SET
      firstname = :firstname,
      lastname = :lastname,
      email = :email,
      password = :password,
      profile_picture = :profile_picture,
      banner = :banner,
      is_active = :is_active
    ";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':firstname' => htmlentities($firstname),
        ':lastname' => htmlentities($lastname),
        ':email' => htmlentities($email),
        ':password' => password_hash(htmlspecialchars($password), PASSWORD_BCRYPT, ['cost' => 12]),
        ':profile_picture' => htmlentities($profile_picture),
        ':banner' => htmlentities($banner),
        ':is_active' => htmlentities($is_active)
      ]);

      return json_encode(['message' => 'The user has successfully been created']);
    } catch (Exception $e) {
      return json_encode(['message' => $e->getMessage()]);
    }
  }

  public function delete($id) {
    ['user_exists' => $user_exists] = $this->is_existing($id);

    if (!$user_exists) {
      return json_encode(["message" => "The user doesn't exist"]);
    }

    $query = "DELETE FROM $this->users_table WHERE id = :id";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':id' => $id
      ]);

      return json_encode(['message' => 'The user has successfully been deleted']);
    } catch (Exception $e) {
      return json_encode(['message' => $e->getMessage()]);
    }
  }
  
  public function update($id, $new_user_data) {
    [$firstname, $lastname, $email, $password, $profile_picture, $banner, $is_active] = $new_user_data;

    $query = "UPDATE $this->users_table SET
      firstname = :firstname,
      lastname = :lastname,
      email = :email,
      password = :password,
      profile_picture = :profile_picture,
      banner = :banner,
      is_active = :is_active
    WHERE id = $id";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':firstname' => htmlentities($firstname),
        ':lastname' => htmlentities($lastname),
        ':email' => htmlentities($email),
        ':password' => password_hash(htmlspecialchars($password), PASSWORD_DEFAULT, ['cost' => 12]),
        ':profile_picture' => htmlentities($profile_picture),
        ':banner' => htmlentities($banner),
        ':is_active' => htmlentities($is_active)
      ]);

      return json_encode(['message' => 'The user has successfully been updated']);
    } catch (Exception $e) {
      return json_encode(['message' => $e->getMessage()]);
    }
  }

  public function login($email, $password) {
    $query = "SELECT * FROM $this->users_table WHERE email = :email";

    $stmt = $this->conn->prepare($query);

    try {
      $stmt->execute([
        ':email' => htmlentities($email),
      ]);

      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        return json_encode(['message' => "This account doesn't exist."]);
      }

      if (password_verify(htmlspecialchars($password), $user['password'])) {
        session_start();

        $_SESSION['user'] = $user;

        return json_encode([
          'message' => 'The user has successfully been logged in.', 
          'success' => true
        ]);
      }

      return json_encode(['message' => "The password doesn't match."]);
    } catch (Exception $e) {
      return json_encode(['message' => $e->getMessage()]);
    }
  }

  public function logout() {
    session_destroy();

    $_SESSION['user'] = null;

    return json_encode(['message' => 'The user has successfully been logged out']);
  }

  public function follow($id, $following_id) {
    ['user_exists' => $user_exists] = $this->is_existing($id);
    ['user_exists' => $following_exists] = $this->is_existing($following_id);

    if (!($user_exists && $following_exists)) {
      return json_encode(['message' => "The user or the following doesn't exist"]);
    }
    
    $query = "INSERT INTO $this->followings_users_table SET
      user_id = :user_id,
      following_id = :following_id
    ";

    $stmt = $this->conn->prepare($query);
  
    try {
      $stmt->execute([
        ':user_id' => htmlentities($id),
        ':following_id' => htmlentities($following_id)
      ]);

      return json_encode(['message' => "User $id is now following user $following_id"]);
    } catch (Exception $e) {
      return json_encode(['message' => $e->getMessage()]);
    };

  }  

  public function get_followings($id) {

    ['user_exists' => $user_exists] = $this->is_existing($id);

    if (!$user_exists) {
      return json_encode(['message' => "The user doesn't exist"]);
    }

    $query = "SELECT following_id FROM $this->followings_users_table WHERE user_id = :id";

    $stmt = $this->conn->prepare($query);
   
    $stmt->execute([
      ':id' => $id,
    ]);

    $followings = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $following) {
      $query = "SELECT * FROM $this->users_table WHERE id = :following_id";

      $stmt = $this->conn->prepare($query);
    
      $stmt->execute([
        ':following_id' => $following['following_id']
      ]);

      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      array_push($followings, $user);
    };

    return json_encode($followings);
  }

  public function get_followers($id) {

    ['user_exists' => $user_exists] = $this->is_existing($id);

    if (!$user_exists) {
      return json_encode(['message' => "The user doesn't exist"]);
    }

    $query = "SELECT user_id FROM $this->followings_users_table WHERE following_id = :id";

    $stmt = $this->conn->prepare($query);
   
    $stmt->execute([
      ':id' => $id,
    ]);

    $followers = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $follower) {
      $query = "SELECT * FROM $this->users_table WHERE id = :user_id";

      $stmt = $this->conn->prepare($query);
    
      $stmt->execute([
        ':user_id' => $follower['user_id']
      ]);

      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      array_push($followers, $user);
    };

    return json_encode($followers);
  }

  public function is_following($id, $following_id) {
    ['user_exists' => $user_exists] = $this->is_existing($id);
    ['user_exists' => $following_exists] = $this->is_existing($following_id);

    if (!($user_exists && $following_exists)) {
      return json_encode(['message' => "The user or the following doesn't exist"]);
    }

    $query = "SELECT * FROM $this->followings_users_table WHERE user_id = :user_id AND following_id = :following_id";
  
    $stmt = $this->conn->prepare($query);
  
    $stmt->execute([
      ':user_id' => $id,
      ':following_id' => $following_id,
    ]);

    return $stmt->rowCount() != 0;
  }

  public function unfollow($id, $following_id) {
    ['user_exists' => $user_exists] = $this->is_existing($id);
    ['user_exists' => $following_exists] = $this->is_existing($following_id);

    if (!($user_exists && $following_exists)) {
      return json_encode(['message' => "The user or the following doesn't exist"]);
    }

    $query = "DELETE FROM $this->followings_users_table WHERE user_id = :user_id AND following_id = :following_id";
    
    $stmt = $this->conn->prepare($query);

    $stmt->execute([
      ':user_id' => $id,
      ':following_id' => $following_id,
    ]);

    return json_encode(['message' => "User $id is now unfollowing user $following_id"]);
  }

  public function is_existing($id) {
    $query = "SELECT * FROM $this->users_table WHERE id = :id";

    $stmt = $this->conn->prepare($query);

    $stmt->execute([
      ':id' => $id
    ]);

    $user_exists = $stmt->rowCount() == 1;

    return [
      'user_exists' => $user_exists, 
      'stmt' => $stmt
    ];
  }
}