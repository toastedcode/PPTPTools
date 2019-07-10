<?php

require_once '../common/database.php';
require_once '../common/jobInfo.php';
require_once '../common/lineInspectionInfo.php';
require_once '../common/report.php';
require_once '../common/time.php';
require_once '../common/userInfo.php';

class LineInspectionReport extends Report
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
      return ("Line Inspections");
   }
   
   protected function getDescription()
   {
      $description = "";
      
      $operatorDescription = "";
      if ($this->employeeNumber == 0)
      {
         $operatorDescription = "all line inspections";
      }
      else
      {
         $userInfo = UserInfo::load($this->employeeNumber);
         $operatorDescription = "line inspections for {$userInfo->getFullName()}";
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
      return (array("Date", "Time", "Inspector", "Operator", "Job", "Work<br/>Center", "Thread 1", "Thread 2", "Thread 3", "Visual"));
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
         
         $result = $database->getLineInspections($this->employeeNumber, $startDateString, $endDateString);
         
         if ($result && ($database->countResults($result) > 0))
         {
            while ($row = $result->fetch_assoc())
            {
               $lineInspectionInfo = LineInspectionInfo::load($row["entryId"]);
               
               if ($lineInspectionInfo)
               {
                  $dateTime = new DateTime($lineInspectionInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $inspectionDate = $dateTime->format("m-d-Y");
                  $inspectionTime = $dateTime->format("h:i A");
                  
                  $inspectorName = "unknown";
                  $user = UserInfo::load($lineInspectionInfo->inspector);
                  if ($user)
                  {
                     $inspectorName = $user->getFullName();
                  }
                  
                  $operatorName = "unknown";
                  $user = UserInfo::load($lineInspectionInfo->operator);
                  if ($user)
                  {
                     $operatorName = $user->getFullName();
                  }
                  
                  $dataRow = array();
                  $dataRow[] = $inspectionDate;
                  $dataRow[] = $inspectionTime;
                  $dataRow[] = $inspectorName;
                  $dataRow[] = $operatorName;
                  $dataRow[] = $lineInspectionInfo->jobNumber;
                  $dataRow[] = $lineInspectionInfo->wcNumber;
                  
                  for ($i = 0; $i < LineInspectionInfo::NUM_INSPECTIONS; $i++)
                  {
                     $dataRow[] = InspectionStatus::getLabel($lineInspectionInfo->inspections[$i]);
                  }
                  
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

$report = new LineInspectionReport();

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