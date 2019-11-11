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
   const INSPECTOR = 3;
   const OPERATOR = 4;
   const INSPECTION = 5;
   const COMMENTS = 6;
   const LAST = 7;
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

function getInspectionId()
{
   $params = getParams();
   
   return ($params->keyExists("inspectionId") ? $params->get("inspectionId") : Inspection::UNKNOWN_INSPECTION_ID);
}

function getInspection()
{
   static $inspection = null;
   
   if ($inspection == null)
   {
      $inspectionId = getInspectionId();
      
      if ($inspectionId != Inspection::UNKNOWN_INSPECTION_ID)
      {
         $inspection = Inspection::load($inspectionId);
      }
      else
      {
         $inspection = getNewInspection();
      }
   }
   
   return ($inspection);
}

function getNewInspection()
{
   $inspection = new Inspection();
   
   $params = getParams();
   
   $inspection->templateId = 
      ($params->keyExists("templateId") ? $params->get("templateId") : Inspection::UNKNOWN_INSPECTION_ID);
      
   $userInfo = Authentication::getAuthenticatedUser();
   if ($userInfo)
   {
      $inspection->inspector = $userInfo->employeeNumber;
   }
   
   $jobNumber =
   ($params->keyExists("jobNumber") ? $params->get("jobNumber") : JobInfo::UNKNOWN_JOB_NUMBER);
   
   $wcNumber =
   ($params->keyExists("wcNumber") ? $params->get("wcNumber") : 0);
   
   $inspection->jobId = JobInfo::getJobIdByComponents($jobNumber, $wcNumber);
   
   if ($inspection->templateId != InspectionTemplate::UNKNOWN_TEMPLATE_ID)
   {
      $inspectionTemplate = InspectionTemplate::load($inspection->templateId);
      
      if ($inspectionTemplate)
      {
         $inspection->initialize($inspectionTemplate);
         
         foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
         {
            for ($sampleIndex = 0; $sampleIndex < $inspectionTemplate->sampleSize; $sampleIndex++)
            {
               $inspectionResult = new InspectionResult();
               $inspectionResult->propertyId = $inspectionProperty->propertyId;
               $inspectionResult->status = InspectionStatus::NON_APPLICABLE;
               
               $inspection->inspectionResults[$inspectionProperty->propertyId][$sampleIndex] = $inspectionResult;
            }
         }
      }

   }
 
   return ($inspection);
}

function getTemplateId()
{
   $templateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
   
   $inspectionId = getInspectionId();
   
   if ($inspectionId != Inspection::UNKNOWN_INSPECTION_ID)
   {
      $inspection = getInspection();
      
      if ($inspection)
      {
         $templateId = $inspection->templateId;
      }
   }
   else
   {
      $params = getParams();
      
      $templateId = ($params->keyExists("templateId") ? $params->get("templateId") : Inspection::UNKNOWN_INSPECTION_ID);
   }

   return ($templateId);
}

function getInspectionTemplate()
{
   static $inspectionTemplate = null;
   
   if ($inspectionTemplate == null)
   {
      $templateId = getTemplateId();
      
      if ($templateId != Inspection::UNKNOWN_INSPECTION_ID)
      {
         $inspectionTemplate = InspectionTemplate::load($templateId);
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
   else
   {
      $params = getParams();
      
      $inspectionType = ($params->keyExists("inspectionType") ? $params->getInt("inspectionType") : InspectionType::UNKNOWN);
   }
   
   return ($inspectionType);
}

function getJobNumber()
{
   $jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
   
   if (getInspectionId() != Inspection::UNKNOWN_INSPECTION_ID)
   {
      $inspection = getInspection();

      if ($inspection)
      {
         $jobInfo = JobInfo::load($inspection->jobId);
         
         if ($jobInfo)
         {
            $jobNumber = $jobInfo->jobNumber;
         }
      }
   }
   else
   {
      $params = getParams();
      
      $jobNumber = ($params->keyExists("jobNumber") ? $params->get("jobNumber") : JobInfo::UNKNOWN_JOB_NUMBER);
   }
   
   return ($jobNumber);
}

function getWcNumber()
{
   $wcNumber = 0;
   
   if (getInspectionId() != Inspection::UNKNOWN_INSPECTION_ID)
   {
      $inspection = getInspection();
      
      if ($inspection)
      {
         $jobInfo = JobInfo::load($inspection->jobId);
         
         if ($jobInfo)
         {
            $wcNumber = $jobInfo->wcNumber;
         }
      }
   }
   else
   {
      $params = getParams();
      
      $wcNumber = ($params->keyExists("wcNumber") ? $params->get("wcNumber") : JobInfo::UNKNOWN_JOB_NUMBER);
   }
   
   return ($wcNumber);
}

function getCustomerPrint()
{
   $customerPrint = "";
   
   $jobNumber = getJobNumber();
   $wcNumber = getWcNumber();
   
   $jobId = JobInfo::getJobIdByComponents($jobNumber, $wcNumber);
   
   if ($jobId != JobInfo::UNKNOWN_JOB_ID)
   {
      $jobInfo = JobInfo::load($jobId);
      
      if ($jobInfo)
      {
         $customerPrint = $jobInfo->customerPrint;
      }
   }
   
   return ($customerPrint);
}

function getInspector()
{
   $inspector = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   
   $inspection = getInspection();
   
   if ($inspection)
   {
      $inspector = $inspection->inspector;
   }
   
   return ($inspector);
}

function getOperator()
{
   $operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   
   $inspection = getInspection();
   
   if ($inspection)
   {
      $operator = $inspection->operator;      
   }
   
   return ($operator);
}

function getComments()
{
   $comments = "";
   
   $inspection = getInspection();
   
   if ($inspection)
   {
      $comments = $inspection->comments;
   }
   
   return ($comments);
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

function getJobNumberOptions()
{
   $options = "<option style=\"display:none\">";
   
   $jobNumbers = JobInfo::getJobNumbers(ONLY_ACTIVE);
   
   $selectedJobNumber = getJobNumber();
   
   // Add selected job number, if not already in the array.
   // Note: This handles the case of viewing an entry that references a non-active job.
   if (($selectedJobNumber != "") &&
      (!in_array($selectedJobNumber, $jobNumbers)))
   {
      $jobNumbers[] = $selectedJobNumber;
      sort($jobNumbers);
   }
   
   foreach ($jobNumbers as $jobNumber)
   {
      $selected = ($jobNumber == $selectedJobNumber) ? "selected" : "";
      
      $options .= "<option value=\"{$jobNumber}\" $selected>{$jobNumber}</option>";
   }
   
   return ($options);
}

function getWcNumberOptions()
{
   $options = "<option style=\"display:none\">";
   
   $jobNumber = getJobNumber();
   
   $workCenters = null;
   if ($jobNumber != JobInfo::UNKNOWN_JOB_NUMBER)
   {
      $workCenters = PPTPDatabase::getInstance()->getWorkCentersForJob($jobNumber);
   }
   else
   {
      $workCenters = PPTPDatabase::getInstance()->getWorkCenters();
   }
   
   $selectedWcNumber = getWcNumber();
   
   foreach ($workCenters as $workCenter)
   {
      $selected = ($workCenter["wcNumber"] == $selectedWcNumber) ? "selected" : "";
      
      $options .= "<option value=\"{$workCenter["wcNumber"]}\" $selected>{$workCenter["wcNumber"]}</option>";
   }
   
   return ($options);
}

function getInspectorOptions()
{
   $options = "<option style=\"display:none\">";
   
   $inspectors = PPTPDatabase::getInstance()->getUsersByRole(Role::INSPECTOR);

   // Create an array of employee numbers.
   $employeeNumbers = array();
   foreach ($inspectors as $inspector)
   {
      $employeeNumbers[] = intval($inspector["employeeNumber"]);
   }
   
   $selectedInspector = getInspector();
   
   // Add selected job number, if not already in the array.
   // Note: This handles the case of viewing an entry with an operator that is not assigned to the OPERATOR role.
   if (($selectedInspector != UserInfo::UNKNOWN_EMPLOYEE_NUMBER) &&
      (!in_array($selectedInspector, $employeeNumbers)))
   {
      $employeeNumbers[] = $selectedInspector;
      sort($employeeNumbers);
   }
   
   foreach ($employeeNumbers as $employeeNumber)
   {
      $userInfo = UserInfo::load($employeeNumber);
      if ($userInfo)
      {
         $selected = ($employeeNumber == $selectedInspector) ? "selected" : "";
         
         $name = $employeeNumber . " - " . $userInfo->getFullName();
         
         $options .= "<option value=\"$employeeNumber\" $selected>$name</option>";
      }
   }
   
   return ($options);
}

function getOperatorOptions()
{
   $options = "<option style=\"display:none\">";
   
   $operators = PPTPDatabase::getInstance()->getUsersByRole(Role::OPERATOR);
   
   // Create an array of employee numbers.
   $employeeNumbers = array();
   foreach ($operators as $operator)
   {
      $employeeNumbers[] = intval($operator["employeeNumber"]);
   }
   
   $selectedOperator = getOperator();
   
   // Add selected job number, if not already in the array.
   // Note: This handles the case of viewing an entry with an operator that is not assigned to the OPERATOR role.
   if (($selectedOperator != UserInfo::UNKNOWN_EMPLOYEE_NUMBER) &&
       (!in_array($selectedOperator, $employeeNumbers)))
   {
      $employeeNumbers[] = $selectedOperator;
      sort($employeeNumbers);
   }
   
   foreach ($employeeNumbers as $employeeNumber)
   {
      $userInfo = UserInfo::load($employeeNumber);
      if ($userInfo)
      {
         $selected = ($employeeNumber == $selectedOperator) ? "selected" : "";
         
         $name = $employeeNumber . " - " . $userInfo->getFullName();
         
         $options .= "<option value=\"$employeeNumber\" $selected>$name</option>";
      }
   }
   
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
      $description = "Next, select the operator responsible for the targeted part inspection.  If any of the categories are not relevant to the part you're inspecting, just leave it set to \"N/A\"";
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
      
      $navBar->cancelButton("location.href = 'inspections.php';");
      $navBar->highlightNavButton("Save", "onSubmit();", false);
   }
   else if ($view == "view_inspection")
   {
      // Case 2
      // Viewing an existing job.
      
      $navBar->highlightNavButton("Ok", "location.href = 'inspections.php';", false);
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
      case InspectionInputField::INSPECTION_TYPE:
      case InspectionInputField::JOB_NUMBER:
      case InspectionInputField::WC_NUMBER:
      {
         $isEditable = false;
         break;
      }
      
      default:
      {
         // Edit status based solely on view.
         break;
      }
   }
   
   return ($isEditable);
}

function getInspections()
{
   $html = "";
   
   $inspection = getInspection();
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspection && $inspectionTemplate)
   {
      $html .=
<<<HEREDOC
      <tr>
         <td></td>
         <td></td>
HEREDOC;
      
      // Sample heading.
      for ($sampleId = 1; $sampleId <= $inspectionTemplate->sampleSize; $sampleId++)
      {
         $html .=
<<<HEREDOC
         <th>Sample $sampleId</th>
HEREDOC;
      }
         
      $html .= "</tr>";
      
      foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
      {
         $html .= "<tr>";
         
         $html .= 
<<<HEREDOC
         <td><div class="expand-button" onclick="showData(this)">+</div><div class="condense-button" style="display:none;" onclick="hideData(this)">-</div></td>
         <td>
            <div class="flex-vertical">
               <div class="inspection-property-name">$inspectionProperty->name</div>
               <div>$inspectionProperty->specification</div>
            </div>
         </td>
HEREDOC;
        
         for ($sampleIndex = 0; $sampleIndex < $inspectionTemplate->sampleSize; $sampleIndex++)
         {
            $inspectionResult = null;
            if (isset($inspection->inspectionResults[$inspectionProperty->propertyId][$sampleIndex]))
            {
               $inspectionResult = $inspection->inspectionResults[$inspectionProperty->propertyId][$sampleIndex];
            }
            
            $html .= getInspectionInput($inspectionProperty, $sampleIndex, $inspectionResult);
         }
            
         $html .= "</tr>";
         
         $html .= "<tr style=\"display:none;\"><td/><td/>";
         
         for ($sampleIndex = 0; $sampleIndex < $inspectionTemplate->sampleSize; $sampleIndex++)
         {
            $inspectionResult = null;
            if (isset($inspection->inspectionResults[$inspectionProperty->propertyId][$sampleIndex]))
            {
               $inspectionResult = $inspection->inspectionResults[$inspectionProperty->propertyId][$sampleIndex];
               
               $html .= getInspectionDataInput($inspectionProperty, $sampleIndex, $inspectionResult);
            }
         }
         
         $html .= "</tr>";
      }
   }
   
   return ($html);
}

function getInspectionInput($inspectionProperty, $sampleIndex, $inspectionResult)
{
   $html = "<td>";
   
   if ($inspectionProperty)
   {
      $name = InspectionResult::getInputName($inspectionProperty->propertyId, $sampleIndex);
      $dataName = $name . "_data";

      $pass = "";
      $fail = "";
      $nonApplicable = "selected";
      $class = "";
      $dataValue = "";
      
      if ($inspectionResult)
      {
         $pass = ($inspectionResult->pass()) ? "selected" : "";
         $warning = ($inspectionResult->warning()) ? "selected" : "";
         $fail = ($inspectionResult->fail()) ? "selected" : "";
         $nonApplicable = ($inspectionResult->nonApplicable()) ? "selected" : "";
         $class = InspectionStatus::getClass($inspectionResult->status);
         $dataValue = $inspectionResult->data;
      }
      
      $nonApplicableValue = InspectionStatus::NON_APPLICABLE;
      $passValue = InspectionStatus::PASS;
      $warningValue = InspectionStatus::WARNING;
      $failValue = InspectionStatus::FAIL;
      
      $disabled = !isEditable(InspectionInputField::COMMENTS) ? "disabled" : "";
      
      $dataUnits = InspectionDataUnits::getAbbreviatedLabel($inspectionProperty->dataUnits);
      
      $html .=
<<<HEREDOC
      <select name="$name" class="inspection-status-input $class" form="input-form" oninput="onInspectionStatusUpdate(this)" $disabled>
         <option value="$nonApplicableValue" $nonApplicable>N/A</option>
         <option value="$passValue" $pass>PASS</option>
         <option value="$warningValue" $warning>WARNING</option>
         <option value="$failValue" $fail>FAIL</option>
      </select>
HEREDOC;
   }
      
   $html .= "</td>";
   
   return ($html);
}

function getInspectionDataInput($inspectionProperty, $sampleIndex, $inspectionResult)
{
   $html = "<td>";
   
   if ($inspectionProperty)
   {
      $name = InspectionResult::getInputName($inspectionProperty->propertyId, $sampleIndex);
      $dataName = $name . "_data";
      $dataValue = "";
      
      if ($inspectionResult)
      {
         $dataValue = $inspectionResult->data;
      }
            
      $disabled = !isEditable(InspectionInputField::COMMENTS) ? "disabled" : "";
      
      $dataUnits = InspectionDataUnits::getAbbreviatedLabel($inspectionProperty->dataUnits);
      
      $html .=
<<<HEREDOC
      <input name="$dataName" type="text" form="input-form" style="width:50px;" value="$dataValue">&nbsp$dataUnits
HEREDOC;
   }
   
   $html .= "</td>";
   
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
         <input id="inspection-id-input" type="hidden" name="inspectionId" value="<?php echo getInspectionId(); ?>">
         <!-- Hidden inputs make sure disabled fields below get posted. -->
         <input type="hidden" name="templateId" value="<?php echo getTemplateId(); ?>">
         <input type="hidden" name="jobNumber" value="<?php echo getJobNumber(); ?>">
         <input type="hidden" name="wcNumber" value="<?php echo getWcNumber(); ?>">
      </form>
      
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
      
         <div class="pptp-form">
            <div class="form-row">

               <div class="form-col">
               
                  <div class="form-item">
                     <div class="form-label">Inspection Type</div>
                     <select id="inspection-type-input" class="form-input-medium" name="inspectionType" form="input-form" oninput="" <?php echo !isEditable(InspectionInputField::INSPECTION_TYPE) ? "disabled" : ""; ?>>
                         <?php echo getInspectionTypeOptions(); ?>
                     </select>
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">Job Number</div>
                     <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" oninput="this.validator.validate(); onJobNumberChange();" <?php echo !isEditable(InspectionInputField::JOB_NUMBER) ? "disabled" : ""; ?>>
                         <?php echo getJobNumberOptions(); ?>
                     </select>
                     &nbsp;&nbsp;
                     <div id="customer-print-div"><a href="<?php $ROOT ?>/uploads/<?php echo getCustomerPrint(); ?>" target="_blank"><?php echo getCustomerPrint(); ?></a></div>
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">WC Number</div>
                     <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" <?php echo !isEditable(InspectionInputField::WC_NUMBER) ? "disabled" : ""; ?>>
                        <?php echo getWcNumberOptions(); ?>
                     </select>
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Inspector</div>
                     <select id="inspector-input" class="form-input-medium" name="inspector" form="input-form" <?php echo !isEditable(InspectionInputField::INSPECTOR) ? "disabled" : ""; ?>>
                        <?php echo getInspectorOptions(); ?>
                     </select>
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">Operator</div>
                     <select id="operator-input" class="form-input-medium" name="operator" form="input-form" <?php echo !isEditable(InspectionInputField::OPERATOR) ? "disabled" : ""; ?>>
                        <?php echo getOperatorOptions(); ?>
                     </select>
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label hide-on-mobile">Inspection</div>
                     <table class="inspection-table">
                        <?php echo getInspections(); ?>
                     </table>
                  </div>
            
                  <div class="form-item">
                     <div class="form-label hide-on-mobile">Comments</div>
                     <textarea form="input-form" class="comments-input" type="text" name="comments" placeholder="Enter comments ..." <?php echo !isEditable(InspectionInputField::COMMENTS) ? "disabled" : ""; ?>><?php echo getComments(); ?></textarea>
                  </div>
         
               </div>

            </div>
         </div>
      
         <?php echo getNavBar(); ?>
         
      </div>
               
      <script>
         var operatorValidator = new SelectValidator("operator-input");

         operatorValidator.init();

         function onInspectionStatusUpdate(element)
         {
            var inspectionStatusClasses = [
            <?php
            for ($inspectionStatus = InspectionStatus::FIRST; $inspectionStatus < InspectionStatus::LAST; $inspectionStatus++)
            {
               $class = InspectionStatus::getClass($inspectionStatus);
               echo "\"$class\"";
               echo ($inspectionStatus < (InspectionStatus::LAST - 1)) ? ", " : "";
            }
            ?>
            ];

            // Clear classes
            for (const inspectionStatusClass of inspectionStatusClasses)
            {
               if (inspectionStatusClass != "")
               {
                  element.classList.remove(inspectionStatusClass);
               }
            }

            // Add new class.
            var inspectionStatus = parseInt(element.value);
            element.classList.add(inspectionStatusClasses[inspectionStatus]);
         }
      </script>
     
   </div>

</body>

</html>