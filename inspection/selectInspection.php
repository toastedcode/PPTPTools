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

abstract class InspectionInputField
{
   const FIRST = 0;
   const INSPECTION_TYPE = InspectionInputField::FIRST;
   const JOB_NUMBER = 1;
   const WC_NUMBER = 2;
   const LAST = 3;
   const COUNT = InspectionInputField::LAST - InspectionInputField::FIRST;
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
   $params = getParams();
   
   return ($params->keyExists("view") ? $params->get("view") : "view_inspection");
}

function getInspectionTypeOptions()
{
   $options = "<option style=\"display:none\">";
   
   for ($inspectionType = InspectionType::FIRST; $inspectionType != InspectionType::LAST; $inspectionType++)
   {
      $label = InspectionType::getLabel($inspectionType);
      
      $options .= "<option value=\"$inspectionType\">$label</option>";
   }
   
   return ($options);
}

function getJobNumberOptions()
{
   $options = "<option style=\"display:none\">";
   
   $jobNumbers = JobInfo::getJobNumbers(ONLY_ACTIVE);
   
   foreach ($jobNumbers as $jobNumber)
   {
      $options .= "<option value=\"{$jobNumber}\">{$jobNumber}</option>";
   }
   
   return ($options);
}

function getWcNumberOptions()
{
   $options = "<option style=\"display:none\">";
   
   return ($options);
}

function getHeading()
{
   $heading = "";
   
   $view = getView();
   
   if ($view == "new_inspection")
   {
      $heading = "Add a New Inspection";
   }
   else if ($view == "edit_inspection")
   {
      $heading = "Update an Inspection";
   }
   else if ($view == "view_inspection")
   {
      $heading = "View an Inspection";
   }
      
   return ($heading);
}

function getDescription()
{
   $description = "";
   
   $view = getView();
   
   if ($view == "new_inspection")
   {
      $description = "Start by selecting a work center, then any of the currently active jobs for that station.  If any of the categories are not relevant to the part you're inspecting, just leave it set to \"N/A\"";
   }
   else if ($view == "edit_inspection")
   {
      $description = "You may revise any of the fields for this inspection and then select save when you're satisfied with the changes.";
   }
   else if ($view == "view_inspection")
   {
      $description = "View a previously saved inspection in detail.";
   }
   
   return ($description);
}
   
function getNavBar()
{
   $view = getView();
   
   $navBar = new Navigation();
   
   $navBar->start();
   
   if (($view == "new_inspection") ||
       ($view == "edit_inspection"))
   {
      // Case 1
      // Creating a new inspection.
      // Editing an existing inspection.
      
      $navBar->cancelButton("submitForm('input-form', 'lineInspection.php', 'view_line_inspections', 'cancel_line_inspection')");
      $navBar->nextButton("submitForm('input-form', 'viewInspection.php', 'new_inspection', '')");
   }
   else if ($view == "view_line_inspection")
   {
      // Case 2
      // Viewing an existing job.
      
      $navBar->highlightNavButton("Ok", "submitForm('input-form', 'jobs.php', 'view_line_inspections', 'no_action')", false);
   }
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

function isEditable($field)
{
   $view = getView();
   
   // Start with the edit mode, as dictated by the view.
   $isEditable = (($view == "new_inspection") ||
                  ($view == "edit_inspection"));
   
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
   <link rel="stylesheet" type="text/css" href="inspection.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="inspection.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <form id="input-form" action="" method="POST">
         <input id="template-id-input" type="hidden" name="templateId">
      </form>
      
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
      
         <div class="pptp-form">
            <div class="form-row">

               <div class="form-col">
               
                  <div class="form-item">
                     <div class="form-label">Inspection Type</div>
                     <select id="inspection-type-input" class="form-input-medium" name="inspectionType" form="input-form" oninput="updateTemplateId();" <?php echo !isEditable(InspectionInputField::INSPECTION_TYPE) ? "disabled" : ""; ?>>
                         <?php echo getInspectionTypeOptions(); ?>
                     </select>
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">Job Number</div>
                     <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" oninput="this.validator.validate(); onJobNumberChange(); updateTemplateId();" <?php echo !isEditable(InspectionInputField::JOB_NUMBER) ? "disabled" : ""; ?>>
                         <?php echo getJobNumberOptions(); ?>
                     </select>
                     &nbsp;&nbsp;
                     <div id="customer-print-div"></div>
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">WC Number</div>
                     <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" oninput="updateTemplateId();" <?php echo !isEditable(InspectionInputField::WC_NUMBER) ? "disabled" : ""; ?>>
                        <?php echo getWcNumberOptions(); ?>
                     </select>
                  </div>
         
               </div>
            </div>
         </div>
      
         <?php echo getNavBar(); ?>
         
      </div>
               
      <script>
         var jobNumberValidator = new SelectValidator("job-number-input");

         jobNumberValidator.init();
      </script>
     
   </div>

</body>

</html>