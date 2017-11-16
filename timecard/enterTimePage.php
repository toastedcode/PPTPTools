<?php

function enterTimePage($timeCardInfo)
{
   $setupTimeHour= $timeCardInfo->setupTimeHour;
   $setupTimeMinute= $timeCardInfo->setupTimeMinute;
   $runTimeHour= $timeCardInfo->runTimeHour;
   $runTimeMinute= $timeCardInfo->runTimeMinute;
   
   echo
   <<<HEREDOC
   <script src="timeCard.js"></script>

   <style>
   .largeTextInput {
      font-size: 40px;
   }

   .adjustTimeButton {
      border-radius: 8px;
      width: 50px;
      height: 50px;
   }
   
   .select-operator-card {
      width: 80%;
      height: 625px;
      margin: auto;
   }

   .nav-div {
      margin: auto;
   }

   .inner-div {
      margin: auto;
      padding: 20px 20px 20px 20px;
      display: table;
   }

   .input-div {
      #display: inline-block;
      display: table-cell;
      vertical-align: middle;
      padding-right: 50px;
   }

   .mdl-card__title {
     height: 50px;
     background: #f4b942;
   }
   </style>

   <div class="mdl-card mdl-shadow--2dp select-operator-card">

   <div class="mdl-card__title">
      <span class="mdl-card__title-text">Enter time</span>
   </div>

   <div class="inner-div">

   <div class="input-div">
   
   <form id="timeCardForm" action="timeCard.php" method="POST">
   
      Setup time (hours):<br>
      <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjustTimeButton" onclick="changeSetupTimeHour(-1)">
         <i class="material-icons">remove</i>
      </button>
      <input id="setupTimeHour-input" class="largeTextInput" name="setupTimeHour" type="number" min="0" max="10" value="$setupTimeHour">
      <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjustTimeButton" onclick="changeSetupTimeHour(1)">
         <i class="material-icons">add</i>
      </button>      
      <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjustTimeButton" onclick="changeSetupTimeMinute(-15)">
         <i class="material-icons">remove</i>
      </button>
      <input id="setupTimeMinute-input" class="largeTextInput" name="setupTimeMinute" type="number" min="0" max="45" value="$setupTimeMinute">
      <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjustTimeButton" onclick="changeSetupTimeMinute(15)">
         <i class="material-icons">add</i>
      </button>
      <br>
      
      Run time (hours):<br>
      <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjustTimeButton" onclick="changeRunTimeHour(-1)">
         <i class="material-icons">remove</i>
      </button>
      <input id="runTimeHour-input" class="largeTextInput" name="runTimeHour" type="number" min="0" max="10" value="$runTimeHour">
      <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjustTimeButton" onclick="changeRunTimeHour(1)">
         <i class="material-icons">add</i>
      </button>
      <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjustTimeButton" onclick="changeRunTimeMinute(-15)">
         <i class="material-icons">remove</i>
      </button>
      <input id="runTimeMinute-input" class="largeTextInput" name="runTimeMinute" type="number" min="0" max="45" value="$setupTimeMinute">
      <button type="button" class="mdl-button mdl-js-button mdl-button--raised adjustTimeButton" onclick="changeRunTimeMinute(15)">
         <i class="material-icons">add</i>
      </button>
      <br>

      <br>

   </form>

   </div>

   </div>
   
   <script>
      function changeSetupTimeHour(delta)
      {
         var field = document.querySelector('#setupTimeHour-input');
         var newValue = parseInt(field.value, 10) + delta;
         
         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 10));
         
         field.value = newValue;
      }
      
      function changeSetupTimeMinute(delta)
      {
         var field = document.querySelector('#setupTimeMinute-input');
         var newValue = parseInt(field.value, 10) + delta;
         
         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 45));
         
         field.value = newValue;
      }
      
      function changeRunTimeHour(delta)
      {
         var field = document.querySelector('#runTimeHour-input');
         var newValue = parseInt(field.value, 10) + delta;
         
         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 10));
         
         field.value = newValue;
      }
      
      function changeRunTimeMinute(delta)
      {
         var field = document.querySelector('#runTimeMinute-input');
         var newValue = parseInt(field.value, 10) + delta;
         
         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 45));
         
         field.value = newValue;
      }
   </script>
HEREDOC;

   echo "<div class=\"nav-div\">";
   
   cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
   backButton("if (validateTime()){submitForm('timeCardForm', 'timeCard.php', 'select_job', 'update_time_card_info');};");
   nextButton("if (validateTime()){submitForm('timeCardForm', 'timeCard.php', 'enter_part_count', 'update_time_card_info');};");
   
   echo "</div>";
   echo "</div>";
   
}
   
?>