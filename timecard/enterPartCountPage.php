<?php

function enterPartCountPage($timeCardInfo)
{
   $panCount = $timeCardInfo->panCount;
   $partsCount = $timeCardInfo->partsCount;
   $scrapCount = $timeCardInfo->partsCount;
   
   echo "<body onload=initKeypad()>";
   
   echo
   <<<HEREDOC
   <script src="timeCard.js"></script>

   <form action="timeCard.php" method="POST">
   
      <input type="hidden" name="action" value="update_time_card_info"/>
      
      Pan count:<br>
      <input type="number" id="panCount-input" name="panCount" class="keypadInputCapable" min="1" max="30" value="$panCount">
      <br>
      
      Good part count:<br>
      <input type="number" name="partsCount" class="keypadInputCapable" min="1" max="10000" value="$partsCount">
      <br>
      
      Scrap part count:<br>
      <input type="number" name="scrapCount" class="keypadInputCapable" min="1" max="10000" value="$scrapCount">
      <br>
      
      <br><br>
      <button type="button" onclick="onCancel()">Cancel</button>
      <button type="submit" name="view" value="enter_time">Back</button>
      <button type="submit" name="view" value="view_time_card">Next</button>

   </form>
   <br><br>
   <script>document.getElementById("panCount-input").focus();</script>
HEREDOC;
   
   insertKeypad();
   
   echo "</body>";
}

?>