<?php

require_once '../common/database.php';
require_once '../common/jobInfo.php';
require_once '../common/partWasherEntry.php';
require_once '../common/report.php';
require_once '../common/time.php';
require_once '../common/timeCardInfo.php';
require_once '../common/userInfo.php';

class PartWasherReport extends Report
{
   function __construct()
   {
      $this->employeeNumber = 0;
      $this->startDate = Time::now("Y-m-d H:i:s");
      $this->endDate = Time::now("Y-m-d H:i:s");
      
      if (isset($_POST["employeeNumber"]))
      {
         $this->employeeNumber = $_POST["employeeNumber"];
      }
      else if (isset($_GET["employeeNumber"]))
      {
         $this->employeeNumber = $_GET["employeeNumber"];
      }
      
      if (isset($_POST["startDate"]))
      {
         $this->startDate = $_POST["startDate"];
      }
      
      if (isset($_POST["endDate"]))
      {
         $this->endDate = $_POST["endDate"];
      }
   }
   
   protected function getTitle()
   {
      return ("Part Washer Log");
   }
   
   protected function getDescription()
   {
      $description = "";
      
      $operatorDescription = "";
      if ($this->employeeNumber == 0)
      {
         $operatorDescription = "all part washer log entries";
      }
      else
      {
         $userInfo = UserInfo::load($this->employeeNumber);
         $operatorDescription = "part washer log entries for {$userInfo->getFullName()}";
      }
      
      $dateString = "";
      if ($this->startDate == $this->endDate)
      {
         $dateTime = new DateTime($this->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         
         $dateString = "from {$dateTime->format("m/d/Y")}";
      }
      else
      {
         $startDateTime = new DateTime($this->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         $endDateTime = new DateTime($this->endDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         
         $dateString = "from {$startDateTime->format("m/d/Y")} to {$endDateTime->format("m/d/Y")}";
      }
      
      $description = "Reporting $operatorDescription $dateString.";
      
      return ($description);
   }
   
   protected function getHeaders()
   {
      return (array("Job #", "WC #", "Operator", "Mfg. Date", "Part Washer", "Wash Date", "Wash Time", "Basket Count", "Part Count"));
   }
   
   protected function getData()
   {
      $data = array();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         // Start date.
         $startDate = new DateTime($this->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         $startDateString = $startDate->format("Y-m-d");
         
         // End date.
         // Increment the end date by a day to make it inclusive.
         $endDate = new DateTime($this->endDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         $endDate->modify('+1 day');
         $endDateString = $endDate->format("Y-m-d");
         
         $result = $database->getPartWasherEntries($this->employeeNumber, $startDateString, $endDateString);
         
         if ($result && ($database->countResults($result) > 0))
         {
            while ($row = $result->fetch_assoc())
            {
               $partWasherEntry = PartWasherEntry::load($row["partWasherEntryId"]);
               
               if ($partWasherEntry)
               {
                  $jobId = JobInfo::UNKNOWN_JOB_ID;
                  $operatorEmployeeNumber =  UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
                  $mismatch = "";
                  
                  // If we have a timeCardId, use that to fill in the job id, operator, and manufacture.
                  $mfgDate = "unknown";
                  $timeCardInfo = TimeCardInfo::load($partWasherEntry->timeCardId);
                  if ($timeCardInfo)
                  {
                     $jobId = $timeCardInfo->jobId;
                     
                     $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $mfgDate = $dateTime->format("m-d-Y");
                     
                     $operatorEmployeeNumber = $timeCardInfo->employeeNumber;
                  }
                  else
                  {
                     $jobId = $partWasherEntry->getJobId();
                     $operatorEmployeeNumber =  $partWasherEntry->getOperator();
                     
                     if ($partWasherEntry->manufactureDate)
                     {
                        $dateTime = new DateTime($partWasherEntry->manufactureDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                        $mfgDate = $dateTime->format("m-d-Y");
                     }
                  }
                  
                  // Use the job id to fill in the job number and work center number.
                  $jobNumber = "unknown";
                  $wcNumber = "unknown";
                  $jobInfo = JobInfo::load($jobId);
                  if ($jobInfo)
                  {
                     $jobNumber = $jobInfo->jobNumber;
                     $wcNumber = $jobInfo->wcNumber;
                  }
                  
                  $operatorName = "unknown";
                  $operator = UserInfo::load($operatorEmployeeNumber);
                  if ($operator)
                  {
                     $operatorName= $operator->getFullName();
                  }
                  
                  $partWasherName = "unknown";
                  $washer = UserInfo::load($partWasherEntry->employeeNumber);
                  if ($washer)
                  {
                     $partWasherName= $washer->getFullName();
                  }
                  
                  $dateTime = new DateTime($partWasherEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $washDate = $dateTime->format("m-d-Y");
                  
                  $dateTime = new DateTime($partWasherEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $washTime = $dateTime->format("h:i a");
                  
                  $dataRow = array($jobNumber, $wcNumber, $operatorName, $mfgDate, $partWasherName, $washDate, $washTime, $partWasherEntry->panCount, $partWasherEntry->partCount);
                    
                  $data[] = $dataRow;
               }
            }
         }
      }
      
      return ($data);
   }
   
   private $employeeNumber;
   
   private $startDate;
   
   private $endDate;
}

$report = new PartWasherReport();

?>

<!DOCTYPE html>
<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="../common/flex.css"/>
<link rel="stylesheet" type="text/css" href="../common/common.css"/>

</head>

<body>

   <?php $report->render(); ?>

</body>

<script>
   javascript:window.print()
</script>

</html>