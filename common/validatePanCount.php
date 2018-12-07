<?php

require_once 'partWasherEntry.php';
require_once 'partWeightEntry.php';
require_once 'timeCardInfo.php';

// *****************************************************************************
//                                   Begin

if (isset($_GET["jobId"]) && 
    isset($_GET["panCount"]) &&
    isset($_GET["page"]))
{
   $isValid = true;
   $otherPanCount = 0;
   
   $jobId = intval($_GET["jobId"]);
   $panCount = intval($_GET["panCount"]);
   $page = $_GET["page"];
   
   $logEntry = null;
   
   if ($page == "partWeightLog")
   {
      $logEntry = PartWasherEntry::getPartWasherEntryForJob($jobId);
   }
   else if ($page == "partWasherLog")
   {
      $logEntry = PartWeightEntry::getPartWeightEntryForJob($jobId);
   }
   
   if ($logEntry)
   {
      $otherPanCount = $logEntry->getPanCount();
   }
   
   if ($otherPanCount != 0)
   {
      $isValid = ($panCount == $otherPanCount);
   }
   
   $result = array('isValidPanCount' => $isValid, 'otherPanCount' => $otherPanCount);
   
   echo json_encode($result);
}
?>