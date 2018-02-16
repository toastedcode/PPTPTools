<?php
class PartWasherEntry
{
   public $partWasherEntryId;
   public $dateTime;
   public $employeeNumber;
   public $panTicketId;
   public $panCount;
   public $partCount;
}

function getPartWasherEntry($partWasherEntryId)
{
   $partWasherEntry = new PartWasherEntry();
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getPartWasherEntry($partWasherEntryId);
      
      if ($result && ($row = $result->fetch_assoc()))
      {
         $partWasherEntry->partWasherEntryId = $row['partWasherEntryId'];
         $partWasherEntry->dateTime = Time::fromMySqlDate($row['dateTime'], "Y-m-d h:i:s");
         $partWasherEntry->employeeNumber = $row['employeeNumber'];
         $partWasherEntry->panTicketId = $row['panTicketId'];
         $partWasherEntry->panCount = $row['panCount'];
         $partWasherEntry->partCount= $row['partCount'];
      }
   }
   
   return ($partWasherEntry);
}
?>