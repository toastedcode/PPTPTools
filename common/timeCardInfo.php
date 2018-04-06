<?php
require_once '../database.php';
require_once('../time.php');

class TimeCardInfo
{
   public $timeCardId;
   public $dateTime;
   public $employeeNumber;
   public $jobNumber;
   public $materialNumber;
   public $setupTime;
   public $runTime;
   public $panCount;
   public $partCount;
   public $scrapCount;
   public $commentCodes;
   public $comments;
   
   public function formatSetupTime()
   {
      return($this->getSetupTimeHours() . ":" . sprintf("%02d", $this->getSetupTimeMinutes()));
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
      return($this->getRunTimeHours() . ":" . sprintf("%02d", $this->getRunTimeMinutes()));
   }
   
   public function getRunTimeHours()
   {
      return (round($this->runTime / 60));
   }
   
   public function getRunTimeMinutes()
   {
      return (round($this->runTime % 60));
   }
   
   public function hasCommentCode($code)
   {
      return (($this->commentCodes & $code) != 0);
   }
   
   public function setCommentCode($code)
   {
      $this->commentCodes |= $code;
   }
   
   public function clearCommentCode($code)
   {
      $this->commentCodes &= ~$code;
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
            
            $timeCardInfo->timeCardId = intval($row['timeCardId']);
            $timeCardInfo->date = Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $timeCardInfo->employeeNumber = intval($row['employeeNumber']);
            $timeCardInfo->jobNumber = $row['jobNumber'];
            $timeCardInfo->materialNumber = intval($row['materialNumber']);
            $timeCardInfo->setupTime = $row['setupTime'];
            $timeCardInfo->runTime = $row['runTime'];
            $timeCardInfo->panCount = intval($row['panCount']);
            $timeCardInfo->partCount = intval($row['partCount']);
            $timeCardInfo->scrapCount = intval($row['scrapCount']);
            $timeCardInfo->commentCodes = intval($row['commentCodes']);
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
      
      echo "timeCardId: " .     $timeCardInfo->timeCardId .           "<br/>";
      echo "dateTime: " .       $timeCardInfo->dateTime .             "<br/>";
      echo "employeeNumber: " . $timeCardInfo->employeeNumber .       "<br/>";
      echo "jobNumber: " .      $timeCardInfo->jobNumber .            "<br/>";
      echo "materialNumber: " . $timeCardInfo->materialNumber .       "<br/>";
      echo "setupTime: " .      $setupTime .                          "<br/>";
      echo "runTime: " .        $runTime .                            "<br/>";
      echo "partCount: " .      $timeCardInfo->partCount .            "<br/>";
      echo "scrapCount: " .     $timeCardInfo->scrapCount .           "<br/>";
      echo "commentCodes:"      dechex($timeCardInfo->commentCodes) . "<br/>"; 
      echo "comments: " .       $timeCardInfo->comments .             "<br/>";
   }
   else
   {
        echo "No time card found.";
   }
}
*/
?>