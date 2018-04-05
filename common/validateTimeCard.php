<?php

require_once 'timeCardInfo.php';

// *****************************************************************************
//                                   Begin

if (isset($_GET["timeCardId"]))
{
   $timeCardId = $_GET["timeCardId"];
   
   $timeCardInfo = TimeCardInfo::load($timeCardId);
   
   if ($timeCardInfo)
   {
      //$timeCardDiv = addslashes(ViewPanTickets::getPanTicketDiv($panTicketId, false));  // isEditable = false
      //$timeCardDiv = str_replace(array("   ", "\n", "\t", "\r"), '', $panTicketDiv);
      $timeCardDiv = "<div>TODO</div>";
      
      echo "{\"isValidTimeCard\":true, \"timeCardDiv\":\"$timeCardDiv\"}";
   }
   else
   {
      echo "{\"isValidTimeCard\":false}";
   }
}
?>