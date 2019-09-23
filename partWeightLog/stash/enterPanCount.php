<?php
require_once '../common/database.php';

class EnterPanCount
{
   public function getHtml()
   {
      $html = "";
      
      $panCountInput = EnterPanCount::panCountInput();
      
      $keypad = Keypad::getHtml($decimal = false);
      
      $navBar = EnterPanCount::navBar();
      
      $jobId = EnterPanCount::getJobId();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="timeCard.php" method="POST">
         <input id="jobId-input" type="hidden" value="$jobId"/>
      </form>

      <div class="flex-vertical content">

         <div class="heading">Enter a Pan Count</div>

         <div class="description">Enter the number of part baskets being weighed.</div>

         <div class="flex-horizontal inner-content">

            <div class="flex-vertical" style="margin-right: 150px; align-items: flex-start;">$panCountInput</div>
            
            <div class="flex-horizontal hide-on-tablet" style="hide-on-tablet">$keypad</div>

         </div>
         
         $navBar
         
      </div>

      <script src="../common/validatePanCount.js"></script>
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "if (validatePanCount()){submitForm('input-form', 'partWeightLog.php', '', 'save_part_washer_entry');};";
         keypad.init();

         document.getElementById("panCount-input").focus();

         var panCountValidator = new IntValidator("panCount-input", 2, 1, 40, false);

         panCountValidator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo ($this->getHtml());
   }
   
   private static function panCountInput()
   {
      $partWeightEntry= EnterPanCount::getPartWeightEntry();
      
      $panCount = ($partWeightEntry->panCount > 0) ? $partWeightEntry->panCount : null;
      
      // When validating the pan count, we want to be specific about where the mismatch occurs.
      // If the entry method used a time card id, we'll validate the pan count on the time card.
      // If a manual entry method was used, we'll validate against the matching part weight log entry.
      $matchTarget = ($partWeightEntry->timeCardId == PartWeightEntry::UNKNOWN_TIME_CARD_ID) ? "part washer log" : "time card";
      
      $html =
<<<HEREDOC
      <!-- Pan count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="panCount-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="panCount" oninput="this.validator.validate(); validatePanCountMatch();" value="$panCount">
         <label class="mdl-textfield__label" for="panCount-input">Pan count</label>
      </div>
      <div id="pan-count-mismatch-warning" style="visibility: hidden; color:red;">Warning: This pan count does not match the pan count from the $matchTarget.</div>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWeightLog.php', 'view_part_weight_log', 'cancel_part_weight_entry')");
      $navBar->backButton("if (validatePanCount()){submitForm('input-form', 'partWeightLog.php', 'select_operator', 'update_part_weight_entry');};");
      $navBar->nextButton("if (validatePanCount()){submitForm('input-form', 'partWeightLog.php', 'enter_weight', 'update_part_weight_entry');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getPartWeightEntry()
   {
      $partWeightEntry = new PartWeightEntry();
      
      if (isset($_SESSION['partWeightEntry']))
      {
         $partWeightEntry= $_SESSION['partWeightEntry'];
      }
      
      return ($partWeightEntry);
   }
   
   private static function getJobId()
   {
      $jobId = PartWeightEntry::UNKNOWN_JOB_ID;
      
      $partWeightEntry = EnterPanCount::getPartWeightEntry();
      
      if ($partWeightEntry)
      {
         // If we have a time card id, the job id can be found in the associated time card.
         if ($partWeightEntry->timeCardId != PartWeightEntry::UNKNOWN_TIME_CARD_ID)
         {
            $timeCardInfo = TimeCardInfo::load($partWeightEntry->timeCardId);
            
            if ($timeCardInfo)
            {
               $jobId = $timeCardInfo->jobId;
            }
         }
         // Otherwise, this is a "manual" entry, and the job id is found in the entry itself.
         else
         {
            $jobId = $partWeightEntry->jobId;
         }
      }
      
      return ($jobId);
   }
}
?>