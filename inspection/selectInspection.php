<?php

require_once '../common/activity.php';
require_once '../common/authentication.php';
require_once '../common/header.php';
require_once '../common/inspection.php';
require_once '../common/inspectionTemplate.php';
require_once '../common/jobInfo.php';
require_once '../common/menu.php';
require_once '../common/params.php';
require_once '../common/root.php';
require_once '../common/userInfo.php';

const ACTIVITY = Activity::INSPECTION;
$activity = Activity::getActivity(ACTIVITY);

const ONLY_ACTIVE = true;

abstract class InspectionInputField
{
   const FIRST = 0;
   const INSPECTION_TYPE = InspectionInputField::FIRST;
   const JOB_NUMBER = 1;
   const WC_NUMBER = 2;
   const TEMPLATE_ID = 3;
   const LAST = 4;
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

function getInspectionTypeOptions()
{
   $options = "<option style=\"display:none\">";
   
   for ($inspectionType = InspectionType::FIRST; $inspectionType != InspectionType::LAST; $inspectionType++)
   {
      if ($inspectionType != InspectionType::OASIS)  // Cannot make manually.
      {
         $label = InspectionType::getLabel($inspectionType);
         
         $options .= "<option value=\"$inspectionType\">$label</option>";
      }
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

function getTemplateOptions()
{
   $options = "<option style=\"display:none\">";
   
   return ($options);
}

function getHeading()
{
   $heading = "Select an Inspection Template";
      
   return ($heading);
}

function getDescription()
{
   $description = "Start by selecting choosing your inspection type and a currently active job.";
   
   return ($description);
}

function isEditable($field)
{
   return (true);
}

// ********************************** BEGIN ************************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../login.php');
   exit;
}

?>

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   
   <link rel="stylesheet" type="text/css" href="../common/theme.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="inspection.css"/>
   
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="inspection.js"></script>

</head>

<body class="flex-vertical flex-top flex-left">
        
   <form id="input-form" action="viewInspection.php" method="POST">
   </form>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="main flex-horizontal flex-top flex-left">
   
      <?php Menu::render(ACTIVITY); ?>
      
      <div class="content flex-vertical flex-top flex-left">
      
         <div class="flex-horizontal flex-v-center flex-h-center">
            <div class="heading"><?php echo getHeading(); ?></div>&nbsp;&nbsp;
            <i id="help-icon" class="material-icons icon-button">help</i>
         </div>
         
         <div id="description" class="description"><?php echo getDescription(); ?></div>
         
         <br>
         
         <div class="flex-column">
         
            <div class="flex-row" style="justify-content: space-evenly;">

               <div class="flex-column">
               
                  <div class="form-item">
                     <div class="form-label">Inspection Type</div>
                     <select id="inspection-type-input" class="form-input-medium" name="inspectionType" form="input-form" oninput="onInspectionTypeChange(); updateTemplateId();" <?php echo !isEditable(InspectionInputField::INSPECTION_TYPE) ? "disabled" : ""; ?>>
                         <?php echo getInspectionTypeOptions(); ?>
                     </select>
                  </div>
         
                  <div id="job-number-input-container" class="form-item">
                     <div class="form-label">Job Number</div>
                     <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" oninput="this.validator.validate(); onJobNumberChange(); updateTemplateId();" <?php echo !isEditable(InspectionInputField::JOB_NUMBER) ? "disabled" : ""; ?>>
                         <?php echo getJobNumberOptions(); ?>
                     </select>
                     &nbsp;&nbsp;
                     <div id="customer-print-div"></div>
                  </div>
         
                  <div id="wc-number-input-container" class="form-item">
                     <div class="form-label">WC Number</div>
                     <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" oninput="updateTemplateId();" <?php echo !isEditable(InspectionInputField::WC_NUMBER) ? "disabled" : ""; ?>>
                        <?php echo getWcNumberOptions(); ?>
                     </select>
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Inspection Template</div>
                     <select id="template-id-input" class="form-input-medium" name="templateId" form="input-form" <?php echo !isEditable(InspectionInputField::WC_NUMBER) ? "disabled" : ""; ?>>
                        <?php echo getTemplateOptions(); ?>
                     </select>
                  </div>
         
               </div>
               
            </div>
            
         </div>
         
         <br>
         
         <div class="flex-horizontal flex-h-center">
            <button id="cancel-button">Cancel</button>&nbsp;&nbsp;&nbsp;
            <button id="next-button" class="accent-button">Next</button>            
         </div>
      
      </div> <!-- content -->
     
   </div> <!-- main -->   
         
   <script>
   
      preserveSession();
      
      const OASIS = <?php echo InspectionType::OASIS; ?>;
      const LINE = <?php echo InspectionType::LINE; ?>;
      const QCP = <?php echo InspectionType::QCP; ?>;
      const IN_PROCESS = <?php echo InspectionType::IN_PROCESS; ?>;
      const GENERIC = <?php echo InspectionType::GENERIC; ?>;
   
      var inspectionTypeValidator = new SelectValidator("inspection-type-input");
      var jobNumberValidator = new SelectValidator("job-number-input");
      var wcNumberValidator = new SelectValidator("wc-number-input");
   
      inspectionTypeValidator.init();
      jobNumberValidator.init();
      wcNumberValidator.init();

      // Setup event handling on all DOM elements.
      document.getElementById("cancel-button").onclick = function(){window.history.back();};
      document.getElementById("next-button").onclick = function(){onSelectInspectionTemplate();};      
      document.getElementById("help-icon").onclick = function(){document.getElementById("description").classList.toggle('shown');};
      document.getElementById("menu-button").onclick = function(){document.getElementById("menu").classList.toggle('shown');};
      
   </script>

</body>

</html>
