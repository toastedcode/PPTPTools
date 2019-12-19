<?php

require_once '../common/database.php';
require_once '../common/jobInfo.php';
require_once '../common/partWeightEntry.php';
require_once '../common/report.php';
require_once '../common/time.php';
require_once '../common/timeCardInfo.php';
require_once '../common/userInfo.php';

class PartWeightReport extends Report
{
   function __construct()
   {
      $this->jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
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
      return ("Part Weight Log");
   }
   
   protected function getDescription()
   {
      $description = "";
      
      $operatorDescription = "";
      if ($this->employeeNumber == 0)
      {
         $operatorDescription = "all part weight log entries";
      }
      else
      {
         $userInfo = UserInfo::load($this->employeeNumber);
         $operatorDescription = "part weight log entries for {$userInfo->getFullName()}";
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
      return (array("Job #", "WC #", "Operator", "Mfg. Date", "Laborer", "Weigh Date", "Weigh Time", "Basket Count", "Weight", "Estimated Part Count"));
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
         
         $result = $database->getPartWeightEntries(JobInfo::UNKNOWN_JOB_ID, $this->employeeNumber, $startDateString, $endDateString, false);
         
         if ($result && ($database->countResults($result) > 0))
         {
            while ($row = $result->fetch_assoc())
            {
               $partWeightEntry = PartWeightEntry::load($row["partWeightEntryId"]);
               
               if ($partWeightEntry)
               {
                   $jobId = $partWeightEntry->getJobId();
                   $operatorEmployeeNumber =  $partWeightEntry->getOperator();
                  
                  // If we have a timeCardId, use that to fill in the job id, operator, and manufacture date.
                  $mfgDate = "unknown";
                  $timeCardInfo = TimeCardInfo::load($partWeightEntry->timeCardId);
                  if ($timeCardInfo)
                  {
                     $jobId = $timeCardInfo->jobId;
                     
                     $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $mfgDate = $dateTime->format("m-d-Y");
                     
                     $operatorEmployeeNumber = $timeCardInfo->employeeNumber;
                  }
                  else
                  {
                     // Otherwise, use any manually entered manufacuture date.
                     if ($partWeightEntry->manufactureDate)
                     {
                        $dateTime = new DateTime($partWeightEntry->manufactureDate);
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
                     $operatorName = $operator->getFullName();
                  }
                  
                  $laborerName = "unknown";
                  $washer = UserInfo::load($partWeightEntry->employeeNumber);
                  if ($washer)
                  {
                     $laborerName = $washer->getFullName();
                  }
                  
                  $dateTime = new DateTime($partWeightEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $washDate = $dateTime->format("m-d-Y");
                  
                  $dateTime = new DateTime($partWeightEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $washTime = $dateTime->format("h:i a");
                  
                  $dataRow = array($jobNumber, $wcNumber, $operatorName, $mfgDate, $laborerName, $washDate, $washTime, $partWeightEntry->panCount, $partWeightEntry->weight, $partWeightEntry->calculatePartCount());
                    
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

$report = new PartWeightReport();

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
