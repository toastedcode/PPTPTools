<?php

require_once 'keypad.php';

function selectJobPage($timeCardInfo)
{
   $jobNumber = $timeCardInfo->jobNumber;
   
   echo "<body onload=initKeypad()>";
   
   echo
<<<HEREDOC
   <script src="timeCard.js"></script>

   <form id="timeCardForm" action="timeCard.php" method="POST">
   
      Job #:
      <input type="text" id="jobNumber-input" name="jobNumber" class="keypadInputCapable" value="$jobNumber" required>
      <!--div class="mdl-textfield mdl-js-textfield">
         <input class="mdl-textfield__input keypadInputCapable" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="jobNumber-input" name="jobNumber" value="$jobNumber">
         <label class="mdl-textfield__label" for="jobNumber-input">Number...</label>
         <span class="mdl-textfield__error">Invalid Job number!</span>
      </div-->

   </form>

   <script>document.getElementById("jobNumber-input").focus();</script>
HEREDOC;
   
   insertKeypad();
   
   cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
   backButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'select_work_center', 'update_time_card_info');};");
   nextButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};");
   
   echo "</body>";
}

?>
