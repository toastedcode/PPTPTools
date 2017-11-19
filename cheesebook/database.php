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

   private $server = "";

   private $user = "";

   private $password = "";

   private $database = "";

   private $connection;

   private $isConnected = false;
}

class CheeseBookDatabase extends MySqlDatabase
{
   public function getUsers()
   {
      $result = $this->query("SELECT * FROM User ORDER BY LastName ASC");

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
      $result = $this->query("SELECT * FROM Post ORDER BY DateTime ASC");

      return ($result);
   }

   public function addPost(
      $dateTime,
      $user,
      $content)
   {
      $query =
         "INSERT INTO Post " .
         "(DateTime, User, Content) " .
         "VALUES " .
         "('$dateTime', '$user', '$content');";

      $result = $this->query($query);
      
      return ($result);
   }
}

?>
