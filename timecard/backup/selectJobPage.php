<?php

require_once 'keypad.php';

function selectJobPage($timeCardInfo)
{
   $jobNumber = $timeCardInfo->jobNumber;
   
   echo "<body onload=initKeypad()>";
   
   echo
<<<HEREDOC
   <script src="timeCard.js"></script>
   <style>
   .largeTextInput {
      font-size: 40px;
   }

   .input-div {
      display: table-cell;
      vertical-align: middle;
      padding-right: 50px;

   }

   .keypad-div {
      display: table-cell;
      vertical-align: middle;
      padding-left: 50px;
   }
   </style>

   <div class="mdl-card mdl-shadow--2dp select-operator-card">

   <div class="mdl-card__title">
      <span class="mdl-card__title-text">Select job</span>
   </div>

   <div class="inner-div">

   <div class="input-div">

   <form id="timeCardForm" action="timeCard.php" method="POST">
   
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="jobNumber-input" class="mdl-textfield__input keypadInputCapable largeTextInput" name="jobNumber" value="$jobNumber">
         <label class="mdl-textfield__label" for="password_input">Job #</label>
      </div>

      <!--input type="text" id="jobNumber-input" name="jobNumber" class="keypadInputCapable" value="$jobNumber" required-->

      <!--div class="mdl-textfield mdl-js-textfield">
         <input class="mdl-textfield__input keypadInputCapable" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="jobNumber-input" name="jobNumber" value="$jobNumber">
         <label class="mdl-textfield__label" for="jobNumber-input">Number...</label>
         <span class="mdl-textfield__error">Invalid Job number!</span>
      </div-->

   </form>

   </div>

   <script>document.getElementById("jobNumber-input").focus();</script>
HEREDOC;
   
   echo "<div class=\"keypad-div\">";
   insertKeypad();
   echo "</div>";  // keypad-div
   
   echo "</div>";  // inner-div
   
   echo "<div class=\"nav-div\">";
   
   cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
   backButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'select_work_center', 'update_time_card_info');};");
   nextButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};");
   
   echo "</div>";
   echo "</div>";
   
   echo "</body>";
}

?>
