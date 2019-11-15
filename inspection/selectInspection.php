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
   $heading = "Add a New Inspection";
      
   return ($heading);
}

function getDescription()
{
   $description = "Start by selecting choosing your inspection type and a currently active job.";
   
   return ($description);
}
   
function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   
   $navBar->cancelButton("location.href = 'inspections.php';");
   $navBar->nextButton("if (validateInspectionSelection()) {submitForm('input-form', 'viewInspection.php', 'new_inspection', '');}");
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

function isEditable($field)
{
   return (true);
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
      </form>
      
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
      
         <div class="pptp-form">
            <div class="form-row">

               <div class="form-col">
               
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
      
         <?php echo getNavBar(); ?>
         
      </div>
               
      <script>
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
      </script>
     
   </div>

</body>

</html>