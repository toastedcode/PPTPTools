<?php
require_once 'database.php';
require_once 'time.php';

class PartWeightEntry
{
   const UNKNOWN_ENTRY_ID = 0;
   const UNKNOWN_TIME_CARD_ID = 0;
   const UNKNOWN_JOB_ID = 0;
   const UNKNOWN_OPERATOR = 0;
   const UNKNOWN_PAN_WEIGHT = 0;
   const UNKNOWN_PALLET_WEIGHT = 0;
   
   const STANDARD_PAN_WEIGHT = 7.1;  // lbs
   const STANDARD_PALLET_WEIGHT = 20.0;  // lbs   
   
   public $partWeightEntryId;
   public $dateTime;
   public $employeeNumber;
   public $timeCardId = PartWeightEntry::UNKNOWN_TIME_CARD_ID;
   public $weight;
   public $panWeight = PartWeightEntry::STANDARD_PAN_WEIGHT;
   public $palletWeight = PartWeightEntry::STANDARD_PALLET_WEIGHT;   
   
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
   
   public function calculatePartCount()
   {
      $partCount = 0;
      
      $jobId = $this->getJobId();
      
      $jobInfo = JobInfo::load($jobId);
      
      if ($jobInfo && ($jobInfo->sampleWeight > JobInfo::UNKNOWN_SAMPLE_WEIGHT))
      {
         $panCount = $this->getPanCount();
         
         $partCount = 
            ($this->weight - ($this->palletWeight + ($panCount * $this->panWeight))) / ($jobInfo->sampleWeight);
         
         $partCount = round($partCount, 0);
      }
      
      return ($partCount);
   }

   public static function load($partWeightEntryId)
   {
      $partWeightEntry = null;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && ($database->isConnected()))
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
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && ($database->isConnected()))
      {
         $result = $database->getPartWeightEntriesByTimeCard($timeCardId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $partWeightEntry = PartWeightEntry::load(intval($row['partWeightEntryId']));
         }
      }
      
      return ($partWeightEntry);
   }
   
   public static function getPartWeightEntriesForJob($jobId)
   {
      $partWeightEntries = array();
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && ($database->isConnected()))
      {
         $result = $database->getPartWeightEntriesByJob($jobId);
         
         while ($result && ($row = $result->fetch_assoc()))
         {
            $partWeightEntries[] = PartWeightEntry::load(intval($row['partWeightEntryId']));
         }
      }
      
      return ($partWeightEntries);
   }
   
   public static function getPanCountForJob($jobId)
   {
      $panCount = 0;
      
      $partWeightEntries = PartWeightEntry::getPartWeightEntriesForJob($jobId);
      
      // Get a total pan count from all part weight entries.
      foreach ($partWeightEntries as $partWeightEntry)
      {
         $panCount += $partWeightEntry->panCount;
      }
      
      return ($panCount);
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