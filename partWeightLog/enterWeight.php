<?php
require_once '../database.php';
require_once '../common/keypad.php';

class EnterWeight
{
   public function getHtml()
   {
      $html = "";
      
      $weightInput = EnterWeight::weightInput();
      
      $keypad = Keypad::getHtml(true);
      
      $navBar = EnterWeight::navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Weight</div>
         <div class="flex-horizontal content-div">
         
            <div class="flex-horizontal" style="flex-grow: 1">$weightInput</div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>
            
         </div>
         
         $navBar
         
      </div>
      
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "submitForm('input-form', 'partWeightLog.php', 'view_weight_log', 'update_part_weight_entry')";
         keypad.init();

         document.getElementById("weight-input").focus();
         
         var validator = new IntValidator("weight-input", 7, 1, 10000, false);
         validator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo (EnterWeight::getHtml());
   }
   
   private function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWeightLog.php', 'view_pan_tickets', 'cancel_part_weight_entry')");
      $navBar->backButton("submitForm('input-form', 'partWeightLog.php', 'select_time_card', 'update_pan_ticket_info');");
      $navBar->highlightNavButton("Save", "if (validateWeight()){submitForm('input-form', 'partWeightLog.php', 'view_part_weight_log', 'save_part_weight_entry');};", false);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function weightInput()
   {
      $html = "";
      
      $weight = EnterWeight::getWeight();
      
      $html =
<<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="weight-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" name="weight" oninput="this.validator.validate()" value="$weight">
         <label class="mdl-textfield__label" for="weight-input">Weight</label>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function getWeight()
   {
      $weight = 0.0;
      
      if (isset($_SESSION['partWeightEntry']))
      {
         $weight = $_SESSION['partWeightEntry']->weight;
      }
      
      return ($weight);
   }
}
?>