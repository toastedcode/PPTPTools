<?php

require_once '../common/commentCodes.php';
require_once '../common/userInfo.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/timeCardInfo.php';
require_once 'enterComments.php';

require_once $_SERVER["DOCUMENT_ROOT"] . "/phpqrcode/phpqrcode.php";

class ViewTimeCard
{
   public static function getHtml($view)
   {
      $html = "";
      
      $timeCardInfo = ViewTimeCard::getTimeCardInfo();
      
      $readOnly = (($view == "view_time_card") || ($view == "use_time_card"));
      
      $titleDiv = ViewTimeCard::titleDiv();
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
      <form id="input-form" action="timeCard.php" method="POST">
         <input type="hidden" name="timeCardId" value="$timeCardInfo->timeCardId"/>
      </form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Time Card</div>

         <div class="pptp-form" style="height:500px;">
            $titleDiv
            <div class="form-row">
               <div class="form-col">
                  $dateDiv
                  $operatorDiv
                  $jobDiv
               </div>
               <div class="form-col">
                  $timeDiv
                  $partsDiv
               </div>
               <div class="form-col">
                  $commentCodesDiv
                  $commentsDiv
               </div>
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
   
   protected static function dateDiv($timeCardInfo)
   {
      $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
      $dateString = $dateTime->format("m-d-Y");
      
      $html =
<<<HEREDOC
         <div class="form-item">
            <div class="form-label">Date</div>
            <input type="text" class="form-input-medium" name="date" style="width:100px;" value="$dateString" disabled />
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
      $jobInfo = JobInfo::load($timeCardInfo->jobNumber);
      if ($jobInfo)
      {
         $wcNumber = $jobInfo->wcNumber;
      }
      
      $html =
<<<HEREDOC
      
      <input id="gross-parts-per-hour-input" type="hidden" value="{$jobInfo->getGrossPartsPerHour()}"/>

      <div class="form-col">

         <div class="form-section-header">Job</div>

         <div class="form-item">
            <div class="form-label">Job #</div>
            <input type="text" class="form-input-medium" style="width:150px;" value="$timeCardInfo->jobNumber" disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Work center #</div>
            <input type="text" class="form-input-medium" style="width:150px;" value="$wcNumber" disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Heat #</div>
            <input id="material-number-input" type="number" class="form-input-medium" form="input-form" name="materialNumber" style="width:150px;" oninput="this.validator.validate()" value="$timeCardInfo->materialNumber" $disabled />
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
      /*
      $userInfo = UserInfo::load($timeCardInfo->approvedBy);
      if ($userInfo)
      {
         $approvalText = "Approved by " . $userInfo->username;
      }
      */
      
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
?>