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
   public $SERVER = "localhost";
   public $USER = "root";
   public $PASSWORD = "";
   public $DATABASE = "pptp";
   
   public function __construct()
   {
      parent::__construct($this->SERVER, $this->USER, $this->PASSWORD, $this->DATABASE);
   }
   
   public function getOperators()
   {
      $result = $this->query("SELECT * FROM operator ORDER BY LastName ASC");

      return ($result);
   }

   public function getOperator(
      $employeeNumber)
   {
      $operator = NULL;

      $result = $this->query("SELECT * FROM operator WHERE EmployeeNumber=" . $employeeNumber . ";");

      if ($row = $result->fetch_assoc())
      {
         $operator = $row;
      }

      return ($operator);
   }

   public function getWorkCenters()
   {
      $result = $this->query("SELECT * FROM workcenter ORDER BY WCNumber ASC");

      return ($result);
   }
   
   public function getTimeCard(
      $timeCardId)
   {
      $query = "SELECT * FROM timecard WHERE TimeCard_Id = $timeCardId";
      
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
         $result = $this->query("SELECT * FROM timecard WHERE Date BETWEEN '" . $startDate . "' AND '" . $endDate . "' ORDER BY Date DESC, TimeCard_ID DESC;");
      }
      else
      {
         $result = $this->query("SELECT * FROM timecard WHERE EmployeeNumber=" . $employeeNumber . " AND Date BETWEEN '" . $startDate . "' AND '" . $endDate . "' ORDER BY Date DESC, TimeCard_ID DESC;");
      }

      return ($result);
   }

   public function newTimeCard(
      $timeCard)
   {
      $query =
         "INSERT INTO timecard " .
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
      "UPDATE timecard " .
      "SET EmployeeNumber = $timeCard->employeeNumber, Date = \"$timeCard->date\", JobNumber = $timeCard->jobNumber, WCNumber = $timeCard->wcNumber, SetupTime = $timeCard->setupTime, RunTime = $timeCard->runTime, PanCount = $timeCard->panCount, PartsCount = $timeCard->partsCount, ScrapCount = $timeCard->scrapCount, Comments = \"$timeCard->comments\" " .
      "WHERE TimeCard_Id = $id;";
     
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteTimeCard(
      $timeCardId)
   {
      $query = "DELETE FROM timecard WHERE TimeCard_Id = $timeCardId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getUser($username)
   {
      $query = "SELECT * FROM user WHERE Username = \"$username\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getSensors()
   {
      $query = "SELECT * FROM sensor ORDER BY wcNumber ASC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getSensor($sensorId)
   {
      $query = "SELECT * FROM sensor WHERE sensorId = \"$sensorId\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getSensorForWorkcenter($wcNumber)
   {
      $query = "SELECT * FROM sensor WHERE wcNumber = \"$wcNumber\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartCounts($wcNumber, $startDate, $endDate)
   {
      
   }
   
   public function getPartCountsByHour($wcNumber, $date)
   {
      
   }
   
   public function getPartCountsByShift($wcNumber, $shift)
   {
      
   }
   
   public function resetPartCounter($sensorId)
   {
      // Record last contact time.
      $query = "UPDATE sensor SET lastContact = NOW() WHERE sensorId = \"$sensorId\";";
      $this->query($query);
      
      // Record the reset time.
      $query = "UPDATE sensor SET resetTime = NOW() WHERE sensorId = \"$sensorId\";";
      $this->query($query);
      
      // Update counter count.
      $query = "UPDATE sensor SET partCount = 0 WHERE sensorId = \"$sensorId\";";
      $this->query($query);
   }
   
   public function updatePartCount($sensorId, $partCount)
   {
      $this->checkForNewSensor($sensorId);
      
      // Record last contact time.
      $query = "UPDATE sensor SET lastContact = NOW() WHERE sensorId = \"$sensorId\";";
      $this->query($query);
      
      if ($partCount > 0)
      {
         // Record last part count time.
         $query = "UPDATE sensor SET lastCount = NOW() WHERE sensorId = \"$sensorId\";";
         $this->query($query);
         
         // Update counter count.
         $query = "UPDATE sensor SET partCount = partCount + $partCount WHERE sensorId = \"$sensorId\";";
         $this->query($query);

         $this->updatePartCount_Hour($sensorId, $partCount);
         $this->updatePartCount_Day($sensorId, $partCount);
         $this->updatePartCount_Shift($sensorId, $partCount);
      }
   }
   
   public function getPanTicket(
         $panTicketId)
   {
      $query = "SELECT * FROM panticket INNER JOIN timecard ON panticket.timeCardId=timecard.TimeCard_ID WHERE panTicketId = $panTicketId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPanTickets(
         $employeeNumber,
         $startDate,
         $endDate)
   {
      $result = NULL;
      if ($employeeNumber == 0)
      {
         $query = "SELECT * FROM panticket INNER JOIN timecard ON panticket.timeCardId=timecard.TimeCard_ID WHERE panticket.date BETWEEN '" . $startDate . "' AND '" . $endDate . "' ORDER BY panticket.date DESC, panTicketId DESC;";
         $result = $this->query($query);
      }
      else
      {
         $query = "SELECT * FROM panticket INNER JOIN timecard ON panticket.timeCardId=timecard.TimeCard_ID WHERE EmployeeNumber=" . $employeeNumber . " AND panticket.date BETWEEN '" . $startDate . "' AND '" . $endDate . "' ORDER BY panticket.date DESC, panTicketId DESC;";
         $result = $this->query($query);
      }
      
      return ($result);
   }
   
   public function newPanTicket(
         $panTicket)
   {
      $query =
      "INSERT INTO panticket " .
      "(date, timeCardId, partNumber, materialNumber) " .
      "VALUES " .
      "('$panTicket->date', '$panTicket->timeCardId', '$panTicket->partNumber', '$panTicket->materialNumber');";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updatePanTicket(
         $panTicketId,
         $panTicket)
   {
      $query =
      "UPDATE panticket " .
      "SET date = $panTicket->date, timeCardId = \"$panTicket->timeCardId\", materialNumber = $panTicket->materialNumber " .
      "WHERE TimeCard_Id = $panTicketId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deletePanTicket(
         $panTicketId)
   {
      $query = "DELETE FROM panticket WHERE panTicketId = $panTicketId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getIncompleteTimeCards($employeeNumber)
   {
      $query = "SELECT * FROM timecard WHERE EmployeeNumber=" . $employeeNumber . " AND NOT EXISTS (SELECT * FROM panTicket WHERE panticket.timeCardId = timecard.TimeCard_Id);";
      
      $result = $this->query($query);
      
      return ($result);
   }
      
   private function checkForNewSensor($sensorId)
   {
      $result = $this->query("SELECT * FROM sensor WHERE sensorId = \"$sensorId\";");
      
      if (mysqli_num_rows($result) == 0)
      {
         $query = 
         "INSERT INTO sensor " .
         "(sensorId, lastContact, partCount, resetTime) " .
         "VALUES (\"$sensorId\", NOW(), 0, NOW());";
         
         $this->query($query);
      }
   }
   
   private function updatePartCount_Hour($sensorId, $partCount)
   {
   }
   
   private function updatePartCount_Day($sensorId, $partCount)
   {
      
   }
   
   private function updatePartCount_Shift($sensorId, $partCount)
   {
      
   }
}

?>
