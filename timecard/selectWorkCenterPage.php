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
      
      echo '<form action="timeCard.php" method="POST">';
      echo '<input type="hidden" name="action" value="update_time_card_info"/>';
      
      // output data of each row
      while($row = $result->fetch_assoc())
      {
         $wcNumber = $row["WCNumber"];
         
         $checked = ($timeCardInfo->wcNumber == $wcNumber) ? " checked" : "";
         
         echo "<input type=\"radio\" name=\"wcNumber\" value=\"$wcNumber\"$checked/>$wcNumber";
      }
      
      echo "<button type=\"button\" onclick=\"onCancel()\">Cancel</button>";
      echo "<button type=\"submit\" name=\"view\" value=\"select_operator\">Back</button>";
      echo "<button type=\"submit\" name=\"view\" value=\"select_job\">Next</button>";
      
      echo '</form>';
   }
}
?>