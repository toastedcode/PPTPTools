<?php
require_once '../database.php';
require_once('../time.php');

class TimeCardInfo
{
   public $timeCardId;
   public $dateTime;
   public $employeeNumber;
   public $jobNumber;
   public $setupTime;
   public $runTime;
   public $panCount;
   public $partCount;
   public $scrapCount;
   public $comments;
   
   public function formatSetupTime()
   {
      return(getSetupTimeHours() . ":" . sprintf("%02d", getSetupTimeMinutes()));
   }
   
   public function getSetupTimeHours()
   {
      return (round($this->setupTime / 60));
   }
   
   public function getSetupTimeMinutes()
   {
      return (round($this->setupTime % 60));
   }
   
   public function formatRunTime()
   {
      return(getRunTimeHours() . ":" . sprintf("%02d", getRunTimeMinutes()));
   }
   
   public function getRunTimeHours()
   {
      return (round($this->runTime / 60));
   }
   
   public function getRunTimeMinutes()
   {
      return (round($this->runTime % 60));
   }
   
   public static function load($timeCardId)
   {
      $timeCardInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getTimeCard($timeCardId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $timeCardInfo = new TimeCardInfo();
            
            $timeCardInfo->timeCardId = $row['timeCardId'];
            $timeCardInfo->date = Time::fromMySqlDate($row['dateTime'], "Y-m-d h:i:s");
            $timeCardInfo->employeeNumber = $row['employeeNumber'];
            $timeCardInfo->jobNumber = $row['jobNumber'];
            $timeCardInfo->setupTime = $row['setupTime'];
            $timeCardInfo->runTime = $row['runTime'];
            $timeCardInfo->panCount = $row['panCount'];
            $timeCardInfo->partCount = $row['partCount'];
            $timeCardInfo->scrapCount = $row['scrapCount'];
            $timeCardInfo->comments = $row['comments'];
         }
      }
      
      return ($timeCardInfo);
   }
}

/*
if (isset($_GET["timeCardId"]))
{
   $timeCardId = $_GET["timeCardId"];
   $timeCardInfo = TimeCardInfo::load($timeCardId);
 
   if ($timeCardInfo)
   {
      $setupTime = $timeCardInfo->getSetupTimeHours() . ":" . $timeCardInfo->getSetupTimeMinutes();
      $runTime = $timeCardInfo->getRunTimeHours() . ":" . $timeCardInfo->getRunTimeMinutes();
      
      echo "timeCardId: " .     $timeCardInfo->timeCardId .     "<br/>";
      echo "dateTime: " .       $timeCardInfo->dateTime .       "<br/>";
      echo "employeeNumber: " . $timeCardInfo->employeeNumber . "<br/>";
      echo "jobNumber: " .      $timeCardInfo->jobNumber .      "<br/>";
      echo "setupTime: " .      $setupTime .                    "<br/>";
      echo "runTime: " .        $runTime .                      "<br/>";
      echo "partCount: " .      $timeCardInfo->partCount .      "<br/>";
      echo "scrapCount: " .     $timeCardInfo->scrapCount .     "<br/>";
      echo "comments: " .       $timeCardInfo->comments .       "<br/>";
   }
   else
   {
        echo "No time card found.";
   }
}
*/
?>