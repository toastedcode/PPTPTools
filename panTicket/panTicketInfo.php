<?php
class PanTicketInfo
{
   // Pan ticket info
   public $panTicketId;
   public $timeCardId;
   public $date;
   public $partNumber;
   public $materialNumber;
   public $weight;
   
   // Time card info
   public $employeeNumber;
   public $jobNumber;
   public $wcNumber;
   public $partsCount;
}

function getPanTicketInfo($panTicketId)
{
   $panTicketInfo = new PanTicketInfo();
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getPanTicket($panTicketId);
      
      $panTicket = $result->fetch_assoc();
      
      if ($panTicket)
      {
         // Pan ticket info
         $panTicketInfo->panTicketId = $panTicket['panTicketId'];
         $panTicketInfo->timeCardId = $panTicket['timeCardId'];
         $panTicketInfo->date = Time::fromMySqlDate($panTicket['panTicket_date'], "Y-m-d h:i:s");
         $panTicketInfo->partNumber = $panTicket['partNumber'];
         $panTicketInfo->materialNumber = $panTicket['materialNumber'];
         $panTicketInfo->weight = $panTicket['weight'];
         
         // Time card info
         $panTicketInfo->employeeNumber = $panTicket['EmployeeNumber'];
         $panTicketInfo->jobNumber = $panTicket['JobNumber'];
         $panTicketInfo->wcNumber = $panTicket['WCNumber'];
         $panTicketInfo->partsCount = $panTicket['PartsCount'];
      }
   }
   
   return ($panTicketInfo);
}
?>