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

      <button type="button" onclick="submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')">Cancel</button>
      <button type="button" onclick="if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'select_work_center', 'update_time_card_info');};">Back</button>
      <button type="button" onclick="if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};">Next</button>

   </form>

   <script>document.getElementById("jobNumber-input").focus();</script>
HEREDOC;
   
   insertKeypad();
   
   echo "</body>";
}

?>
