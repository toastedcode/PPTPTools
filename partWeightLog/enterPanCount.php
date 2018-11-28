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
      
      $html =
<<<HEREDOC
      <form id="input-form" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Part Count</div>
         <div class="flex-horizontal content-div">

            <div class="flex-vertical" style="justify-content: space-evenly; flex-grow: 1">$panCountInput</div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>

         </div>
         
         $navBar
         
      </div>

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
      
      $html =
<<<HEREDOC
      <!-- Pan count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="panCount-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="panCount" oninput="this.validator.validate()" value="$panCount">
         <label class="mdl-textfield__label" for="panCount-input">Pan count</label>
      </div>
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
}
?>