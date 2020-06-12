<?php

require_once '../common/database.php';
require_once '../common/partInspectionInfo.php';
require_once '../common/report.php';
require_once '../common/time.php';
require_once '../common/userInfo.php';

class PartInspectionReport extends Report
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
      return (array("Inspector", "Date", "Time", "Work Center #", "Part #", "Part Count", "Failure Count", "Efficiency"));
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
         
         $result = $database->getPartInspections($this->employeeNumber, $startDateString, $endDateString);
         
         if ($result && ($database->countResults($result) > 0))
         {
            while ($row = $result->fetch_assoc())
            {
               $partInspectionInfo = PartInspectionInfo::load($row["entryId"]);
               
               if ($partInspectionInfo)
               {
                  $operatorName = "unknown";
                  $operator = UserInfo::load($partInspectionInfo->employeeNumber);
                  if ($operator)
                  {
                     $operatorName = $operator->getFullName();
                  }
                  
                  $dateTime = new DateTime($partInspectionInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $date = $dateTime->format("m-d-Y");
                  $time = $dateTime->format("h:i a");
                  
                  $workCenter = ($partInspectionInfo->wcNumber == 0) ? "unknown" : $partInspectionInfo->wcNumber;
                  
                  $dataRow = array();
                  $dataRow[] = $operatorName;
                  $dataRow[] = $date;
                  $dataRow[] = $time;
                  $dataRow[] = $workCenter;
                  $dataRow[] = $partInspectionInfo->partNumber;
                  $dataRow[] = $partInspectionInfo->partCount;
                  $dataRow[] = $partInspectionInfo->failures;
                  $dataRow[] = $partInspectionInfo->efficiency;
                  
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

$report = new PartInspectionReport();

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