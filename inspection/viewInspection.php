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
   const OPERATOR = 3;
   const INSPECTION = 4;
   const COMMENTS = 5;
   const LAST = 6;
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
   }
   
   return ($inspection);
}

function getInspectionTemplate()
{
   static $inspectionTemplate = null;
   
   if ($inspectionTemplate == null)
   {
      $inspection = getInspection();
      
      if ($inspection)
      {
         $inspectionTemplate = InspectionTemplate::load($inspection->templateId);
      }
   }
   
   return ($inspectionTemplate);
}

function getInspectionType()
{
   $inspectionType = 0;
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspectionTemplate)
   {
      $inspectionType = $inspectionTemplate->inspectionType;
   }
   
   return ($inspectionType);
}

function getJobNumber()
{
   $jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
   
   $inspection = getInspection();
   
   if ($inspection)
   {
      $jobInfo = JobInfo::load($inspection->jobId);
      
      if ($jobInfo)
      {
         $jobNumber = $jobInfo->jobNumber;
      }
   }
   
   return ($jobNumber);
}

function getWcNumber()
{
   $wcNumber = 0;
   
   $inspection = getInspection();
   
   if ($inspection)
   {
      $jobInfo = JobInfo::load($inspection->jobId);
      
      if ($jobInfo)
      {
         $wcNumber = $jobInfo->wcNumber;
      }
   }
   
   return ($wcNumber);
}

function getOperator()
{
   $operator = 0;
   
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
   
/*
   protected static function inspectionDiv($lineInspectionInfo, $view)
   {
      $isDisabled = ($view == "view_line_inspection");
      $disabled = $isDisabled? "disabled" : "";
      
      $selected= ($lineInspectionInfo->jobNumber == JobInfo::UNKNOWN_JOB_NUMBER) ? "selected" : "";
      
      $options = "<option disabled $selected hidden>Select job</option>";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getActiveJobs(null);
         
         $i = 0;
         while ($result && ($row = $result->fetch_assoc()))
         {
            $jobNumber = $row["jobNumber"];
            
            $selected = "";
            if ($jobNumber == $lineInspectionInfo->jobNumber)
            {
               $selected = "selected";
            }
            
            $options .= "<option value=\"$jobNumber\" $selected>" . $jobNumber . "</option>";
            
            $i++;
         }
      }
      
      $wcNumberInput = ViewLineInspection::getWcNumberInput("", $lineInspectionInfo->wcNumber, $isDisabled);
      
      $operatorInput = ViewLineInspection::getOperatorInput($lineInspectionInfo, $isDisabled);
      
      $inspectionInput = array();
      $fail = array();
      for ($i = 0; $i < LineInspectionInfo::NUM_INSPECTIONS; $i++)
      {
         $inspectionInput[$i] = ViewLineInspection::inspectionInput($lineInspectionInfo, $i, $isDisabled);
      }
      
      $html =
<<<HEREDOC
      <div class="form-col">

         <div class="form-item">
            <div class="form-label">Job Number</div>
            <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" oninput="updateWCNumberInput(); updateCustomerPrint();" $disabled>
               $options
            </select>
            &nbsp;&nbsp;
            <div id="customer-print-div"></div>
         </div>

         <div class="form-item">
            <div class="form-label">WC Number</div>
            <div id="wc-number-input-div">$wcNumberInput</div>
         </div>

         <div class="form-item">
            <div class="form-label">Operator</div>
            $operatorInput
         </div>
         
         <div class="form-item">
            <div class="form-label hide-on-mobile">Inspection</div>
            <table class="inspection-table">
               <tr>
                  <td>Thread #1</td>
                  {$inspectionInput[0]}
               </tr>
               <tr>
                  <td>Thread #2</td>
                  {$inspectionInput[1]}
               </tr>
               <tr>
                  <td>Thread #3</td>
                  {$inspectionInput[2]}
               </tr>
               <tr>
                  <td>Visual</td>
                  {$inspectionInput[3]}
               </tr>
               <tr>
                  <td>Undercut</td>
                  {$inspectionInput[4]}
               </tr>
               <tr>
                  <td>Depth</td>
                  {$inspectionInput[5]}
               </tr>
            </table>
         </div>
   
         <div class="form-item">
            <div class="form-label hide-on-mobile">Comments</div>
            <textarea form="input-form" class="comments-input" type="text" name="comments" placeholder="Enter comments ...">$lineInspectionInfo->comments</textarea>
         </div>

      </div>
HEREDOC;
      
      return ($html);
   }
   */

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
      $navBar->highlightNavButton("Save", "submitForm('input-form', 'lineInspection.php', 'view_line_inspections', 'save_line_inspection');", false);
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

function getInspections()
{
   $html = "";
   
   $inspection = getInspection();
   
   $inspectionTemplate = getInspectionTemplate();
   
   if ($inspection && $inspectionTemplate)
   {
      $i = 0;
      foreach ($inspectionTemplate->inspectionProperties as $inspectionProperty)
      {
         $inspectionResult = $inspection->inspectionResults[$i];
         
         $inspectionInput = getInspectionInput($inspectionProperty, $inspectionResult);
         
         $html .= 
<<<HEREDOC
         <tr>
            <td>$inspectionProperty->propertyName</td>
            $inspectionInput
         </tr>\n
HEREDOC;
         
         $i++;
      }
   }
   
   return ($html);
}

function getInspectionInput($inspectionProperty, $inspectionResult)
{
   $html = "";
   
   $name = "inspectionProperty_" . $inspectionProperty->propertyName;
   $pass = ($inspectionResult->pass()) ? "checked" : "";
   $fail = ($inspectionResult->fail()) ? "checked" : "";
   $nonApplicable = ($inspectionResult->nonApplicable()) ? "checked" : "";
   
   $passId = $name . "-pass-button";
   $failId = $name . "-fail-button";
   $nonApplicableId = $name . "-na-button";
   
   $html =
<<<HEREDOC
      <td>
         <input id="$passId" type="radio" class="invisible-radio-button pass" form="input-form" name="$name" value="1" $pass/>
         <label for="$passId">
            <div class="select-button">PASS</div>
         </label>
      </td>
      <td>
         <input id="$failId" type="radio" class="invisible-radio-button fail" form="input-form" name="$name" value="2" $fail/>
         <label for="$failId">
            <div class="select-button">FAIL</div>
         </label>
      </td>
      <td>
         <input id="$nonApplicableId" type="radio" class="invisible-radio-button nonApplicable" form="input-form" name="$name" value="3" $nonApplicable/>
         <label for="$nonApplicableId">
            <div class="select-button">N/A</div>
         </label>
      </td>
HEREDOC;
   
   return ($html);
}
   
/*
   
   public static function inspectionInput($lineInspectionInfo, $inspectionIndex, $isDisabled)
   {
      $name = LineInspectionInfo::getInspectionName($inspectionIndex);
      $pass = ($lineInspectionInfo->inspections[$inspectionIndex] == InspectionStatus::PASS) ? "checked" : "";
      $fail = ($lineInspectionInfo->inspections[$inspectionIndex] == InspectionStatus::FAIL) ? "checked" : "";
      $nonApplicable = ($lineInspectionInfo->inspections[$inspectionIndex] == InspectionStatus::UNKNOWN) ? "checked" : "";
      
      $passId = $name . "-pass-button";
      $failId = $name . "-fail-button";
      $nonApplicableId = $name . "-na-button";
      
      $html = 
<<<HEREDOC
      <td>
         <input id="$passId" type="radio" class="invisible-radio-button pass" form="input-form" name="$name" value="1" $pass/>
         <label for="$passId">
            <div class="select-button">PASS</div>
         </label>
      </td>
      <td>
         <input id="$failId" type="radio" class="invisible-radio-button fail" form="input-form" name="$name" value="2" $fail/>
         <label for="$failId">
            <div class="select-button">FAIL</div>
         </label>
      </td>
      <td>
         <input id="$nonApplicableId" type="radio" class="invisible-radio-button nonApplicable" form="input-form" name="$name" value="0" $nonApplicable/>
         <label for="$nonApplicableId">
            <div class="select-button">N/A</div>
         </label>
      </td>
HEREDOC;
      
      return ($html);
   }
*/

// *****************************************************************************
//                          AJAX request handling
// *****************************************************************************

global $ROOT;

if (isset($_GET["action"]))
{
   switch ($_GET["action"])
   {
      case "get_wc_number_input":
      {
         $jobNumber = $_GET["jobNumber"];
         $isDisabled = filter_var($_GET["isDisabled"], FILTER_VALIDATE_BOOLEAN);
         
         $wcNumberInput =  ViewLineInspection::getWcNumberInput($jobNumber, $isDisabled);
         
         echo $wcNumberInput;
         break;
      }
      
      case "get_customer_print_link":
      {
         $jobNumber = $_GET["jobNumber"];
         
         $database = new PPTPDatabase();
         
         $database->connect();
         
         if ($database->isConnected())
         {
            $customerPrint = null;

            $result = $database->getJobsByJobNumber($jobNumber);
            
            while ($result && ($row = $result->fetch_assoc()))
            {
               $jobInfo = JobInfo::load($row["jobId"]);
               
               if (($jobInfo) && ($jobInfo->customerPrint))
               {
                  $customerPrint = $jobInfo->customerPrint;
                  break;
               }
            }
            
            if ($customerPrint)
            {
               echo "<a href=\"$ROOT/uploads/$customerPrint\" target=\"_blank\">$customerPrint</a>";
            }
         }
         break;
      }
   }
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
                     <div id="customer-print-div"></div>
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">WC Number</div>
                     <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" <?php echo !isEditable(InspectionInputField::WC_NUMBER) ? "disabled" : ""; ?>>
                        <?php echo getWcNumberOptions(); ?>
                     </select>
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">Operator</div>
                     <select id="operator-input" class="form-input-medium" name="operator" form="input-form" $disabled>
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
         var jobNumberValidator = new SelectValidator("job-number-input");

         jobNumberValidator.init();
      </script>
     
   </div>

</body>

</html>