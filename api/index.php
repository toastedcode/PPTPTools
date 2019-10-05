<?php

require_once 'rest.php';
require_once '../common/inspection.php';
require_once '../common/inspectionTemplate.php';
require_once '../common/jobInfo.php';
require_once '../common/partWasherEntry.php';
require_once '../common/partWeightEntry.php';
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
               $result->isActiveJob = ($jobInfo->status == JobStatus::ACTIVE);
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

$router->add("jobs", function($params) {
   $result = new stdClass();
   
   $result->success = true;
   $result->jobs = JobInfo::getJobNumbers(true);  // only active
   
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

$router->add("users", function($params) {
   $result = new stdClass();
   
   $database = PPTPDatabase::getInstance();
   $dbaseResult = null;
   
   if (isset($params["role"]))
   {
      $dbaseResult = $database->getUsersByRole(intval($params["role"]));
   }
   else
   {
      $dbaseResult = $database->getUsers();
   }
   
   if ($dbaseResult)
   {
      $result->success = true;
      $result->operators = array();
      
      while ($row = $dbaseResult->fetch_assoc())
      {
         $userInfo = UserInfo::load($row["employeeNumber"]);
         
         if ($userInfo)
         {
            $operatorInfo = new stdClass();
            $operatorInfo->employeeNumber = $userInfo->employeeNumber;
            $operatorInfo->name = $userInfo->getFullName();
            $result->operators[] = $operatorInfo;
         }
      }
   }
   else
   {
      $result->status = false;
      $result->error = "No users found.";
   }
   
   echo json_encode($result);
});

$router->add("savePartWasherEntry", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   $dbaseResult = null;
   
   $partWasherEntry = null;

   if (isset($params["entryId"]) && 
       is_numeric($params["entryId"]) && 
       (intval($params["entryId"]) != PartWasherEntry::UNKNOWN_ENTRY_ID))
   {
      //  Updated entry
      $partWasherEntry = PartWasherEntry::load($params["entryId"]);
      
      if (!$partWasherEntry)
      {
         $result->success = false;
         $result->error = "No existing part washer entry found.";
      }
   }
   else
   {
      // New entry.
      $partWasherEntry = new PartWasherEntry();
      
      // Use current date/time as entry time.
      $partWasherEntry->dateTime = Time::now("Y-m-d h:i:s A");
   }
   
   if ($result->success)
   {      
      if (isset($params["timeCardId"]) && is_numeric($params["timeCardId"]))
      {
         //
         // Time card entry
         //
         
         $partWasherEntry->timeCardId = intval($params["timeCardId"]);
      }
      else if (isset($params["jobNumber"]) &&
               isset($params["wcNumber"]) &&
               isset($params["manufactureDate"]) &&
               isset($params["operator"]))
      {
         //
         // Manual entry
         //

         $jobId = JobInfo::getJobIdByComponents($params->get("jobNumber"), $params->getInt("wcNumber"));
         
         if ($jobId != JobInfo::UNKNOWN_JOB_ID)
         {
            $partWasherEntry->jobId = $jobId;
            $partWasherEntry->manufactureDate = $params["manufactureDate"];
            $partWasherEntry->operator = intval($params["operator"]);
         }
         else
         {
            $result->success = false;
            $result->error = "Failed to lookup job ID.";
         }
      }
      else
      {
         $result->success = false;
         $result->error = "Missing parameters.";
      }
      
      if ($result->success)
      {
         if (isset($params["washer"]) &&
             isset($params["panCount"]) &&
             isset($params["partCount"]))
         {
            $partWasherEntry->employeeNumber = intval($params["washer"]);
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

$router->add("savePartWeightEntry", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   $dbaseResult = null;
   
   $partWeightEntry = null;
   
   if (isset($params["entryId"]) &&
       is_numeric($params["entryId"]) &&
       (intval($params["entryId"]) != PartWasherEntry::UNKNOWN_ENTRY_ID))
   {
      //  Updated entry
      $partWeightEntry = PartWeightEntry::load($params["entryId"]);
      
      if (!$partWeightEntry)
      {
         $result->success = false;
         $result->error = "No existing part weight entry found.";
      }
   }
   else
   {
      // New entry.
      $partWeightEntry = new PartWeightEntry();
      
      // Use current date/time as entry time.
      $partWeightEntry->dateTime = Time::now("Y-m-d h:i:s A");
   }
   
   if ($result->success)
   {
      if (isset($params["timeCardId"]) && is_numeric($params["timeCardId"]))
      {
         //
         // Time card entry
         //
         
         $partWeightEntry->timeCardId = intval($params["timeCardId"]);
      }
      else if (isset($params["jobNumber"]) &&
               isset($params["wcNumber"]) &&
               isset($params["manufactureDate"]) &&
               isset($params["operator"]) &&
               isset($params["panCount"]))
      {
         //
         // Manual entry
         //
         
         $jobId = JobInfo::getJobIdByComponents($params->get("jobNumber"), $params->getInt("wcNumber"));
         
         if ($jobId != JobInfo::UNKNOWN_JOB_ID)
         {
            $partWeightEntry->jobId = $jobId;
            $partWeightEntry->manufactureDate = $params["manufactureDate"];
            $partWeightEntry->operator = intval($params["operator"]);
         }
         else
         {
            $result->success = false;
            $result->error = "Failed to lookup job ID.";
         }
         
         $partWeightEntry->panCount = intval($params["panCount"]);
      }
      else
      {
         $result->success = false;
         $result->error = "Missing parameters.";
      }
      
      if ($result->success)
      {
         if (isset($params["laborer"]) &&
             isset($params["partWeight"]))
         {
            $partWeightEntry->employeeNumber = intval($params["laborer"]);
            $partWeightEntry->weight = floatval($params["partWeight"]);
            
            if ($partWeightEntry->partWeightEntryId == PartWeightEntry::UNKNOWN_ENTRY_ID)
            {
               $dbaseResult = $database->newPartWeightEntry($partWeightEntry);
            }
            else
            {
               $dbaseResult = $database->updatePartWeightEntry($partWeightEntry);
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
   
$router->add("deletePartWeightEntry", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   
   if (isset($params["entryId"]) &&
       is_numeric($params["entryId"]) &&
       (intval($params["entryId"]) != PartWeightEntry::UNKNOWN_ENTRY_ID))
   {
      $entryId = intval($params["entryId"]);
      
      $partWeightEntry = PartWeightEntry::load($entryId);
      
      if ($partWeightEntry)
      {
         $dbaseResult = $database->deletePartWeightEntry($entryId);
         
         if ($dbaseResult)
         {
            $result->success = true;
         }
         else
         {
            $result->success = false;
            $result->error = "Database query failed.";
         }
      }
      else
      {
         $result->success = false;
         $result->error = "No existing part weight entry found.";
      }
   }
   
   echo json_encode($result);
});

$router->add("inspectionTemplate", function($params) {
   $result = new stdClass();
   $result->success = false;
   
   if (is_numeric($params["inspectionType"]) &&
       isset($params["jobNumber"]) &&
       is_numeric($params["wcNumber"]))
   {
      $inspectionType = intval($params["inspectionType"]);
      $jobNumber = $params["jobNumber"];
      $wcNumber = intval($params["wcNumber"]);
      
      $jobId = JobInfo::getJobIdByComponents($jobNumber, $wcNumber);
      
      $inspectionTemplate = InspectionTemplate::getInspectionTemplate($inspectionType, $jobId);
      
      if ($inspectionTemplate)
      {
         $result->templateId = $inspectionTemplate->templateId;
      }
      else
      {
         $result->templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
      }

      $result->success = true;
   }
   else
   {
      $result->success = false;
      $result->error = "Missing parameters.";
   }
   
   echo json_encode($result);
});

$router->add("saveInspection", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   $dbaseResult = null;
   
   $inspection = null;
   
   if (isset($params["inspectionId"]) &&
       is_numeric($params["inspectionId"]) &&
       (intval($params["inspectionId"]) != Inspection::UNKNOWN_INSPECTION_ID))
   {
      //  Updated entry
      $inspection = Inspection::load($params["inspectionId"]);
      
      if (!$inspection)
      {
         $result->success = false;
         $result->error = "No existing inspection found.";
      }
   }
   else
   {
      // New entry.
      $inspection = new Inspection();
      
      // Use current date/time as entry time.
      $inspection->dateTime = Time::now("Y-m-d h:i:s A");
   }
   
   if ($result->success)
   {
      if (isset($params["templateId"]) &&
          isset($params["jobNumber"]) &&
          isset($params["wcNumber"]) &&
          isset($params["inspector"]) &&
          isset($params["operator"]) &&
          isset($params["comments"]))
      {
         $jobId = JobInfo::getJobIdByComponents($params->get("jobNumber"), $params->getInt("wcNumber"));
         
         if ($jobId != JobInfo::UNKNOWN_JOB_ID)
         {
            $inspection->templateId = intval($params["templateId"]);
            $inspection->jobId = $jobId;
            $inspection->inspector = intval($params["inspector"]);
            $inspection->operator = intval($params["operator"]);
            $inspection->comments = $params["comments"];
            
            $inspectionTemplate = InspectionTemplate::load($inspection->templateId);
            
            if ($inspectionTemplate)
            {
               foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
               {
                  $name = "property" . $inspectionProperty->propertyId;
                  $dataName = "propertyData" . $inspectionProperty->propertyId;
                  
                  if (isset($params[$name]))
                  {
                     $inspectionResult = new InspectionResult();
                     $inspectionResult->propertyId = $inspectionProperty->propertyId;
                     $inspectionResult->status = intval($params[$name]);
                     
                     if (isset($params[$dataName]))
                     {
                        $inspectionResult->data = $params[$dataName];
                     }
                     
                     $inspection->inspectionResults[$inspectionResult->propertyId] = $inspectionResult;
                  }
                  else
                  {
                     $result->success = false;
                     $result->error = "Missing property [$name]";
                  }
               }
                     
               if ($result->success)
               {
                  if ($inspection->inspectionId == Inspection::UNKNOWN_INSPECTION_ID)
                  {
                     $dbaseResult = $database->newInspection($inspection);
                  }
                  else
                  {
                     $dbaseResult = $database->updateInspection($inspection);
                  }
                  
                  if (!$dbaseResult)
                  {
                     $result->success = false;
                     $result->error = "Database query failed.";
                  }
               }
            }
            else
            {
               $result->success = false;
               $result->error = "Failed to lookup inspection template.";
            }
         }
         else
         {
            $result->success = false;
            $result->error = "Failed to lookup job ID.";
         }
      }
      else
      {
         $result->success = false;
         $result->error = "Missing parameters.";
      }
   }
   
   echo json_encode($result);
});

$router->add("deleteInspection", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   
   if (isset($params["inspectionId"]) &&
       is_numeric($params["inspectionId"]) &&
       (intval($params["inspectionId"]) != Inspection::UNKNOWN_INSPECTION_ID))
   {
      $inspectionId = intval($params["inspectionId"]);
      
      $inspection = Inspection::load($inspectionId);
      
      if ($inspection)
      {
         $dbaseResult = $database->deleteInspection($inspectionId);
         
         if ($dbaseResult)
         {
            $result->success = true;
         }
         else
         {
            $result->success = false;
            $result->error = "Database query failed.";
         }
      }
      else
      {
         $result->success = false;
         $result->error = "No existing inspection found.";
      }
   }
   
   echo json_encode($result);
});

$router->route();
?>