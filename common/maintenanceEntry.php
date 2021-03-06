<?php

require_once 'equipmentInfo.php';
require_once 'jobInfo.php';
require_once 'machinePartInfo.php';
require_once 'maintenanceCategory.php';
require_once 'userInfo.php';

class MaintenanceEntry
{
   const UNKNOWN_ENTRY_ID = 0;
   
   const MINUTES_PER_HOUR = 60;
   
   public $maintenanceEntryId;
   public $dateTime;
   public $maintenanceDateTime;
   public $employeeNumber;
   public $jobNumber;
   public $wcNumber;   
   public $equipmentId;
   public $categoryId;
   public $maintenanceTime;  // minutes
   public $partId;
   public $comments;
   
   public function __construct()
   {
      $this->maintenanceEntryId = MaintenanceEntry::UNKNOWN_ENTRY_ID;
      $this->dateTime = null;
      $this->maintenanceDateTime = null;
      $this->employeeNumber = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      $this->jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
      $this->wcNumber = JobInfo::UNKNOWN_WC_NUMBER;
      $this->equipmentId = EquipmentInfo::UNKNOWN_EQUIPMENT_ID;
      $this->categoryId = MaintenanceCategory::UNKNOWN_CATEGORY_ID;
      $this->maintenanceTime = 0;  // minutes
      $this->partId = MachinePartInfo::UNKNOWN_PART_ID;
      $this->comments = "";
   }
   
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
            $maintenanceEntry->maintenanceDateTime = Time::fromMySqlDate($row['maintenanceDateTime'], "Y-m-d H:i:s");;
            $maintenanceEntry->employeeNumber = intval($row['employeeNumber']);
            $maintenanceEntry->jobNumber = $row['jobNumber'];
            $maintenanceEntry->wcNumber = intval($row['wcNumber']);
            $maintenanceEntry->equipmentId = intval($row['equipmentId']);
            $maintenanceEntry->categoryId = intval($row['categoryId']);
            $maintenanceEntry->maintenanceTime = intval($row['maintenanceTime']);
            $maintenanceEntry->partId = intval($row['partId']);
            $maintenanceEntry->comments = $row['comments'];            
         }
      }
      
      return ($maintenanceEntry);
   }
}

/*
if (isset($_GET["maintenanceEntryId"]))
{
   $maintenanceEntryId = $_GET["maintenanceEntryId"];
   
   $maintenanceEntry = MaintenanceEntry::load($maintenanceEntryId);
 
   if ($maintenanceEntry)
   {
      $maintenanceTime = $maintenanceEntry->formatMaintenanceTime();
      
      echo "entryId: " .             $maintenanceEntry->maintenanceEntryId .  "<br/>";
      echo "dateTime: " .            $maintenanceEntry->dateTime .            "<br/>";
      echo "maintenanceDateTime: " . $maintenanceEntry->maintenanceDateTime . "<br/>";      
      echo "employeeNumber: " .      $maintenanceEntry->employeeNumber .      "<br/>";
      echo "jobNumber: " .           $maintenanceEntry->jobNumber .           "<br/>";
      echo "wcNumber: " .            $maintenanceEntry->wcNumber .            "<br/>";
      echo "equipmentId: " .         $maintenanceEntry->equipmentId .         "<br/>";
      echo "categoryId: " .          $maintenanceEntry->categoryId .          "<br/>";
      echo "maintenanceTime: " .     $maintenanceTime .                       "<br/>";
      echo "comments: " .            $maintenanceEntry->comments .            "<br/>";
   }
   else
   {
        echo "No maintenance entry found.";
   }
}

echo "<select>" . MaintenanceType::getOptions(MaintenanceType::UNKNOWN) . "</select><br/><br/>";
echo "<select>" . MaintenanceCategory::getOptions(MaintenanceCategory::UNKNOWN_CATEGORY_ID, MaintenanceType::UNKNOWN) . "</select><br/><br/>";
echo "<select>" . MachinePartInfo::getOptions(MachinePartInfo::UNKNOWN_PART_ID) . "</select><br/><br/>";
*/

?>