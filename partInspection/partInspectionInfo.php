<?php
class PartInspectionInfo
{
   public $partInspectionId;
   public $dateTime;
   public $employeeNumber;
   public $wcNumber;
   public $partNumber;
   public $partCount;
   public $failures;
   public $efficiency;
}

function getPartInspectionInfo($partInspectionId)
{
   $partInspectionInfo = new PartInspectionInfo();
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getPartInspection($panTicketId);
      
      $partInspection = $result->fetch_assoc();
      
      if ($partInspection)
      {

         $partInspection->partInspectionId = $partInspection['partInspectionId'];
         $partInspection->dateTime = Time::fromMySqlDate($partInspection['dateTime'], "Y-m-d h:i:s");
         $partInspection->employeeNumber = $partInspection['employeeNumber'];
         $partInspection->wcNumber = $partInspection['wcNumber'];
         $partInspection->partNumber = $partInspection['partNumber'];
         $partInspection->partCount= $partInspection['$partCount'];
         $partInspection->failures= $partInspection['failures'];
         $partInspection->efficiency= $partInspection['efficiency'];
      }
   }
   
   return ($partInspectionInfo);
}
?>