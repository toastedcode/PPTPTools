<?php
require_once '../database.php';

class SelectJob
{
   public static function getHtml()
   {
      $html = "";
      
      $jobInput = SelectJob::jobInput();
      
      $keypad = Keypad::getHtml();
            
      $navBar = SelectJob::navBar();
      
      $html =
      <<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Job</div>
         <div class="flex-horizontal content-div">

            <div class="flex-horizontal" style="flex-grow: 1">$jobInput</div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>
            <script type="text/javascript">initKeypad()</script>

         </div>
         
         $navBar
         
      </div>

      <script type="text/javascript">
         initKeypad();
         document.getElementById("jobNumber-input").focus();

         var jobValidator = new IntValidator("jobNumber-input", 5, 1, 10000, false);
         jobValidator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (SelectJob::getHtml());
   }
   
   private static function jobInput()
   {
      $jobNumber = SelectJob::getJobNumber();
      
      $html = 
<<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="jobNumber-input" form="timeCardForm" class="mdl-textfield__input keypadInputCapable large-text-input" name="jobNumber" oninput="this.validator.validate()" value="$jobNumber">
         <label class="mdl-textfield__label" for="jobNumber-input">Job #</label>
      </div>
HEREDOC;

      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'select_work_center', 'update_time_card_info');};");
      $navBar->nextButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getJobNumber()
   {
      $jobNumber = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $jobNumber = $_SESSION['timeCardInfo']->jobNumber;
      }
      
      return ($jobNumber);
   }
}
?>