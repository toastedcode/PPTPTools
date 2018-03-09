<?php
require_once '../navigation.php';
require_once '../user.php';
require_once '../common/timeCardInfo.php';
require_once '../common/jobInfo.php';

class ViewTimeCard
{
   public static function getHtml($readOnly)
   {
      $html = "";
      
      $timeCardInfo = ViewTimeCard::getTimeCardInfo();
      
      $titleDiv = ViewTimeCard::titleDiv();
      $dateDiv = ViewTimeCard::dateDiv($timeCardInfo);
      $operatorDiv = ViewTimeCard::operatorDiv($timeCardInfo);
      $jobDiv = ViewTimeCard::jobDiv($timeCardInfo, $readOnly);
      $timeDiv = ViewTimeCard::timeDiv($timeCardInfo, $readOnly);
      $partsDiv = ViewTimeCard::partsDiv($timeCardInfo, $readOnly);
      $commentsDiv = ViewTimeCard::commentsDiv($timeCardInfo, $readOnly);
      
      $navBar = ViewTimeCard::navBar($timeCardInfo, $readOnly);
      
      $html =
<<<HEREDOC
      <form id="input-form" action="timeCard.php" method="POST">
         <input type="hidden" name="timeCardId" value="$timeCardInfo->timeCardId"/>
      </form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Time Card</div>

         <div class="flex-vertical content-div">
         <div class="flex-vertical time-card-div">
            <div class="flex-horizontal">
               $titleDiv
               $dateDiv
            </div>
            <div class="flex-horizontal" style="align-items: flex-start;">
               $operatorDiv
               $timeDiv
            </div>
            <div class="flex-horizontal" style="align-items: flex-start;">
               $jobDiv
               $partsDiv
            </div>
            $commentsDiv
         </div>
         </div>
         
         $navBar
         
      </div>

      <script>
         var jobValidator = new IntValidator("jobNumber-input", 5, 1, 10000, false);
         var setupTimeHourValidator = new IntValidator("setupTimeHour-input", 2, 0, 10, false);
         var setupTimeMinuteValidator = new IntValidator("setupTimeMinute-input", 2, 0, 59, false);
         var runTimeHourValidator = new IntValidator("runTimeHour-input", 2, 0, 10, false);
         var runTimeMinuteValidator = new IntValidator("runTimeMinute-input", 2, 0, 59, false);
         var panCountValidator = new IntValidator("panCount-input", 1, 1, 25, false);
         var partsCountValidator = new IntValidator("partsCount-input", 6, 0, 100000, true);
         var scrapCountValidator = new IntValidator("scrapCount-input", 6, 0, 100000, true);

         jobValidator.init();
         setupTimeHourValidator.init();
         setupTimeMinuteValidator.init();
         runTimeHourValidator.init();
         runTimeMinuteValidator.init();
         panCountValidator.init();
         partsCountValidator.init();
         scrapCountValidator.init();
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
      <div class="flex-horizontal time-card-table-col">
         <h1>Time Card</h1>
      </div>
HEREDOC;

      return ($html);
   }
   
   protected static function dateDiv($timeCardInfo)
   {
      $dateString = Time::toJavascriptDate($timeCardInfo->date);
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Date</h3></div>
            <input type="date" class="medium-text-input" style="width:180px;" value="$dateString" disabled/>
         </div>
      </div>
HEREDOC;
      return ($html);
   }
   
   protected static function operatorDiv($timeCardInfo)
   {
      $name = "";
      $operator = User::getUser($timeCardInfo->employeeNumber);
      if ($operator)
      {
         $name = $operator->getFullName();
      }
      
      $html = 
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Operator</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Name</h3></div>
            <input type="text" class="medium-text-input" style="width:200px;" value="$name" disabled>
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Employee #</h3></div>
            <input type="text" class="medium-text-input" style="width:100px;" value="$timeCardInfo->employeeNumber" disabled>
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
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Job</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job #</h3></div>
            <input id="jobNumber-input" type="text" class="medium-text-input" form="input-form" name="jobNumber" style="width:150px;" oninput="jobValidator.validate()" value="$timeCardInfo->jobNumber" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Work center #</h3></div>
            <input type="text" class="medium-text-input" style="width:150px;" value="$wcNumber" disabled />
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function timeDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
      // Pad minutes to 2 digits.
      $setupTimeMinutes = str_pad($timeCardInfo->getSetupTimeMinutes(), 2, '0', STR_PAD_LEFT);
      $runTimeMinutes = str_pad($timeCardInfo->getRunTimeMinutes(), 2, '0', STR_PAD_LEFT);
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Time</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Setup time</h3></div>
            <input id="setupTimeHour-input" type="number" class="medium-text-input" form="input-form" name="setupTimeHours" style="width:50px;" oninput="setupTimeHourValidator.validate()" value="{$timeCardInfo->getSetupTimeHours()}" $disabled />
            <div style="padding: 5px;">:</div>
            <input id="setupTimeMinute-input" type="number" class="medium-text-input" form="input-form" name="setupTimeMinutes" style="width:50px;" oninput="setupTimeMinuteValidator.validate()" value="$setupTimeMinutes" $disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Run time</h3></div>
            <input id="runTimeHour-input" type="number" class="medium-text-input" form="input-form" name="runTimeHours" style="width:50px;" oninput="runTimeHourValidator.validate()" value="{$timeCardInfo->getRunTimeHours()}" $disabled />
            <div style="padding: 5px;">:</div>
            <input id="runTimeMinute-input" type="number" class="medium-text-input" form="input-form" name="runTimeMinutes" style="width:50px;" oninput="runTimeMinuteValidator.validate()"value="$runTimeMinutes" $disabled />
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function partsDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Part Counts</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Pan count</h3></div>
            <input id="panCount-input" type="number" class="medium-text-input" form="input-form" name="panCount" style="width:100px;" oninput="panCountValidator.validate()" value="$timeCardInfo->panCount" $disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Good count</h3></div>
            <input id="partsCount-input" type="number" class="medium-text-input" form="input-form" name="partCount" style="width:100px;" oninput="partsCountValidator.validate()" value="$timeCardInfo->partCount" $disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Scrap count</h3></div>
            <input id="scrapCount-input" type="number" class="medium-text-input" form="input-form" name="scrapCount" style="width:100px;" oninput="scrapCountValidator.validate()" value="$timeCardInfo->scrapCount" $disabled />
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
      <div class="flex-horizontal">
         <textarea form="input-form" class="comments-input" type="text" form="input-form" name="comments" rows="4" maxlength="256" $disabled>$timeCardInfo->comments</textarea>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function navBar($timeCardInfo, $readOnly)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if ($timeCardInfo->timeCardId == 0)
      {
         // Case 1
         // Viewing as last step of creating a new time card.
         
         $navBar->cancelButton("submitForm('input-form', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
         $navBar->backButton("submitForm('input-form', 'timeCard.php', 'enter_comments', 'update_time_card_info');");
         $navBar->highlightNavButton("Save", "if (validateCard()){submitForm('input-form', 'timeCard.php', 'view_time_cards', 'save_time_card');};", false);
      }
      else if ($readOnly == true)
      {
         // Case 2
         // Viewing single time card selected from table of time cards.
         $navBar->printButton("onPrintTimeCard($timeCardInfo->timeCardId)");
         $navBar->highlightNavButton("Ok", "submitForm('input-form', 'timeCard.php', 'view_time_cards', 'no_action')", false);
      }
      else 
      {   
         // Case 3
         // Editing a single time card selected from table of time cards.
         $navBar->cancelButton("submitForm('input-form', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
         $navBar->printButton("onPrintTimeCard($timeCardInfo->timeCardId)");
         $navBar->highlightNavButton("Save", "if (validateCard()){submitForm('input-form', 'timeCard.php', 'view_time_cards', 'save_time_card');};", false);
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
      else if (isset($_SESSION['timeCardInfo']))
      {
         $timeCardInfo = $_SESSION['timeCardInfo'];
      }
      
      return ($timeCardInfo);
   }
}
?>