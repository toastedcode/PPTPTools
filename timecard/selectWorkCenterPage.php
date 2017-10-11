<?php
require_once '../database.php';

function selectWorkCenterPage($timeCardInfo)
{
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getWorkCenters();
      
      echo '<script src="timeCard.js"></script>';
      
      echo '<form id="timeCardForm" action="timeCard.php" method="POST">';
      
      // output data of each row
      while($row = $result->fetch_assoc())
      {
         $wcNumber = $row["WCNumber"];
         
         $checked = ($timeCardInfo->wcNumber == $wcNumber) ? " checked" : "";
         
         echo "<input type=\"radio\" name=\"wcNumber\" value=\"$wcNumber\"$checked/>$wcNumber";
      }
      
      echo
      <<<HEREDOC
        <br/>
        
        <button type="button" onclick="submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')">Cancel</button>
        <button type="button" onclick="submitForm('timeCardForm', 'timeCard.php', 'select_operator', 'update_time_card_info')">Back</button>
        <button type="button" onclick="submitForm('timeCardForm', 'timeCard.php', 'select_job', 'update_time_card_info')">Next</button>
        
        </form>
HEREDOC;
   }
}
?>