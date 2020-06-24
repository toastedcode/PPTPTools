<?php

require_once 'databaseKey.php';
require_once 'jobInfo.php';
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
   
   public function rowsAffected()
   {
      return(mysqli_affected_rows($this->connection));
   }
   
   public function lastInsertId()
   {
      return (mysqli_insert_id($this->connection));
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
   public static function getInstance()
   {
      if (!PPTPDatabase::$databaseInstance)
      {
         self::$databaseInstance = new PPTPDatabase();
         
         self::$databaseInstance->connect();
      }
      
      return (self::$databaseInstance);
   }
   
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
      $query = "SELECT DISTINCT wcNumber FROM job WHERE jobNumber = \"$jobNumber\" ORDER BY wcNumber ASC;";

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
      $query = "SELECT * FROM user ORDER BY firstName ASC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getUsersByRole($role)
   {
      $roleClause = "";
      if ($role != Role::UNKNOWN)
      {
         $roleClause = "WHERE roles = $role";
      }
      
      $query = "SELECT * FROM user $roleClause ORDER BY firstName ASC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getUsersByRoles($roles)
   {
      $result = null;
      
      if (sizeof($roles) > 0)
      {
         $rolesClause = "roles in (";
         
         $count = 0;
         foreach ($roles as $role)
         {
            $rolesClause .= "'$role'";
            
            $count++;
            
            if ($count < sizeof($roles))
            {
               $rolesClause .= ", ";
            }
         }
         
         $rolesClause .= ")";
         
         $query = "SELECT * FROM user WHERE $rolesClause ORDER BY firstName ASC;";

         $result = $this->query($query);
      }
      
      return ($result);
   }
   
   public function newUser($userInfo)
   {
      $query =
      "INSERT INTO user " .
      "(employeeNumber, username, password, roles, permissions, firstName, lastName, email, authToken) " .
      "VALUES " .
      "('$userInfo->employeeNumber', '$userInfo->username', '$userInfo->password', '$userInfo->roles', '$userInfo->permissions', '$userInfo->firstName', '$userInfo->lastName', '$userInfo->email', '$userInfo->authToken');";
 
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateUser($userInfo)
   {
      $query =
      "UPDATE user " .
      "SET username = '$userInfo->username', password = '$userInfo->password', roles = '$userInfo->roles', permissions = '$userInfo->permissions', firstName = '$userInfo->firstName', lastName = '$userInfo->lastName', email = '$userInfo->email', authToken = '$userInfo->authToken' " .
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
      $jobId,
      $employeeNumber,
      $startDate,
      $endDate,
      $useMfgDate)
   {      
      $jobClause = "";
      if ($jobId != JobInfo::UNKNOWN_JOB_ID)
      {
         // Job id may be in the part washer entry itself, or in the associated time card.
         $jobClause = "(partwasher.jobId = '$jobId' OR timecard.jobId = '$jobId') AND ";
      }
      
      $employeeClause = "";
      if ($employeeNumber != UserInfo::UNKNOWN_EMPLOYEE_NUMBER)
      {
         $employeeClause = "partwasher.employeeNumber = '$employeeNumber' AND";
      }
      
      $dateTimeClause = "";
      if ($useMfgDate == true)
      {
         // Manufacture time may be in the part washer entry itself, or in the associated time card.
         $dateTimeClause = "((partwasher.manufactureDate BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "') OR" .
                           " (timecard.dateTime BETWEEN '" .   Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "'))";
      }
      else
      {
         $dateTimeClause = "partwasher.dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "'";
      }
      
      $query = "SELECT partwasher.* FROM partwasher " .
               "LEFT JOIN timecard ON partwasher.timeCardId = timecard.timeCardId " .
               "WHERE $jobClause $employeeClause $dateTimeClause ORDER BY partwasher.dateTime DESC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartWasherEntriesByTimeCard($timeCardId)
   {
      $query = "SELECT * FROM partwasher WHERE timeCardId = \"$timeCardId\" ORDER BY dateTime DESC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newPartWasherEntry(
      $partWasherEntry)
   {
      $dateTime = Time::toMySqlDate($partWasherEntry->dateTime);
      
      $manufactureDate = "null";  // Note: Must use "null" for dates, rather than "".
      if ($partWasherEntry->manufactureDate)
      {
         $manufactureDate = "'" . Time::toMySqlDate($partWasherEntry->manufactureDate) . "'";
      }
      
      $query =
      "INSERT INTO partwasher " .
      "(dateTime, employeeNumber, timeCardId, panCount, partCount, jobId, operator, manufactureDate) " .
      "VALUES " .
      "('$dateTime', '$partWasherEntry->employeeNumber', '$partWasherEntry->timeCardId', '$partWasherEntry->panCount', '$partWasherEntry->partCount', '$partWasherEntry->jobId', '$partWasherEntry->operator', $manufactureDate);";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updatePartWasherEntry(
      $partWasherEntry)
   {
      $dateTime = Time::toMySqlDate($partWasherEntry->dateTime);
      
      $manufactureDate = "null";  // Note: Must use "null" for dates, rather than "".
      if ($partWasherEntry->manufactureDate)
      {
         $manufactureDate = "'" . Time::toMySqlDate($partWasherEntry->manufactureDate) . "'";
      }
      
      $query =
      "UPDATE partwasher " .
      "SET dateTime = \"$dateTime\", employeeNumber = $partWasherEntry->employeeNumber, timeCardId = $partWasherEntry->timeCardId, panCount = $partWasherEntry->panCount, partCount = $partWasherEntry->partCount, jobId = $partWasherEntry->jobId, operator = $partWasherEntry->operator, manufactureDate = $manufactureDate " .
      "WHERE partWasherEntryId = $partWasherEntry->partWasherEntryId;";

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
      $jobId,
      $employeeNumber,
      $startDate,
      $endDate,
      $useMfgDate)
   {
      $jobClause = "";
      if ($jobId != JobInfo::UNKNOWN_JOB_ID)
      {
         // Job id may be in the part weight entry itself, or in the associated time card.
         $jobClause = "(partweight.jobId = '$jobId' OR timecard.jobId = '$jobId') AND ";
      }
            
      $employeeClause = "";
      if ($employeeNumber != UserInfo::UNKNOWN_EMPLOYEE_NUMBER)
      {
         $employeeClause = "partweight.employeeNumber = '$employeeNumber' AND";
      }
      
      $dateTimeClause = "";
      if ($useMfgDate == true)
      {
         // Manufacture time may be in the part weight entry itself, or in the associated time card.
         $dateTimeClause = "((partweight.manufactureDate BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "') OR" .
                           " (timecard.dateTime BETWEEN '" .   Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "'))";
      }
      else
      {
         $dateTimeClause = "partweight.dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "'";
      }
      
      
      
      $query = "SELECT partweight.* FROM partweight " .
               "LEFT JOIN timecard ON partweight.timeCardId = timecard.timeCardId " .
               "WHERE $jobClause $employeeClause $dateTimeClause ORDER BY partweight.dateTime DESC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPartWeightEntriesByTimeCard($timeCardId)
   {
      $query = "SELECT * FROM partweight WHERE timeCardId = \"$timeCardId\" ORDER BY dateTime DESC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newPartWeightEntry(
      $partWeightEntry)
   {
      $dateTime = Time::toMySqlDate($partWeightEntry->dateTime);
      
      $manufactureDate = "null";  // Note: Must use "null" for dates, rather than "".
      if ($partWeightEntry->manufactureDate)
      {
         $manufactureDate = "'" . Time::toMySqlDate($partWeightEntry->manufactureDate) . "'";
      }

      $query =
      "INSERT INTO partweight " .
      "(dateTime, employeeNumber, timeCardId, weight, jobId, operator, manufactureDate, panCount) " .
      "VALUES " .
      "('$dateTime', '$partWeightEntry->employeeNumber', '$partWeightEntry->timeCardId', '$partWeightEntry->weight', '$partWeightEntry->jobId', '$partWeightEntry->operator', $manufactureDate, '$partWeightEntry->panCount');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updatePartWeightEntry(
      $partWeightEntry)
   {
      $dateTime = Time::toMySqlDate($partWeightEntry->dateTime);
      
      $manufactureDate = "null";  // Note: Must use "null" for dates, rather than "".
      if ($partWeightEntry->manufactureDate)
      {
         $manufactureDate = "'" . Time::toMySqlDate($partWeightEntry->manufactureDate) . "'";
      }
            
      $query =
      "UPDATE partweight " .
      "SET dateTime = \"$dateTime\", employeeNumber = $partWeightEntry->employeeNumber, timeCardId = $partWeightEntry->timeCardId, weight = $partWeightEntry->weight, jobId = $partWeightEntry->jobId, operator = $partWeightEntry->operator, manufactureDate = $manufactureDate, panCount = $partWeightEntry->panCount " .
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
   
   public function getJobNumbers($onlyActive)
   {
      $active = JobStatus::ACTIVE;
      $deleted = JobStatus::DELETED;
      
      $statusClause = "status != $deleted";
      if ($onlyActive)
      {
         $statusClause = "status = $active";
      }
      
      $query = "SELECT DISTINCT jobNumber FROM job WHERE $statusClause ORDER BY jobNumber ASC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getJobs($jobNumber, $jobStatuses)
   {
      $jobNumberClause = "";
      if ($jobNumber != "All")
      {
         $jobNumberClause = "jobNumber = '$jobNumber' AND ";
      }
      
      $jobStatusClause = "(";
      $or = false;
      for ($jobStatus = JobStatus::FIRST; $jobStatus < JobStatus::LAST; $jobStatus++)
      {
         if ($jobStatuses[$jobStatus])
         {
            if ($or)
            {
               $jobStatusClause .= " OR ";
            }
            
            $jobStatusClause .= "status = $jobStatus";
               
            $or = true;
         }
      }
      $jobStatusClause .= ")";
      
      
      $query = "SELECT * FROM job WHERE $jobNumberClause $jobStatusClause ORDER BY jobNumber ASC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getActiveJobs($wcNumber)
   {
      $active = JobStatus::ACTIVE;
      
      $wcClause = $wcNumber ? "wcNumber = '$wcNumber' AND" : "";
      
      $query = "SELECT * FROM job WHERE $wcClause status = $active ORDER BY jobNumber ASC;";

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
   
   public function getJobByComponents($jobNumber, $wcNumber)
   {
      $query = "SELECT * FROM job WHERE jobNumber = \"$jobNumber\" AND wcNumber = \"$wcNumber\";";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newJob($jobInfo)
   {
      $dateTime = Time::toMySqlDate($jobInfo->dateTime);
      
      $query =
      "INSERT INTO job " .
      "(jobNumber, creator, dateTime, partNumber, sampleWeight, wcNumber, cycleTime, netPercentage, status, customerPrint, inProcessTemplateId, lineTemplateId, qcpTemplateId) " .
      "VALUES " .
      "('$jobInfo->jobNumber', '$jobInfo->creator', '$dateTime', '$jobInfo->partNumber', '$jobInfo->sampleWeight', '$jobInfo->wcNumber', '$jobInfo->cycleTime', '$jobInfo->netPercentage', '$jobInfo->status', '$jobInfo->customerPrint', '$jobInfo->inProcessTemplateId', '$jobInfo->lineTemplateId', '$jobInfo->qcpTemplateId');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateJob($jobInfo)
   {
      $dateTime = Time::toMySqlDate($jobInfo->dateTime);
      
      $query =
         "UPDATE job " .
         "SET creator = '$jobInfo->creator', dateTime = '$dateTime', partNumber = '$jobInfo->partNumber', sampleWeight = '$jobInfo->sampleWeight', wcNumber = '$jobInfo->wcNumber', cycleTime = '$jobInfo->cycleTime', netPercentage = '$jobInfo->netPercentage', status = '$jobInfo->status', customerPrint = '$jobInfo->customerPrint', inProcessTemplateId = '$jobInfo->inProcessTemplateId', lineTemplateId = '$jobInfo->lineTemplateId',  qcpTemplateId = '$jobInfo->qcpTemplateId' " .
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
   //                            Inspection Templates
   // **************************************************************************
   
   public function getInspectionTemplates($inspectionType)
   {
      $typeClause = "";
      if ($inspectionType != InspectionType::UNKNOWN)
      {
         $typeClause = "WHERE inspectionType = $inspectionType ";
      }
      
      $query = "SELECT * FROM inspectiontemplate $typeClause ORDER BY name ASC;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getInspectionTemplate($templateId)
   {
      $query = "SELECT * FROM inspectiontemplate WHERE templateId = $templateId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getInspectionProperties($templateId)
   {
      $query = "SELECT * FROM inspectionproperty WHERE templateId = $templateId ORDER BY ordering ASC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newInspectionTemplate($inspectionTemplate)
   {
      $query =
      "INSERT INTO inspectiontemplate " .
      "(name, description, inspectionType, sampleSize, optionalProperties, notes) " .
      "VALUES " .
      "('$inspectionTemplate->name', '$inspectionTemplate->description', '$inspectionTemplate->inspectionType', '$inspectionTemplate->sampleSize', '$inspectionTemplate->optionalProperties', '$inspectionTemplate->notes');";

      $result = $this->query($query);
      
      if ($result)
      {
         // Get the last auto-increment id, which should be the inspection id.
         $templateId = mysqli_insert_id($this->getConnection());
         
         foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
         {
            $query =
            "INSERT INTO inspectionproperty " .
            "(templateId, name, specification, dataType, dataUnits, ordering) " .
            "VALUES " .
            "('$templateId', '$inspectionProperty->name', '$inspectionProperty->specification', '$inspectionProperty->dataType', '$inspectionProperty->dataUnits', '$inspectionProperty->ordering');";

            $result &= $this->query($query);
            
            if (!$result)
            {
               break;
            }
         }
      }
      
      return ($result);
   }
   
   public function updateInspectionTemplate($inspectionTemplate)
   {
      $query =
      "UPDATE inspectiontemplate " .
      "SET name = '$inspectionTemplate->name', description = '$inspectionTemplate->description', inspectionType = '$inspectionTemplate->inspectionType', sampleSize = '$inspectionTemplate->sampleSize', optionalProperties = '$inspectionTemplate->optionalProperties', notes = '$inspectionTemplate->notes' " .
      "WHERE templateId = '$inspectionTemplate->templateId';";

      $result = $this->query($query);

      if ($result)
      {
         foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
         {
            if ($inspectionProperty->propertyId == InspectionProperty::UNKNOWN_PROPERTY_ID)
            {
               // New property.
               $query =
               "INSERT INTO inspectionproperty " .
               "(templateId, name, specification, dataType, dataUnits, ordering) " .
               "VALUES " .
               "('$inspectionTemplate->templateId', '$inspectionProperty->name', '$inspectionProperty->specification', '$inspectionProperty->dataType', '$inspectionProperty->dataUnits', '$inspectionProperty->ordering');";

               $result &= $this->query($query);
            }
            else
            {
               // Updated property.
               $query =
               "UPDATE inspectionproperty " .
               "SET name = '$inspectionProperty->name', specification = '$inspectionProperty->specification', dataType =  '$inspectionProperty->dataType', dataUnits = '$inspectionProperty->dataUnits', ordering = '$inspectionProperty->ordering' " .
               "WHERE propertyId = '$inspectionProperty->propertyId';";

               $result &= $this->query($query);
            }
            
            if (!$result)
            {
               break;
            }
         }
      }
      
      return ($result);
   }
   
   public function deleteInspectionTemplate($templateId)
   {
      $query = "DELETE FROM inspectiontemplate WHERE templateId = $templateId;";
      $result = $this->query($query);
      
      $query = "DELETE FROM inspectionproperty WHERE templateId = $templateId;";
      $result &= $this->query($query);
      
      $query = "SELECT inspectionId FROM inspection WHERE templateId = $templateId;";
      $searchResult = $this->query($query);

      while ($searchResult && ($row = $searchResult->fetch_assoc()))
      {
         $this->deleteInspection(intval($row['inspectionId']));
      }
      
      return ($result);
   }
   
   // **************************************************************************
   //                                Inspections
   // **************************************************************************
   
   public function getInspections($inspectionType, $jobNumber, $inspector, $operator, $startDate, $endDate)
   {
      $userClause = "";
      if (($inspector != 0) || ($operator != 0))
      {
         $userClause = "(";
         
         if ($inspector != 0)
         {
            $userClause .= "inspector = $inspector";
         }
         
         if (($inspector != 0) && ($operator != 0))
         {
            $userClause .= " OR ";
         }
            
         if ($operator != 0)
         {
            $userClause .= "operator = $operator";
         }
         
         $userClause .= ") AND";
      }         
      
      $jobNumberClause = "";
      if ($jobNumber != "All")
      {
         $jobNumberClause = "jobNumber = '$jobNumber' AND ";
      }
      
      $typeClause = "";
      if ($inspectionType != InspectionType::UNKNOWN)
      {
         $typeClause = "inspectiontemplate.inspectionType = $inspectionType AND ";
      }
      
      $query = "SELECT * FROM inspection " .
               "INNER JOIN inspectiontemplate ON inspection.templateId = inspectiontemplate.templateId " .
               "WHERE $userClause $jobNumberClause $typeClause inspection.dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY inspection.dateTime DESC, inspectionId DESC;";

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
               "WHERE inspectionresult.inspectionId = $inspectionId ORDER BY inspectionproperty.ordering ASC, inspectionresult.sampleIndex ASC;";

      $result = $this->query($query);

      return ($result);
   }
   
   public function newInspection($inspection)
   {
      $dateTime = Time::toMySqlDate($inspection->dateTime);
      
      $query =
      "INSERT INTO inspection " .
      "(templateId, dateTime, inspector, comments, jobId, jobNumber, wcNumber, operator) " .
      "VALUES " .
      "('$inspection->templateId', '$dateTime', '$inspection->inspector', '$inspection->comments', '$inspection->jobId', '$inspection->jobNumber', '$inspection->wcNumber', '$inspection->operator');";

      $result = $this->query($query);
      
      if ($result)
      {
         // Get the last auto-increment id, which should be the inspection id.
         $inspectionId = mysqli_insert_id($this->getConnection());
         
         foreach ($inspection->inspectionResults as $inspectionRow)
         {
            foreach ($inspectionRow as $inspectionResult)
            {
               $query =
               "INSERT INTO inspectionresult " .
               "(inspectionId, propertyId, sampleIndex, dateTime, status, data) " .
               "VALUES " .
               "('$inspectionId', '$inspectionResult->propertyId', '$inspectionResult->sampleIndex', '$dateTime', '$inspectionResult->status', '$inspectionResult->data');";

               $result &= $this->query($query);
               
               if (!$result)
               {
                  break;
               }
            }
         }
      }
      
      return ($result);
   }
   
   public function updateInspection($inspection)
   {
      $dateTime = Time::toMySqlDate($inspection->dateTime);
      
      $query =
      "UPDATE inspection " .
      "SET dateTime = '$dateTime', inspector = '$inspection->inspector', comments = '$inspection->comments', jobId = '$inspection->jobId', jobNumber = '$inspection->jobNumber', wcNumber = '$inspection->wcNumber', operator = '$inspection->operator'  " .
      "WHERE inspectionId = '$inspection->inspectionId';";

      $result = $this->query($query);
      
      if ($result)
      {
         foreach ($inspection->inspectionResults as $inspectionRow)
         {
            foreach ($inspectionRow as $inspectionResult)
            {
               $query = 
               "SELECT * FROM inspectionresult " .
               "WHERE inspectionId = '$inspection->inspectionId' AND propertyId = '$inspectionResult->propertyId' AND sampleIndex='$inspectionResult->sampleIndex';";

               $seachResult = $this->query($query);
               
               if (MySqlDatabase::countResults($seachResult) == 0)
               {
                  // New result.
                  $query =
                  "INSERT INTO inspectionresult " .
                  "(inspectionId, propertyId, sampleIndex, dateTime, status, data) " .
                  "VALUES " .
                  "('$inspection->inspectionId', '$inspectionResult->propertyId', '$inspectionResult->sampleIndex', '$dateTime', '$inspectionResult->status', '$inspectionResult->data');";

                  $result &= $this->query($query);
               }
               else
               {
                  // Detect a change to the inspection result.
                  $row = $seachResult->fetch_assoc();
                  $changed = (($inspectionResult->status != intval($row['status'])) ||
                              ($inspectionResult->data != $row['data']));
                  
                  //  Only update date if a change is detected.
                  $dateClause = $changed ? "dateTime = '$dateTime', " : "";
                  
                  // Updated result.
                  $query =
                  "UPDATE inspectionresult " .
                  "SET $dateClause status = '$inspectionResult->status', data = '$inspectionResult->data' " .
                  "WHERE inspectionId = '$inspection->inspectionId' AND propertyId = '$inspectionResult->propertyId' AND sampleIndex='$inspectionResult->sampleIndex';";

                  $result &= $this->query($query);
               }

               if (!$result)
               {
                  break;
               }
            }
         }
      }
      
      return ($result);
   }
   
   public function deleteInspection($inspectionId)
   {
      $query = "DELETE FROM inspection WHERE inspectionId = $inspectionId;";
      
      $result = $this->query($query);
      
      $query = "DELETE FROM inspectionresult WHERE inspectionId = $inspectionId;";
      
      $result &= $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                                 Printer
   // **************************************************************************
   
   public function getPrinter($printerName)
   {
      $printerName = addslashes($printerName);
      
      $query = "SELECT * FROM printer WHERE printerName = '$printerName';";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newPrinter($printerInfo)
   {
      $printerName = addslashes($printerInfo->printerName);
      
      $dateTime = Time::toMySqlDate($printerInfo->lastContact);
      
      $isConnected = intval($printerInfo->isConnected);
      
      $query =
      "INSERT INTO printer " .
      "(printerName, model, isConnected, lastContact) " .
      "VALUES " .
      "('$printerName', '$printerInfo->model', '$isConnected', '$dateTime');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updatePrinter($printerInfo)
   {
      $printerName = addslashes($printerInfo->printerName);
      
      $dateTime = Time::toMySqlDate($printerInfo->lastContact);
      
      $isConnected = intval($printerInfo->isConnected);

      $query =
      "UPDATE printer " .
      "SET model = '$printerInfo->model', isConnected = '$isConnected', lastContact = '$dateTime' " .
      "WHERE printerName = '$printerName';";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deletePrinter($printerName)
   {
      $query = "DELETE FROM printer WHERE printerName = '$printerName';";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPrinters()
   {
      $query = "SELECT * FROM printer;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                                 Print Job
   // **************************************************************************
   
   public function getPrintJob($printJobId)
   {
      $query = "SELECT * FROM printjob WHERE printJobId = $printJobId;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getPrintJobIds()
   {
      $queued = PrintJobStatus::QUEUED;
      $pending = PrintJobStatus::PENDING;
      $printing = PrintJobStatus::PRINTING;
      $error = PrintJobStatus::ERROR;
      $statusClause = "WHERE status IN ($queued, $pending, $printing, $error)";
      
      $query = "SELECT printJobId FROM printjob $statusClause ORDER BY dateTime ASC;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function newPrintJob($printJob)
   {
      $printerName = addslashes($printJob->printerName);
      
      $dateTime = Time::toMySqlDate($printJob->dateTime);
      
      $query =
      "INSERT INTO printjob " .
      "(owner, dateTime, description, printerName, copies, status, xml) " . 
      "VAlUES " .
      "('$printJob->owner', '$dateTime', '$printJob->description', '$printerName', '$printJob->copies', '$printJob->status', '$printJob->xml');";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function setPrintJobStatus($printJobId, $status)
   {
      $query = "UPDATE printjob SET status = '$status' WHERE printJobId = $printJobId;";

      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deletePrintJob($printJobId)
   {
      $query = "DELETE FROM printjob WHERE printJobId = $printJobId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   // **************************************************************************
   //                              Maintenance Log
   // **************************************************************************
   
   public function getMaintenanceEntry(
      $maintenanceEntryId)
   {
      $query = "SELECT * FROM maintenance WHERE maintenanceEntryId = $maintenanceEntryId";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function getMaintenanceEntries(
      $employeeNumber,
      $startDate,
      $endDate)
   {
      $result = NULL;
      if ($employeeNumber == 0)
      {
         $query = "SELECT * FROM maintenance WHERE dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC, maintenanceEntryId DESC;";
         
         $result = $this->query($query);
      }
      else
      {
         $query = "SELECT * FROM maintenance WHERE employeeNumber=" . $employeeNumber . " AND dateTime BETWEEN '" . Time::toMySqlDate($startDate) . "' AND '" . Time::toMySqlDate($endDate) . "' ORDER BY dateTime DESC, maintenanceEntryId DESC;";
         
         $result = $this->query($query);
      }
      
      return ($result);
   }
   
   public function newMaintenanceEntry(
      $maintenanceEntry)
   {
      $dateTime = Time::toMySqlDate($maintenanceEntry->dateTime);
      $approvedDateTime = Time::toMySqlDate($maintenanceEntry->approvedDateTime);
      
      $comments = mysqli_real_escape_string($this->getConnection(), $maintenanceEntry->comments);
      
      $query =
      "INSERT INTO maintenance " .
      "(dateTime, employeeNumber, category, wcNumber, operator, maintenanceTime, comments, approvedBy, approvedDateTime) " .
      "VALUES " .
      "('$dateTime', '$maintenanceEntry->employeeNumber', '$maintenanceEntry->category', '$maintenanceEntry->wcNumber', '$maintenanceEntry->operator', '$maintenanceEntry->maintenanceTime', '$comments', '$maintenanceEntry->approvedBy', '$approvedDateTime');";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function updateMaintenanceEntry(
      $maintenanceEntry)
   {
      $dateTime = Time::toMySqlDate($maintenanceEntry->dateTime);
      $approvedDateTime = Time::toMySqlDate($maintenanceEntry->approvedDateTime);
      
      $comments = mysqli_real_escape_string($this->getConnection(), $maintenanceEntry->comments);
      
      $query =
      "UPDATE maintenance " .
      "SET dateTime = \"$dateTime\", employeeNumber = $maintenanceEntry->employeeNumber, category = $maintenanceEntry->category, wcNumber = $maintenanceEntry->wcNumber, operator = $maintenanceEntry->operator, maintenanceTime = $maintenanceEntry->maintenanceTime, comments = \"$comments\", approvedBy = $maintenanceEntry->approvedBy, approvedDateTime = \"$approvedDateTime\" " .
      "WHERE maintenanceEntryId = $maintenanceEntry->maintenanceEntryId;";
      
      $result = $this->query($query);
      
      return ($result);
   }
   
   public function deleteMaintenanceEntry(
      $maintenanceEntryId)
   {
      $query = "DELETE FROM maintenance WHERE maintenanceEntryId = $maintenanceEntryId;";
      
      $result = $this->query($query);
      
      return ($result);
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
   
   private static $databaseInstance = null;
}

?>
