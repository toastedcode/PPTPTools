<?php
require_once '../common/database.php';
require_once '../common/keypad.php';

abstract class SelectTimeCard
{
   public function getHtml()
   {
      $html = "";
      
      $timeCardIdInput = SelectTimeCard::timeCardIdInput();
      
      $keypad = Keypad::getHtml(false);
      
      $navBar = $this->navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>

      <div class="flex-vertical content">

         <div class="heading">Enter a Time Card Code</div>

         <div class="description">Time Card codes can be found on Pan Tickets underneath the QR scancode.</div>

         <div class="flex-horizontal inner-content">
      
            <div class="flex-vertical" style="align-items: flex-start; margin-right:150px;">
               <div class="flex-horizontal" style="flex-grow:1; flex-shrink: 0;">$timeCardIdInput</div>
               <div id="time-card-div" style="flex-grow:1; flex-shrink: 0; width:350px;"></div>
            </div>
            
            <div class="flex-horizontal hide-on-tablet">$keypad</div>
         
         </div>
      
         $navBar
         
      </div>

      <link rel="stylesheet" type="text/css" href="../common/validateTimeCard.css"/>
      
      <script src="../common/validateTimeCard.js"></script>
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "if (validateTimeCardId()){submitForm('input-form', 'partWasherLog.php', 'enter_part_count', 'update_part_washer_entry');}";
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
   
   protected abstract function navBar();
   
   private function timeCardIdInput()
   {
      $timeCardId = SelectTimeCard::getTimeCardId();
      
      $html =
<<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="time-card-id-input" type="number" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" name="timeCardId" oninput="this.validator.validate()" value="$timeCardId">
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