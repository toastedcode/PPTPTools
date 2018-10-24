<?php
require_once '../common/database.php';

class EnterPartCount
{
   public static function getHtml()
   {
      $html = "";
      
      $partCountInput = EnterPartCount::partCountInput();
      
      $keypad = Keypad::getHtml($decimal = false);
      
      $navBar = EnterPartCount::navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Part Count</div>
         <div class="flex-horizontal content-div">

            <div class="flex-vertical" style="justify-content: space-evenly; flex-grow: 1">$partCountInput</div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>

         </div>
         
         $navBar
         
      </div>

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
      
      $html =
<<<HEREDOC
      <!-- Pan count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="panCount-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="panCount" oninput="this.validator.validate()" value="$panCount">
         <label class="mdl-textfield__label" for="panCount-input">Pan count</label>
      </div>

      <!-- Part count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="partCount-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="partCount" oninput="this.validator.validate()" value="$partCount">
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
      $navBar->backButton("if (validatePartCount()){submitForm('input-form', 'partWasherLog.php', 'select_pan_ticket', 'update_part_washer_entry');};");
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
}
?>