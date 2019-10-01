<?php
require_once 'database.php';
require_once 'jobInfo.php';
require_once 'time.php';
require_once 'userInfo.php';

abstract class InspectionStatus
{
   const FIRST = 0;
   const UNKNOWN = InspectionStatus::FIRST;
   const PASS = 1;
   const FAIL = 2;
   const LAST = 3;
   const COUNT = InspectionStatus::LAST - InspectionStatus::FIRST;
   
   public static function getLabel($inspectionStatus)
   {
      $labels = array("---", "PASS", "FAIL");
      
      return ($labels[$inspectionStatus]);
   }
   
   public static function getClass($inspectionStatus)
   {
      $classes = array("", "pass", "fail");
      
      return ($classes[$inspectionStatus]);
   }
}

abstract class InspectionType
{
   const FIRST = 0;
   const UNKNOWN = InspectionStatus::FIRST;
   const OASIS = 1;
   const LINE = 2;
   const QCP = 3;
   const COUNT = InspectionType::LAST - InspectionType::FIRST;
   
   public static function getLabel($inspectionType)
   {
      $labels = array("---", "Oasis Inspection", "Line Inspection", "QCP Inspection");
      
      return ($labels[$inspectionType]);
   }
}

abstract class InspectionDataType
{
   const FIRST = 0;
   const UNKNOWN = InspectionDataType::FIRST;
   const PASS_FAIL = 1;
   const INTEGER = 2;
   const DECIMAL = 3;
   const STRING = 4;
   const BOOL = 4;
   const COUNT = InspectionDataType::LAST - InspectionDataType::FIRST;
   
   public static function getLabel($dataType)
   {
      $labels = array("---", "Pass/Fail", "Integer", "Decimal", "String");
      
      return ($labels[$dataType]);
   }
}

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
      $inspectionInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
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
}

class InspectionInfo
{
   const UNKNOWN_INSPECTION_ID = 0;
   
   const UNKNOWN_TEMPLATE_ID = 0;
   
   public $inspectionId;
   public $inspectionType;
   public $templateId;
   public $dateTime;
   public $inspector;
   public $operator;
   public $jobId;
   public $inspectionResults;
   public $comments;
   
   public function __construct()
   {
      $this->inspectionId = InspectionInfo::UNKNOWN_INSPECTION_ID;
      
      $this->inspector = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->inspectionType = InspectionType::UNKNOWN;
      $this->templateId = InspectionInfo::UNKNOWN_TEMPLATE_ID;
      $this->inspector = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->jobId = JobInfo::UNKNOWN_JOB_ID;
      $this->inspectionResults = array();
      $this->comments = "";
   }
   
   public static function load($inspectionId)
   {
      $inspectionInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getInspection($inspectionId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $inspectionInfo = new InspectionInfo();
            
            $inspectionInfo->inspectionId = intval($row['inspectionId']);
            $inspectionInfo->templateId = intval($row['templateId']);
            $inspectionInfo->dateTime = Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $inspectionInfo->inspector = intval($row['inspector']);
            $inspectionInfo->operator = intval($row['operator']);
            $inspectionInfo->jobId = $row['jobId'];
            $inspectionInfo->comments = $row['comments'];
            
            $result = $database->getInspectionResults($inspectionId);
            
            while ($result && ($row = $result->fetch_assoc()))
            {
               $inspectionInfo->inspectionResults[] = InspectionResult::load($row);
            }
         }
      }
      
      return ($inspectionInfo);
   }
}

/*
if (isset($_GET["inspectionId"]))
{
   $inspectionId = $_GET["inspectionId"];
   $inspectionInfo = InspectionInfo::load($inspectionId);
 
   if ($inspectionInfo)
   {
      echo "inspectionId: " .   $inspectionInfo->inspectionId .           "<br/>";
      echo "templateId: " .   $inspectionInfo->templateId .           "<br/>";
      echo "dateTime: " .  $inspectionInfo->dateTime .         "<br/>";
      echo "inspector: " . $inspectionInfo->inspector .         "<br/>";
      echo "operator: " .  $inspectionInfo->operator .          "<br/>";
      echo "jobId: " . $inspectionInfo->jobId .         "<br/>";
      foreach ($inspectionInfo->inspectionResults as $inspectionResult)
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
            }
         }
         
         echo $inspectionResult->propertyName . " : " . $value . "<br/>";
      }
      echo "comments: " .  $inspectionInfo->comments .         "<br/>";
   }
   else
   {
        echo "No line inspection found.";
   }
}
*/
?>