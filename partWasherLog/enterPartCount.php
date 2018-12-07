<?php
require_once '../common/database.php';
require_once '../common/partWeightEntry.php';

class EnterPartCount
{
   public static function getHtml()
   {
      $html = "";
      
      $partCountInput = EnterPartCount::partCountInput();
      
      $keypad = Keypad::getHtml($decimal = false);
      
      $navBar = EnterPartCount::navBar();
      
      $jobId = EnterPartCount::getJobId();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="timeCard.php" method="POST">
         <input id="jobId-input" type="hidden" value="$jobId"/>
      </form>

      <div class="flex-vertical content">

         <div class="heading">Enter Part Counts</div>

         <div class="description">Accurate part counts are essential for the success of a job.  Enter the number of baskets washed and the number of counted finished parts.</div>

         <div class="flex-horizontal inner-content">

            <div class="flex-vertical" style="margin-right:150px; align-items: flex-start;">$partCountInput</div>
            
            <div class="flex-horizontal hide-on-tablet">$keypad</div>
         
         </div>
      
         $navBar
         
      </div>

      <script src="../common/validatePanCount.js"></script>
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "if (validatePartCount()){submitForm('input-form', 'partWasherLog.php', '', 'save_part_washer_entry');};";
         keypad.init();

         document.getElementById("panCount-input").focus();

         var panCountValidator = new IntValidator("panCount-input", 2, 1, 40, false);
         var partsCountValidator = new IntValidator("partCount-input", 6, 1, 100000, false);

         panCountValidator.init();
         partsCountValidator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (EnterPartCount::getHtml());
   }
   
   private static function partCountInput()
   {
      $partWasherEntry= EnterPartCount::getPartWasherEntry();
      
      $panCount = $partWasherEntry->panCount;
      $partCount = $partWasherEntry->partCount;
      
      // When validating the pan count, we want to be specific about where the mismatch occurs.
      // If the entry method used a time card id, we'll validate the pan count on the time card.
      // If a manual entry method was used, we'll validate against the matching part weight log entry.
      $matchTarget = ($partWasherEntry->timeCardId == PartWasherEntry::UNKNOWN_TIME_CARD_ID) ? "part weight log" : "time card"; 
      
      $html =
<<<HEREDOC
      <!-- Pan count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="panCount-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="panCount" oninput="this.validator.validate(); validatePanCountMatch();" value="$panCount">
         <label class="mdl-textfield__label" for="panCount-input">Pan count</label>
      </div>
      <div id="pan-count-mismatch-warning" style="visibility: hidden; color:red;">Warning: This pan count does not match the pan count from the $matchTarget.</div>

      <!-- Part count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="partCount-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="partCount" oninput="this.validator.validate();" value="$partCount">
         <label class="mdl-textfield__label" for="partCount-input">Part count</label>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'cancel_part_washer_entry')");
      $navBar->backButton("if (validatePartCount()){submitForm('input-form', 'partWasherLog.php', 'select_operator', 'update_part_washer_entry');};");
      $navBar->highlightNavButton("Save", "if (validatePartCount()){submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'save_part_washer_entry');};", false);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getPartWasherEntry()
   {
      $partWasherEntry = new PartWasherEntry();
      
      if (isset($_SESSION['partWasherEntry']))
      {
         $partWasherEntry= $_SESSION['partWasherEntry'];
      }
      
      return ($partWasherEntry);
   }
   
   private static function getJobId()
   {
      $jobId = PartWeightEntry::UNKNOWN_JOB_ID;
      
      $partWasherEntry = EnterPartCount::getPartWasherEntry();
      
      if ($partWasherEntry)
      {
         // If we have a time card id, the job id can be found in the associated time card.
         if ($partWasherEntry->timeCardId != PartWasherEntry::UNKNOWN_TIME_CARD_ID)
         {
            $timeCardInfo = TimeCardInfo::load($partWasherEntry->timeCardId);
            
            if ($timeCardInfo)
            {
               $jobId = $timeCardInfo->jobId;
            }
         }
         // Otherwise, this is a "manual" entry, and the job id is found in the entry itself.
         else
         {
            $jobId = $partWasherEntry->jobId;
         }
      }
      
      return ($jobId);
   }
}
?>