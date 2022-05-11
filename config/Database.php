<?php
class Database {

  private $host = 'localhost';
  private $dbname = 'sns';
  private $username = 'sns';
  private $password = 'sns123';
  private $port = '3306';
  private $conn;

  public function connect() {
    $this->conn = null;
    
    try {
      $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;port=$this->port", $this->username, $this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOException $e) {
      echo "Connection Error: $e";
    }

    return $this->conn;
  }
}
