<?php

require_once 'rest.php';
require_once '../common/jobInfo.php';
require_once '../common/partWasherEntry.php';
require_once '../common/timeCardInfo.php';
require_once '../common/userInfo.php';

// *****************************************************************************
//                                   Begin

$router = new Router();
$router->setLogging(false);

$router->add("timeCardInfo", function($params) {
   $result = new stdClass();

   if (isset($params["timeCardId"]))
   {
      $result->timeCardId = $params["timeCardId"];
      
      $timeCardInfo = TimeCardInfo::load($params["timeCardId"]);
      
      if ($timeCardInfo)
      {
         $result->success = true;
         $result->timeCardInfo = $timeCardInfo;
         
         if ($params->getBool("expandedProperties"))
         {
            $jobInfo = JobInfo::load($timeCardInfo->jobId);
            
            if ($jobInfo)
            {
               $result->jobNumber = $jobInfo->jobNumber;
               $result->wcNumber = $jobInfo->wcNumber;
            }
            
            $userInfo = UserInfo::load($timeCardInfo->employeeNumber);
            
            if ($userInfo)
            {
               $result->operatorName = $userInfo->getFullName();
            }
         }
      }
      else
      {
         $result->success = false;
         $result->error = "Invalid time card ID.";
      }
   }
   else
   {
      $result->success = false;
      $result->error = "No time card ID specified.";
   }
   
   echo json_encode($result);
});

$router->add("wcNumbers", function($params) {
   $result = new stdClass();
   
   $database = PPTPDatabase::getInstance();
   $dbaseResult = null;
   
   if (isset($params["jobNumber"]))
   {
      $dbaseResult = $database->getWorkCentersForJob($params["jobNumber"]);
   }
   else
   {
      $dbaseResult = $database->getWorkCenters();
   }
   
   if ($dbaseResult)
   {
      $result->success = true;
      $result->wcNumbers = array();
      
      while ($row = $dbaseResult->fetch_assoc())
      {
         $result->wcNumbers[] = $row["wcNumber"];
      }
   }
   else
   {
      $result->status = false;
      $result->error = "No work centers found.";
   }
   
   echo json_encode($result);
});

$router->add("savePartWasherEntry", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   $dbaseResult = null;
   
   $partWasherEntry = null;
   
   if (isset($params["partWasherEntryId"]))
   {
      //  Updated entry
      $partWasherEntry = PartWasherEntry::load($params["partWasherEntryId"]);
      
      if (!$partWasherEntry)
      {
         $result->success = false;
         $result->error = "No existing part entry found.";
      }
   }
   else
   {
      // New entry.
      $partWasherEntry = new PartWasherEntry();
   }
   
   if ($result->success)
   {
      // Use current date/time as entry time.
      $partWasherEntry->dateTime = Time::now("Y-m-d h:i:s A");
      
      if (isset($params["timeCardId"]))
      {
         // Time card entry
         $partWasherEntry->timeCardId = intval($params["timeCardId"]);
      }
      else if (isset($params["jobNumber"]) &&
               isset($params["wcNumber"]) &&
               isset($params["manufactureDate"]) &&
               isset($params["operator"]))
      {
         // Manual entry
         $partWasherEntry->jobNumber = $params["jobNumber"];
         $partWasherEntry->wcNumber = $params["wcNumber"];
         $partWasherEntry->manufactureDate = $params["manufactureDate"];
         $partWasherEntry->operator = intval($params["operator"]);
      }
      else
      {
         $result->success = false;
         $result->error = "Missing parameters.";
      }
      
      if ($result->success)
      {
         if (isset($params["laborer"]) &&
             isset($params["panCount"]) &&
             isset($params["partCount"]))
         {
            $partWasherEntry->employeeNumber = intval($params["laborer"]);
            $partWasherEntry->panCount = intval($params["panCount"]);
            $partWasherEntry->partCount = intval($params["partCount"]);
            
            if ($partWasherEntry->partWasherEntryId == PartWasherEntry::UNKNOWN_ENTRY_ID)
            {
               $dbaseResult = $database->newPartWasherEntry($partWasherEntry);
            }
            else
            {
               $dbaseResult = $database->updatePartWasherEntry($partWasherEntry);
            }
            
            if (!$dbaseResult)
            {
               $result->success = false;
               $result->error = "Database query failed.";
            }
         }
         else
         {
            $result->success = false;
            $result->error = "Missing parameters.";
         }
      }
   }

   echo json_encode($result);
});

$router->route();
?>