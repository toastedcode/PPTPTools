<?php
require_once 'commentCodes.php';
require_once 'database.php';
require_once 'jobInfo.php';
require_once 'time.php';
require_once 'userInfo.php';

class TimeCardInfo
{
   const UNKNOWN_TIME_CARD_ID = 0;
   
   const MINUTES_PER_HOUR = 60;
   
   public $timeCardId = TimeCardInfo::UNKNOWN_TIME_CARD_ID;
   public $dateTime;
   public $employeeNumber = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   public $jobId;
   public $materialNumber;
   public $setupTime;
   public $runTime;
   public $panCount;
   public $partCount;
   public $scrapCount;
   public $commentCodes;
   public $comments;
   public $approvedBy = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   public $approvedDateTime;
   
   public function formatSetupTime()
   {
      return($this->getSetupTimeHours() . ":" . sprintf("%02d", $this->getSetupTimeMinutes()));
   }
   
   public function getSetupTimeHours()
   {
      return ((int)($this->setupTime / 60));
   }
   
   public function getSetupTimeMinutes()
   {
      return ($this->setupTime % 60);
   }
   
   public function formatRunTime()
   {
      return($this->getRunTimeHours() . ":" . sprintf("%02d", $this->getRunTimeMinutes()));
   }
   
   public function getRunTimeHours()
   {
      return ((int)($this->runTime / 60));
   }
   
   public function getRunTimeMinutes()
   {
      return ($this->runTime % 60);
   }
   
   public function formatTotalTime()
   {
      return($this->getTotalTimeHours() . ":" . sprintf("%02d", $this->getTotalTimeMinutes()));
   }
   
   public function getTotalTime()
   {
      return ($this->runTime + $this->setupTime);
   }
   
   public function getTotalTimeHours()
   {
      return (round($this->getTotalTime() / 60));
   }
   
   public function getTotalTimeMinutes()
   {
      return (round($this->getTotalTime()% 60));
   }
   
   public function hasCommentCode($code)
   {
      $hasCode = false;
      
      $commentCode = CommentCode::getCommentCode($code);
      
      if ($commentCode)
      {
         $hasCode = (($this->commentCodes & $commentCode->bits) != 0);
      }
      
      return ($hasCode);
   }
   
   public function setCommentCode($code)
   {
      $commentCode = CommentCode::getCommentCode($code);
      
      if ($commentCode)
      {
         $this->commentCodes |= $commentCode->bits;
      }
   }
   
   public function clearCommentCode($code)
   {
      $commentCode = CommentCode::getCommentCode($code);
      
      if ($commentCode)
      {
         $this->commentCodes &= ~($commentCode->bits);
      }
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
            $timeCardInfo->dateTime= Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $timeCardInfo->employeeNumber = intval($row['employeeNumber']);
            $timeCardInfo->jobId = $row['jobId'];
            $timeCardInfo->materialNumber = intval($row['materialNumber']);
            $timeCardInfo->setupTime = $row['setupTime'];
            $timeCardInfo->runTime = $row['runTime'];
            $timeCardInfo->panCount = intval($row['panCount']);
            $timeCardInfo->partCount = intval($row['partCount']);
            $timeCardInfo->scrapCount = intval($row['scrapCount']);
            $timeCardInfo->commentCodes = intval($row['commentCodes']);
            $timeCardInfo->comments = $row['comments'];
            $timeCardInfo->approvedBy = intval($row['approvedBy']);
            $timeCardInfo->approvedDateTime = Time::fromMySqlDate($row['approvedDateTime'], "Y-m-d H:i:s");
         }
      }
      
      return ($timeCardInfo);
   }
   
   public function getEfficiency()
   {
      $efficiency = 0.0;
      
      // Retrieve the associated job.
      $jobInfo = JobInfo::load($this->jobId);
      
      if ($jobInfo)
      {
         // Calculate the total number of parts that could be potentially created in the run time.
         $potentialParts = (($this->runTime / TimeCardInfo::MINUTES_PER_HOUR) * $jobInfo->getGrossPartsPerHour());
         
         if ($potentialParts > 0)
         {
            // Calculate the efficiency.
            $efficiency = (($this->partCount / $potentialParts) * 100);
         }
      }
      
      return ($efficiency);
   }
   
   public function requiresApproval()
   {
      return (($this->getSetupTimeHours() + $this->getSetupTimeMinutes()) > 0);
   }
   
   public function isApproved()
   {
      // A time card is considered approved if there was no setup time, or if a manager has approved the setup time.
      return (!$this->requiresApproval() || ($this->approvedBy > 0));
   }
   
   public function incompleteTime()
   {
      return (($this->setupTime == 0) && ($this->runTime == 0));
   }
   
   public function incompletePanCount()
   {
      return ($this->panCount == 0);
   }
   
   public function incompletePartCount()
   {
      return (($this->partCount == 0) && ($this->scrapCount == 0));
   }
   
   public function isComplete()
   {
      return (!($this->incompleteTime() || 
               $this->incompletePanCount() || 
               $this->incompletePartCount()));
   }
}

/*
if (isset($_GET["timeCardId"]))
{
   $timeCardId = $_GET["timeCardId"];
   $timeCardInfo = TimeCardInfo::load($timeCardId);
 
   if ($timeCardInfo)
   {
      $runTime = $timeCardInfo->formatRunTime();
      $setupTime = $timeCardInfo->formatSetupTime();
      $totalTime = $timeCardInfo->formatTotalTime();
      
      echo "timeCardId: " .       $timeCardInfo->timeCardId .           "<br/>";
      echo "dateTime: " .         $timeCardInfo->dateTime .             "<br/>";
      echo "employeeNumber: " .   $timeCardInfo->employeeNumber .       "<br/>";
      echo "jobId: " .            $timeCardInfo->jobId .                "<br/>";
      echo "materialNumber: " .   $timeCardInfo->materialNumber .       "<br/>";
      echo "runTime: " .          $runTime .                            "<br/>";
      echo "setupTime: " .        $setupTime .                          "<br/>";
      echo "totalTime: " .        $totalTime .                          "<br/>";
      echo "partCount: " .        $timeCardInfo->partCount .            "<br/>";
      echo "scrapCount: " .       $timeCardInfo->scrapCount .           "<br/>";
      echo "commentCodes:" .      dechex($timeCardInfo->commentCodes) . "<br/>"; 
      echo "comments: " .         $timeCardInfo->comments .             "<br/>";
      echo "approvedBy: " .       $timeCardInfo->approvedBy .           "<br/>";
      echo "approvedDateTime: " . $timeCardInfo->approvedDateTime .     "<br/>";
   }
   else
   {
        echo "No time card found.";
   }
}
*/
?>