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

$router->add("grossPartsPerHour", function($params) {
   $result = new stdClass();
   
   if (isset($params["jobNumber"]) &&
       isset($params["wcNumber"]))
   {
      $jobInfo = null;
      
      $jobId = JobInfo::getJobIdByComponents($params->get("jobNumber"), $params->getInt("wcNumber"));
      
      if ($jobId != JobInfo::UNKNOWN_JOB_ID)
      {
         $jobInfo = JobInfo::load($jobId);
      }
      
      if ($jobInfo)
      {
         $result->success = true;
         $result->grossPartsPerHour = $jobInfo->getGrossPartsPerHour();
      }
      else
      {
         $result->success = false;
         $result->error = "Failed to lookup job ID.";
         $result->jobNumber = $params->get("jobNumber");
         $result->wcNumber = $params->getInt("wcNumber");
      }
   }
   else
   {
      $result->success = false;
      $result->error = "Missing parameters.";
   }
   
   echo json_encode($result);
});

$router->add("saveTimeCard", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   $dbaseResult = null;
   
   $timeCardInfo = null;
   
   if (isset($params["timeCardId"]) &&
       is_numeric($params["timeCardId"]) &&
       (intval($params["timeCardId"]) != TimeCardInfo::UNKNOWN_TIME_CARD_ID))
   {
      //  Updated entry
      $timeCardInfo = TimeCardInfo::load(intval($params["timeCardId"]));
      
      if (!$timeCardInfo)
      {
         $result->success = false;
         $result->error = "No existing part weight entry found.";
      }
   }
   else
   {
      // New time card.
      $timeCardInfo = new TimeCardInfo();
      
      // Use current date/time as time card time.
      $timeCardInfo->dateTime = Time::now("Y-m-d h:i:s A");
   }
   
   if ($result->success)
   {
      if (isset($params["operator"]) &&
          isset($params["jobNumber"]) &&
          isset($params["wcNumber"]) &&
          isset($params["materialNumber"]) &&
          isset($params["setupTime"]) &&
          isset($params["approvedBy"]) &&
          isset($params["runTime"]) &&
          isset($params["panCount"]) &&
          isset($params["partCount"]) &&
          isset($params["scrapCount"]) &&
          isset($params["comments"]))
      {
         $jobId = JobInfo::getJobIdByComponents($params->get("jobNumber"), $params->getInt("wcNumber"));
         
         if ($jobId != JobInfo::UNKNOWN_JOB_ID)
         {
            $timeCardInfo->employeeNumber = intval($params["operator"]);
            $timeCardInfo->jobId = $jobId;
            $timeCardInfo->materialNumber = intval($params["materialNumber"]);
            $timeCardInfo->setupTime = intval($params["setupTime"]);
            $timeCardInfo->approvedBy = intval($params["approvedBy"]);
            $timeCardInfo->runTime = intval($params["runTime"]);
            $timeCardInfo->panCount = intval($params["panCount"]);
            $timeCardInfo->partCount = intval($params["partCount"]);
            $timeCardInfo->scrapCount = intval($params["scrapCount"]);
            $timeCardInfo->comments = $params["comments"];
            
            $commentCodes = CommentCode::getCommentCodes();
            
            foreach ($commentCodes as $commentCode)
            {
               $code = $commentCode->code;
               $name = "code-" . $code;
               
               if (isset($params[$name]))
               {
                  $timeCardInfo->setCommentCode($code);
               }
               else
               {
                  $timeCardInfo->clearCommentCode($code);
               }
            }
            
            if ($timeCardInfo->timeCardId == TimeCardInfo::UNKNOWN_TIME_CARD_ID)
            {
               $dbaseResult = $database->newTimeCard($timeCardInfo);
            }
            else
            {
               $dbaseResult = $database->updateTimeCard($timeCardInfo);
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

$router->add("deleteTimeCard", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   
   if (isset($params["timeCardId"]) &&
       is_numeric($params["timeCardId"]) &&
       (intval($params["timeCardId"]) != TimeCardInfo::UNKNOWN_TIME_CARD_ID))
   {
      $timeCardId = intval($params["timeCardId"]);
      
      $timeCardInfo = TimeCardInfo::load($timeCardId);
      
      if ($timeCardInfo)
      {
         $dbaseResult = $database->deleteTimeCard($timeCardId);
         
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
         $result->error = "No existing time card found.";
      }
   }
   else
   {
      $result->success = false;
      $result->error = "Missing parameters.";
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

$router->add("inspectionTemplates", function($params) {
   $result = new stdClass();
   $result->success = false;
   $result->templates = array();
   
   if (is_numeric($params["inspectionType"]))
   {
      $inspectionType = intval($params["inspectionType"]);

      $jobId = JobInfo::UNKNOWN_JOB_ID;
      
      if (isset($params["jobNumber"]) &&
          is_numeric($params["wcNumber"]))
      {
         $jobNumber = $params["jobNumber"];
         $wcNumber = intval($params["wcNumber"]);
         $jobId = JobInfo::getJobIdByComponents($jobNumber, $wcNumber);
      }
      
      $templateIds = InspectionTemplate::getInspectionTemplatesForJob($inspectionType, $jobId);
      
      foreach ($templateIds as $templateId)
      {
         $inspectionTemplate = InspectionTemplate::load($templateId);
         
         if ($inspectionTemplate)
         {
            $result->templates[] = $inspectionTemplate;
         }
      }

      $result->success = true;
   }
   else
   {
      $result->success = false;
      $result->error = "No inspection type specified.";
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
               $inspection->initialize($inspectionTemplate);
               
               foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
               {
                  for ($sampleIndex = 0; $sampleIndex < $inspectionTemplate->sampleSize; $sampleIndex++)
                  {
                     $name = InspectionResult::getInputName($inspectionProperty->propertyId, $sampleIndex);
                     $dataName = $name . "_data";
                     
                     if (isset($params[$name]))
                     {
                        $inspectionResult = new InspectionResult();
                        $inspectionResult->propertyId = $inspectionProperty->propertyId;
                        $inspectionResult->sampleIndex = $sampleIndex;
                        $inspectionResult->status = intval($params[$name]);
                        $inspectionResult->data = $params[$dataName];
                        
                        $inspection->inspectionResults[$inspectionResult->propertyId][$sampleIndex] = $inspectionResult;
                     }
                     else
                     {
                        $result->success = false;
                        $result->error = "Missing property [$name]";
                        break;
                     }
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

$router->add("saveInspectionTemplate", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   $dbaseResult = null;
   
   $inspectionTemplate = null;
   
   if (isset($params["templateId"]) &&
       is_numeric($params["templateId"]) &&
       (intval($params["templateId"]) != InspectionTemplate::UNKNOWN_TEMPLATE_ID))
   {
      //  Updated entry
      $inspectionTemplate = InspectionTemplate::load($params["templateId"]);
      
      if (!$inspectionTemplate)
      {
         $result->success = false;
         $result->error = "No existing template found.";
      }
   }
   else
   {
      // New entry.
      $inspectionTemplate = new InspectionTemplate();
   }
   
   if ($result->success)
   {
      if (isset($params["templateName"]) &&
          isset($params["templateDescription"]) &&
          isset($params["inspectionType"]) &&
          isset($params["sampleSize"]))
      {
         $inspectionTemplate->name = $params["templateName"];
         $inspectionTemplate->description = $params["templateDescription"];
         $inspectionTemplate->inspectionType = intval($params["inspectionType"]);
         $inspectionTemplate->sampleSize = intval($params["sampleSize"]);
         
         // Clear properties list.
         $inspectionTemplate->inspectionProperties = array();
         
         $propertyIndex = 0;
         $name = "property" . $propertyIndex;
         
         while (isset($params[$name . "_name"]))
         {
            if (isset($params[$name . "_specification"]) &&
                isset($params[$name . "_dataType"]) &&
                isset($params[$name . "_dataUnits"]))
            {
               $inspectionProperty = new InspectionProperty();
               
               $inspectionProperty->templateId = $inspectionTemplate->templateId;
               $inspectionProperty->name = $params[$name . "_name"];
               $inspectionProperty->specification = $params[$name . "_specification"];
               $inspectionProperty->dataType = intval($params[$name . "_dataType"]);
               $inspectionProperty->dataUnits = intval($params[$name . "_dataUnits"]);
               $inspectionProperty->ordering = $propertyIndex;
               
               $inspectionTemplate->inspectionProperties[] = $inspectionProperty;
            }
            else
            {
               $result->success = false;
               $result->error = "Missing parameters for property[$propertyIndex].";
               break;
            }

            $propertyIndex++;
            $name = "property" . $propertyIndex;
         }
               
         if ($result->success)
         {
            if ($inspectionTemplate->templateId == InspectionTemplate::UNKNOWN_TEMPLATE_ID)
            {
               $dbaseResult = $database->newInspectionTemplate($inspectionTemplate);
            }
            else
            {
               $dbaseResult = $database->updateInspectionTemplate($inspectionTemplate);
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
         $result->error = "Missing parameters.";
      }
   }
   
   echo json_encode($result);
});
   
$router->add("deleteInspectionTemplate", function($params) {
   $result = new stdClass();
   $result->success = true;
   
   $database = PPTPDatabase::getInstance();
   
   if (isset($params["templateId"]) &&
       is_numeric($params["templateId"]) &&
       (intval($params["templateId"]) != InspectionTemplate::UNKNOWN_TEMPLATE_ID))
   {
      $templateId = intval($params["templateId"]);
      
      $inspectionTemplate = InspectionTemplate::load($templateId);
      
      if ($inspectionTemplate)
      {
         $dbaseResult = $database->deleteInspectionTemplate($templateId);
         
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
         $result->error = "No existing template found.";
      }
   }
   
   echo json_encode($result);
});

$router->route();
?>