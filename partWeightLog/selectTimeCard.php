<?php
require_once '../database.php';
require_once '../common/keypad.php';

class SelectTimeCard
{
   public function getHtml()
   {
      $html = "";
      
      $timeCardIdInput = SelectTimeCard::timeCardIdInput();
      
      $keypad = Keypad::getHtml(false);
      
      $navBar = SelectTimeCard::navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Time Card</div>
         <div class="flex-horizontal content-div" style="align-items:stretch;">
         
            <div class="flex-vertical" style="flex-grow:1;">
               <div class="flex-horizontal" style="flex-grow:1; flex-shrink: 0;">$timeCardIdInput</div>
               <div id="time-card-div" style="flex-grow:1; flex-shrink: 0; width:350px;"></div>
            </div>
            
            <div class="flex-horizontal" style="flex-grow:1">$keypad</div>
            
         </div>
         
         $navBar
         
      </div>
      
      <script src="../common/validateTimeCard.js"></script>
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "if (validateTimeCardId()){submitForm('input-form', 'partWeightLog.php', 'enter_part_count', 'update_part_weight_entry');}";
         keypad.init();

         document.getElementById("time-card-id-input").focus();
         
         var validator = new TimeCardIdValidator("time-card-id-input");
         validator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo (SelectTimeCard::getHtml());
   }
   
   private function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWeightLog.php', 'view_part_weight_log', 'cancel_part_weight_entry')");
      $navBar->backButton("submitForm('input-form', 'partWeightLog.php', 'select_operator', 'update_part_weight_entry')");
      $navBar->nextButton("if (validateTimeCardId()){submitForm('input-form', 'partWeightLog.php', 'enter_weight', 'update_part_weight_entry');}");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function timeCardIdInput()
   {
      $timeCardId = SelectTimeCard::getTimeCardId();
      
      $html =
<<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="time-card-id-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" name="timeCardId" oninput="this.validator.validate()" value="$timeCardId">
         <label class="mdl-textfield__label" for="time-card-id-input">Time card #</label>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private function getTimeCardId()
   {
      $timeCardId = null;
      
      if (isset($_POST['timeCardId']))
      {
         $timeCardId= $_POST['timeCardId'];
      }
      
      return ($timeCardId);
   }
}
?>