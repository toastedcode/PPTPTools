<?php

interface Database
{
   public function connect();

   public function disconnect();

   public function isConnected();

   public function query(
      $query);
}

class MySqlDatabase implements Database
{
   function __construct(
      $server,
      $user,
      $password,
      $database)
   {
      $this->server = $server;
      $this->user = $user;
      $this->password = $password;
      $this->database = $database;
   }

   public function connect()
   {
      // Create connection
      $this->connection = new mysqli($this->server, $this->user, $this->password, $this->database);

      // Check connection
      if ($this->connection->connect_error)
      {
         // TODO?
      }
      else
      {
         $this->isConnected = true;
      }
   }

   public function disconnect()
   {
      if ($this->isConnected())
      {
         $this->connection->close();
      }
   }

   public function isConnected()
   {
      return ($this->isConnected);
   }
   
   public function escapeString($string)
   {
      return (mysqli_real_escape_string($this->connection, $string));
   }
   
   public function query(
      $query)
   {
      $result = NULL;

      if ($this->isConnected())
      {
         $result = $this->connection->query($query);
      }

      return ($result);
   }
   
   protected $connection;

   private $server = "";

   private $user = "";

   private $password = "";

   private $database = "";

   private $isConnected = false;
}

class CheeseBookDatabase extends MySqlDatabase
{
   public function getUsers()
   {
      $result = $this->query("SELECT * FROM User ORDER BY FirstName ASC");

      return ($result);
   }

   public function getUser(
      $userId)
   {
      $operator = NULL;

      $result = $this->query("SELECT * FROM user WHERE id=" . $userId . ";");

      if ($row = $result->fetch_assoc())
      {
         $operator = $row;
      }

      return ($operator);
   }

   public function getPosts()
   {
      $result = $this->query("SELECT * FROM Post ORDER BY DateTime DESC");

      return ($result);
   }
   
   public function getPost(
      $postId)
   {
      $post = NULL;
      
      $result = $this->query("SELECT * FROM post WHERE id=" . $postId . ";");
      
      if ($row = $result->fetch_assoc())
      {
         $post = $row;
      }
      
      return ($post);
   }

   public function addPost(
      $userId,
      $content)
   {
      $result = NULL;
      
      $query = sprintf(
         "INSERT INTO post (dateTime, userId, content) VALUES (now(), '%s', '%s');",
         $userId,
         $this->escapeString($content));

      if ($this->query($query))
      {
         $result = $this->getPost($this->connection->insert_id);
      }
      
      return ($result);
   }
   
   public function addComment(
         $userId,
         $postId,
         $content)
   {
      $result = NULL;
      
      $query = sprintf(
            "INSERT INTO comment (dateTime, userId, postId, content) VALUES (now(), '%s', '%s', '%s');",
            $userId,
            $postId,
            $this->escapeString($content));
      
      if ($this->query($query))
      {
         $result = $this->getComment($this->connection->insert_id);
      }
      
      return ($result);
   }
   
   public function getComments(
      $postId)
   {
      $result = $this->query("SELECT * FROM comment WHERE postId=" . $postId . " ORDER BY dateTime ASC");
      
      return ($result);
   }
   
   public function getComment(
      $commentId)
   {
      $comment = NULL;
      
      $result = $this->query("SELECT * FROM comment WHERE id=" . $commentId . ";");
      
      if ($row = $result->fetch_assoc())
      {
         $comment = $row;
      }
      
      return ($comment);
   }
   
   public function addLike(
      $userId,
      $postId)
   {
      $result = NULL;
      
      $query = sprintf("INSERT INTO cheddar (userId, postId) VALUES (%d, %d);",
                       $userId,
                       $postId);
      
      if ($this->query($query))
      {
         $result = $this->getPost($this->connection->insert_id);
      }
      
      return ($result);
   }
   
   public function getLikes($postId)
   {
      $result = $this->query("SELECT * FROM cheddar WHERE postId=" . $postId . ";");
      
      return ($result);
   }
   
   public function isLiked($userId, $postId)
   {
      $result = $this->query("SELECT * FROM cheddar WHERE postId=" . $postId . " AND userId=" . $userId . ";");
      
      return (mysqli_num_rows($result) != 0);
   }
}

?>
