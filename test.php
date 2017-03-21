<?php

require 'keypad.php';

function selectJobPage()
{
   echo
<<<HEREDOC
   <body onload=initKeypad()>
   Job #:<br>
   <input type="text" id="jobNumber-input" name="jobNumber" class="keypadInputCapable" value="">
   </body>
   <script>document.getElementById("jobNumber-input").focus();</script>
HEREDOC;

   insertKeypad();
}

selectJobPage();
?>