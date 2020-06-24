<?php

require_once 'jobInfo.php';
require_once 'userInfo.php';

abstract class MaintenanceCategory
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const CATEGORY_1 = MaintenanceCategory::FIRST;
   const CATEGORY_2 = 2;
   const CATEGORY_3 = 3;
   const LAST = 4;
   const COUNT = MaintenanceCategory::LAST - MaintenanceCategory::FIRST;
   
   public static function getLabel($maintenanceCategory)
   {
      $labels = array("---", "CATEGORY_1", "CATEGORY_2", "CATEGORY_3");
      
      return ($labels[$maintenanceCategory]);
   }
}

class MaintenanceEntry
{
   const UNKNOWN_ENTRY_ID = 0;
   
   const MINUTES_PER_HOUR = 60;
   
   public $maintenanceEntryId = MaintenanceEntry::UNKNOWN_ENTRY_ID;
   public $dateTime = null;
   public $employeeNumber = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   public $category = MaintenanceCategory::UNKNOWN;
   public $wcNumber = JobInfo::UNKNOWN_WC_NUMBER;
   public $operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   public $maintenanceTime = 0;  // minutes
   public $comments = "";
   public $approvedBy = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   public $approvedDateTime = null;
   
   public function formatMaintenanceTime()
   {
      return($this->getMaintenanceTimeHours() . ":" . sprintf("%02d", $this->getMaintenanceTimeMinutes()));
   }
   
   public function getMaintenanceTimeHours()
   {
      return ((int)($this->maintenanceTime / 60));
   }
   
   public function getMaintenanceTimeMinutes()
   {
      return ($this->maintenanceTime % 60);
   }
   
   public static function load($maintenanceEntryId)
   {
      $maintenanceEntry = null;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && ($database->isConnected()))
      {
         $result = $database->getMaintenanceEntry($maintenanceEntryId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $maintenanceEntry = new MaintenanceEntry();
            
            $maintenanceEntry->maintenanceEntryId = intval($row['maintenanceEntryId']);
            $maintenanceEntry->dateTime= Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $maintenanceEntry->employeeNumber = intval($row['employeeNumber']);
            $maintenanceEntry->category = intval($row['category']);
            $maintenanceEntry->wcNumber = intval($row['wcNumber']);
            $maintenanceEntry->operator = intval($row['operator']);
            $maintenanceEntry->maintenanceTime = intval($row['maintenanceTime']);
            $maintenanceEntry->comments = $row['comments'];
            $maintenanceEntry->approvedBy = intval($row['approvedBy']);
            $maintenanceEntry->approvedDateTime = Time::fromMySqlDate($row['approvedDateTime'], "Y-m-d H:i:s");
         }
      }
      
      return ($maintenanceEntry);
   }
   
   public function isApproved()
   {
      return ($this->approvedBy != UserInfo::UNKNOWN_EMPLOYEE_NUMBER);
   }
}


if (isset($_GET["maintenanceEntryId"]))
{
   $maintenanceEntryId = $_GET["maintenanceEntryId"];
   
   $maintenanceEntry = MaintenanceEntry::load($maintenanceEntryId);
 
   if ($maintenanceEntry)
   {
      $maintenanceTime = $maintenanceEntry->formatMaintenanceTime();
      $category = MaintenanceCategory::getLabel($maintenanceEntry->category);
      
      echo "entryId: " .          $maintenanceEntry->maintenanceEntryId .    "<br/>";
      echo "dateTime: " .         $maintenanceEntry->dateTime .              "<br/>";
      echo "employeeNumber: " .   $maintenanceEntry->employeeNumber .        "<br/>";
      echo "category: " .         $category .                                "<br/>";
      echo "wcNumber: " .         $maintenanceEntry->wcNumber .              "<br/>";
      echo "operator: " .         $maintenanceEntry->operator .              "<br/>";
      echo "maintenanceTime: " .  $maintenanceTime .                         "<br/>";
      echo "comments: " .         $maintenanceEntry->comments .              "<br/>";
      echo "approvedBy: " .       $maintenanceEntry->approvedBy .            "<br/>";
      echo "approvedDateTime: " . $maintenanceEntry->approvedDateTime .      "<br/>";
   }
   else
   {
        echo "No maintenance entry found.";
   }
}

?>