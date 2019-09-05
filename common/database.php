<?php

require_once 'databaseKey.php';
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
   
   public static function countResults($result)
   {
      return (mysqli_num_rows($result));
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
  
   public function __construct()
   {
      global $SERVER, $USER, $PASSWORD, $DATABASE;
      
      parent::__construct($SERVER, $USER, $PASSWORD, $DATABASE);
   }
   
   // **************************************************************************
   //                             Operators
   // **************************************************************************
   
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
   
   // **************************************************************************
   //                              Work Centers
   // **************************************************************************

   public function getWorkCenters()
   {
      $result = $this->query("SELECT * FROM workcenter ORDER BY WCNumber ASC");

      return ($result);
   }
   
   public function getActiveWorkCenters()
   {
      $active = JobStatus::ACTIVE;

      $query = "SELECT DISTINCT workcenter.wcNumber FROM workcenter INNER JOIN job ON job.wcNumber = workcenter.wcNumber WHERE job.status = $active ORDER BY workcenter.wcNumber ASC;";

      $result = $this->query($query);

      return ($result);
   }
   
   public function getWorkCentersForJob($jobNumber)
   {
      $query = "SELECT DISTINCT wcNumber FROM job WHERE jobNumber = $jobNumber ORDER BY wcNumber ASC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                                Time Cards
   // **************************************************************************
   
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
         "(employeeNumber, dateTime, jobId, materialNumber, setupTime, runTime, panCount, partCount, scrapCount, commentCodes, comments, approvedBy) " .
         "VALUES " .
         "('$timeCardInfo->employeeNumber', '$date', '$timeCardInfo->jobId', '$timeCardInfo->materialNumber', '$timeCardInfo->setupTime', '$timeCardInfo->runTime', '$timeCardInfo->panCount', '$timeCardInfo->partCount', '$timeCardInfo->scrapCount', '$timeCardInfo->commentCodes', '$comments', '$timeCardInfo->approvedBy');";

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
      "SET employeeNumber = $timeCardInfo->employeeNumber, dateTime = \"$dateTime\", jobId = \"$timeCardInfo->jobId\", materialNumber = \"$timeCardInfo->materialNumber\", setupTime = $timeCardInfo->setupTime, runTime = $timeCardInfo->runTime, panCount = $timeCardInfo->panCount, partCount = $timeCardInfo->partCount, scrapCount = $timeCardInfo->scrapCount, commentCodes = $timeCardInfo->commentCodes, comments = \"$comments\", approvedBy = $timeCardInfo->approvedBy " .
      "WHERE timeCardId = $timeCardInfo->timeCardId;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteTimeCard(
      $timeCardId)
   {
      $query = "DELETE FROM timecard WHERE timeCardId = $timeCardId;";
      
      $result = $this->query($query);
      
      $query = "DELETE FROM partweight WHERE timeCardId = $timeCardId;";
      
      $result = $this->query($query);
      
      $query = "DELETE FROM partwasher WHERE timeCardId = $timeCardId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getIncompleteTimeCards($employeeNumber)
   {
      $query = "SELECT * FROM timecard WHERE EmployeeNumber=" . $employeeNumber . " AND NOT EXISTS (SELECT * FROM panticket WHERE panticket.timeCardId = timecard.TimeCard_Id) ORDER BY Date DESC, TimeCard_ID DESC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                                 Users
   // **************************************************************************
   
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
   
   public function getUsers()
   {
      $query = "SELECT * FROM user ORDER BY username ASC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getUsersByRole($role)
   {
      $query = "SELECT * FROM user WHERE roles = $role ORDER BY username ASC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newUser($userInfo)
   {
      $query =
      "INSERT INTO user " .
      "(employeeNumber, username, password, roles, permissions, firstName, lastName, email) " .
      "VALUES " .
      "('$userInfo->employeeNumber', '$userInfo->username', '$userInfo->password', '$userInfo->roles', '$userInfo->permissions', '$userInfo->firstName', '$userInfo->lastName', '$userInfo->email');";
 
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateUser($userInfo)
   {
      $query =
      "UPDATE user " .
      "SET username = '$userInfo->username', password = '$userInfo->password', roles = '$userInfo->roles', permissions = '$userInfo->permissions', firstName = '$userInfo->firstName', lastName = '$userInfo->lastName', email = '$userInfo->email' " .
      "WHERE employeeNumber = '$userInfo->employeeNumber';";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteUser($employeeNumber)
   {
      $query = "DELETE FROM user WHERE employeeNumber = '$employeeNumber';";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                                 Sensors
   // **************************************************************************
  
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
   
   // **************************************************************************
   //                               Part Counts
   // **************************************************************************
   
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
   
   // **************************************************************************
   //                               Part Inspections
   // **************************************************************************
      
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
   
   // **************************************************************************
   //                              Part Washer Log
   // **************************************************************************
   
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
   
   public function getPartWasherEntriesByTimeCard($timeCardId)
   {
      $query = "SELECT * FROM partwasher WHERE timeCardId = \"$timeCardId\" ORDER BY dateTime DESC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartWasherEntriesByJob($jobId)
   {
      // A tricky query because the job might be in the part washer entry, or it might be in the associated time card.
      $query = "SELECT partWasherEntryId FROM partwasher WHERE jobId = $jobId " .
               "UNION " .
               "SELECT partWasherEntryId FROM partwasher INNER JOIN timecard ON partwasher.timeCardId = timecard.timeCardId WHERE timecard.jobId = $jobId";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newPartWasherEntry(
      $partWasherEntry)
   {
      $dateTime = Time::toMySqlDate($partWasherEntry->dateTime);
      
      $query =
      "INSERT INTO partwasher " .
      "(dateTime, employeeNumber, timeCardId, panCount, partCount, jobId, operator) " .
      "VALUES " .
      "('$dateTime', '$partWasherEntry->employeeNumber', '$partWasherEntry->timeCardId', '$partWasherEntry->panCount', '$partWasherEntry->partCount', '$partWasherEntry->jobId', '$partWasherEntry->operator');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updatePartWasherEntry(
      $partWasherEntry)
   {
      $dateTime = Time::toMySqlDate($partWasherEntry->dateTime);
      
      $query =
      "UPDATE partwasher " .
      "SET dateTime = \"$dateTime\", employeeNumber = $partWasherEntry->employeeNumber, timeCardId = $partWasherEntry->timeCardId, panCount = $partWasherEntry->panCount, partCount = $partWasherEntry->partCount, jobId = $partWasherEntry->jobId, operator = $partWasherEntry->operator " .
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
   
   public function deleteAllPartWasherEntries(
      $timeCardId)
   {
      $query = "DELETE FROM partwasher WHERE timeCardId = $timeCardId;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                              Part Weight Log
   // **************************************************************************
   
   public function getPartWeightEntry(
      $partWeightEntryId)
   {
      $query = "SELECT * FROM partweight WHERE partWeightEntryId = \"$partWeightEntryId\";";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartWeightEntries(
      $employeeNumber,
      $startDate,
      $endDate)
   {
      $result = NULL;
      if ($employeeNumber == 0)
      {
         $query = "SELECT * FROM partweight WHERE dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC;";
         
         $result = $this->query($query);
      }
      else
      {
         $query = "SELECT * FROM partweight WHERE employeeNumber =" . $employeeNumber . " AND dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC;";
         
         $result = $this->query($query);
      }
      
      return ($result);
   }
   
   public function getPartWeightEntriesByTimeCard($timeCardId)
   {
      $query = "SELECT * FROM partweight WHERE timeCardId = \"$timeCardId\" ORDER BY dateTime DESC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartWeightEntriesByJob($jobId)
   {
      // A tricky query because the job might be in the part weight entry, or it might be in the associated time card.
      $query = "SELECT partWeightEntryId FROM partweight WHERE jobId = $jobId " .
               "UNION " .
               "SELECT partWeightEntryId FROM partweight INNER JOIN timecard ON partweight.timeCardId = timecard.timeCardId WHERE timecard.jobId = $jobId;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newPartWeightEntry(
      $partWeightEntry)
   {
      $dateTime = Time::toMySqlDate($partWeightEntry->dateTime);
      
      $query =
      "INSERT INTO partweight " .
      "(dateTime, employeeNumber, timeCardId, weight, jobId, operator, panCount) " .
      "VALUES " .
      "('$dateTime', '$partWeightEntry->employeeNumber', '$partWeightEntry->timeCardId', '$partWeightEntry->weight', '$partWeightEntry->jobId', '$partWeightEntry->operator', '$partWeightEntry->panCount');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updatePartWeightEntry(
      $partWeightEntry)
   {
      $dateTime = Time::toMySqlDate($partWasherEntry->dateTime);
      
      $query =
      "UPDATE partweight " .
      "SET dateTime = \"$dateTime\", employeeNumber = $partWasherEntry->employeeNumber, timeCardId = $partWasherEntry->timeCardId, weight = $partWasherEntry->weight, jobId = $partWeightEntry->jobId, operator = $partWeightEntry->operator, panCount = $partWeightEntry->panCount " .
      "WHERE partWeightEntryId = $partWeightEntry->partWeightEntryId;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deletePartWeightEntry(
      $partWeightEntryId)
   {
      $query = "DELETE FROM partweight WHERE partWeightEntryId = $partWeightEntryId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteAllPartWeightEntries(
      $timeCardId)
   {
      $query = "DELETE FROM partweight WHERE timeCardId = $timeCardId;";

      $result = $this->query($query);
      
      return ($result);
   }
      
   // **************************************************************************
   //                                  Jobs
   // **************************************************************************
   
   public function getJobNumbers()
   {
      $deleted = JobStatus::DELETED;
      
      $query = "SELECT DISTINCT jobNumber FROM job WHERE status != $deleted ORDER BY jobNumber DESC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getJobs($jobNumber, $startDate, $endDate, $onlyActiveJobs)
   {
      $active = JobStatus::ACTIVE;
      $deleted = JobStatus::DELETED;
      
      $jobNumberClause = "";
      if ($jobNumber != "All")
      {
         $jobNumberClause = "jobNumber = '$jobNumber' AND ";
      }
      
      $whereClause = "dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "'";
      if ($onlyActiveJobs)
      {
         $whereClause .= " AND status = $active";
      }
      else
      {
         $whereClause .= " AND status != $deleted";
      }
      
      $query = "SELECT * FROM job WHERE $jobNumberClause $whereClause ORDER BY dateTime DESC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getActiveJobs($wcNumber)
   {
      $active = JobStatus::ACTIVE;
      
      $wcClause = $wcNumber ? "wcNumber = '$wcNumber' AND" : "";
      
      $query = "SELECT * FROM job WHERE $wcClause status = $active ORDER BY dateTime DESC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getJob($jobId)
   {
      $query = "SELECT * FROM job WHERE jobId = $jobId;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getJobsByJobNumber($jobNumber)
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
      "(jobNumber, creator, dateTime, partNumber, wcNumber, cycleTime, netPercentage, status, customerPrint) " .
      "VALUES " .
      "('$jobInfo->jobNumber', '$jobInfo->creator', '$dateTime', '$jobInfo->partNumber', '$jobInfo->wcNumber', '$jobInfo->cycleTime', '$jobInfo->netPercentage', '$jobInfo->status', '$jobInfo->customerPrint');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateJob($jobInfo)
   {
      $dateTime = Time::toMySqlDate($jobInfo->dateTime);
      
      $query =
         "UPDATE job " .
         "SET creator = '$jobInfo->creator', dateTime = '$dateTime', partNumber = '$jobInfo->partNumber', wcNumber = '$jobInfo->wcNumber', cycleTime = '$jobInfo->cycleTime', netPercentage = '$jobInfo->netPercentage', status = '$jobInfo->status', customerPrint = '$jobInfo->customerPrint' " .
         "WHERE jobId = '$jobInfo->jobId';";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateJobStatus($jobId, $status)
   {
      $query =
         "UPDATE job " .
         "SET status = '$status' " .
         "WHERE jobId = '$jobId';";

      $result = $this->query($query);

      return ($result);
   }
   
   public function deleteJob($jobId)
   {
      $query = "DELETE FROM job WHERE jobId = '$jobId';";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getCommentCodes()
   {
      $query = "SELECT * FROM comment;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                                 Signs
   // **************************************************************************
   
   public function getSign(
      $signId)
   {
      $query = "SELECT * FROM sign WHERE signId = \"$signId\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getSigns()
   {
      $query = "SELECT * FROM sign ORDER BY signId ASC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newSign(
      $signInfo)
   {
      $query =
      "INSERT INTO sign " .
      "(name, description, url) " .
      "VALUES " .
      "('$signInfo->name', '$signInfo->description', '$signInfo->url');";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateSign(
      $signInfo)
   {
      $query =
      "UPDATE sign " .
      "SET name = '$signInfo->name', description = '$signInfo->description', url = '$signInfo->url'" .
      "WHERE signId = $signInfo->signId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteSign(
      $signId)
   {
      $query = "DELETE FROM sign WHERE signId = $signId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                          Line Inspections
   // **************************************************************************
   
   public function getLineInspections($employeeNumber, $jobNumber, $startDate, $endDate)
   {
      $operatorClause = "";
      if ($employeeNumber != 0)
      {
         $operatorClause = "operator = $employeeNumber AND ";
      }
      
      $jobNumberClause = "";
      if ($jobNumber != "All")
      {
         $jobNumberClause = "jobNumber = '$jobNumber' AND ";
      }
      
      $query = "SELECT * FROM lineinspection WHERE $operatorClause $jobNumberClause dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC, entryId DESC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getLineInspection($entryId)
   {
      $query = "SELECT * FROM lineinspection WHERE entryId = \"$entryId\";";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newLineInspection($lineInspectionInfo)
   {
      $dateTime = Time::toMySqlDate($lineInspectionInfo->dateTime);
      
      $query =
      "INSERT INTO lineinspection " .
      "(dateTime, inspector, operator, jobNumber, wcNumber, inspection1, inspection2, inspection3, inspection4, inspection5, inspection6, comments) " .
      "VALUES " .
      "('$dateTime', '$lineInspectionInfo->inspector', '$lineInspectionInfo->operator', '$lineInspectionInfo->jobNumber', '$lineInspectionInfo->wcNumber', '{$lineInspectionInfo->inspections[0]}', '{$lineInspectionInfo->inspections[1]}', '{$lineInspectionInfo->inspections[2]}', '{$lineInspectionInfo->inspections[3]}', '{$lineInspectionInfo->inspections[4]}', '{$lineInspectionInfo->inspections[5]}', '$lineInspectionInfo->comments');";

      $result = $this->query($query);

      return ($result);
   }
   
   public function updateLineInspection($lineInspectionInfo)
   {
      $dateTime = Time::toMySqlDate($lineInspectionInfo->dateTime);
      
      $query =
      "UPDATE lineinspection " .
      "SET dateTime = '$dateTime',  inspector = '$lineInspectionInfo->inspector', operator = '$lineInspectionInfo->operator', jobNumber = '$lineInspectionInfo->jobNumber', wcNumber = '$lineInspectionInfo->wcNumber', inspection1 = '{$lineInspectionInfo->inspections[0]}', inspection2 = '{$lineInspectionInfo->inspections[1]}', inspection3 = '{$lineInspectionInfo->inspections[2]}', inspection4 = '{$lineInspectionInfo->inspections[3]}', inspection5 = '{$lineInspectionInfo->inspections[4]}', inspection6 = '{$lineInspectionInfo->inspections[5]}', comments = '$lineInspectionInfo->comments' " .
      "WHERE entryId = '$lineInspectionInfo->entryId';";
      
      $result = $this->query($query);

      return ($result);
   }
   
   public function deleteLineInspection($entryId)
   {
      $query = "DELETE FROM lineinspection WHERE entryId = $entryId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                                Inspections
   // **************************************************************************
   
   public function getInspections($jobNumber, $operator, $inspectionType, $startDate, $endDate)
   {
      $operatorClause = "";
      if ($operator != 0)
      {
         $operatorClause = "operator = $operator AND ";
      }
      
      $jobNumberClause = "";
      if ($jobNumber != "All")
      {
         $jobNumberClause = "jobNumber = '$jobNumber' AND ";
      }
      
      $typeClause = "";
      if ($inspectionType != InspectionType::UNKNOWN)
      {
         $jobNumberClause = "inspectionType = $inspectionType AND ";
      }
      
      $query = "SELECT * FROM inspection WHERE $operatorClause $jobNumberClause $typeClause dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC, inspectionId DESC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getInspection($inspectionId)
   {
      $query = "SELECT * FROM inspection WHERE inspectionId = $inspectionId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getInspectionResults($inspectionId)
   {
      $query = "SELECT * FROM inspectionresult " .
               "INNER JOIN inspectionproperty ON inspectionresult.propertyId = inspectionproperty.propertyId " .
               "WHERE inspectionresult.inspectionId = $inspectionId ORDER BY inspectionproperty.ordering ASC;";
      
      $result = $this->query($query);

      return ($result);
   }
   
   public function newInspection($inspectionInfo)
   {
      $dateTime = Time::toMySqlDate($inspectionInfo->dateTime);
      
      $query =
      "INSERT INTO inspection " .
      "(templateId, dateTime, inspector, operator, jobId, comments) " .
      "VALUES " .
      "('$templateId', '$dateTime', '$inspectionInfo->inspector', '$inspectionInfo->operator', '$inspectionInfo->jobId', '$inspectionInfo->comments');";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateInspection($inspectionInfo)
   {
      $dateTime = Time::toMySqlDate($inspectionInfo->dateTime);
      
      $query =
      "UPDATE inspection " .
      "SET templateId = '$inspectionInfo->templateId', dateTime = '$dateTime',  inspector = '$inspectionInfo->inspector', operator = '$inspectionInfo->operator', jobId = '$inspectionInfo->jobId', comments = '$inspectionInfo->comments' " .
      "WHERE inspectionId = '$inspectionInfo->inspectionId';";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteInspection($inspectionId)
   {
      $query = "DELETE FROM inspection WHERE inspectionId = $inspectionId;";
      
      $result = $this->query($query);
      
      $query = "DELETE FROM inspectionresult WHERE inspectionId = $inspectionId;";
      
      $result = $this->query($query);
   }
   
   // **************************************************************************
   //                                  Private
   // **************************************************************************
   
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
