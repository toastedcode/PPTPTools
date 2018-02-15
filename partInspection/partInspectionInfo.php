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
      $result = $database->getPartInspection($partInspectionId);
      
      if ($result)
      {
         $partInspection = $result->fetch_assoc();
      
         if ($partInspection)
         {
            $partInspectionInfo->partInspectionId = $partInspection['partInspectionId'];
            $partInspectionInfo->dateTime = Time::fromMySqlDate($partInspection['dateTime'], "Y-m-d H:i:s");
            $partInspectionInfo->employeeNumber = $partInspection['employeeNumber'];
            $partInspectionInfo->wcNumber = $partInspection['wcNumber'];
            $partInspectionInfo->partNumber = $partInspection['partNumber'];
            $partInspectionInfo->partCount= $partInspection['partCount'];
            $partInspectionInfo->failures= $partInspection['failures'];
            $partInspectionInfo->efficiency= $partInspection['efficiency'];
         }
      }
   }
   
   return ($partInspectionInfo);
}
?>