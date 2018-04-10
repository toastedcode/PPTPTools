<?php

require_once 'database.php';

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
   
   public static function load($partInspectionId)
   {
      $partInspectionInfo = new PartInspectionInfo();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getPartInspection($partInspectionId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $partInspectionInfo->partInspectionId = $row['partInspectionId'];
            $partInspectionInfo->dateTime = Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $partInspectionInfo->employeeNumber = $row['employeeNumber'];
            $partInspectionInfo->wcNumber = $row['wcNumber'];
            $partInspectionInfo->partNumber = $row['partNumber'];
            $partInspectionInfo->partCount= $row['partCount'];
            $partInspectionInfo->failures= $row['failures'];
            $partInspectionInfo->efficiency= $row['efficiency'];
         }
      }
      
      return ($partInspectionInfo);
   }
}
?>