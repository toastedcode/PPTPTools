<?php

require_once '../common/database.php';

class MachinePartInfo
{
   const UNKNOWN_PART_ID = 0;
   
   const UNKNOWN_PART_NUMBER = "";
   
   public $partId;
   public $partNumber;
   public $description;
   public $inventoryCount;
   
   public function __construct()
   {
      $this->partId = MachinePartInfo::UNKNOWN_PART_ID;
      $this->partNumber = MachinePartInfo::UNKNOWN_PART_NUMBER;
      $this->description = "";
      $this->inventoryCount = 0;
   }
   
   public static function load($partId)
   {
      $machinePartInfo = null;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getPartInventoryPart($partId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $machinePartInfo = new MachinePartInfo();
            
            $machinePartInfo->partId = intval($row['partId']);
            $machinePartInfo->partNumber = $row['partNumber'];
            $machinePartInfo->description = $row['description'];
            $machinePartInfo->inventoryCount = intval($row['inventoryCount']);
         }
      }
      
      return ($machinePartInfo);
   }
   
   public static function getOptions($selectedPartId, $allowNull)
   {
      $html = "";
      
      if ($allowNull == true)
      {
         $html = "<option value=\"" . MachinePartInfo::UNKNOWN_PART_ID . "\"></option>";
      }
      else
      {
         $html = "<option style=\"display:none\">";
      }
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getPartInventory();
         
         while ($result && ($row = $result->fetch_assoc()))
         {
            $partId = intval($row["partId"]);
            $partNumber = $row["partNumber"];
            $selected = ($partId == $selectedPartId) ? "selected" : "";
            
            $html .= "<option value=\"$partId\" $selected>$partNumber</option>";
         }
      }
      
      return ($html);
   }
}

/*
if (isset($_GET["partId"]))
{
   $partId = $_GET["partId"];
 
   $machinePartInfo = MachinePartInfo::load($partId);
 
   if ($machinePartInfo)
   {
      echo "partId: " .         $machinePartInfo->partId .         "<br/>";
      echo "partNumber: " .     $machinePartInfo->partNumber .     "<br/>";
      echo "description: " .    $machinePartInfo->description .    "<br/>";
      echo "inventoryCount: " . $machinePartInfo->inventoryCount . "<br/>";
   }
   else
   {
      echo "No machine part found.";
   }
}
*/

?>
 