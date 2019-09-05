<?php
require_once 'database.php';
require_once 'time.php';

class PartWasherEntry
{
   const UNKNOWN_ENTRY_ID = 0;
   const UNKNOWN_TIME_CARD_ID = 0;
   const UNKNOWN_JOB_ID = 0;
   const UNKNOWN_OPERATOR = 0;
   
   public $partWasherEntryId = PartWasherEntry::UNKNOWN_ENTRY_ID;
   public $dateTime;
   public $employeeNumber;
   public $timeCardId = PartWasherEntry::UNKNOWN_TIME_CARD_ID;
   public $panCount;
   public $partCount;
   
   // These attributes were added for manual entry when no time card is available.
   public $jobId = PartWasherEntry::UNKNOWN_JOB_ID;
   public $operator = PartWasherEntry::UNKNOWN_OPERATOR;
   
   public function getJobId()
   {
      $jobId = $this->jobId;
      
      if ($this->timeCardId != PartWasherEntry::UNKNOWN_TIME_CARD_ID)
      {
         $timeCardInfo = TimeCardInfo::load($this->timeCardId);
         
         if ($timeCardInfo)
         {
            $jobId= $timeCardInfo->jobId;
         }
      }
      
      return ($jobId);
   }
   
   public function getOperator()
   {
      $operator = $this->operator;
      
      if ($this->timeCardId != PartWasherEntry::UNKNOWN_TIME_CARD_ID)
      {
         $timeCardInfo = TimeCardInfo::load($this->timeCardId);
         
         if ($timeCardInfo)
         {
            $operator= $timeCardInfo->employeeNumber;
         }
      }
      
      return ($operator);
   }
   
   public function getPanCount()
   {
      $panCount = $this->panCount;
      
      if ($this->timeCardId != PartWasherEntry::UNKNOWN_TIME_CARD_ID)
      {
         $timeCardInfo = TimeCardInfo::load($this->timeCardId);
         
         if ($timeCardInfo)
         {
            $panCount = $timeCardInfo->panCount;
         }
      }
      
      return ($panCount);
   }

   public static function load($partWasherEntryId)
   {
      $partWasherEntry = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getPartWasherEntry($partWasherEntryId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $partWasherEntry = new PartWasherEntry();
            
            $partWasherEntry->partWasherEntryId = intval($row['partWasherEntryId']);
            $partWasherEntry->dateTime = Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $partWasherEntry->employeeNumber = intval($row['employeeNumber']);
            $partWasherEntry->timeCardId = intval($row['timeCardId']);
            $partWasherEntry->panCount = intval($row['panCount']);
            $partWasherEntry->partCount = intval($row['partCount']);
            
            // These attributes were added for manual entry when no time card is available.
            $partWasherEntry->jobId = intval($row['jobId']);
            $partWasherEntry->operator = intval($row['operator']);
         }
      }
      
      return ($partWasherEntry);
   }
   
   public static function getPartWasherEntryForTimeCard($timeCardId)
   {
      $partWasherEntry = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getPartWasherEntriesByTimeCard($timeCardId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $partWasherEntry = PartWasherEntry::load(intval($row['partWasherEntryId']));
         }
      }
      
      return ($partWasherEntry);
   }
   
   public static function getPartWasherEntryForJob($jobId)
   {
      $partWasherEntry = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getPartWasherEntriesByJob($jobId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            // Note: Assumes one entry per job.
            $partWasherEntry = PartWasherEntry::load(intval($row['partWasherEntryId']));
         }
      }
      
      return ($partWasherEntry);
   }
}

/*
 if (isset($_GET["partWasherEntryId"]))
 {
    $partWasherEntryId = $_GET["partWasherEntryId"];
    $partWasherEntry = PartWasherEntry::load($partWasherEntryId);
    
    if ($partWasherEntry)
    {
       echo "partWasherEntryId: " . $partWasherEntry->partWasherEntryId . "<br/>";
       echo "dateTime: " .          $partWasherEntry->dateTime .          "<br/>";
       echo "employeeNumber: " .    $partWasherEntry->employeeNumber .    "<br/>";
       echo "timeCardId: " .        $partWasherEntry->timeCardId .        "<br/>";
       echo "panCount: " .          $partWasherEntry->panCount .          "<br/>";
       echo "partCount: " .         $partWasherEntry->partCount .         "<br/>";
       echo "jobId: " .             $partWasherEntry->jobId .             "<br/>";
       echo "operator: " .          $partWasherEntry->operator .          "<br/>";
    }
    else
    {
       echo "No part washer entry found.";
    }
 }
 */
?>