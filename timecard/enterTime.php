<?php
require_once '../common/database.php';

class EnterTime
{
   public static function getHtml()
   {
      $html = "";
      
      $timeInput = EnterTime::timeInput();
      
      $navBar = EnterTime::navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Time</div>
         <div class="flex-vertical content-div" style="justify-content: space-evenly">
            $timeInput
         </div>
         
         $navBar
         
      </div>

      <script>
         var runTimeHourValidator = new IntValidator("runTimeHour-input", 2, 0, 10, false);
         var runTimeMinuteValidator = new IntValidator("runTimeMinute-input", 2, 0, 59, false);
         var setupTimeHourValidator = new IntValidator("setupTimeHour-input", 2, 0, 10, false);
         var setupTimeMinuteValidator = new IntValidator("setupTimeMinute-input", 2, 0, 59, false);

         runTimeHourValidator.init();
         runTimeMinuteValidator.init();
         setupTimeHourValidator.init();
         setupTimeMinuteValidator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (EnterTime::getHtml());
   }
   
   private static function timeInput()
   {
      $timeCardInfo = EnterTime::getTimeCardInfo();

      $runTimeHours = $timeCardInfo->getRunTimeHours();
      $runTimeMinutes = $timeCardInfo->getRunTimeMinutes();
      $setupTimeHours = $timeCardInfo->getSetupTimeHours();
      $setupTimeMinutes = $timeCardInfo->getSetupTimeMinutes();
      $totalTimeHours = $timeCardInfo->getTotalTimeHours();
      $totalTimeMinutes = $timeCardInfo->getTotalTimeMinutes();
      
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
            
      $html =
<<<HEREDOC
      <!-- Run Time -->
      <div class="flex-vertical">
         <div>Run Time</div>
         <div class="flex-horizontal">
            <!-- Hours input -->
            <div id="run-time-hours-div" class="flex-vertical">
               <div>Hours</div>
               <div class="flex-horizontal">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeRunTimeHour(-1)">
                     <i class="material-icons">remove</i>
                  </button>
                  <input id="runTimeHour-input" form="input-form" class="large-text-input time-input" name="runTimeHours" type="number" oninput="this.validator.validate(); autoFillTotalTime();" value="$runTimeHours">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeRunTimeHour(1)">
                     <i class="material-icons">add</i>
                  </button>
               </div>
            </div>
            <!-- Minutes input -->
            <div class="flex-vertical">
               <div>Minutes</div>
               <div class="flex-horizontal">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeRunTimeMinute(-15)">
                     <i class="material-icons">remove</i>
                  </button>
                  <input id="runTimeMinute-input" form="input-form" class="large-text-input time-input" name="runTimeMinutes" type="number" oninput="this.validator.validate(); autoFillTotalTime();" value="$runTimeMinutes">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeRunTimeMinute(15)">
                     <i class="material-icons">add</i>
                  </button>
               </div>
            </div>
         </div>
      </div>

      <!-- Setup Time -->
      <div id="setup-time-div" class="flex-vertical">
         <div>Setup Time</div>
         <div class="flex-horizontal">
            <!-- Hours input -->
            <div id="setup-time-hours-div" class="flex-vertical">
               <div>Hours</div>
               <div class="flex-horizontal">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeSetupTimeHour(-1); updateApproval();">
                     <i class="material-icons">remove</i>
                  </button>
                  <input id="setupTimeHour-input" form="input-form" class="large-text-input time-input" name="setupTimeHours" type="number" oninput="this.validator.validate(); autoFillTotalTime(); updateApproval();" value="$setupTimeHours">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeSetupTimeHour(1); updateApproval();">
                     <i class="material-icons">add</i>
                  </button> 
               </div>
            </div>
            <!-- Minutes input -->
            <div class="flex-vertical">
               <div>Minutes</div>
               <div class="flex-horizontal">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeSetupTimeMinute(-15); updateApproval();">
                     <i class="material-icons">remove</i>
                  </button>
                  <input id="setupTimeMinute-input" form="input-form" class="large-text-input time-input" name="setupTimeMinutes" type="number" oninput="this.validator.validate(); autoFillTotalTime(); updateApproval();" value="$setupTimeMinutes">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeSetupTimeMinute(15); updateApproval();">
                     <i class="material-icons">add</i>
                  </button>
               </div>
            </div>
         </div>
      </div>

      <input id="approvedBy-input" type="hidden" value="$timeCardInfo->approvedBy" />
      <input type="hidden" value="$timeCardInfo->approvedDateTime" />
      <div style="height:25px;">
         <div id="approval-div" class="unapproval $approval">Setup time requires approval by a supervisor.</div>
      </div>

      <!-- Total Time -->
      <div class="flex-vertical">
         <div>Total Time</div>
         <div class="flex-horizontal">
            <!-- Hours input -->
            <div class="flex-vertical">
               <div>Hours</div>
               <div class="flex-horizontal">
                  <input id="totalTimeHour-input" class="large-text-input time-input" type="number" value="$totalTimeHours" disabled>
               </div>
            </div>
            <!-- Minutes input -->
            <div class="flex-vertical">
               <div>Minutes</div>
               <div class="flex-horizontal">
                  <input id="totalTimeMinute-input" class="large-text-input time-input" type="number" value="$totalTimeMinutes" disabled>
               </div>
            </div>
         </div>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("if (validateTime()){submitForm('input-form', 'timeCard.php', 'enter_material_number', 'update_time_card_info');};");
      $navBar->nextButton("if (validateTime()){submitForm('input-form', 'timeCard.php', 'enter_part_count', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getTimeCardInfo()
   {
      $timeCardInfo = new TimeCardInfo();
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $timeCardInfo = $_SESSION['timeCardInfo'];
      }
      
      return ($timeCardInfo);
   }
}
?>