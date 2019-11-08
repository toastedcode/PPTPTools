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
   const WARNING = 2;
   const FAIL = 3;
   const NON_APPLICABLE = 4;
   const LAST = 5;
   const COUNT = InspectionStatus::LAST - InspectionStatus::FIRST;
   
   public static function getLabel($inspectionStatus)
   {
      $labels = array("---", "PASS", "WARNING", "FAIL", "N/A");
      
      return ($labels[$inspectionStatus]);
   }
   
   public static function getClass($inspectionStatus)
   {
      $classes = array("", "pass", "warning", "fail", "n/a");
      
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
   const GENERIC = 5;
   const LAST = 6;
   const COUNT = InspectionType::LAST - InspectionType::FIRST;
   
   public static function getLabel($inspectionType)
   {
      $labels = array("---", "Oasis Inspection", "Line Inspection", "QCP Inspection", "In Process", "Generic");
      
      return ($labels[$inspectionType]);
   }
}

abstract class InspectionDataType
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const INTEGER = InspectionDataType::FIRST;
   const DECIMAL = 2;
   const STRING = 3;
   const LAST = 4;
   const COUNT = InspectionDataType::LAST - InspectionDataType::FIRST;
   
   public static function getLabel($dataType)
   {
      $labels = array("---", "Integer", "Decimal", "String");
      
      return ($labels[$dataType]);
   }
}

abstract class InspectionDataUnits
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const INCHES = InspectionDataUnits::FIRST;
   const MILLIMETERS = 2;
   const DEGREES = 3;
   const LAST = 4;
   const COUNT = InspectionDataUnits::LAST - InspectionDataUnits::FIRST;
   
   public static function getLabel($dataType)
   {
      $labels = array("---", "Inches", "Millimeters", "Degrees");
      
      return ($labels[$dataType]);
   }
   
   public static function getAbbreviatedLabel($dataType)
   {
      $labels = array("---", "\"\"", "mm", "&#176;");
      
      return ($labels[$dataType]);
   }
}
?>