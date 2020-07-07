<?php

require_once '../common/authentication.php';
require_once '../common/header.php';
require_once '../common/inspection.php';
require_once '../common/inspectionTemplate.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/oasisReport/oasisReport.php';
require_once '../common/params.php';
require_once '../common/root.php';
require_once '../common/userInfo.php';

function getParams()
{
   static $params = null;
   
   if (!$params)
   {
      $params = Params::parse();
   }
   
   return ($params);
}

function getInspectionId()
{
   $params = getParams();
   
   return ($params->keyExists("inspectionId") ? $params->get("inspectionId") : Inspection::UNKNOWN_INSPECTION_ID);
}

function getOasisReport()
{
   static $oasisReport = null;
   
   if ($oasisReport == null)
   {
      $inspectionId = getInspectionId();
      
      if ($inspectionId != Inspection::UNKNOWN_INSPECTION_ID)
      {
         $oasisReport = OasisReport::load($inspectionId);
      }
   }
   
   return ($oasisReport);
}

function getHeading()
{
   $heading = "Oasis Inspection Report";
   
   return ($heading);
}

function getDescription()
{
   $description = "";
   
   return ($description);
}

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   $navBar->highlightNavButton("Ok", "location.href = 'inspections.php';", false);
   $navBar->end();
   
   return ($navBar->getHtml());
}

function getUserField($userField)
{
   $value = "";
   
   $oasisReport = getOasisReport();
   
   if ($oasisReport)
   {
      $value = $oasisReport->getUserField($userField)->getValue();
   }
   
   return ($value);
}

function getInspector()
{
   $inspector = "";
   
   $oasisReport = getOasisReport();
   
   if ($oasisReport)
   {
      $employeeNumber = intval($oasisReport->getUserField(UserFieldType::EMPLOYEE_NUMBER)->getValue());
      
      $userInfo = UserInfo::load($employeeNumber);
      
      if ($userInfo)
      {
         $inspector = $employeeNumber . " - " . $userInfo->getFullName();
      }
      else
      {
         $inspector = $employeeNumber;
      }
   }
   
   return ($inspector);
}

function getSamples()
{
   $html = "";
   
   $oasisReport = getOasisReport();
   
   if ($oasisReport)
   {
      /*
      for ($i = 0; $i < $oasisReport->getPartInspectionCount(); $i++)
      {
         $inspection = $oasisReport->getPartInspection($i);
         
         if ($inspection)
         {
            $html .= $inspection->toHtml();
         }
      }
      */
      
      $html .= $oasisReport->toHtml();
   }
   
   return ($html);
}

// ********************************** BEGIN ************************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../home.php');
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
   <link rel="stylesheet" type="text/css" href="../common/oasisReport/oasisReport.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="inspection.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
         
         <!-- div>
            <div>
               <label>Insp. Type</label>
               <input type="text" value="<?php echo getUserField(UserFieldType::INSPECTION_TYPE) ?>" disabed></input>
            </div>
            
            <div>
               <label>Inspector</label>
               <input type="text" value="<?php echo getInspector() ?>" disabed></input>
            </div>
            
            <div>
               <label>Comments</label>
               <input type="text" value="<?php echo getUserField(UserFieldType::COMMENTS) ?>" disabed></input>
            </div>
            
            <div>
               <label>Parts Made</label>
               <input type="text" value="<?php echo getUserField(UserFieldType::PART_COUNT) ?>" disabed></input>
            </div>
            
            <div>
               <div>Sample Size</div>
               <input type="text" value="<?php echo getUserField(UserFieldType::SAMPLE_SIZE) ?>" disabed></input>
            </div>
            
            <div>
               <div>Machine Number</div>
               <input type="text" value="<?php echo getUserField(UserFieldType::MACHINE_NUMBER) ?>" disabed></input>
            </div>
            
            <div>
               <div>Date</div>
               <input type="text" value="<?php echo getUserField(UserFieldType::DATE) ?>" disabed></input>
            </div>
            
            <div>
               <div>Part Number</div>
               <input type="text" value="<?php echo getUserField(UserFieldType::PART_NUMBER) ?>" disabed></input>
            </div>     
            
            <div>
               <div>Efficiency</div>
               <input type="text" value="<?php echo getUserField(UserFieldType::EFFICIENCY) ?>" disabed></input>
            </div>          
         </div-->
         
         <?php echo getSamples() ?>
         
         <?php echo getNavBar(); ?>
         
      </div>
               
      <script>
         preserveSession();
      </script>
     
   </div>

</body>

</html>