<?php
class TimeCardInfo
{
   public $timeCardId;
   public $date;
   public $employeeNumber;
   public $jobNumber;
   public $wcNumber;
   public $setupTimeHour = 0;
   public $setupTimeMinute = 0;
   public $runTimeHour = 0;
   public $runTimeMinute = 0;
   public $panCount;
   public $partsCount;
   public $scrapCount;
   public $comments;
}

function getTimeCardInfo($timeCardId)
{
   $timeCardInfo = new TimeCardInfo();
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getTimeCard($timeCardId);
      
      $timeCard = $result->fetch_assoc();
      
      $timeCardInfo = new TimeCardInfo();
      $timeCardInfo->timeCardId = $timeCard['TimeCard_ID'];
      $timeCardInfo->date = $timeCard['Date'];
      $timeCardInfo->employeeNumber = $timeCard['EmployeeNumber'];
      $timeCardInfo->jobNumber = $timeCard['JobNumber'];
      $timeCardInfo->wcNumber = $timeCard['WCNumber'];
      $timeCardInfo->setupTimeHour = round($timeCard['SetupTime'] / 60);
      $timeCardInfo->setupTimeMinute = round($timeCard['SetupTime'] % 60);
      $timeCardInfo->runTimeHour = round($timeCard['RunTime'] / 60);
      $timeCardInfo->runTimeMinute = round($timeCard['RunTime'] % 60);
      $timeCardInfo->panCount = $timeCard['PanCount'];
      $timeCardInfo->partsCount = $timeCard['PartsCount'];
      $timeCardInfo->scrapCount = $timeCard['ScrapCount'];
      $timeCardInfo->comments = $timeCard['Comments'];
   }
   
   return ($timeCardInfo);
}
?>