<?php
class PanTicketInfo
{
   public $panTicketId;
   public $timeCardId;
   public $date;
   public $partNumber;
   public $materialsNumber;
   public $weight;
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
      
      $panTicketInfo->panTicketId = $panTicket['panTicketId'];
      $panTicketInfo->timeCardId = $panTicket['timeCardId'];
      $panTicketInfo->date = $panTicket['date'];
      $panTicketInfo->partNumber = $panTicket['partNumber'];
      $panTicketInfo->materialsNumber = $panTicket['materialsNumber'];
      $panTicketInfo->weight = $panTicket['weight'];
   }
   
   return ($panTicketInfo);
}
?>