<?php

require_once '../common/commentCodes.php';
require_once '../common/header.php';
require_once '../common/userInfo.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/params.php';
require_once '../common/timeCardInfo.php';

const ONLY_ACTIVE = true;

abstract class TimeCardInputField
{
   const FIRST = 0;
   const MANUFACTURE_DATE = PartWasherLogInputField::FIRST;
   const MANUFACTURE_TIME = 1;
   const OPERATOR = 2;
   const JOB_NUMBER = 3;
   const WC_NUMBER = 4;
   const MATERIAL_NUMBER = 5;
   const RUN_TIME = 6;
   const SETUP_TIME = 7;
   const PAN_COUNT = 8;
   const PART_COUNT = 9;
   const SCRAP_COUNT = 10;
   const COMMENTS = 11;
   const LAST = 12;
   const COUNT = PartWasherLogInputField::LAST - PartWasherLogInputField::FIRST;
}

abstract class View
{
   const NEW_TIME_CARD = 0;
   const VIEW_TIME_CARD = 1;
   const EDIT_TIME_CARD = 2;
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
   $view = View::VIEW_TIME_CARD;
   
   if (getTimeCardId() == TimeCardInfo::UNKNOWN_TIME_CARD_ID)
   {
      $view = View::NEW_TIME_CARD;
   }
   else if (Authentication::checkPermissions(Permission::EDIT_TIME_CARD))
   {
      $view = View::EDIT_TIME_CARD;
   }
   
   return ($view);
}

function isEditable($field)
{
   $view = getView();
   
   // Start with the edit mode, as dictated by the view.
   $isEditable = (($view == View::NEW_TIME_CARD) ||
                  ($view == View::EDIT_TIME_CARD));
   
   switch ($field)
   {         
      case TimeCardInputField::OPERATOR:
      {
         $isEditable = ((Authentication::getAuthenticatedUser()->roles == Role::ADMIN) ||
                        (Authentication::getAuthenticatedUser()->roles == Role::SUPER_USER));
         break;
      }
         
      case TimeCardInputField::WC_NUMBER:
      {
         // Edit status determined by job number selection.
         $isEditable &= (getJobNumber() != JobInfo::UNKNOWN_JOB_NUMBER);
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

function getTimeCardId()
{
   $timeCardId = TimeCardInfo::UNKNOWN_TIME_CARD_ID;
   
   $params = getParams();
   
   if ($params->keyExists("timeCardId"))
   {
      $timeCardId = $params->getInt("timeCardId");
   }
   
   return ($timeCardId);
}

function getTimeCardInfo()
{
   static $timeCardInfo = null;
   
   if ($timeCardInfo == null)
   {
      $params = Params::parse();
      
      if ($params->keyExists("timeCardId"))
      {
         $timeCardInfo = TimeCardInfo::load($params->get("timeCardId"));
      }
      else
      {
         $timeCardInfo = new TimeCardInfo();
         
         $timeCardInfo->employeeNumber = Authentication::getAuthenticatedUser()->employeeNumber;
      }
   }
   
   return ($timeCardInfo);
}

function getOperator()
{
   $operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $operator = $timeCardInfo->employeeNumber;
   }
   
   return ($operator);
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
   
   // Add selected operator, if not already in the array.
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

function getCommentCodesDiv()
{
   $timeCardInfo = getTimeCardInfo();
   
   $disabled = !isEditable(TimeCardInputField::COMMENTS) ? "disabled" : "";
   
   $commentCodes = CommentCode::getCommentCodes();
   
   $leftColumn = "";
   $rightColumn = "";
   $index = 0;
   foreach ($commentCodes as $commentCode)
   {
      $id = "code-" . $commentCode->code . "-input";
      $name = "code-" . $commentCode->code;
      $checked = ($timeCardInfo->hasCommentCode($commentCode->code) ? "checked" : "");
      $description = $commentCode->description;
      
      $codeDiv =
<<< HEREDOC
      <div class="form-item">
         <input id="$id" type="checkbox" class="comment-checkbox" form="input-form" name="$name" $checked $disabled/>
         <label for="$id" class="form-input-medium">$description</label>
      </div>
HEREDOC;
      
      if (($index % 2) == 0)
      {
         $leftColumn .= $codeDiv;
      }
      else
      {
         $rightColumn .= $codeDiv;
      }
      
      $index++;
   }
   
   $html =
<<<HEREDOC
   <input type="hidden" form="input-form" name="commentCodes" value="true"/>
   <div class="form-col">
      <div class="form-section-header">Codes</div>
      <div class="form-row">
         <div class="form-col">
            $leftColumn
         </div>
         <div class="form-col">
            $rightColumn
         </div>
      </div>
   </div>
HEREDOC;
   
   return ($html);
}

function canApprove()
{
   return (Authentication::checkPermissions(Permission::APPROVE_TIME_CARDS));
}

function getApprovalButton()
{
   $html = "";
   
   $approvingUser = Authentication::getAuthenticatedUser();
      
   $html =
<<<HEREDOC
   <button id="approve-button" class="approval" onclick="approve($approvingUser->employeeNumber)" style="width: 100px;">Approve</button>
HEREDOC;
      
   return ($html);
}

function getUnapprovalButton()
{
   $html = "";
   
   $approvingUser = Authentication::getAuthenticatedUser();
      
   $html =
<<<HEREDOC
   <button id="unapprove-button" class="approval" onclick="unapprove($approvingUser->employeeNumber)" style="width: 100px;">Unapprove</button>
HEREDOC;
   
   return ($html);
}

function getApprovalText()
{
   $approvalText = "";
   
   $timeCardInfo = getTimeCardInfo();
   
   if (($timeCardInfo->requiresApproval()) &&
       ($timeCardInfo->isApproved()))
   {
      $approvalText = "Approved by supervisor";

      $userInfo = UserInfo::load($timeCardInfo->approvedBy);
      
      if ($userInfo)
      {
         $approvalText = "Approved by " . $userInfo->getFullName();
      }
   }
}

function getJobNumber()
{
   $jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $jobId = $timeCardInfo->jobId;
      
      $jobInfo = JobInfo::load($jobId);
      
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
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $jobId = $timeCardInfo->jobId;
      
      $jobInfo = JobInfo::load($jobId);
      
      if ($jobInfo)
      {
         $wcNumber = $jobInfo->wcNumber;
      }
   }
   
   return ($wcNumber);
}

function getMaterialNumber()
{
   $materialNumber = 0;
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $materialNumber = $timeCardInfo->materialNumber;
   }
   
   return ($materialNumber);
}

function getGrossPartsPerHour()
{
   $grossPartsPerHour = 0;
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $jobId = $timeCardInfo->jobId;
      
      $jobInfo = JobInfo::load($jobId);
      
      if ($jobInfo)
      {
         $grossPartsPerHour = $jobInfo->getGrossPartsPerHour();
      }
   }
   
   return ($grossPartsPerHour);
}

function getHeading()
{
   $heading = "";
   
   switch (getView())
   {
      case View::NEW_TIME_CARD:
      {
         $heading = "Create a New Time Card";
         break;
      }
      
      case View::EDIT_TIME_CARD:
      {
         $heading = "Update a Time Card";
         break;
      }
      
      case View::VIEW_TIME_CARD:
      default:
      {
         $heading = "View a Time Card";
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
      case View::NEW_TIME_CARD:
      {
         $description = "Enter all required fields for your time card.  Once you're satisfied, click Save below to add this time card to the system.";
         break;
      }
         
      case View::EDIT_TIME_CARD:
      {
         $description = "You may revise any of the fields for this time card and then select save when you're satisfied with the changes.";
         break;
      }
         
      case View::VIEW_TIME_CARD:
      default:
      {
         $description = "View a previously saved time card in detail.";
         break;
      }
   }
   
   return ($description);
}

function getManufactureDate()
{
   $mfgDate = Time::now(Time::$javascriptDateFormat);
   
   if (getView() != View::NEW_TIME_CARD)
   {
      $timeCardInfo = getTimeCardInfo();
      
      if ($timeCardInfo)
      {
         $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));
         $mfgDate = $dateTime->format(Time::$javascriptDateFormat);
      }
   }
   
   return ($mfgDate);
}

function getManufactureTime()
{
   $mfgTime = Time::now(Time::$javascriptTimeFormat);
   
   if (getView() != View::NEW_TIME_CARD)
   {
      $timeCardInfo = getTimeCardInfo();
      
      if ($timeCardInfo)
      {
         $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));
         $mfgTime = $dateTime->format(Time::$javascriptTimeFormat);
      }
   }
   
   return ($mfgTime);
}

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   
   $view = getView();
   
   if (($view == View::NEW_TIME_CARD) ||
       ($view == View::EDIT_TIME_CARD))
   {
      // Case 1
      // Creating a new time card.
      // Editing an existing time card.
      
      $navBar->cancelButton("window.history.back();");
      $navBar->highlightNavButton("Save", "onSubmit();", false);
   }
   else if ($view == View::VIEW_TIME_CARD)
   {
      // Case 2
      // Viewing an existing entry.
      
      $navBar->highlightNavButton("Ok", "location.href = 'viewTimeCards.php'", false);
   }
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

// *****************************************************************************

header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

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
   <link rel="stylesheet" type="text/css" href="timeCard.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="timeCard.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <form id="input-form" action="" method="POST">
         <input id="time-card-id-input" type="hidden" name="timeCardId" value="<?php echo getTimeCardId(); ?>">
         <input type="hidden" name="operator" value="<?php echo getOperator(); ?>">
         <input id="approved-by-input" type="hidden" form="input-form" name="approvedBy" value="<?php echo getTimeCardInfo()->approvedBy; ?>" />
         <input id="approved-date-time-input" type="hidden" form="input-form" name="approvedDateTime" value="<?php echo getTimeCardInfo()->approvedDateTime; ?>" />
         <input id="run-time-input" type="hidden" name="runTime" value="<?php echo getTimeCardInfo()->runTime; ?>">
         <input id="setup-time-input" type="hidden" name="setupTime" value="<?php echo getTimeCardInfo()->setupTime; ?>">
         <input id="gross-parts-per-hour-input" type="hidden" value="<?php echo getGrossPartsPerHour(); ?>">
      </form>
      
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
         
          <div class="flex-horizontal inner-content" style="justify-content: flex-start; flex-wrap: wrap;">

            <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">
            
               <div class="form-item">
                  <div class="form-label">Mfg. Date</div>
                  <input type="date" class="form-input-medium" name="date" value="<?php echo getManufactureDate(); ?>" disabled>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Mfg. Time</div>
                  <input type="time" class="form-input-medium" name="time" value="<?php echo getManufactureTime(); ?>" disabled>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Operator</div>
                  <select id="operator-input" class="form-input-medium" name="operator" form="input-form" oninput="this.validator.validate();" <?php echo !isEditable(TimeCardInputField::OPERATOR) ? "disabled" : ""; ?>>
                     <?php echo getOperatorOptions(); ?>
                  </select>
               </div>
         
               <div class="form-col">         
                  <div class="form-section-header">Job</div>         
                  <div class="form-item">
                     <div class="form-label">Job Number</div>
                     <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" oninput="this.validator.validate(); onJobNumberChange();" <?php echo !isEditable(TimeCardInputField::JOB_NUMBER) ? "disabled" : ""; ?>>
                        <?php echo getJobNumberOptions(); ?>
                     </select>
                  </div>       
                  <div class="form-item">
                     <div class="form-label">Work Center</div>
                     <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" oninput="this.validator.validate(); onWcNumberChange();" <?php echo !isEditable(TimeCardInputField::WC_NUMBER) ? "disabled" : ""; ?>>
                        <?php echo getWcNumberOptions(); ?>
                     </select>
                  </div>       
                  <div class="form-item">
                     <div class="form-label">Heat #</div>
                     <input id="material-number-input" type="number" class="form-input-medium" form="input-form" name="materialNumber" style="width:100px;" oninput="this.validator.validate()" value="<?php echo getMaterialNumber(); ?>" <?php echo !isEditable(TimeCardInputField::MATERIAL_NUMBER) ? "disabled" : ""; ?>>
                  </div>         
               </div>
      
            </div>
            
            <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">
            
               <div class="form-col">
               
                  <div class="form-section-header">Time</div>
                  
                  <div class="form-item">
                     <div class="form-label">Run time</div>
                     <input id="run-time-hour-input" type="number" class="form-input-medium" form="input-form" name="runTimeHours" style="width:50px;" oninput="this.validator.validate(); onRunTimeChange();" value="<?php echo getTimeCardInfo()->getRunTimeHours(); ?>" <?php echo !isEditable(TimeCardInputField::RUN_TIME) ? "disabled" : ""; ?> />
                     <div style="padding: 5px;">:</div>
                     <input id="run-time-minute-input" type="number" class="form-input-medium" form="input-form" name="runTimeMinutes" style="width:50px;" oninput="this.validator.validate(); onRunTimeChange();" value="<?php echo getTimeCardInfo()->getRunTimeMinutes(); ?>" step="15" <?php echo !isEditable(TimeCardInputField::RUN_TIME) ? "disabled" : ""; ?> />
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">Setup time</div>
                     <div class="form-col">
                        <div class="form-row" style="justify-content:flex-start">
                           <input id="setup-time-hour-input" type="number" class="form-input-medium $approval" form="input-form" name="setupTimeHours" style="width:50px;" oninput="this.validator.validate(); onSetupTimeChange();" value="<?php echo getTimeCardInfo()->getSetupTimeHours(); ?>" <?php echo !isEditable(TimeCardInputField::SETUP_TIME) ? "disabled" : ""; ?> />
                           <div style="padding: 5px;">:</div>
                           <input id="setup-time-minute-input" type="number" class="form-input-medium $approval" form="input-form" name="setupTimeMinutes" style="width:50px;" oninput="this.validator.validate(); onSetupTimeChange();" value="<?php echo getTimeCardInfo()->getSetupTimeMinutes(); ?>" step="15" <?php echo !isEditable(TimeCardInputField::SETUP_TIME) ? "disabled" : ""; ?> />
                           <?php echo getApprovalButton(); ?>
                           <?php echo getUnapprovalButton(); ?>
                        </div>
                        <div id="approved-text" class="approved">Approved by supervisor.</div>
                        <div id="unapproved-text" class="unapproved">Requires approval by supervisor.</div>
                     </div>
                  </div>
                  
               </div>
               
               <div class="form-col">
                  
                  <div class="form-section-header">Part Counts</div>
                  
                  <div class="form-item">
                     <div class="form-label">Basket count</div>
                     <input id="pan-count-input" type="number" class="form-input-medium" form="input-form" name="panCount" style="width:100px;" oninput="panCountValidator.validate()" value="<?php echo getTimeCardInfo()->panCount; ?>" <?php echo !isEditable(TimeCardInputField::PAN_COUNT) ? "disabled" : ""; ?> />
                  </div>
            
                  <div class="form-item">
                     <div class="form-label">Good count</div>
                     <input id="part-count-input" type="number" class="form-input-medium" form="input-form" name="partCount" style="width:100px;" oninput="partsCountValidator.validate(); onPartCountChange();" value="<?php echo getTimeCardInfo()->partCount; ?>" <?php echo !isEditable(TimeCardInputField::PART_COUNT) ? "disabled" : ""; ?> />
                  </div>
            
                  <div class="form-item">
                     <div class="form-label">Scrap count</div>
                     <input id="scrap-count-input" type="number" class="form-input-medium" form="input-form" name="scrapCount" style="width:100px;" oninput="scrapCountValidator.validate()" value="<?php echo getTimeCardInfo()->scrapCount; ?>" <?php echo !isEditable(TimeCardInputField::SCRAP_COUNT) ? "disabled" : ""; ?> />
                  </div>
            
                  <div class="form-item">
                     <div class="form-label">Efficiency</div>
                     <input id="efficiency-input" type="number" class="form-input-medium" style="width:100px;" value="" disabled />
                     <div>&nbsp%</div>
                  </div>
            
               </div>
               
            </div>
            
            <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">
            
               <?php echo getCommentCodesDiv(); ?>
               
               <div class="form-col">
                  <div class="form-section-header">Comments</div>
                  <div class="form-item">
                     <textarea form="input-form" class="comments-input" type="text" form="input-form" name="comments" rows="4" maxlength="256" style="width:300px" <?php echo !isEditable(TimeCardInputField::COMMENTS) ? "disabled" : ""; ?>><?php echo getTimeCardInfo()->comments; ?></textarea>
                  </div>
               </div>
               
            </div>

         </div>
         
         <?php echo getNavBar(); ?>
         
      </div>
      
      <script>
         preserveSession();
         
         function userCanApprove()
         {
            return (<?php echo canApprove() ? "true" : "false"; ?>);
         }
         
         var operatorValidator = new SelectValidator("operator-input");
         var jobNumberValidator = new SelectValidator("job-number-input");
         var wcNumberValidator = new SelectValidator("wc-number-input");
         var materialNumberValidator = new IntValidator("material-number-input", 4, 1, 9999, false);
         var runTimeHourValidator = new IntValidator("run-time-hour-input", 2, 0, 16, true);
         var runTimeMinuteValidator = new IntValidator("run-time-minute-input", 2, 0, 59, true);
         var setupTimeHourValidator = new IntValidator("setup-time-hour-input", 2, 0, 16, true);
         var setupTimeMinuteValidator = new IntValidator("setup-time-minute-input", 2, 0, 59, true);
         var panCountValidator = new IntValidator("pan-count-input", 2, 0, 40, true);
         var partsCountValidator = new IntValidator("part-count-input", 6, 0, 100000, true);
         var scrapCountValidator = new IntValidator("scrap-count-input", 6, 0, 100000, true);

         operatorValidator.init();
         jobNumberValidator.init();
         wcNumberValidator.init();
         materialNumberValidator.init();
         runTimeHourValidator.init();
         runTimeMinuteValidator.init();
         setupTimeHourValidator.init();
         setupTimeMinuteValidator.init();
         panCountValidator.init();
         partsCountValidator.init();
         scrapCountValidator.init();
   
         updateEfficiency();
         updateApproval();
      </script>
     
   </div>

</body>

</html>