<?php

require_once 'database.php';

class EquipmentInfo
{
   const UNKNOWN_EQUIPMENT_ID = 0;
   
   public $equipmentId;
   public $name;
   
   public function __construct()
   {
      $this->equipmentId = EquipmentInfo::UNKNOWN_EQUIPMENT_ID;
      $this->name = "";
   }
   
   public static function load($equipmentId)
   {
      $equipmentInfo = null;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && ($database->isConnected()))
      {
         $result = $database->getEquipment($equipmentId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $equipmentInfo = new EquipmentInfo();
            
            $equipmentInfo->equipmentId = intval($row['equipmentId']);
            $equipmentInfo->name = $row['name'];
         }
      }
      
      return ($equipmentInfo);
   }
   
   public static function getEquipmentOptions($selectedEquipmentId)
   {
      $html = "<option style=\"display:none\">";
      
      $equipments = PPTPDatabase::getInstance()->getEquipments();
      
      foreach ($equipments as $equipment)
      {
         $equipmentId = intval($equipment['equipmentId']);
         $name = $equipment['name'];
         $selected = ($equipmentId == $selectedEquipmentId) ? "selected" : "";
         
         $html .= "<option value=\"$equipmentId\" $selected>$name</option>";
      }
      
      return ($html);
   }
}

/*
if (isset($_GET["equipmentId"]))
{
   $equipmentId = $_GET["equipmentId"];
   
   $equipmentInfo = EquipmentInfo::load($equipmentId);
 
   if ($equipmentInfo)
   {
      echo "equipmentId: " . $equipmentInfo->equipmentId .  "<br/>";
      echo "name: " .        $equipmentInfo->name .         "<br/>";
   }
   else
   {
        echo "No equipment found.";
   }
}
*/

?>