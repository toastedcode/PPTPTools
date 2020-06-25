<?php

require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/panTicket.php';
require_once '../common/params.php';
require_once '../common/partWeightEntry.php';
require_once '../common/timeCardInfo.php';
require_once '../common/userInfo.php';

const ONLY_ACTIVE = true;

abstract class PartWeightLogInputField
{
   const FIRST = 0;
   const TIME_CARD_ID = PartWeightLogInputField::FIRST;
   const JOB_NUMBER = 1;
   const WC_NUMBER = 2;
   const MANUFACTURE_DATE = 3;
   const OPERATOR = 4;
   const WEIGH_DATE = 5;
   const LABORER = 6;
   const PAN_COUNT = 7;
   const PART_WEIGHT = 8;
   const PAN_WEIGHT = 9;
   const PALLET_WEIGHT = 10;
   const PART_COUNT = 11;
   const LAST = 12;
   const COUNT = PartWeightLogInputField::LAST - PartWeightLogInputField::FIRST;
}

abstract class View
{
   const NEW_PART_WEIGHT_ENTRY = 0;
   const VIEW_PART_WEIGHT_ENTRY = 1;
   const EDIT_PART_WEIGHT_ENTRY = 2;
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
   $view = View::VIEW_PART_WEIGHT_ENTRY;
   
   if (getEntryId() == PartWeightEntry::UNKNOWN_ENTRY_ID)
   {
      $view = View::NEW_PART_WEIGHT_ENTRY;
   }
   else if (Authentication::checkPermissions(Permission::EDIT_PART_WEIGHT_LOG))
   {
      $view = View::EDIT_PART_WEIGHT_ENTRY;
   }
   
   return ($view);
}

function getPartWeightEntry()
{
   static $partWeightEntry = null;
   
   if ($partWeightEntry == null)
   {
      $params = Params::parse();

      if ($params->keyExists("entryId"))
      {
         $partWeightEntry = PartWeightEntry::load($params->get("entryId"));
      }
   }
   
   return ($partWeightEntry);
}

function getEntryId()
{
   $entryId = PartWeightEntry::UNKNOWN_ENTRY_ID;
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {
      $entryId = $partWeightEntry->partWeightEntryId;
   }
   
   return ($entryId);
}

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   
   $view = getView();
   
   if (($view == View::NEW_PART_WEIGHT_ENTRY) ||
       ($view == View::EDIT_PART_WEIGHT_ENTRY))
   {
      // Case 1
      // Creating a new entry.
      // Editing an existing entry.
      
      $navBar->cancelButton("onCancel();");
      $navBar->highlightNavButton("Save", "onSubmit();", false);
   }
   else if ($view == View::VIEW_PART_WEIGHT_ENTRY)
   {
      // Case 2
      // Viewing an existing entry.
      
      $navBar->highlightNavButton("Ok", "submitForm('input-form', 'partWeightLog.php', 'view_part_weight_log', 'no_action')", false);
   }
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

function isEditable($field)
{
   $view = getView();
   
   // Start with the edit mode, as dictated by the view.
   $isEditable = (($view == View::NEW_PART_WEIGHT_ENTRY) ||
                  ($view == View::EDIT_PART_WEIGHT_ENTRY));
   
   switch ($field)
   {      
      case PartWeightLogInputField::JOB_NUMBER:
      case PartWeightLogInputField::OPERATOR:
      case PartWeightLogInputField::MANUFACTURE_DATE:
      {
         // Edit status disabled by time card ID.
         $isEditable &= (getTimeCardId() == TimeCardInfo::UNKNOWN_TIME_CARD_ID);
         break;
      }
      
      case PartWeightLogInputField::WC_NUMBER:
      {
         // Edit status determined by both time card ID and job number selection.
         $isEditable &= ((getTimeCardId() == TimeCardInfo::UNKNOWN_TIME_CARD_ID) &&
                         (getJobNumber() != JobInfo::UNKNOWN_JOB_NUMBER));         
         break;
      }
      
      case PartWeightLogInputField::WEIGH_DATE:
      {
         // Weigh date is restricted to current date/time.
         $isEditable = false;
         break;
      }
      
      case PartWeightLogInputField::LABORER:
      {
         // Only administrative users can make an entry under another user's name.
         $userInfo = Authentication::getAuthenticatedUser();
         if ($userInfo)
         {
            $isEditable &= (($userInfo->roles == Role::SUPER_USER) ||
                            ($userInfo->roles == Role::ADMIN));
         }
         break;
      }
      
      case PartWeightLogInputField::PAN_WEIGHT:
      case PartWeightLogInputField::PALLET_WEIGHT:
      case PartWeightLogInputField::PART_COUNT:
      {
         $isEditable = false;
      }
      
      case PartWeightLogInputField::TIME_CARD_ID:
      case PartWeightLogInputField::PART_WEIGHT:
      case PartWeightLogInputField::PAN_COUNT:
      default:
      {
         // Edit status based solely on view.
         break;
      }
   }

   return ($isEditable);
}

function getHeading()
{
   $heading = "";
   
   $view = getView();
   
   if ($view == View::NEW_PART_WEIGHT_ENTRY)
   {
      $heading = "Add to the Part Weight Log";
   }
   else if ($view == View::EDIT_PART_WEIGHT_ENTRY)
   {
      $heading = "Update the Part Weight Log";
   }
   else if ($view == View::VIEW_PART_WEIGHT_ENTRY)
   {
      $heading = "View a Part Weight Log Entry";
   }
   
   return ($heading);
}

function getDescription()
{
   $description = "";
   
   $view = getView();
   
   if ($view == View::NEW_PART_WEIGHT_ENTRY)
   {
      $description = "Create a new entry in the part weight log.  Starting with the time card ID is the fastest and most accurate way of entering the required job information, or simply enter the information manually if a time card is not available.";
   }
   else if ($view == View::EDIT_PART_WEIGHT_ENTRY)
   {
      $description = "You may revise any of the fields for this log entry and then select save when you're satisfied with the changes.";
   }
   else if ($view == View::VIEW_PART_WEIGHT_ENTRY)
   {
      $description = "View a previously saved log entry in detail.";
   }
   
   return ($description);
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

function getManufactureDate()
{
   $manufactureDate = null;
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));
      $manufactureDate = $dateTime->format(Time::$javascriptDateFormat);
   }
   else
   {
      $partWeightEntry = getPartWeightEntry();
      
      if ($partWeightEntry)
      {
         $dateTime = new DateTime($partWeightEntry->manufactureDate, new DateTimeZone('America/New_York'));
         $manufactureDate = $dateTime->format(Time::$javascriptDateFormat);         
      }
   }
   
   return ($manufactureDate);
}

function getWeighDate()
{
   $weighDate = Time::now(Time::$javascriptDateFormat);
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {   
      $dateTime = new DateTime($partWeightEntry->dateTime, new DateTimeZone('America/New_York'));
      $weighDate = $dateTime->format(Time::$javascriptDateFormat);
   }
   
   return ($weighDate);
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

function getLaborerOptions()
{
   $options = "<option style=\"display:none\">";
   
   $laborers = PPTPDatabase::getInstance()->getUsersByRole(Role::LABORER);
   
   // Create an array of employee numbers.
   $employeeNumbers = array();
   foreach ($laborers as $laborer)
   {
      $employeeNumbers[] = intval($laborer["employeeNumber"]);
   }
   
   $selectedLaborer = getLaborer();
   
   // Add selected laborer, if not already in the array.
   // Note: This handles the case of viewing an entry with a laborer that is not assigned to the LABORER role.
   if (($selectedLaborer != UserInfo::UNKNOWN_EMPLOYEE_NUMBER) &&
      (!in_array($selectedLaborer, $employeeNumbers)))
   {
      $employeeNumbers[] = $selectedLaborer;
      sort($employeeNumbers);
   }
   
   foreach ($employeeNumbers as $employeeNumber)
   {
      $userInfo = UserInfo::load($employeeNumber);
      if ($userInfo)
      {
         $selected = ($employeeNumber == $selectedLaborer) ? "selected" : "";
         
         $name = $employeeNumber . " - " . $userInfo->getFullName();
         
         $options .= "<option value=\"$employeeNumber\" $selected>$name</option>";
      }
   }
   
   return ($options);
}

function getTimeCardId()
{
   $timeCardId = TimeCardInfo::UNKNOWN_TIME_CARD_ID;
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {
      $timeCardId = $partWeightEntry->timeCardId;
   }
   else
   {
      $params = getParams();
      
      if ($params->keyExists("timeCardId"))
      {
         $timeCardId = $params->getInt("timeCardId");
      }
   }
   
   return ($timeCardId);
}

function getTimeCardInfo()
{
   $timeCardInfo = null;
   
   $timeCardId = getTimeCardId();
   
   if ($timeCardId != TimeCardInfo::UNKNOWN_TIME_CARD_ID)
   {
      $timeCardInfo = TimeCardInfo::load($timeCardId);
   }
   
   return ($timeCardInfo);
}

function getJobId()
{
   $jobId = JobInfo::UNKNOWN_JOB_ID;
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $jobId = $timeCardInfo->jobId;
   }
   else
   {
      $partWeightEntry = getPartWeightEntry();
      
      if ($partWeightEntry)
      {
         $jobId = $partWeightEntry->jobId;
      }
   }
   
   return ($jobId);
}

function getJobNumber()
{
   $jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
   
   $jobId = getJobId();
   
   $jobInfo = JobInfo::load($jobId);
   
   if ($jobInfo)
   {
      $jobNumber = $jobInfo->jobNumber;
   }
   
   return ($jobNumber);
}

function getWcNumber()
{
   $wcNumber = JobInfo::UNKNOWN_WC_NUMBER;
   
   $jobId = getJobId();
   
   $jobInfo = JobInfo::load($jobId);
   
   if ($jobInfo)
   {
      $wcNumber = $jobInfo->wcNumber;
   }
   
   return ($wcNumber);
}

function getSampleWeight()
{
   $sampleWeight = 0.0;
   
   $jobId = getJobId();
   
   $jobInfo = JobInfo::load($jobId);
   
   if ($jobInfo)
   {
      $sampleWeight = $jobInfo->sampleWeight;
   }
   
   return ($sampleWeight);
}

function getOperator()
{
   $operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $operator = $timeCardInfo->employeeNumber;
   }
   else
   {
      $partWeightEntry = getPartWeightEntry();
      
      if ($partWeightEntry)
      {
         $operator = $partWeightEntry->operator;
      }
   }
   
   return ($operator);
}

function getLaborer()
{
   $laborer = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {
      $laborer = $partWeightEntry->employeeNumber;
   }
   else
   {
      $userInfo = Authentication::getAuthenticatedUser();
      
      if ($userInfo)
      {
         $laborer = $userInfo->employeeNumber;
      }
   }
   
   return ($laborer);
}

function getPanCount()
{
   $panCount = 0;
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {
      $panCount = $partWeightEntry->panCount;
   }
   
   return ($panCount);
}

function getPartWeight()
{
   $partWeight = 0;
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {
      $partWeight = $partWeightEntry->weight;
   }
   
   return ($partWeight);
}

function getPanWeight()
{
   $panWeight = PartWeightEntry::STANDARD_PAN_WEIGHT;
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {
      $panWeight = $partWeightEntry->panWeight;
   }
   
   return ($panWeight);
}

function getPalletWeight()
{
   $palletWeight = PartWeightEntry::STANDARD_PALLET_WEIGHT;
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {
      $palletWeight = $partWeightEntry->palletWeight;
   }
   
   return ($palletWeight);
}

function getCalculatedPartCount()
{
   $partCount = "";
   
   $partWeightEntry = getPartWeightEntry();
   
   if ($partWeightEntry)
   {
      $partCount = $partWeightEntry->calculatePartCount();
   }
   
   return ($partCount);
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
   <link rel="stylesheet" type="text/css" href="partWeightLog.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="partWeightLog.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <form id="input-form" action="" method="POST">
         <input id="entry-id-input" type="hidden" name="entryId" value="<?php echo getEntryId(); ?>">
         <input type="hidden" name="laborer" value="<?php echo getLaborer(); ?>">
         <input type="hidden" name="weighDate" value="<?php echo getWeighDate(); ?>">
      </form>
      
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
         
         <div class="pptp-form">
            <div class="form-row">
            <div class="form-col" style="margin-right: 20px;">  
               <div class="form-section-header">Pan Ticket Entry</div>               
               <div class="form-item">
                  <div class="form-label">Pan Ticket #</div>
                  <input id="pan-ticket-code-input" class="form-input-medium" type="text" style="width:50px;" name="panTicketCode" form="input-form" oninput="onTouched(); this.validator.validate(); onPanTicketCodeChange()" value="<?php $timeCardId = getTimeCardId(); echo ($timeCardId == 0) ? "" : PanTicket::getPanTicketCode($timeCardId);?>" <?php echo !isEditable(PartWeightLogInputField::TIME_CARD_ID) ? "disabled" : ""; ?>>
               </div>               
            
               <div class="form-section-header">Manual Entry</div>
               <div class="form-item">
                  <div class="form-label">Job Number</div>
                  <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" oninput="onTouched(); this.validator.validate(); onJobNumberChange();" <?php echo !isEditable(PartWeightLogInputField::JOB_NUMBER) ? "disabled" : ""; ?>>
                     <?php echo getJobNumberOptions(); ?>
                  </select>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Work Center</div>
                  <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" oninput="onTouched(); this.validator.validate(); onWcNumberChange();" <?php echo !isEditable(PartWeightLogInputField::WC_NUMBER) ? "disabled" : ""; ?>>
                     <?php echo getWcNumberOptions(); ?>
                  </select>
               </div>
               
               <div class="flex-horizontal">
                  <div class="form-item">
                     <div class="form-label">Manufacture Date</div>
                     <div class="flex-horizontal">
                        <input id="manufacture-date-input" class="form-input-medium" type="date" name="manufactureDate" form="input-form" oninput="onTouched(); " value="<?php echo getManufactureDate(); ?>" <?php echo !isEditable(PartWeightLogInputField::MANUFACTURE_DATE) ? "disabled" : ""; ?>>
                        &nbsp<button id="today-button" form="" onclick="onTodayButton()" <?php echo !isEditable(PartWeightLogInputField::MANUFACTURE_DATE) ? "disabled" : ""; ?>>Today</button>
                        &nbsp<button id="yesterday-button" form="" onclick="onYesterdayButton()" <?php echo !isEditable(PartWeightLogInputField::MANUFACTURE_DATE) ? "disabled" : ""; ?>>Yesterday</button>
                     </div>
                  </div>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Operator</div>
                  <select id="operator-input" class="form-input-medium" name="operator" form="input-form" oninput="onTouched(); this.validator.validate();" <?php echo !isEditable(PartWeightLogInputField::OPERATOR) ? "disabled" : ""; ?>>
                     <?php echo getOperatorOptions(); ?>
                  </select>
               </div>
            </div>
            
            <div class="form-col">
               <!--  Purely for display -->
               <div class="form-item">
                  <div class="form-label">Weigh Date</div>
                  <input class="form-input-medium" type="date" value="<?php echo getWeighDate(); ?>" <?php echo !isEditable(PartWeightLogInputField::WEIGH_DATE) ? "disabled" : ""; ?>>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Laborer</div>
                  <select id="laborer-input" class="form-input-medium" name="laborer" form="input-form" oninput="onTouched(); this.validator.validate();" <?php echo !isEditable(PartWeightLogInputField::LABORER) ? "disabled" : ""; ?>>
                     <?php echo getLaborerOptions(); ?>
                  </select>
               </div>               
                              
               <div class="form-item">
                  <div class="form-label">Basket Count</div>
                  <input id="pan-count-input" class="form-input-medium" type="number" name="panCount" form="input-form" oninput="onTouched(); this.validator.validate(); updateCalculatedPartCount();" value="<?php echo getPanCount(); ?>" <?php echo !isEditable(PartWeightLogInputField::PAN_COUNT) ? "disabled" : ""; ?>>
               </div>
                           
               <div class="form-item">
                  <div class="form-label">Part Weight</div>
                  <input id="part-weight-input" class="form-input-medium" type="number" name="partWeight" form="input-form" oninput="onTouched(); this.validator.validate(); updateCalculatedPartCount();" value="<?php echo getPartWeight(); ?>" <?php echo !isEditable(PartWeightLogInputField::PART_WEIGHT) ? "disabled" : ""; ?>>&nbsp;lbs
               </div>
               
               <div class="form-item">
                  <div class="form-label">Pan Weight</div>
                  <input id="pan-weight-input" class="form-input-medium" type="number" style="width:50px;" name="panWeight" form="input-form" oninput="onTouched();" value="<?php echo getPanWeight(); ?>" <?php echo !isEditable(PartWeightLogInputField::PAN_WEIGHT) ? "disabled" : ""; ?>>&nbsp;lbs
               </div>
               
               <div class="form-item">
                  <div class="form-label">Pallet Weight</div>
                  <input id="pallet-weight-input" class="form-input-medium" type="number" style="width:50px;" name="palletWeight" form="input-form" oninput="onTouched();"value="<?php echo getPalletWeight(); ?>" <?php echo !isEditable(PartWeightLogInputField::PALLET_WEIGHT) ? "disabled" : ""; ?>>&nbsp;lbs
               </div>
               
               <div class="form-item">
                  <div class="form-label">Estimated Part Count</div>
                  <input id="part-count-input" class="form-input-medium" type="number" style="width:100px;" value="<?php echo getCalculatedPartCount(); ?>" <?php echo !isEditable(PartWeightLogInputField::PART_COUNT) ? "disabled" : ""; ?>>
               </div>
               
            </div>
            </div>
                     
         </div>
         
         <?php echo getNavBar(); ?>
         
      </div>
      
      <script>
         preserveSession();
         
         var panTicketCodeValidator = new HexValidator("pan-ticket-code-input", 4, 1, 65536, true);
         var jobNumberValidator = new SelectValidator("job-number-input");
         var wcNumberValidator = new SelectValidator("wc-number-input");
         var operatorValidator = new SelectValidator("operator-input");
         var laborerValidator = new SelectValidator("laborer-input");
         var panCountValidator = new IntValidator("pan-count-input", 2, 1, 40, false);
         var partWeightValidator = new DecimalValidator("part-weight-input", 7, 1, 10000, 2, false);

         panTicketCodeValidator.init();
         jobNumberValidator.init();
         wcNumberValidator.init();
         operatorValidator.init();
         laborerValidator.init();
         panCountValidator.init();
         partWeightValidator.init();

         var sampleWeight = <?php echo getSampleWeight(); ?>;
      </script>
     
   </div>

</body>

</html>