<?php

require_once '../common/commentCodes.php';
require_once '../common/header.php';
require_once '../common/userInfo.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/params.php';
require_once '../common/timeCardInfo.php';
//require_once 'enterComments.php';

require_once $_SERVER["DOCUMENT_ROOT"] . "/phpqrcode/phpqrcode.php";

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
   
   return ($params->keyExists("view") ? $params->get("view") : "");
}

function isEditable($field)
{
   $view = getView();
   
   // Start with the edit mode, as dictated by the view.
   $isEditable = (($view == "new_time_card") ||
                  ($view == "edit_time_card"));
   
   switch ($field)
   {         
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
      }
   }
   
   return ($timeCardInfo);
}

function getOperatorName()
{
   return ("");
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

function getApprovalButton()
{
   $html = "";
   
   $timeCardInfo = getTimeCardInfo();
   
   $approval = "no-approval-required";
   if ($timeCardInfo->requiresApproval())
   {
      if ($timeCardInfo->isApproved())
      {
         $approval = "approved";
         
      }
      else
      {
         $approval = "unapproved";
      }
   }

   if (Authentication::checkPermissions(Permission::APPROVE_TIME_CARDS))
   {
      $approvingUser = Authentication::getAuthenticatedUser();
      
      $approvalButton =
<<<HEREDOC
      <button id="approve-button" class="unapproval $approval" onclick="approve($approvingUser->employeeNumber)" style="width: 100px;">Approve</button>
HEREDOC;
   }
      
   return ($html);
}

function getUnapprovalButton()
{
   $html = "";
   
   $timeCardInfo = getTimeCardInfo();
   
   $approval = "no-approval-required";
   if ($timeCardInfo->requiresApproval())
   {
      if ($timeCardInfo->isApproved())
      {
         $approval = "approved";
         
      }
      else
      {
         $approval = "unapproved";
      }
   }
   
   if (Authentication::checkPermissions(Permission::APPROVE_TIME_CARDS))
   {
      $approvingUser = Authentication::getAuthenticatedUser();
      
      $unapprovalButton =
<<<HEREDOC
      <button id="unapprove-button" class="approval $approval" onclick="unapprove()" style="width: 100px;">Unapprove</button>
HEREDOC;
   }
   
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
   
   $view = getView();
   
   if ($view == "new_time_card")
   {
      $heading = "Create a New Time Card";
   }
   else if ($view == "edit_time_card")
   {
      $heading = "Update a Time Card";
   }
   else if ($view == "view_time_card")
   {
      $heading = "View a Time Card";
   }

   return ($heading);
}

function getDescription()
{
   $description = "";
   
   $view = getView();
   
   if ($view == "new_time_card")
   {
      $description = "Enter all required fields for your time card.  Once you're satisfied, click Save below to add this time card to the system.";
   }
   else if ($view == "edit_time_card")
   {
      $description = "You may revise any of the fields for this time card and then select save when you're satisfied with the changes.";
   }
   else if ($view == "view_time_card")
   {
      $description = "View a previously saved time card in detail.";
   }
   
   return ($description);
}

function getManufactureDate()
{
   $mfgDate = Time::now(Time::$javascriptDateFormat);
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));
      $mfgDate = $dateTime->format(Time::$javascriptDateFormat);
   }
   
   return ($mfgDate);
}

function getManufactureTime()
{
   $mfgTime = Time::now(Time::$javascriptTimeFormat);
   
   $timeCardInfo = getTimeCardInfo();
   
   if ($timeCardInfo)
   {
      $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));
      $mfgTime = $dateTime->format(Time::$javascriptTimeFormat);
   }
   
   return ($mfgTime);
}

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   
   $view = getView();
   
   if (($view == "new_time_card") ||
       ($view == "edit_time_card"))
   {
      // Case 1
      // Creating a new time card.
      // Editing an existing time card.
      
      $navBar->cancelButton("submitForm('input-form', 'viewTimeCards.php', '', '')");
      $navBar->highlightNavButton("Save", "onSubmit();", false);
   }
   else if ($view == "view_time_card")
   {
      // Case 2
      // Viewing an existing entry.
      
      $navBar->highlightNavButton("Ok", "submitForm('input-form', 'viewTimeCards.php', '', '')", false);
   }
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

/*
class ViewTimeCard
{
   public static function getHtml($view)
   {
      $html = "";
      
      $timeCardInfo = ViewTimeCard::getTimeCardInfo();
      
      $readOnly = (($view == "view_time_card") || ($view == "use_time_card"));

      $headingDiv = ViewTimeCard::headingDiv($timeCardInfo, $view);
      $descriptionDiv = ViewTimeCard::descriptionDiv($timeCardInfo, $view);
      $dateDiv = ViewTimeCard::dateDiv($timeCardInfo);
      $operatorDiv = ViewTimeCard::operatorDiv($timeCardInfo);
      $jobDiv = ViewTimeCard::jobDiv($timeCardInfo, $readOnly);
      $timeDiv = ViewTimeCard::timeDiv($timeCardInfo, $readOnly);
      $partsDiv = ViewTimeCard::partsDiv($timeCardInfo, $readOnly);
      $commentsDiv = ViewTimeCard::commentsDiv($timeCardInfo, $readOnly);
      $commentCodesDiv = ViewTimeCard::commentCodesDiv($timeCardInfo, $readOnly);
      
      $navBar = ViewTimeCard::navBar($timeCardInfo, $view);
      
      $html =
<<<HEREDOC
      <form id="input-form" action="" method="POST">
         <input type="hidden" name="timeCardId" value="$timeCardInfo->timeCardId"/>
      </form>

      <div class="flex-vertical content">

         $headingDiv

         $descriptionDiv

         <div class="flex-horizontal inner-content" style="justify-content: flex-start; flex-wrap: wrap;">

            <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">
               $dateDiv
               $operatorDiv
               $jobDiv
            </div>
            <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">
               $timeDiv
               $partsDiv
            </div>
            <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">
               $commentCodesDiv
               $commentsDiv
            </div>

         </div>
         
         $navBar
         
      </div>

      <script>
         var materialNumberValidator = new IntValidator("material-number-input", 4, 1, 9999, false);
         var runTimeHourValidator = new IntValidator("runTimeHour-input", 2, 0, 10, false);
         var runTimeMinuteValidator = new IntValidator("runTimeMinute-input", 2, 0, 59, false);
         var setupTimeHourValidator = new IntValidator("setupTimeHour-input", 2, 0, 10, false);
         var setupTimeMinuteValidator = new IntValidator("setupTimeMinute-input", 2, 0, 59, false);
         var panCountValidator = new IntValidator("panCount-input", 2, 1, 40, false);
         var partsCountValidator = new IntValidator("partsCount-input", 6, 0, 100000, true);
         var scrapCountValidator = new IntValidator("scrapCount-input", 6, 0, 100000, true);

         materialNumberValidator.init();
         runTimeHourValidator.init();
         runTimeMinuteValidator.init();
         setupTimeHourValidator.init();
         setupTimeMinuteValidator.init();
         panCountValidator.init();
         partsCountValidator.init();
         scrapCountValidator.init();

         autoFillEfficiency();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render($readOnly)
   {
      echo (ViewTimeCard::getHtml($readOnly));
   }
   
   protected static function titleDiv()
   {
      $html =
<<<HEREDOC
      <div class="form-title">Time Card</div>
HEREDOC;

      return ($html);
   }
   
   protected static function headingDiv($timeCardInfo, $view)
   {
      $heading = "";
      if ($view == "edit_time_card")
      {
         if ($timeCardInfo->timeCardId == 0)
         {
            $heading = "Review Your New Time Card";
         }
         else
         {
            $heading = "Update a Time Card";
         }
      }
      else if ($view == "view_time_card")
      {
         $heading = "View a Time Card";
      }
      
      $html =
      <<<HEREDOC
      <div class="heading">$heading</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function descriptionDiv($timeCardInfo, $view)
   {
      $description = "";
      if ($view == "edit_time_card")
      {
         if ($timeCardInfo->timeCardId == 0)
         {
            $description = "Review all the fields of your time card and make any necessary corrections.  Once you're satisfied, click Save below to add this time card to the system.";
         }
         else
         {
            $description = "You may revise any of the fields for this time card and then select save when you're satisfied with the changes.";
         }
      }
      else if ($view == "view_time_card")
      {
         $description = "View a previously saved time card in detail.";
      }
      
      $html =
      <<<HEREDOC
      <div class="description">$description</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function dateDiv($timeCardInfo)
   {
      $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
      $dateString = $dateTime->format("m-d-Y");
      
      $html =
<<<HEREDOC
         <div class="form-item">
            <div class="form-label">Date</div>
            <input type="text" class="form-input-medium" name="date" style="width:100px;" value="$dateString" disabled>
         </div>
HEREDOC;
      return ($html);
   }
   
   protected static function operatorDiv($timeCardInfo)
   {
      $name = "";
      $operator = UserInfo::load($timeCardInfo->employeeNumber);
      if ($operator)
      {
         $name = $operator->getFullName();
      }
      
      $html = 
<<<HEREDOC
      <div class="form-col">
         <div class="form-section-header">Operator</div>
         <div class="form-item">
            <div class="form-label">Name</div>
            <input type="text" class="form-input-medium" style="width:150px;" value="$name" disabled>
         </div>
         <div class="form-item">
            <div class="form-label">Employee #</div>
            <input type="text" class="form-input-medium" style="width:100px;" value="$timeCardInfo->employeeNumber" disabled>
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function jobDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
      $wcNumber = "unknown";
      $jobInfo = JobInfo::load($timeCardInfo->jobId);
      if ($jobInfo)
      {
         $wcNumber = $jobInfo->wcNumber;
      }
      
      $html =
<<<HEREDOC
      
      <input id="gross-parts-per-hour-input" type="hidden" value="{$jobInfo->getGrossPartsPerHour()}">

      <div class="form-col">

         <div class="form-section-header">Job</div>

         <div class="form-item">
            <div class="form-label">Job #</div>
            <input type="text" class="form-input-medium" style="width:150px;" value="$jobInfo->jobNumber" disabled>
         </div>

         <div class="form-item">
            <div class="form-label">Work center #</div>
            <input type="text" class="form-input-medium" style="width:150px;" value="$wcNumber" disabled>
         </div>

         <div class="form-item">
            <div class="form-label">Heat #</div>
            <input id="material-number-input" type="number" class="form-input-medium" form="input-form" name="materialNumber" style="width:150px;" oninput="this.validator.validate()" value="$timeCardInfo->materialNumber" $disabled>
         </div>

      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function timeDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
      $approval = "no-approval-required";
      if ($timeCardInfo->requiresApproval())
      {
         if ($timeCardInfo->isApproved())
         {
            $approval = "approved";
            
         }
         else
         {
            $approval = "unapproved";
         }
      }
      
      $approvalText = "Approved by supervisor";
      *
      $userInfo = UserInfo::load($timeCardInfo->approvedBy);
      if ($userInfo)
      {
         $approvalText = "Approved by " . $userInfo->username;
      }
      *
      
      $approvalButton = "";
      $unapprovalButton = "";
      if (Authentication::checkPermissions(Permission::APPROVE_TIME_CARDS))
      {
         $approvingUser = Authentication::getAuthenticatedUser();
         $approvalButton = 
<<<HEREDOC
         <button id="approve-button" class="unapproval $approval" onclick="approve($approvingUser->employeeNumber)" style="width: 100px;">Approve</button>
HEREDOC;
         $unapprovalButton =
<<<HEREDOC
         <button id="unapprove-button" class="approval $approval" onclick="unapprove()" style="width: 100px;">Unapprove</button>
HEREDOC;
      }
      
      // Pad minutes to 2 digits.
      $runTimeMinutes = str_pad($timeCardInfo->getRunTimeMinutes(), 2, '0', STR_PAD_LEFT);
      $setupTimeMinutes = str_pad($timeCardInfo->getSetupTimeMinutes(), 2, '0', STR_PAD_LEFT);
      
      $html =
<<<HEREDOC
      <div class="form-col">
         <div class="form-section-header">Time</div>
         
         <div class="form-item">
            <div class="form-label">Run time</div>
            <input id="runTimeHour-input" type="number" class="form-input-medium" form="input-form" name="runTimeHours" style="width:50px;" oninput="runTimeHourValidator.validate(); autoFillEfficiency();" value="{$timeCardInfo->getRunTimeHours()}" $disabled />
            <div style="padding: 5px;">:</div>
            <input id="runTimeMinute-input" type="number" class="form-input-medium" form="input-form" name="runTimeMinutes" style="width:50px;" oninput="runTimeMinuteValidator.validate();  autoFillEfficiency();"value="$runTimeMinutes" $disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Setup time</div>
            <div class="form-col">
               <div class="form-row">
                  <input id="setupTimeHour-input" type="number" class="form-input-medium $approval" form="input-form" name="setupTimeHours" style="width:50px;" oninput="setupTimeHourValidator.validate(); updateApproval();" value="{$timeCardInfo->getSetupTimeHours()}" $disabled />
                  <div style="padding: 5px;">:</div>
                  <input id="setupTimeMinute-input" type="number" class="form-input-medium $approval" form="input-form" name="setupTimeMinutes" style="width:50px;" oninput="setupTimeMinuteValidator.validate(); updateApproval();" value="$setupTimeMinutes" $disabled />
                  <input id="approvedBy-input" type="hidden" form="input-form" name="approvedBy" value="$timeCardInfo->approvedBy" />
                  <input type="hidden" form="input-form" name="approvedDateTime" value="$timeCardInfo->approvedDateTime" />
                  $approvalButton
                  $unapprovalButton
               </div>
               <div id="approval-div" class="approval $approval">$approvalText</div>
               <div id="unapproval-div" class="unapproval $approval">Requires supervisor approval</div>
            </div>
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function partsDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      $efficiency = round($timeCardInfo->getEfficiency(), 2);
      
      $html =
<<<HEREDOC
      <div class="form-col">
         
         <div class="form-section-header">Part Counts</div>
         
         <div class="form-item">
            <div class="form-label">Basket count</div>
            <input id="panCount-input" type="number" class="form-input-medium" form="input-form" name="panCount" style="width:100px;" oninput="panCountValidator.validate()" value="$timeCardInfo->panCount" $disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Good count</div>
            <input id="partsCount-input" type="number" class="form-input-medium" form="input-form" name="partCount" style="width:100px;" oninput="partsCountValidator.validate(); autoFillEfficiency();" value="$timeCardInfo->partCount" $disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Scrap count</div>
            <input id="scrapCount-input" type="number" class="form-input-medium" form="input-form" name="scrapCount" style="width:100px;" oninput="scrapCountValidator.validate()" value="$timeCardInfo->scrapCount" $disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Efficiency</div>
            <input id="efficiency-input" type="number" class="form-input-medium" style="width:100px;" value="$efficiency" disabled />
            <div>&nbsp%</div>
         </div>

      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function commentsDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
      $html =
<<<HEREDOC
      <div class="form-col">
         <div class="form-section-header">Comments</div>
         <div class="form-item">
            <textarea form="input-form" class="comments-input" type="text" form="input-form" name="comments" rows="4" maxlength="256" style="width:300px" $disabled>$timeCardInfo->comments</textarea>
         </div>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function commentCodesDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
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
   
   protected static function qrDiv($timeCardInfo)
   {
      if ($timeCardInfo->timeCardId != 0)
      {
         // TODO: Derive domain name.
         $url = "www.roboxes.com/pptp/timecard/timeCard.php?view=use_time_card&timeCardId=$timeCardInfo->timeCardId";
         
         // http://phpqrcode.sourceforge.net/
         QRcode::png($url, "qrCode.png");
         
         $html =
<<<HEREDOC
         <div class="form-col" style="align-items:center">
            <div><img src="qrCode.png"></image></div>
            <div>TC$timeCardInfo->timeCardId</div>
         </div>
HEREDOC;
      }
      
      return ($html);
   }
   
   protected static function navBar($timeCardInfo, $view)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if ($view == "view_time_card")
      {
         // Case 1
         // Viewing single time card selected from table of time cards.
         
         $navBar->printButton("onPrintTimeCard($timeCardInfo->timeCardId)");
         
         $navBar->highlightNavButton("Ok", "submitForm('input-form', 'timeCard.php', 'view_time_cards', 'no_action')", false);
      }
      else if ($view == "edit_time_card")
      {   
         if ($timeCardInfo->timeCardId == 0)
         {
            // Case 2
            // Viewing as last step of creating a new time card.
            
            $navBar->cancelButton("submitForm('input-form', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
            $navBar->backButton("submitForm('input-form', 'timeCard.php', 'enter_comments', 'update_time_card_info');");
            $navBar->highlightNavButton("Save", "if (validateCard()){submitForm('input-form', 'timeCard.php', 'view_time_cards', 'save_time_card');};", false);
         }
         else
         {
            // Case 3
            // Editing a single time card selected from table of time cards.
            
            $navBar->cancelButton("submitForm('input-form', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
            
            $navBar->printButton("onPrintTimeCard($timeCardInfo->timeCardId)");
                     
            $navBar->highlightNavButton("Save", "if (validateCard()){submitForm('input-form', 'timeCard.php', 'view_time_cards', 'save_time_card');};", false);
         }
      }
      else if ($view == "use_time_card")
      {
         // Case 4
         // Selecting an action from a scanned time card.
         
         if (Authentication::checkPermissions(Permission::EDIT_TIME_CARD))
         {
            $navBar->highlightNavButton("Edit", "submitForm('input-form', 'timeCard.php', 'edit_time_card', 'update_time_card_info');", false);
         }
         else
         {
            $navBar->mainMenuButton();
         }
         
         if (Authentication::checkPermissions(Permission::EDIT_PART_WEIGHT_LOG))
         {
            $navBar->highlightNavButton("Weigh Parts", "submitForm('input-form', '../partWeightLog/partWeightLog.php?timeCardId=$timeCardInfo->timeCardId', 'enter_weight', 'new_part_weight_entry')", true);
         }
         
         if (Authentication::checkPermissions(Permission::EDIT_PART_WASHER_LOG))
         {
            $navBar->highlightNavButton("Count Parts", "submitForm('input-form', '../partWasherLog/partWasherLog.php?timeCardId=$timeCardInfo->timeCardId', 'enter_part_count', 'new_part_washer_entry')", true);
         }
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getTimeCardInfo()
   {
      $timeCardInfo = new TimeCardInfo();
      
      if (isset($_POST['timeCardId']))
      {
         $timeCardInfo = TimeCardInfo::load($_POST['timeCardId']);
      }
      else if (isset($_GET['timeCardId']))
      {
         $timeCardInfo = TimeCardInfo::load($_GET['timeCardId']);
      }
      else if (isset($_SESSION['timeCardInfo']))
      {
         $timeCardInfo = $_SESSION['timeCardInfo'];
      }
      
      return ($timeCardInfo);
   }
}
*/
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
   <script src="../common/validate.js"></script>
   <script src="timeCard.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <form id="input-form" action="" method="POST">
         <input id="time-card-id-input" type="hidden" name="timeCardId" value="<?php echo getTimeCardId(); ?>">
         <input id="approved-by-input" type="hidden" form="input-form" name="approvedBy" value="<?php echo getTimeCardInfo()->approvedBy; ?>" />
         <input id="approved-date-time-input" type="hidden" form="input-form" name="approvedDateTime" value="<?php echo getTimeCardInfo()->approvedDateTime; ?>" />
         <input id="run-time-input" type="hidden" name="runTime" value="<?php echo getTimeCardInfo()->runTime; ?>">
         <input id="setup-time-input" type="hidden" name="setupTime" value="<?php echo getTimeCardInfo()->runTime; ?>">
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
                     <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" oninput="this.validator.validate();" <?php echo !isEditable(TimeCardInputField::WC_NUMBER) ? "disabled" : ""; ?>>
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
                     <input id="run-time-hour-input" type="number" class="form-input-medium" form="input-form" name="runTimeHours" style="width:50px;" oninput="runTimeHourValidator.validate(); updateRunTime(); autoFillEfficiency();" value="<?php echo getTimeCardInfo()->getRunTimeHours(); ?>" <?php echo !isEditable(TimeCardInputField::RUN_TIME) ? "disabled" : ""; ?> />
                     <div style="padding: 5px;">:</div>
                     <input id="run-time-minute-input" type="number" class="form-input-medium" form="input-form" name="runTimeMinutes" style="width:50px;" oninput="runTimeMinuteValidator.validate(); updateRunTime(); autoFillEfficiency();" value="<?php echo getTimeCardInfo()->getRunTimeMinutes(); ?>" step="15" <?php echo !isEditable(TimeCardInputField::RUN_TIME) ? "disabled" : ""; ?> />
                  </div>
         
                  <div class="form-item">
                     <div class="form-label">Setup time</div>
                     <div class="form-col">
                        <div class="form-row">
                           <input id="setup-time-hour-input" type="number" class="form-input-medium $approval" form="input-form" name="setupTimeHours" style="width:50px;" oninput="setupTimeHourValidator.validate(); updateSetupTime(); updateApproval();" value="<?php echo getTimeCardInfo()->getSetupTimeHours(); ?>" <?php echo !isEditable(TimeCardInputField::SETUP_TIME) ? "disabled" : ""; ?> />
                           <div style="padding: 5px;">:</div>
                           <input id="setup-time-minute-input" type="number" class="form-input-medium $approval" form="input-form" name="setupTimeMinutes" style="width:50px;" oninput="setupTimeMinuteValidator.validate(); updateSetupTime(); updateApproval();" value="<?php echo getTimeCardInfo()->getSetupTimeMinutes(); ?>" step="15" <?php echo !isEditable(TimeCardInputField::SETUP_TIME) ? "disabled" : ""; ?> />
                           <?php echo getApprovalButton(); ?>
                           <?php echo getUnapprovalButton(); ?>
                        </div>
                        <div id="approval-div" class="approval"><?php echo getApprovalText(); ?></div>
                        <div id="unapproval-div" class="unapproval" style="display:none;">Requires supervisor approval</div>
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
                     <input id="part-count-input" type="number" class="form-input-medium" form="input-form" name="partCount" style="width:100px;" oninput="partsCountValidator.validate(); autoFillEfficiency();" value="<?php echo getTimeCardInfo()->partCount; ?>" <?php echo !isEditable(TimeCardInputField::PART_COUNT) ? "disabled" : ""; ?> />
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
         var operatorValidator = new SelectValidator("operator-input");
         var jobNumberValidator = new SelectValidator("job-number-input");
         var wcNumberValidator = new SelectValidator("wc-number-input");
         var materialNumberValidator = new IntValidator("material-number-input", 4, 1, 9999, false);
         var runTimeHourValidator = new IntValidator("run-time-hour-input", 2, 0, 10, false);
         var runTimeMinuteValidator = new IntValidator("run-time-minute-input", 2, 0, 59, false);
         var setupTimeHourValidator = new IntValidator("setup-time-hour-input", 2, 0, 10, false);
         var setupTimeMinuteValidator = new IntValidator("setup-time-minute-input", 2, 0, 59, false);
         var panCountValidator = new IntValidator("pan-count-input", 2, 1, 40, false);
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
   
         autoFillEfficiency();
      </script>
     
   </div>

</body>

</html>