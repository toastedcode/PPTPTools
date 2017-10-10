<?php

function enterPartCountPage($timeCardInfo)
{
   echo "<body onload=initKeypad()>";
   
   echo
   <<<HEREDOC
   <form action="timeCard.php" method="POST">
   
      <input type="hidden" name="view" value="view_time_card"/>';
      <input type="hidden" name="action" value="update_time_card_info"/>';
      
      Pan count:<br>
      <input type="number" id="panCount-input" name="panCount" class="keypadInputCapable" min="1" max="30">
      <br>
      
      Good part count:<br>
      <input type="number" name="partsCount" class="keypadInputCapable" min="1" max="10000">
      <br>
      
      Scrap part count:<br>
      <input type="number" name="scrapCount" class="keypadInputCapable" min="1" max="10000">
      <br>
      
      <br><br>
      <input type="submit" value="Submit">

   </form>
   <br><br>
   <script>document.getElementById("panCount-input").focus();</script>
HEREDOC;
   
   insertKeypad();
   
   echo "</body>";
}

?>