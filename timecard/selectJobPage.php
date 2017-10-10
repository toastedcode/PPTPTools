<?php

require_once 'keypad.php';

function selectJobPage($timeCardInfo)
{
   echo "<body onload=initKeypad()>";
   
   echo
<<<HEREDOC
   <form action="timeCard.php" method="POST">
   
      <input type="hidden" name="view" value="enter_time"/>';
      <input type="hidden" name="action" value="update_time_card_info"/>';
   
      Job #:
      <input type="text" id="jobNumber-input" name="jobNumber" class="keypadInputCapable" value="">

      <input type="submit" value="Submit">
   </form>

   <script>document.getElementById("jobNumber-input").focus();</script>
HEREDOC;
   
   insertKeypad();
   
   echo "</body>";
}

?>
