<?php

require_once '../database.php';
require_once 'panTicketInfo.php';
require_once 'viewPanTicketsPage.php';

// *****************************************************************************
//                                   Begin

if (isset($_GET["panTicketId"]))
{
   $panTicketId = $_GET["panTicketId"];
   
   $panTicketInfo = getPanTicketInfo($panTicketId);
   
   if ($panTicketInfo)
   {
      $panTicketDiv = addslashes(ViewPanTickets::getPanTicketDiv($panTicketId, false));  // isEditable = false
      $panTicketDiv = str_replace(array("   ", "\n", "\t", "\r"), '', $panTicketDiv);
      
      echo "{\"isValidPanTicket\":true, \"panTicketDiv\":\"$panTicketDiv\"}";
   }
   else
   {
      echo "{\"isValidPanTicket\":false}";
   }
}
?>