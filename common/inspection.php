<?php

require_once 'inspectionDefs.php';
require_once 'inspectionTemplate.php';

class InspectionResult
{
   const UNKNOWN_INSPECTION_ID = 0;
   
   const UNKNOWN_PROPERTY_ID = 0;
   
   const COMMENT_SAMPLE_INDEX = 99;
   
   public $inspectionId;
   public $propertyId;
   public $status;
   public $data;
   
   public function __construct()
   {
      $this->inspectionId = InspectionResult::UNKNOWN_INSPECTION_ID;
      $this->propertyId = InspectionResult::UNKNOWN_PROPERTY_ID;
      $this->sampleIndex = 0;
      $this->status = InspectionStatus::UNKNOWN;
      $this->data = null;
   }
   
   public static function load($row)
   {
      $inspectionResult = null;
      
      if ($row)
      {
         $inspectionResult = new InspectionResult();
         
         $inspectionResult->inspectionId = intval($row['inspectionId']);
         $inspectionResult->propertyId = intval($row['propertyId']);
         $inspectionResult->sampleIndex = intval($row['sampleIndex']);
         $inspectionResult->status = intval($row['status']);
         $inspectionResult->data = $row['data'];
      }
      
      return ($inspectionResult);
   }
   
   public function pass()
   {
      return ($this->status == InspectionStatus::PASS);
   }
   
   public function warning()
   {
      return ($this->status == InspectionStatus::WARNING);
   }
   
   public function fail()
   {
      return ($this->status == InspectionStatus::FAIL);
   }
   
   public function nonApplicable()
   {
      return ($this->status == InspectionStatus::NON_APPLICABLE);
   }
   
   public static function getInputName($propertyId, $sampleIndex)
   {
      return ("property" . $propertyId . "_sample" . $sampleIndex);
   }
}

class Inspection
{
   const UNKNOWN_INSPECTION_ID = 0;
   
   public $inspectionId;
   public $dateTime;
   public $templateId;
   public $inspector;
   public $comments;
   
   // Properties for job-based inspections (LINE, QCP, IN_PROCESS).
   public $jobId;
   public $operator;
   
   // Optional properties for GENERIC inspections.
   public $jobNumber;
   public $wcNumber;

   public $inspectionResults;
   
   public function __construct()
   {
      $this->inspectionId = Inspection::UNKNOWN_INSPECTION_ID;
      $this->templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
      $this->inspector = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->comments = "";
      $this->jobId = JobInfo::UNKNOWN_JOB_ID;
      $this->operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
      $this->wcNumber = JobInfo::UNKNOWN_WC_NUMBER;
      $this->inspectionResults = null;  // 2D array, indexed as [propertyId][sampleIndex]
   }
   
   public function initialize($inspectionTemplate)
   {
      if ($inspectionTemplate)
      {
         $this->inspectionResults = array();
         
         foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
         {
            $this->inspectionResults[$inspectionProperty->propertyId] = array($inspectionTemplate->sampleSize);
            
            for ($sampleSize = 0; $sampleSize < $inspectionTemplate->sampleSize; $sampleSize++)
            {
               $this->inspectionResults[$inspectionProperty->propertyId][$sampleSize] = null; 
            }
         }
      }
   }
   
   public static function load($inspectionId)
   {
      $inspection = null;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getInspection($inspectionId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $inspection = new Inspection();
            
            $inspection->inspectionId = intval($row['inspectionId']);
            $inspection->templateId = intval($row['templateId']);
            $inspection->dateTime = Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $inspection->inspector = intval($row['inspector']);
            $inspection->comments = $row['comments'];
            $inspection->jobId = $row['jobId'];
            $inspection->operator = intval($row['operator']);
            $inspection->jobNumber = $row['jobNumber'];
            $inspection->wcNumber = intval($row['wcNumber']);
            
            $result = $database->getInspectionResults($inspectionId);
            
            while ($result && ($row = $result->fetch_assoc()))
            {
               $inspectionResult = InspectionResult::load($row);
               
               if ($inspectionResult)
               {
                  if (!isset($inspection->inspectionResults[$inspectionResult->propertyId]))
                  {
                     $inspection->inspectionResults[$inspectionResult->propertyId] = array();
                  }
                  
                  $inspection->inspectionResults[$inspectionResult->propertyId][$inspectionResult->sampleIndex] = $inspectionResult;
               }
            }
         }
      }
      
      return ($inspection);
   }
   
   public function getCount()
   {
      $count = 0;
      
      if ($this->inspectionResults)
      {
         foreach ($this->inspectionResults as $inspectionRow)
         {
            $count += count($inspectionRow);
         }
      }
      
      return ($count);
   }
   
   public function getCountByStatus($inspectionStatus)
   {
      $count = 0;
      
      if ($this->inspectionResults)
      {
         foreach ($this->inspectionResults as $inspectionRow)
         {
            foreach ($inspectionRow as $inspectionResult)
            {
               if ($inspectionResult->status == $inspectionStatus)
               {
                  $count++;
               }
            }
         }
      }
      
      return ($count);
   }
   
   public function pass()
   {
      return (!$this->fail() && !$this->warning());
   }
   
   public function warning()
   {
      return (!$this->fail() && ($this->getCountByStatus(InspectionStatus::WARNING) > 0));
   }
   
   public function fail()
   {
      return ($this->getCountByStatus(InspectionStatus::FAIL) > 0);
   }
   
   public function getInspectionStatus()
   {
      $inspectionStatus = InspectionStatus::UNKNOWN;
      
      if ($this->fail())
      {
         $inspectionStatus = InspectionStatus::FAIL;
      }
      else if ($this->warning())
      {
         $inspectionStatus = InspectionStatus::WARNING;
      }
      else
      {
         $inspectionStatus = InspectionStatus::PASS;
      }
      
      return ($inspectionStatus);
   }
}

/*
if (isset($_GET["inspectionId"]))
{
   $inspectionId = $_GET["inspectionId"];
   $inspection = Inspection::load($inspectionId);
   $inspectionTemplate = InspectionTemplate::load($inspection->templateId);
 
   if ($inspection && $inspectionTemplate)
   {
      echo "inspectionId: " . $inspection->inspectionId . "<br/>";
      echo "templateId: " .   $inspection->templateId .   "<br/>";
      echo "dateTime: " .     $inspection->dateTime .     "<br/>";
      echo "inspector: " .    $inspection->inspector .    "<br/>";
      echo "jobId: " .        $inspection->jobId .        "<br/>";
      echo "operator: " .     $inspection->operator .     "<br/>";
      echo "jobNumber: " .    $inspection->jobNumber .    "<br/>";
      echo "wcNumber: " .     $inspection->wcNumber .     "<br/>";
      
      echo "inspections: " .  count($inspection->inspectionResults) . "<br/>";
 
      foreach ($inspection->inspectionResults as $inspectionRow)
      {
         foreach ($inspectionRow as $inspectionResult)
         {
            echo "[$inspectionResult->propertyId][$inspectionResult->sampleIndex] : " . InspectionStatus::getLabel($inspectionResult->status) . "<br/>";
         }
      }
      
      echo "comments: " .  $inspection->comments .         "<br/>";
   }
   else
   {
      echo "No inspection found.";
   }
}
*/
?>