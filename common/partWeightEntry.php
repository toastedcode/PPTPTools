<?php
require_once 'database.php';
require_once 'time.php';

class PartWeightEntry
{
   const UNKNOWN_ENTRY_ID = 0;
   const UNKNOWN_TIME_CARD_ID = 0;
   const UNKNOWN_JOB_ID = 0;
   const UNKNOWN_OPERATOR = 0;
   
   public $partWeightEntryId;
   public $dateTime;
   public $employeeNumber;
   public $timeCardId = PartWeightEntry::UNKNOWN_TIME_CARD_ID;
   public $weight;
   
   // These attributes were added for manual entry when no time card is available.
   public $jobId = PartWeightEntry::UNKNOWN_JOB_ID;
   public $operator = PartWeightEntry::UNKNOWN_OPERATOR;
   public $manufactureDate = null;
   public $panCount = 0;
   
   public function getJobId()
   {
      $jobId = $this->jobId;
      
      if ($this->timeCardId != PartWeightEntry::UNKNOWN_TIME_CARD_ID)
      {
         $timeCardInfo = TimeCardInfo::load($this->timeCardId);
         
         if ($timeCardInfo)
         {
            $jobId = $timeCardInfo->jobId;
         }
      }
      
      return ($jobId);
   }
   
   public function getOperator()
   {
      $operator = $this->operator;
      
      if ($this->timeCardId != PartWeightEntry::UNKNOWN_TIME_CARD_ID)
      {
         $timeCardInfo = TimeCardInfo::load($this->timeCardId);
         
         if ($timeCardInfo)
         {
            $operator = $timeCardInfo->employeeNumber;
         }
      }
      
      return ($operator);
   }
   
   public function getPanCount()
   {
      $panCount = $this->panCount;
      
      if ($this->timeCardId != PartWeightEntry::UNKNOWN_TIME_CARD_ID)
      {
         $timeCardInfo = TimeCardInfo::load($this->timeCardId);
         
         if ($timeCardInfo)
         {
            $panCount = $timeCardInfo->panCount;
         }
      }
      
      return ($panCount);
   }

   public static function load($partWeightEntryId)
   {
      $partWeightEntry = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getPartWeightEntry($partWeightEntryId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $partWeightEntry = new PartWeightEntry();
            
            $partWeightEntry->partWeightEntryId = intval($row['partWeightEntryId']);
            $partWeightEntry->dateTime = Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $partWeightEntry->employeeNumber = intval($row['employeeNumber']);
            $partWeightEntry->timeCardId = intval($row['timeCardId']);
            $partWeightEntry->weight = doubleval($row['weight']);
            
            // These attributes were added for manual entry when no time card is available.
            $partWeightEntry->jobId = intval($row['jobId']);
            $partWeightEntry->operator = intval($row['operator']);
            if ($row['manufactureDate'])
            {
               $partWeightEntry->manufactureDate = Time::fromMySqlDate($row['manufactureDate'], "Y-m-d H:i:s");
            }
            $partWeightEntry->panCount = intval($row['panCount']);
         }
      }
      
      return ($partWeightEntry);
   }
   
   public static function getPartWeightEntryForTimeCard($timeCardId)
   {
      $partWeightEntry = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getPartWeightEntriesByTimeCard($timeCardId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $partWeightEntry = PartWeightEntry::load(intval($row['partWeightEntryId']));
         }
      }
      
      return ($partWeightEntry);
   }
   
   public static function getPartWeightEntryForJob($jobId)
   {
      $partWeightEntry = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getPartWeightEntriesByJob($jobId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            // Note: Assumes one entry per job.
            $partWeightEntry = PartWeightEntry::load(intval($row['partWeightEntryId']));
         }
      }
      
      return ($partWeightEntry);
   }
}

/*
if (isset($_GET["id"]))
{
   $partWeightEntryId = $_GET["id"];
   $partWeightEntry = PartWeightEntry::load($partWeightEntryId);
   
   if ($partWeightEntry)
   {
      echo "partWeightEntryId: " . $partWeightEntry->partWeightEntryId . "<br/>";
      echo "dateTime: " .          $partWeightEntry->dateTime .          "<br/>";
      echo "employeeNumber: " .    $partWeightEntry->employeeNumber .    "<br/>";
      echo "timeCardId: " .        $partWeightEntry->timeCardId .        "<br/>";
      echo "weight: " .            $partWeightEntry->weight .            "<br/>";
      echo "jobId: " .             $partWasherEntry->jobId .             "<br/>";
      echo "operator: " .          $partWasherEntry->operator .          "<br/>";
      echo "manufactureDate: " .   $partWasherEntry->manufactureDate .   "<br/>";
      echo "panCount: " .          $partWasherEntry->panCount .          "<br/>";
   }
   else
   {
      echo "No part weight found.";
   }
}
*/
?>