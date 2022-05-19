<?php

class Comment
{
    private $conn;
    private $comments_table = 'comments';
    private $subcomments_table = 'sub_comments';
    private $_user;

    public function __construct($db, $user)
    {
        $this->conn = $db;
        $this->_user = $user;
    }

    public function get_comments($id)
    {
        $query = "SELECT
        comments.content, comments.id
        FROM comments
        JOIN comments_posts_users
            ON comments.id = comments_posts_users.comment_id 
                AND comments_posts_users.post_id = :id
        ORDER BY published_at DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':id' => $id
        ]);

        $comments = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $comment) {
            array_push($comments, $comment);
        }

        return $comments;
    }

    public function get_subcomments($id)
    {
        $query = "SELECT
        sub_comments.content, sub_comments.id
        FROM sub_comments
        JOIN comments_users_subcomments
            ON sub_comments.id = comments_users_subcomments.sub_comments_id
                AND comments_users_subcomments.comment_id = :comment_id
        ORDER BY published_at DESC
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':comment_id' => $id
        ]);

        $subcomments = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $subcomment) {
            array_push($subcomments, $subcomment);
        }

        return $subcomments;
    }


    public function under_post($post_id, $content, $user_id)
    {
        $query = "INSERT INTO $this->comments_table SET 
            content = :content,
            published_at = :datetime
        ";

        $datetime = date_create()->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([
                ":content" => $content,
                ":datetime" => $datetime
            ]);

            $comment_id = $this->conn->lastInsertId();

            $query = "INSERT INTO comments_posts_users SET  
                post_id = :post_id, 
                comment_id = :comment_id,
                user_id = :user_id
            ";

            $stmt = $this->conn->prepare($query);

            $stmt->execute([
                ":post_id" => $post_id,
                ":comment_id" => $comment_id,
                ":user_id" => $user_id,
            ]);

            return json_encode([
                'message' => 'The comment has successfully been posted.',
                'success' => true
            ]);
        } catch (Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function under_comment($comment_id, $content, $user_id)
    {
        $query = "INSERT INTO $this->subcomments_table SET 
            content = :content,
            published_at = :datetime
        ";

        $datetime = date_create()->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([
                ":content" => $content,
                ":datetime" => $datetime
            ]);

            $sub_comment_id = $this->conn->lastInsertId();

            $query = "INSERT INTO comments_posts_users SET  
                comment_id = :comment_id,
                user_id = :user_id,
                sub_comments_id = :sub_comment_id
            ";

            $stmt = $this->conn->prepare($query);

            $stmt->execute([
                ":comment_id" => $comment_id,
                ":user_id" => $user_id,
                ":sub_comment_id" => $sub_comment_id
            ]);

            return json_encode([
                'message' => 'The comment has successfully been posted.',
                'success' => true
            ]);
        } catch (Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function delete($id, $type){
        
        if($type === 'comment'){
            ['comment_exists' => $comment_exists, 'stmt' => $stmt] = $this->is_comment_existing($id);
    
            if (!$$comment_exists) {
                return json_encode([
                  'message' => "The comment does not exist."
                ]);
              }
    
            $query = "DELETE FROM $this->comments_table where id = :id";

        }else{
            ['subcomment_exists' => $subcomment_exists, 'stmt' => $stmt] = $this->is_subcomment_existing($id);
    
            if (!$$subcomment_exists) {
                return json_encode([
                  'message' => "The comment does not exist."
                ]);
              }
    
            $query = "DELETE FROM $this->subcomments_table where id = :id";

        }

        $stmt = $this->conn->prepare($query);

        try{
            $stmt->execute([
                ':id' => $id
              ]);

            return json_encode([
                'message' => 'The comment has been successfully deleted.',
                'success' => true
              ]);
        }catch (Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function is_comment_existing($id){
        $query = "SELECT * $this->comments_table where id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':id' => $id
          ]);

        $comment_exists = $stmt->rowCount() == 1;

        return [
            'comment_exists' => $comment_exists,
            'stmt' => $stmt
        ];


    }

    public function is_subcomment_existing($id){
        $query = "SELECT * $this->subcomments_table where id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':id' => $id
          ]);

        $subcomment_exists = $stmt->rowCount() == 1;

        return [
            'subcomment_exists' => $subcomment_exists,
            'stmt' => $stmt
        ];


    }
}
