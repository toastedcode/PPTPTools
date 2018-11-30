<?php
require_once 'database.php';
require_once 'time.php';

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

class LineInspectionInfo
{
   const INVALID_ENTRY_ID = 0;
   
   const NUM_INSPECTIONS = 4;
   
   public $entryId;
   public $dateTime;
   public $inspector;
   public $operator;
   public $jobNumber;
   public $wcNumber;
   public $inspections;
   public $comments;
   
   public function __construct()
   {
      $entryId = LineInspectionInfo::INVALID_ENTRY_ID;
      
      $this->inspector = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
      $this->wcNumber = 0;
      $this->inspections = array(InspectionStatus::UNKNOWN, InspectionStatus::UNKNOWN, InspectionStatus::UNKNOWN, InspectionStatus::UNKNOWN);
      $this->visualInspection = false;
      $this->comments = "";
   }
   
   public static function getInspectionName($inspectionIndex)
   {
      return ("inspection" . ($inspectionIndex + 1));
   }
   
   public static function load($entryId)
   {
      $lineInspectionInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getLineInspection($entryId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $lineInspectionInfo = new LineInspectionInfo();
            
            $lineInspectionInfo->entryId = intval($row['entryId']);
            $lineInspectionInfo->dateTime = Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $lineInspectionInfo->inspector = intval($row['inspector']);
            $lineInspectionInfo->operator = intval($row['operator']);
            $lineInspectionInfo->jobNumber = $row['jobNumber'];
            $lineInspectionInfo->wcNumber = intval($row['wcNumber']);
            for ($i = 0; $i < LineInspectionInfo::NUM_INSPECTIONS; $i++)
            {
               $name = LineInspectionInfo::getInspectionName($i);
               $lineInspectionInfo->inspections[$i] = intval($row[$name]);
            }
            $lineInspectionInfo->comments = $row['comments'];
         }
      }
      
      return ($lineInspectionInfo);
   }
}

/*
if (isset($_GET["entryId"]))
{
   $entryId = $_GET["entryId"];
   $lineInspectionInfo = LineInspectionInfo::load($entryId);
 
   if ($lineInspectionInfo)
   {
      echo "entryId: " .   $lineInspectionInfo->entryId.           "<br/>";
      echo "dateTime: " .  $lineInspectionInfo->dateTime .         "<br/>";
      echo "inspector: " . $lineInspectionInfo->inspector.         "<br/>";
      echo "operator: " .  $lineInspectionInfo->operator.          "<br/>";
      echo "jobNumber: " . $lineInspectionInfo->jobNumber.         "<br/>";
      for ($i = 0; $i < LineInspectionInfo::NUM_INSPECTIONS; $i++)
      {
         $name = "inspection" . ($i + 1);
         echo $name . "[" . $i . "]: " . $lineInspectionInfo->threadInspections[$i] . "<br/>";
      }
      echo "comments: " .  $lineInspectionInfo->comments .         "<br/>";
   }
   else
   {
        echo "No line inspection found.";
   }
}
*/
?>