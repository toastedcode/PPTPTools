<?php

require_once 'inspectionDefs.php';

class InspectionProperty
{
   const UNKNOWN_PROPERTY_ID = 0;
   
   public $propertyId;
   public $templateId;
   public $propertyName;
   public $dataType;
   public $ordering;
   
   public function __construct()
   {
      $this->propertyId = InspectionProperty::UNKNOWN_PROPERTY_ID;
      $this->templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
      $this->propertyName = "";
      $this->dataType = InspectionDataType::UNKNOWN;
      $this->ordering = 0;
   }
   
   public static function load($row)
   {
      $inspectionProperty = null;
      
      if ($row)
      {
         $inspectionProperty = new InspectionProperty();
         
         $inspectionProperty->propertyId = intval($row['propertyId']);
         $inspectionProperty->templateId = intval($row['templateId']);
         $inspectionProperty->propertyName = $row['propertyName'];
         $inspectionProperty->dataType = intval($row['dataType']);
         $inspectionProperty->ordering = intval($row['ordering']);
      }
      
      return ($inspectionProperty);
   }
}

class InspectionTemplate
{
   const UNKNOWN_TEMPLATE_ID = 0;
   
   public $templateId;
   public $inspectionType;
   public $name;
   public $description;
   public $inspectionProperties;
   
   public function __construct()
   {
      $this->templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
      $this->inspectionType = InspectionType::UNKNOWN;
      $this->name = "";
      $this->description = "";
      $this->inspectionProperties = array();
   }
   
   public static function load($templateId)
   {
      $inspectionTemplate = null;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getInspectionTemplate($templateId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $inspectionTemplate = new InspectionTemplate();
            
            $inspectionTemplate->templateId = intval($row['templateId']);
            $inspectionTemplate->inspectionType = intval($row['inspectionType']);
            $inspectionTemplate->name = $row['name'];
            $inspectionTemplate->description = $row['description'];
            
            $result = $database->getInspectionProperties($templateId);
            
            while ($result && ($row = $result->fetch_assoc()))
            {
               $inspectionTemplate->inspectionProperties[] = InspectionProperty::load($row);
            }
         }
      }
      
      return ($inspectionTemplate);
   }
}

/*
if (isset($_GET["templateId"]))
{
   $templateId = $_GET["templateId"];
   $inspectionTemplate = InspectionTemplate::load($templateId);
   if ($inspectionTemplate)
   {
      echo "templateId: " .     $inspectionTemplate->templateId .                               "<br/>";
      echo "inspectionType: " . InspectionType::getLabel($inspectionTemplate->inspectionType) . "<br/>";
      echo "name: " .           $inspectionTemplate->name .                                     "<br/>";
      echo "description: " .    $inspectionTemplate->description .                              "<br/>";
      
      foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
      {
         echo $inspectionProperty->propertyName . ": " . InspectionDataType::getLabel($inspectionProperty->dataType) . ", " . $inspectionProperty->ordering . "<br/>";
      }
   }
   else
   {
      echo "No inspection template found.";
   }
}
*/
?>