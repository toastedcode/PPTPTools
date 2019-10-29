<?php

require_once 'inspectionDefs.php';

class InspectionProperty
{
   const UNKNOWN_PROPERTY_ID = 0;
   
   public $propertyId;
   public $templateId;
   public $name;
   public $specification;
   public $dataType;
   public $ordering;
   
   public function __construct()
   {
      $this->propertyId = InspectionProperty::UNKNOWN_PROPERTY_ID;
      $this->templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
      $this->name = "";
      $this->specification = "";
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
         $inspectionProperty->name = $row['name'];
         $inspectionProperty->specification = $row['specification'];
         $inspectionProperty->dataType = intval($row['dataType']);
         $inspectionProperty->ordering = intval($row['ordering']);
      }
      
      return ($inspectionProperty);
   }
}

class InspectionTemplate
{
   const UNKNOWN_TEMPLATE_ID = 0;
   
   const OASIS_INSPECTION_TEMPLATE_ID = 1;
   const LINE_INSPECTION_TEMPLATE_ID = 2;
   
   public $templateId;
   public $inspectionType;
   public $name;
   public $description;
   public $sampleSize;
   public $inspectionProperties;
   
   public function __construct()
   {
      $this->templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
      $this->inspectionType = InspectionType::UNKNOWN;
      $this->name = "";
      $this->description = "";
      $this->sampleSize = 1;
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
            $inspectionTemplate->sampleSize = intval($row['sampleSize']);
            
            $result = $database->getInspectionProperties($templateId);
            
            while ($result && ($row = $result->fetch_assoc()))
            {
               $inspectionTemplate->inspectionProperties[] = InspectionProperty::load($row);
            }
         }
      }
      
      return ($inspectionTemplate);
   }
   
   public static function getInspectionTemplate($inspectionType, $jobId)
   {
      $inspectionTemplate = null;
      
      $templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;

      switch ($inspectionType)
      {
         case InspectionType::OASIS:
         {
            $templateId = InspectionTemplate::OASIS_INSPECTION_TEMPLATE_ID;
            break;
         }
            
         case InspectionType::LINE:
         {
            $templateId = InspectionTemplate::LINE_INSPECTION_TEMPLATE_ID;
            break;
         }
            
         case InspectionType::QCP:
         case InspectionType::IN_PROCESS:
         {
           $jobInfo = JobInfo::load($jobId);
           if ($jobInfo)
           {
              if ($inspectionType == InspectionType::QCP)
              {
                 $templateId = $jobInfo->qcpInspectionTemplateId;
              }
              else if ($inspectionType == InspectionType::IN_PROCESS)
              {
                 $templateId = $jobInfo->inlineInspectionTemplateId;
              }
           }
           break;
         }
            
         default:
         {
            break;
         }
      }
      
      if ($templateId != InspectionTemplate::UNKNOWN_TEMPLATE_ID)
      {
         $inspectionTemplate = InspectionTemplate::load($templateId);
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
      echo "sampleSize: " .     $inspectionTemplate->sampleSize .                               "<br/>";
      
      
      foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
      {
         echo $inspectionProperty->name . ": " . InspectionDataType::getLabel($inspectionProperty->dataType) . ", " . $inspectionProperty->ordering . "<br/>";
      }
   }
   else
   {
      echo "No inspection template found.";
   }
}
*/
?>