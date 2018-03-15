<?php

require_once 'time.php';

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
   
   protected function getConnection()
   {
      return ($this->connection);
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

      if ($result && ($row = $result->fetch_assoc()))
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
   
   public function getActiveWorkCenters()
   {
      $active = JobStatus::ACTIVE;

      $query = "SELECT * FROM workCenter INNER JOIN job ON job.wcNumber = workcenter.wcNumber WHERE job.status = $active ORDER BY workcenter.wcNumber ASC;";
      
      $result = $this->query($query);

      return ($result);
   }
   
   public function getTimeCard(
      $timeCardId)
   {
      $query = "SELECT * FROM timecard WHERE timeCardId = $timeCardId";
      
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
         $query = "SELECT * FROM timecard WHERE dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC, timeCardId DESC;";

         $result = $this->query($query);
      }
      else
      {
         $query = "SELECT * FROM timecard WHERE employeeNumber=" . $employeeNumber . " AND dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC, timeCardId DESC;";
         
         $result = $this->query($query);
      }

      return ($result);
   }

   public function newTimeCard(
      $timeCardInfo)
   {
      $date = Time::toMySqlDate($timeCardInfo->dateTime);
      
      $comments = mysqli_real_escape_string($this->getConnection(), $timeCardInfo->comments);
      
      $query =
         "INSERT INTO timecard " .
         "(employeeNumber, dateTime, jobNumber, materialNumber, setupTime, runTime, panCount, partCount, scrapCount, commentCodes, comments) " .
         "VALUES " .
         "('$timeCardInfo->employeeNumber', '$date', '$timeCardInfo->jobNumber', '$timeCardInfo->materialNumber', '$timeCardInfo->setupTime', '$timeCardInfo->runTime', '$timeCardInfo->panCount', '$timeCardInfo->partCount', '$timeCardInfo->scrapCount', '$timeCardInfo->commentCodes', '$comments');";
      
      $result = $this->query($query);
      
      return ($result);
   }

   public function updateTimeCard(
      $timeCardInfo)
   {
      $dateTime = Time::toMySqlDate($timeCardInfo->dateTime);
      
      $comments = mysqli_real_escape_string($this->getConnection(), $timeCardInfo->comments);
      
      $query =
      "UPDATE timecard " .
      "SET employeeNumber = $timeCardInfo->employeeNumber, dateTime = \"$dateTime\", jobNumber = \"$timeCardInfo->jobNumber\", materialNumber = \"$timeCardInfo->materialNumber\", setupTime = $timeCardInfo->setupTime, runTime = $timeCardInfo->runTime, panCount = $timeCardInfo->panCount, partCount = $timeCardInfo->partCount, scrapCount = $timeCardInfo->scrapCount, commentCodes = $timeCardInfo->commentCodes, comments = \"$comments\" " .
      "WHERE timeCardId = $timeCardInfo->timeCardId;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteTimeCard(
      $timeCardId)
   {
      $query = "DELETE FROM timecard WHERE timeCardId = $timeCardId;";
      
      $result = $this->query($query);
      
      $query = "DELETE FROM panticket WHERE timeCardId = $timeCardId;";
      
      $this->query($query);
      
      return ($result);
   }
   
   public function getUser($employeeNumber)
   {
      $query = "SELECT * FROM user WHERE employeeNumber = \"$employeeNumber\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getUserByName($username)
   {
      $query = "SELECT * FROM user WHERE username = \"$username\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getUsersByPermissions($permissionMask)
   {
      $query = "SELECT * FROM user WHERE ((permissions & $permissionMask) > 0);";

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
      $now = Time::toMySqlDate(Time::now("Y-m-d H:i:s"));
      
      // Record last contact time.
      $query = "UPDATE sensor SET lastContact = \"$now\" WHERE sensorId = \"$sensorId\";";
      $this->query($query);
      
      // Record the reset time.
      $query = "UPDATE sensor SET resetTime = \"$now\" WHERE sensorId = \"$sensorId\";";
      $this->query($query);
      
      // Update counter count.
      $query = "UPDATE sensor SET partCount = 0 WHERE sensorId = \"$sensorId\";";
      $this->query($query);
   }
   
   public function updatePartCount($sensorId, $partCount)
   {
      $this->checkForNewSensor($sensorId);
      
      $now = Time::toMySqlDate(Time::now("Y-m-d H:i:s"));
      
      // Record last contact time.
      $query = "UPDATE sensor SET lastContact = \"$now\" WHERE sensorId = \"$sensorId\";";
      $this->query($query);
      
      if ($partCount > 0)
      {
         // Record last part count time.
         $query = "UPDATE sensor SET lastCount = \"$now\" WHERE sensorId = \"$sensorId\";";
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
      $query = "SELECT *, panticket.date AS panTicket_date, timecard.date AS timeCard_date FROM panticket INNER JOIN timecard ON panticket.timeCardId=timecard.TimeCard_ID WHERE panTicketId = $panTicketId;";

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
         $query = "SELECT *, panticket.date AS panTicket_date, timecard.date AS timeCard_date FROM panticket INNER JOIN timecard ON panticket.timeCardId=timecard.TimeCard_ID WHERE panticket.date BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY panticket.date DESC, panTicketId DESC;";
         $result = $this->query($query);
      }
      else
      {
         $query = "SELECT *, panticket.date AS panTicket_date, timecard.date AS timeCard_date FROM panticket INNER JOIN timecard ON panticket.timeCardId=timecard.TimeCard_ID WHERE EmployeeNumber=" . $employeeNumber . " AND panticket.date BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY panticket.date DESC, panTicketId DESC;";
         $result = $this->query($query);
      }
      
      return ($result);
   }
   
   public function newPanTicket(
         $panTicket)
   {
      $date = Time::toMySqlDate($panTicket->date);
      
      $query =
      "INSERT INTO panticket " .
      "(date, timeCardId, partNumber, materialNumber) " .
      "VALUES " .
      "('$date', '$panTicket->timeCardId', '$panTicket->partNumber', '$panTicket->materialNumber');";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updatePanTicket(
         $panTicketId,
         $panTicket)
   {
      $date = Time::toMySqlDate($panTicket->date);
      
      $query =
      "UPDATE panticket " .
      "SET date = \"$date\", timeCardId = $panTicket->timeCardId, partNumber = $panTicket->partNumber, materialNumber = $panTicket->materialNumber, weight = $panTicket->weight " .
      "WHERE panTicketId = $panTicketId;";
      
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
      $query = "SELECT * FROM timecard WHERE EmployeeNumber=" . $employeeNumber . " AND NOT EXISTS (SELECT * FROM panticket WHERE panticket.timeCardId = timecard.TimeCard_Id) ORDER BY Date DESC, TimeCard_ID DESC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
      
   public function newPartInspection($partInspection)
   {
      $date = Time::toMySqlDate($partInspection->dateTime);
      
      $query =
      "INSERT INTO partinspection " .
      "(dateTime, employeeNumber, wcNumber, partNumber, partCount, failures, efficiency) " .
      "VALUES " .
      "('$date', '$partInspection->employeeNumber', '$partInspection->wcNumber', '$partInspection->partNumber', '$partInspection->partCount', '$partInspection->failures', '$partInspection->efficiency');";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartInspection(
         $partInspectionId)
   {
      $query = "SELECT * FROM partinspection WHERE partInspectionId = \"$partInspectionId\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartInspections(
      $employeeNumber,
      $startDate,
      $endDate)
   {
      $result = NULL;
      if ($employeeNumber == 0)
      {
         $query = "SELECT * FROM partinspection WHERE dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC;";

         $result = $this->query($query);
      }
      else
      {
         $query = "SELECT * FROM partinspection WHERE employeeNumber =" . $employeeNumber . " AND dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC;";
         
         $result = $this->query($query);
      }
      
      return ($result);
   }
   
   public function getPartWasherEntry($partWasherEntryId)
   {
      $query = "SELECT * FROM partwasher WHERE partWasherEntryId = \"$partWasherEntryId\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartWasherEntries(
      $employeeNumber,
      $startDate,
      $endDate)
   {
      $result = NULL;
      if ($employeeNumber == 0)
      {
         $query = "SELECT * FROM partwasher WHERE dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC;";

         $result = $this->query($query);
      }
      else
      {
         $query = "SELECT * FROM partwasher WHERE employeeNumber =" . $employeeNumber . " AND dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC;";
         
         $result = $this->query($query);
      }
      
      return ($result);
   }
   
   public function newPartWasherEntry(
      $partWasherEntry)
   {
      $dateTime = Time::toMySqlDate($partWasherEntry->dateTime);
      
      $query =
      "INSERT INTO partwasher " .
      "(dateTime, employeeNumber, panTicketId, panCount, partCount) " .
      "VALUES " .
      "('$dateTime', '$partWasherEntry->employeeNumber', '$partWasherEntry->panTicketId', '$partWasherEntry->panCount', '$partWasherEntry->partCount');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updatePartWasherEntry(
      $partWasherEntryId,
      $partWasherEntry)
   {
      $dateTime = Time::toMySqlDate($partWasherEntry->dateTime);
      
      $query =
      "UPDATE partwasher " .
      "SET dateTime = \"$dateTime\", employeeNumber = $partWasherEntry->employeeNumber, panTicketId = $partWasherEntry->panTicketId, panCount = $partWasherEntry->panCount, partCount = $partWasherEntry->partCount" .
      "WHERE partWasherEntryId = $partWasherEntryId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deletePartWasherEntry(
      $partWasherEntryId)
   {
      $query = "DELETE FROM partwasher WHERE partWasherEntryId = $partWasherEntryId;";
      
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
   
   public function getJobs($startDate, $endDate, $onlyActiveJobs)
   {
      $active = JobStatus::ACTIVE;
      $deleted = JobStatus::DELETED;
      
      $whereClause = "WHERE dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "'";
      if ($onlyActiveJobs)
      {
         $whereClause .= " AND status = $active";
      }
      else
      {
         $whereClause .= " AND status != $deleted";
      }
      
      $query = "SELECT * FROM job $whereClause;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getActiveJobs($wcNumber)
   {
      $active = JobStatus::ACTIVE;
      
      $query = "SELECT * FROM job WHERE wcNumber = '$wcNumber' AND status = $active;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getJob($jobNumber)
   {
      $query = "SELECT * FROM job WHERE jobNumber = \"$jobNumber\";";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newJob($jobInfo)
   {
      $dateTime = Time::toMySqlDate($jobInfo->dateTime);
      
      $query =
      "INSERT INTO job " .
      "(jobNumber, creator, dateTime, partNumber, wcNumber, cycleTime, netPartsPerHour, status) " .
      "VALUES " .
      "('$jobInfo->jobNumber', '$jobInfo->creator', '$dateTime', '$jobInfo->partNumber', '$jobInfo->wcNumber', '$jobInfo->cycleTime', '$jobInfo->netPartsPerHour', '$jobInfo->status');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateJob($jobInfo)
   {
      $dateTime = Time::toMySqlDate($jobInfo->dateTime);
      
      $query =
         "UPDATE job " .
         "SET creator = '$jobInfo->creator', dateTime = '$dateTime', partNumber = '$jobInfo->partNumber', wcNumber = '$jobInfo->wcNumber', cycleTime = '$jobInfo->cycleTime', netPartsPerHour = '$jobInfo->netPartsPerHour', status = '$jobInfo->status' " .
         "WHERE jobNumber = '$jobInfo->jobNumber';";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateJobStatus($jobNumber, $status)
   {
      $query =
         "UPDATE job " .
         "SET status = '$status' " .
         "WHERE jobNumber = '$jobNumber';";

      $result = $this->query($query);

      return ($result);
   }
   
   public function deleteJob($jobNumber)
   {
      $query = "DELETE FROM job WHERE jobNumber = '$jobNumber';";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getCommentCodes()
   {
      $query = "SELECT * FROM comment;";
      
      $result = $this->query($query);
      
      return ($result);
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
