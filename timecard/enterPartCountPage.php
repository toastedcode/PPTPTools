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
   </style>

   <form id="timeCardForm" action="timeCard.php" method="POST">
   
      Pan count:<br>
      <input type="number" id="panCount-input" name="panCount" class="keypadInputCapable largeTextInput" min="1" max="4" value="$panCount">
      <br>
      
      Good part count:<br>
      <input type="number" id="partsCount-input" name="partsCount" class="keypadInputCapable largeTextInput" min="0" max="10000" value="$partsCount">
      <br>
      
      Scrap part count:<br>
      <input type="number" id="scrapCount-input" name="scrapCount" class="keypadInputCapable largeTextInput" min="0" max="10000" value="$scrapCount">
      <br>
      
      <br><br>

   </form>
   <br><br>
   <script>document.getElementById("panCount-input").focus();</script>
HEREDOC;
   
   insertKeypad();
   
   cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
   backButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};");
   nextButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'edit_time_card', 'update_time_card_info');};");
   
   echo "</body>";
}

?>