<?php
require_once '../database.php';

class EnterTime
{
   public static function getHtml()
   {
      $html = "";
      
      $timeInput = EnterTime::timeInput();
      
      $navBar = EnterTime::navBar();
      
      $html =
<<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Time</div>
         <div class="flex-vertical content-div" style="justify-content: space-evenly">
            $timeInput
         </div>
         
         $navBar
         
      </div>

      <script>
         var setupTimeHourValidator = new IntValidator("setupTimeHour-input", 2, 0, 10, false);
         var setupTimeMinuteValidator = new IntValidator("setupTimeMinute-input", 2, 0, 59, false);
         var runTimeHourValidator = new IntValidator("runTimeHour-input", 2, 0, 10, false);
         var runTimeMinuteValidator = new IntValidator("runTimeMinute-input", 2, 0, 59, false);

         setupTimeHourValidator.init();
         setupTimeMinuteValidator.init();
         runTimeHourValidator.init();
         runTimeMinuteValidator.init();
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
      
      $setupTimeHour= $timeCardInfo->setupTimeHour;
      $setupTimeMinute= $timeCardInfo->setupTimeMinute;
      $runTimeHour= $timeCardInfo->runTimeHour;
      $runTimeMinute= $timeCardInfo->runTimeMinute;
      
      $html =
<<<HEREDOC
      <!-- Setup Time -->
      <div id="setup-time-div" class="flex-vertical">
         <div>Setup Time</div>
         <div class="flex-horizontal">
            <!-- Hours input -->
            <div id="setup-time-hours-div" class="flex-vertical">
               <div>Hours</div>
               <div class="flex-horizontal">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeSetupTimeHour(-1)">
                     <i class="material-icons">remove</i>
                  </button>
                  <input id="setupTimeHour-input" form="timeCardForm" class="large-text-input" name="setupTimeHour" type="number" oninput="this.validator.validate()" value="$setupTimeHour">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeSetupTimeHour(1)">
                     <i class="material-icons">add</i>
                  </button> 
               </div>
            </div>
            <!-- Minutes input -->
            <div class="flex-vertical">
               <div>Minutes</div>
               <div class="flex-horizontal">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeSetupTimeMinute(-15)">
                     <i class="material-icons">remove</i>
                  </button>
                  <input id="setupTimeMinute-input" form="timeCardForm" class="large-text-input" name="setupTimeMinute" type="number" oninput="this.validator.validate()" value="$setupTimeMinute">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeSetupTimeMinute(15)">
                     <i class="material-icons">add</i>
                  </button>
               </div>
            </div>
         </div>
      </div>

      <!-- Run Time -->
      <div class="flex-vertical">
         <div>Run Time</div>
         <div class="flex-horizontal">
            <!-- Hours input -->
            <div id="setup-time-hours-div" class="flex-vertical">
               <div>Hours</div>
               <div class="flex-horizontal">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeRunTimeHour(-1)">
                     <i class="material-icons">remove</i>
                  </button>
                  <input id="runTimeHour-input" form="timeCardForm" class="large-text-input" name="runTimeHour" type="number" oninput="this.validator.validate()" value="$runTimeHour">
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
                  <input id="runTimeMinute-input" form="timeCardForm" class="large-text-input" name="runTimeMinute" type="number" oninput="this.validator.validate()" value="$runTimeMinute">
                  <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjust-time-button" onclick="changeRunTimeMinute(15)">
                     <i class="material-icons">add</i>
                  </button>
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
      $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("if (validateTime()){submitForm('timeCardForm', 'timeCard.php', 'select_job', 'update_time_card_info');};");
      $navBar->nextButton("if (validateTime()){submitForm('timeCardForm', 'timeCard.php', 'enter_part_count', 'update_time_card_info');};");
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