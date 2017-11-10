<?php

function enterPartCountPage($timeCardInfo)
{
   $panCount = $timeCardInfo->panCount;
   $partsCount = $timeCardInfo->partsCount;
   $scrapCount = $timeCardInfo->scrapCount;
   
   echo "<body onload=initKeypad()>";
   
   echo
   <<<HEREDOC
   <script src="timeCard.js"></script>

   <style>
   .largeTextInput {
      font-size: 40px;
   }

   .select-operator-card {
      width: 80%;
      height: 600px;
      margin: auto;
      padding: 10px;
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

   .keypad-div {
      #display: inline-block;
      display: table-cell;
      vertical-align: middle;
      padding-left: 50px;
   }
   </style>

   <div class="mdl-card mdl-shadow--2dp select-operator-card">

   <div class="inner-div">

   <div class="input-div">

   <form id="timeCardForm" action="timeCard.php" method="POST">
   
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
      <input id="panCount-input" class="mdl-textfield__input keypadInputCapable largeTextInput" name="panCount" value="$panCount">
         <label class="mdl-textfield__label" for="panCount-input">Pan count</label>
      </div>
      <br/>
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
      <input id="partsCount-input" class="mdl-textfield__input keypadInputCapable largeTextInput" name="partsCount" value="$partsCount">
         <label class="mdl-textfield__label" for="partsCount-input">Good part count</label>
      </div>
      <br/>
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
      <input id="scrapCount-input" class="mdl-textfield__input keypadInputCapable largeTextInput" name="scrapCount" min="0" max="10000" value="$scrapCount">
         <label class="mdl-textfield__label" for="scrapCount-input">Scrap part count</label>
      </div>

   </form>

   </div>

   <script>document.getElementById("panCount-input").focus();</script>
HEREDOC;
   
   echo "<div class=\"keypad-div\">";
   insertKeypad();
   echo "</div>";  // keypad-div

   echo "</div>";  // inner-div
   
   echo "<div class=\"nav-div\">";
   
   cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
   backButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};");
   nextButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'edit_time_card', 'update_time_card_info');};");
   
   echo "</div>";
   echo "</div>";
   
   echo "</body>";
}

?>