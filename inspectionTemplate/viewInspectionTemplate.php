<?php

require_once '../common/authentication.php';
require_once '../common/header.php';
require_once '../common/inspection.php';
require_once '../common/inspectionTemplate.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/params.php';
require_once '../common/root.php';
require_once '../common/userInfo.php';

const ONLY_ACTIVE = true;

abstract class InspectionTemplateInputField
{
   const FIRST = 0;
   const NAME = InspectionTemplateInputField::FIRST;
   const DESCRIPTION = 1;
   const INSPECTION_TYPE = 2;
   const SAMPLE_SIZE = 3;
   const PROPERTIES = 4;
   const LAST = 5;
   const COUNT = InspectionTemplateInputField::LAST - InspectionTemplateInputField::FIRST;
}

abstract class View
{
   const NEW_INSPECTION_TEMPLATE = 0;
   const VIEW_INSPECTION_TEMPLATE = 1;
   const EDIT_INSPECTION_TEMPLATE = 2;
}

function getParams()
{
   static $params = null;
   
   if (!$params)
   {
      $params = Params::parse();
   }
   
   return ($params);
}

function getView()
{
   $view = View::VIEW_INSPECTION_TEMPLATE;
   
   if (getTemplateId() == InspectionTemplate::UNKNOWN_TEMPLATE_ID)
   {
      $view = View::NEW_INSPECTION_TEMPLATE;
   }
   else if (Authentication::checkPermissions(Permission::EDIT_LINE_INSPECTION))
   {
      $view = View::EDIT_INSPECTION_TEMPLATE;
   }
   
   return ($view);
}

function getTemplateId()
{
   $params = getParams();
   
   return ($params->keyExists("templateId") ? $params->get("templateId") : InspectionTemplate::UNKNOWN_TEMPLATE_ID);
}

function getInspectionTemplate()
{
   static $inspectionTemplate = null;
   
   if (!$inspectionTemplate)
   {
      $templateId = getTemplateId();

      if ($templateId != InspectionTemplate::UNKNOWN_TEMPLATE_ID)
      {
         $inspectionTemplate = InspectionTemplate::load($templateId);
      }
      else
      {
         $inspectionTemplate = new InspectionTemplate();
         
         // Start with a single property.
         $inspectionTemplate->inspectionProperties[] = new InspectionProperty();
      }
   }
   
   return ($inspectionTemplate);
}

function getInspectionType()
{
   $inspectionType = InspectionType::UNKNOWN;
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspectionTemplate)
   {
      $inspectionType = $inspectionTemplate->inspectionType;
   }
   
   return ($inspectionType);
}

function getInspectionName()
{
   $inspectionName = "";
   
   $inspectionTemplate = getInspectionTemplate();

   if ($inspectionTemplate)
   {
      $inspectionName = $inspectionTemplate->name;
   }
   
   return ($inspectionName);
}

function getInspectionDescription()
{
   $description = "";
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspectionTemplate)
   {
      $description = $inspectionTemplate->description;
   }
   
   return ($description);
}

function getSampleSize()
{
   $sampleSize = InspectionTemplate::DEFAULT_SAMPLE_SIZE;
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspectionTemplate)
   {
      $sampleSize = $inspectionTemplate->sampleSize;
   }
   
   return ($sampleSize);
}

function getInspectionTypeOptions()
{
   $options = "<option style=\"display:none\">";
   
   $selectedInspectionType = getInspectionType();
   
   for ($inspectionType = InspectionType::FIRST; $inspectionType != InspectionType::LAST; $inspectionType++)
   {
      $selected = ($inspectionType == $selectedInspectionType) ? "selected" : "";
      
      $label = InspectionType::getLabel($inspectionType);
      
      $options .= "<option value=\"$inspectionType\" $selected>$label</option>";
   }
   
   return ($options);
}

function getDataTypeOptions($inspectionProperty)
{
   $options = "<option style=\"display:none\">";
   
   $selectedDataType = $inspectionProperty->dataType;
   
   for ($dataType = InspectionDataType::FIRST; $dataType != InspectionDataType::LAST; $dataType++)
   {
      $selected = ($dataType == $selectedDataType) ? "selected" : "";
      
      $label = InspectionDataType::getLabel($dataType);
      
      $options .= "<option value=\"$dataType\" $selected>$label</option>";
   }
   
   return ($options);
}

function getDataUnitsOptions($inspectionProperty)
{
   $options = "<option style=\"display:none\">";
   
   $selectedDataType = $inspectionProperty->dataType;
   
   for ($dataUnits = InspectionDataUnits::FIRST; $dataUnits != InspectionDataUnits::LAST; $dataUnits++)
   {
      $selected = ($dataUnits == $selectedDataType) ? "selected" : "";
      
      $label = InspectionDataUnits::getLabel($dataUnits);
      
      $options .= "<option value=\"$dataUnits\" $selected>$label</option>";
   }
   
   return ($options);
}

function getHeading()
{
   $heading = "";
   
   switch (getView())
   {
      case View::NEW_INSPECTION_TEMPLATE:
         {
            $heading = "Create a New Inspection Template";
            break;
         }
         
      case View::EDIT_INSPECTION_TEMPLATE:
         {
            $heading = "Update an Inspection Template";
            break;
         }
         
      case View::VIEW_INSPECTION_TEMPLATE:
      default:
         {
            $heading = "View an Inspection Template";
            break;
         }
   }
   
   return ($heading);
}

function getDescription()
{
   $description = "";
   
   switch (getView())
   {
      case View::NEW_INSPECTION_TEMPLATE:
      {
         $description = "Blah blah blah.";
         break;
      }
         
      case View::EDIT_INSPECTION_TEMPLATE:
      {
         $description = "Blah blah blah.";
         break;
      }
         
      case View::VIEW_INSPECTION_TEMPLATE:
      default:
      {
         $description = "Blah blah blah.";
         break;
      }
   }
   
   return ($description);
}

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   
   $view = getView();
   
   if (($view == View::NEW_INSPECTION_TEMPLATE) ||
       ($view == View::EDIT_INSPECTION_TEMPLATE))
   {
      // Case 1
      // Creating a new template.
      // Editing an existing template.
      
      $navBar->cancelButton("location.href = 'inspectionTemplates.php'");
      $navBar->highlightNavButton("Save", "onSubmit();", false);
   }
   else if ($view == View::VIEW_INSPECTION_TEMPLATE)
   {
      // Case 2
      // Viewing an existing template.
      
      $navBar->highlightNavButton("Ok", "location.href = 'inspectionTemplates.php'", false);
   }
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

function isEditable($field)
{
   $view = getView();
   
   // Start with the edit mode, as dictated by the view.
   $isEditable = (($view == View::NEW_INSPECTION_TEMPLATE) ||
                  ($view == View::EDIT_INSPECTION_TEMPLATE));
   
   switch ($field)
   {
      default:
      {
         // Edit status based solely on view.
         break;
      }
   }
   
   return ($isEditable);
}

function getInspectionProperties()
{
   $html = "";
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspectionTemplate)
   {
      $propertyIndex = 0;
      foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
      {        
         $name = "property" . $propertyIndex;
         $dataTypeOptions = getDataTypeOptions($inspectionProperty);
         $dataUnitsOptions = getDataUnitsOptions($inspectionProperty);

         $html .=
<<<HEREDOC
         <tr>
            <td></td>
            <td><input name="{$name}_name" type="text" form="input-form" value="$inspectionProperty->name"></td>
            <td><input name="{$name}_specification" type="text" form="input-form" value="$inspectionProperty->specification"></td>
            <td><select name="{$name}_dataType" form="input-form">$dataTypeOptions</select></td>
            <td><select name="{$name}_dataUnits" form="input-form">$dataUnitsOptions</select></td>
            <td></td>
         </tr>
HEREDOC;
      }
   }
   
   return ($html);
}

// *****************************************************************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../pptpTools.php');
   exit;
}

?>

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="../common/form.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   <link rel="stylesheet" type="text/css" href="inspectionTemplate.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="inspectionTemplate.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <form id="input-form" action="" method="POST">
         <input id="inspection-id-input" type="hidden" name="templateId" value="<?php echo getTemplateId(); ?>">
         <!-- Hidden inputs make sure disabled fields below get posted. -->
      </form>
      
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
      
         <div class="pptp-form">
            <div class="form-row">

               <div class="form-col">
               
                  <div class="form-item">
                     <div class="form-label">Inspection Type</div>
                     <select name="inspectionType" class="form-input-medium" form="input-form" <?php echo !isEditable(InspectionTemplateInputField::INSPECTION_TYPE) ? "disabled" : ""; ?>>
                         <?php echo getInspectionTypeOptions(); ?>
                     </select>
                  </div>
               
                  <div class="form-item">
                     <div class="form-label">Inspection Name</div>
                     <input name="templateName" type="text" form="input-form" value="<?php echo getInspectionName() ?>">
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Description</div>
                     <input name="templateDescription" type="text" form="input-form" value="<?php echo getInspectionDescription() ?>">
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Sample Size</div>
                     <input name="sampleSize" type="number" form="input-form" value="<?php echo getSampleSize() ?>">
                  </div>
                  
                  <table>
                     <tr>
                        <th></th>
                        <th>Property</th>
                        <th>Specification</th>
                        <th>Data Type</th>
                        <th>Units</th>
                        <th></th>
                     <tr>
                     <?php echo getInspectionProperties() ?>
                  </table>
         
               </div>

            </div>
         </div>
      
         <?php echo getNavBar(); ?>
         
      </div>
               
      <script>
      </script>
     
   </div>

</body>

</html>