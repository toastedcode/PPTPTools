<?php

require_once 'inspectionDefs.php';
require_once 'inspectionTemplate.php';

class InspectionResult
{
   public $propertyName;
   public $dataType;
   public $value;
   
   public function __construct()
   {
      $this->propertyName = "";
      $this->dataType = InspectionDataType::UNKNOWN;
      $value = "";
   }
   
   public static function load($row)
   {
      $inspectionResult = null;
      
      if ($row)
      {
         $inspectionResult = new InspectionResult();
         
         $inspectionResult->propertyName = $row['propertyName'];
         $inspectionResult->dataType = intval($row['dataType']);
         $inspectionResult->value = $row['value'];
      }
      
      return ($inspectionResult);
   }
   
   public function getStatus()
   {
      $inspectionStatus = InspectionStatus::NON_APPLICABLE;
      
      switch ($this->dataType)
      {
         case InspectionDataType::PASS_FAIL:
         {
            $inspectionStatus = intval($this->value);
            break;
         }
            
         default:
         {
            break;
         }
      }
      
      return ($inspectionStatus);
   }
   
   public function pass()
   {
      return ($this->getStatus() == InspectionStatus::PASS);
   }
   
   public function fail()
   {
      return ($this->getStatus() == InspectionStatus::FAIL);
   }
   
   public function nonApplicable()
   {
      return ($this->getStatus() == InspectionStatus::NON_APPLICABLE);
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
      
      $this->inspector = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
      $this->inspector = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->jobId = JobInfo::UNKNOWN_JOB_ID;
      $this->inspectionResults = array();
      $this->comments = "";
   }
   
   public static function load($inspectionId)
   {
      $inspectionInfo = null;
      
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
               $inspection->inspectionResults[] = InspectionResult::load($row);
            }
         }
      }
      
      return ($inspection);
   }
   
   public function getCount()
   {
      return (count($this->inspectionResults));
   }
   
   public function getPassCount()
   {
      $count = 0;
      
      foreach ($this->inspectionResults as $inspectionResult)
      {
         if ($inspectionResult->pass())
         {
            $count++;
         }
      }
      
      return ($count);
   }
   
   public function getFailCount()
   {
      return ($this->getCount() - $this->getPassCount());
   }
   
   public function pass()
   {
      return ($this->getFailCount() == 0);
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
         $value = "";
         switch ($inspectionResult->dataType)
         {
            case InspectionDataType::PASS_FAIL:
            {
               $inspectionStatus = intval($inspectionResult->value);
               $value = InspectionStatus::getLabel($inspectionStatus);
               break;
            }
 
            default:
            {
               $value = $inspectionResult->value;
               break;
            }
         }
 
         echo $inspectionResult->propertyName . " : " . $value . "<br/>";
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