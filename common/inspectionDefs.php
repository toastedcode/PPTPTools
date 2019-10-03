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
   const NON_APPLICABLE = 3;
   const LAST = 4;
   const COUNT = InspectionStatus::LAST - InspectionStatus::FIRST;
   
   public static function getLabel($inspectionStatus)
   {
      $labels = array("---", "PASS", "FAIL", "N/A");
      
      return ($labels[$inspectionStatus]);
   }
   
   public static function getClass($inspectionStatus)
   {
      $classes = array("", "pass", "fail", "n/a");
      
      return ($classes[$inspectionStatus]);
   }
}

abstract class InspectionType
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const OASIS = InspectionType::FIRST;
   const LINE = 2;
   const QCP = 3;
   const IN_PROCESS = 4;
   const LAST = 5;
   const COUNT = InspectionType::LAST - InspectionType::FIRST;
   
   public static function getLabel($inspectionType)
   {
      $labels = array("---", "Oasis Inspection", "Line Inspection", "QCP Inspection", "In Process");
      
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
?>