<?php
require_once 'database.php';
require_once('time.php');

class PartWeightEntry
{
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
   public $panCount = 0;

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
            $partWeightEntry->panCount = intval($row['panCount']);
         }
      }
      
      return ($partWeightEntry);
   }
   
   public static function getPartWeightEntryForTimeCard($timeCardId)
   {
      $partWeightEntry= null;
      
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
}

/*
if (isset($_GET["id"]))
{
   $partWeightEntryId = $_GET["id"];
   $partWeightEntry = PartWeightEntry::load($partWeightEntryId);
   
   if ($partWeightEntry)
   {
      echo "partWeightEntryId: " . $partWeightEntry->partWeightEntryId . "<br/>";
      echo "dateTime: " .           $partWeightEntry->dateTime .          "<br/>";
      echo "employeeNumber: " .     $partWeightEntry->employeeNumber .    "<br/>";
      echo "timeCardId: " .         $partWeightEntry->timeCardId .        "<br/>";
      echo "weight: " .             $partWeightEntry->weight .            "<br/>";
   }
   else
   {
      echo "No part weight found.";
   }
}
*/
?>