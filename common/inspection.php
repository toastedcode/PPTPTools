<?php

require_once 'inspectionDefs.php';
require_once 'inspectionTemplate.php';

class InspectionResult
{
   const UNKNOWN_INSPECTION_ID = 0;
   
   const UNKNOWN_PROPERTY_ID = 0;
   
   public $inspectionId;
   public $propertyId;
   public $status;
   public $data;
   
   public function __construct()
   {
      $this->inspectionId = InspectionResult::UNKNOWN_INSPECTION_ID;
      $this->propertyId = InspectionResult::UNKNOWN_PROPERTY_ID;
      $this->status = InspectionStatus::UNKNOWN;
      $this->data = null;
   }
   
   public static function load($row)
   {
      $inspectionResult = null;
      
      if ($row)
      {
         $inspectionResult = new InspectionResult();
         
         $inspectionResult->inspectionId = $row['inspectionId'];
         $inspectionResult->propertyId = $row['propertyId'];
         $inspectionResult->status = intval($row['status']);
         $inspectionResult->data = $row['data'];
      }
      
      return ($inspectionResult);
   }
   
   public function pass()
   {
      return ($this->status == InspectionStatus::PASS);
   }
   
   public function fail()
   {
      return ($this->status == InspectionStatus::FAIL);
   }
   
   public function nonApplicable()
   {
      return ($this->status == InspectionStatus::NON_APPLICABLE);
   }
}

class Inspection
{
   const UNKNOWN_INSPECTION_ID = 0;
   
   public $inspectionId;
   public $templateId;
   public $dateTime;
   public $inspector;
   public $operator;
   public $jobId;
   public $inspectionResults;
   public $comments;
   
   public function __construct()
   {
      $this->inspectionId = Inspection::UNKNOWN_INSPECTION_ID;
      
      $this->templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
      $this->inspector = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->jobId = JobInfo::UNKNOWN_JOB_ID;
      $this->inspectionResults = array();
      $this->comments = "";
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
            $inspection->operator = intval($row['operator']);
            $inspection->jobId = $row['jobId'];
            $inspection->comments = $row['comments'];
            
            $result = $database->getInspectionResults($inspectionId);
            
            while ($result && ($row = $result->fetch_assoc()))
            {
               $inspectionResult = InspectionResult::load($row);
               $inspection->inspectionResults[$inspectionResult->propertyId] = $inspectionResult;
            }
         }
      }
      
      return ($inspection);
   }
   
   public function getCount()
   {
      return (count($this->inspectionResults));
   }
   
   public function getCountByStatus($inspectionStatus)
   {
      $count = 0;
      
      foreach ($this->inspectionResults as $inspectionResult)
      {
         if ($inspectionResult->status == $inspectionStatus)
         {
            $count++;
         }
      }
      
      return ($count);
   }
   
   public function pass()
   {
      return ($this->getCountByStatus(InspectionStatus::FAIL) == 0);
   }
   
   public function fail()
   {
      return (!$this->pass());
   }
}

/*
if (isset($_GET["inspectionId"]))
{
   $inspectionId = $_GET["inspectionId"];
   $inspection = Inspection::load($inspectionId);
 
   if ($inspection)
   {
      echo "inspectionId: " . $inspection->inspectionId . "<br/>";
      echo "templateId: " .   $inspection->templateId .   "<br/>";
      echo "dateTime: " .     $inspection->dateTime .     "<br/>";
      echo "inspector: " .    $inspection->inspector .    "<br/>";
      echo "operator: " .     $inspection->operator .     "<br/>";
      echo "jobId: " .        $inspection->jobId .        "<br/>";
      
      foreach ($inspection->inspectionResults as $inspectionResult)
      {
         echo $inspectionResult->propertyId . " : " . InspectionStatus::getLabel($inspectionResult->status) . "<br/>";
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