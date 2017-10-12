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

   <form id="timeCardForm" action="timeCard.php" method="POST">
   
      Pan count:<br>
      <input type="number" id="panCount-input" name="panCount" class="keypadInputCapable" min="1" max="4" value="$panCount">
      <br>
      
      Good part count:<br>
      <input type="number" id="partsCount-input" name="partsCount" class="keypadInputCapable" min="0" max="10000" value="$partsCount">
      <br>
      
      Scrap part count:<br>
      <input type="number" id="scrapCount-input" name="scrapCount" class="keypadInputCapable" min="0" max="10000" value="$scrapCount">
      <br>
      
      <br><br>
      <button type="button" onclick="submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')">Cancel</button>
      <button type="button" onclick="if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};">Back</button>
      <button type="button" onclick="if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'edit_time_card', 'update_time_card_info');};">Next</button>

   </form>
   <br><br>
   <script>document.getElementById("panCount-input").focus();</script>
HEREDOC;
   
   insertKeypad();
   
   echo "</body>";
}

?>