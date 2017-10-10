<?php

require_once 'keypad.php';

function selectJobPage($timeCardInfo)
{
   $jobNumber = $timeCardInfo->jobNumber;
   
   echo "<body onload=initKeypad()>";
   
   echo
<<<HEREDOC
   <script src="timeCard.js"></script>

   <form action="timeCard.php" method="POST">
   
      <input type="hidden" name="view" value="enter_time"/>
      <input type="hidden" name="action" value="update_time_card_info"/>
   
      Job #:
      <input type="text" id="jobNumber-input" name="jobNumber" class="keypadInputCapable" value="$jobNumber">

      <button type="button" onclick="onCancel()">Cancel</button>
      <button type="submit" name="view" value="select_work_center">Back</button>
      <button type="submit" name="view" value="enter_time">Next</button>
   </form>

   <script>document.getElementById("jobNumber-input").focus();</script>
HEREDOC;
   
   insertKeypad();
   
   echo "</body>";
}

?>
