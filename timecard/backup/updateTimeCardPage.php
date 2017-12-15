<?php

function updateTimeCardPage($timeCardInfo)
{
   $success = false;
  
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $setupTime = (($timeCardInfo->setupTimeHour * 60) + $timeCardInfo->setupTimeMinute);
      $runTime = (($timeCardInfo->runTimeHour * 60) + $timeCardInfo->runTimeMinute);
      
      $timeCard = new stdClass();
      
      $timeCard->date = $timeCardInfo->date;
      $timeCard->employeeNumber = $timeCardInfo->employeeNumber;
      $timeCard->jobNumber = $timeCardInfo->jobNumber;
      $timeCard->wcNumber = $timeCardInfo->wcNumber;
      $timeCard->setupTime = $setupTime;
      $timeCard->runTime = $runTime;
      $timeCard->panCount = $timeCardInfo->panCount;
      $timeCard->partsCount = $timeCardInfo->partsCount;
      $timeCard->scrapCount = $timeCardInfo->scrapCount;
      $timeCard->comments = $timeCardInfo->comments;
      
      if ($timeCardInfo->timeCardId != 0)
      {
         $database->updateTimeCard($timeCardInfo->timeCardId, $timeCard);
      }
      else
      {
         $database->newTimeCard($timeCard);
      }
      
      $success = true;
   }
   
   if ($success)
   {
      echo 'Successful operation.<br>';
   }
   else
   {
      echo 'Unsuccessful operation.<br>';
   }
   
   echo '<button type="button" onclick="location.href=\'../pptpTools.php\';">Time Cards</button>';
}

?>