<?php

require_once 'partWasherEntry.php';
require_once 'partWeightEntry.php';

// *****************************************************************************
//                                   Begin

if (isset($_GET["jobId"]) && isset($_GET["panCount"]))
{
   $jobId = $_GET["jobId"];
   $timeCardId = $_GET["panCount"];
   
   $partWeightEntry = PartWeightEntry::loadForJob($jobId);
   $partWashEntry = PartWasherEntry::loadForJob($jobId);
   
   if ($partWeightEntry && $partWashEntry && 
      ($partWeightEntry->panCount == $partWashEntry->panCount))
   {
      echo "{\"isValidPanCount\":true}";
   }
   else
   {
      echo "{\"isValidPanCount\":false}";
   }
}
?>