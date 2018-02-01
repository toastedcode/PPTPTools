<?php

require_once 'panTicketInfo.php';

// *****************************************************************************
//                                   Begin

if (isset($_GET["panTicketId"]))
{
   $panTicketId = $_GET["panTicketId"];
   
   $panTicketInfo = getPanTicketInfo($panTicketId);
   
   if ($panTicketInfo.panTicketId == $panTicketId)
   {
      echo "{\"isValidPanTicket\":true}";
   }
   else
   {
      echo "{\"isValidPanTicket\":false}";
   }
}
?>