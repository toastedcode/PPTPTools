<?php
require_once 'database.php';
require_once('time.php');

class PartWeightEntry
{
   public $partWeightEntryId;
   public $dateTime;
   public $employeeNumber;
   public $timeCardId;
   public $weight;

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