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

const INVALID_PROPERTY_INDEX = -1;

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

function getDataTypeOptions($selectedDataType)
{
   $options = "<option value\"" . InspectionDataType::UNKNOWN . "\"></option>";
   
   for ($dataType = InspectionDataType::FIRST; $dataType != InspectionDataType::LAST; $dataType++)
   {
      $selected = ($dataType == $selectedDataType) ? "selected" : "";
      
      $label = InspectionDataType::getLabel($dataType);
      
      $options .= "<option value=\"$dataType\" $selected>$label</option>";
   }
   
   return ($options);
}

function getDataUnitsOptions($selectedDataUnits)
{
   $options = "<option value\"" . InspectionDataUnits::UNKNOWN . "\"></option>";
   
   for ($dataUnits = InspectionDataUnits::FIRST; $dataUnits != InspectionDataUnits::LAST; $dataUnits++)
   {
      $selected = ($dataUnits == $selectedDataUnits) ? "selected" : "";
      
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

function getOptionalProperties()
{
   $html = "";
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspectionTemplate)
   {
      for ($optionalProperty = OptionalInspectionProperties::FIRST; 
           $optionalProperty < OptionalInspectionProperties::LAST; 
           $optionalProperty++)
      {
         $name = "optional-property-$optionalProperty-input";
         $label = OptionalInspectionProperties::getLabel($optionalProperty);
         $checked = $inspectionTemplate->isOptionalPropertySet($optionalProperty) ? "checked" : "";
         
         $html .=
<<<HEREDOC
         <input type="checkbox" name="$name" form="input-form" value="1" $checked>$label&nbsp;&nbsp;
HEREDOC;
      }
   }
   
   return ($html);
}

function getInspectionPropertyCount()
{
   $count = 0;
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspectionTemplate)
   {
      $count = count($inspectionTemplate->inspectionProperties);
   }
   
   return ($count);
   
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
         $html .= getInspectionRow($propertyIndex, $inspectionProperty);
         
         $propertyIndex++;
      }
   }
   
   return ($html);
}

function getInspectionRow($propertyIndex, $inspectionProperty)
{
   
   $name = "property" . $propertyIndex;
   
   $propertyId = $inspectionProperty ? $inspectionProperty->propertyId : "0";
   $propertyName = $inspectionProperty ? $inspectionProperty->name : "";
   $specification = $inspectionProperty ? $inspectionProperty->specification : "";
   $dataType = $inspectionProperty ? $inspectionProperty->dataType : InspectionDataType::UNKNOWN;
   $dataUnits = $inspectionProperty ? $inspectionProperty->dataUnits : InspectionDataUnits::UNKNOWN;
   
   $dataTypeOptions = getDataTypeOptions($dataType);
   $dataUnitsOptions = getDataUnitsOptions($dataUnits);
   
   $html =
<<<HEREDOC
   <tr>
      <input name = "{$name}_propertyId" type="hidden" form="input-form" value="$propertyId">
      <input name="{$name}_ordering" type="hidden" form="input-form" value="0">
      <td></td>
      <td><input name="{$name}_name" type="text" form="input-form" value="$propertyName"></td>
      <td><input name="{$name}_specification" type="text" form="input-form" value="$specification"></td>
      <td><select name="{$name}_dataType" form="input-form">$dataTypeOptions</select></td>
      <td><select name="{$name}_dataUnits" form="input-form">$dataUnitsOptions</select></td>
      <td></td>
   </tr>
HEREDOC;
   
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
         
         <div class="flex-vertical inner-content">
            <div class="pptp-form">
               <div class="form-row">
   
                  <div class="form-col">
                  
                     <div class="form-item">
                        <div class="form-label">Inspection Type</div>
                        <select id="inspection-type-input" name="inspectionType" class="form-input-medium" form="input-form" oninput="onInspectionTypeChange();" <?php echo !isEditable(InspectionTemplateInputField::INSPECTION_TYPE) ? "disabled" : ""; ?>>
                            <?php echo getInspectionTypeOptions(); ?>
                        </select>
                     </div>
                  
                     <div class="form-item">
                        <div class="form-label">Inspection Name</div>
                        <input name="templateName" type="text" class="form-input-medium" style="width: 250px;" form="input-form" value="<?php echo getInspectionName() ?>">
                     </div>
                     
                     <div class="form-item">
                        <div class="form-label">Description</div>
                        <input name="templateDescription" type="text" class="form-input-medium" style="width: 450px;" form="input-form" value="<?php echo getInspectionDescription() ?>">
                     </div>
                     
                     <div class="form-item">
                        <div class="form-label">Sample Size</div>
                        <input name="sampleSize" type="number" class="form-input-medium" style="width: 50px;" form="input-form" value="<?php echo getSampleSize() ?>">
                     </div>
                     
                     <div id="optional-properties-input-container" class="form-item">
                        <div class="form-label">Optional Properties</div>
                        <?php echo getOptionalProperties() ?>                     
                     </div>
                     
                     <div class="form-item">
                        <table id="property-table">
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
                     
                     <div class="form-item" style="justify-content: flex-end;">
                        <button style="width: 50px; height: 30px;" onclick="onAddProperty()">+</button>
                     </div>
            
                  </div>
   
               </div>
            </div>
         </div>
      
         <?php echo getNavBar(); ?>
         
      </div>
               
      <script>
         const OASIS = <?php echo InspectionType::OASIS; ?>;
         const LINE = <?php echo InspectionType::LINE; ?>;
         const QCP = <?php echo InspectionType::QCP; ?>;
         const IN_PROCESS = <?php echo InspectionType::IN_PROCESS; ?>;
         const GENERIC = <?php echo InspectionType::GENERIC; ?>;
      
         var propertyCount = <?php echo getInspectionPropertyCount(); ?>;
      
         function getNewInspectionRow()
         {
            var innerHtml = "<?php echo preg_replace( "/\r|\n/", "", addslashes(getInspectionRow("@", null)));?>";

            innerHtml = innerHtml.replace(/@/g, propertyCount);
            console.log(innerHtml);

            propertyCount++;

            return (innerHtml);
         }

         // Initialize visibility of optional properties.
         onInspectionTypeChange();
      </script>
     
   </div>

</body>

</html>