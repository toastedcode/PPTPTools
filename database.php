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

class PPTPDatabase extends MySqlDatabase
{
   public function getOperators()
   {
      $result = $this->query("SELECT * FROM Operator");

      return ($result);
   }

   public function getOperator(
      $employeeNumber)
   {
      $operator = NULL;

      $result = $this->query("SELECT * FROM Operator WHERE EmployeeNumber=" . $employeeNumber . ";");

      if ($row = $result->fetch_assoc())
      {
         $operator = $row;
      }

      return ($operator);
   }

   public function getWorkCenters()
   {
      $result = $this->query("SELECT * FROM WorkCenter");

      return ($result);
   }
   
   public function getTimeCard(
      $timeCardId)
   {
      $query = "SELECT * FROM TimeCard WHERE TimeCard_Id = $timeCardId";
      
      $result = $this->query($query);
      
      return ($result);
   }

   public function getTimeCards(
      $employeeNumber,
      $startDate,
      $endDate)
   {
      $result = NULL;
      if ($employeeNumber == 0)
      {
         $result = $this->query("SELECT * FROM TimeCard WHERE Date BETWEEN '" . $startDate . "' AND '" . $endDate . "';");
      }
      else
      {
         $result = $this->query("SELECT * FROM TimeCard WHERE EmployeeNumber=" . $employeeNumber . " AND Date BETWEEN '" . $startDate . "' AND '" . $endDate . "';");
      }

      return ($result);
   }

   public function newTimeCard(
      $timeCard)
   {
      $query =
         "INSERT INTO TimeCard " .
         "(EmployeeNumber, Date, JobNumber, WCNumber, SetupTime, RunTime, PanCount, PartsCount, ScrapCount, Comments) " .
         "VALUES " .
         "('$timeCard->employeeNumber', '$timeCard->date', '$timeCard->jobNumber', '$timeCard->wcNumber', '$timeCard->setupTime', '$timeCard->runTime', '$timeCard->panCount', '$timeCard->partsCount', '$timeCard->scrapCount', '$timeCard->comments');";

      $result = $this->query($query);
      
      return ($result);
   }

   public function updateTimeCard(
      $id,
      $timeCard)
   {
      $query =
      "UPDATE TimeCard " .
      "SET EmployeeNumber = $timeCard->employeeNumber, Date = \"$timeCard->date\", JobNumber = $timeCard->jobNumber, WCNumber = $timeCard->wcNumber, SetupTime = $timeCard->setupTime, RunTime = $timeCard->runTime, PanCount = $timeCard->panCount, PartsCount = $timeCard->partsCount, ScrapCount = $timeCard->scrapCount, Comments = \"$timeCard->comments\" " .
      "WHERE TimeCard_Id = $id;";
     
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteTimeCard(
      $timeCardId)
   {
      $query = "DELETE FROM TimeCard WHERE TimeCard_Id = $timeCardId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getUser($username)
   {
      $query = "SELECT * FROM User WHERE Username = \"$username\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
}

?>
