<?php

function viewTimeCardPage($timeCardInfo)
{
   $date = date('Y-m-d');
   $name = NULL;
   
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      if ($operator = $database->getOperator($timeCardInfo->employeeNumber))
      {
         $name = $operator["FirstName"] . " " . $operator["LastName"];
      }
   }
   
   echo
   <<<HEREDOC
   <script src="timeCard.js"></script>

   <form id="timeCardForm" action="timeCard.php" method="POST">
   
     <input type="hidden" name="action" value="update_time_card"/>
     <input type="hidden" name="date" value="$date">
     
     Date:<br>
     <input id="date-input" type="date" value="$date" disabled>
     <br>
     
     Name:<br>
     <input type="text" name="name" value="$name" disabled>
     <br>
     
     Employee #:<br>
     <input type="text" value="$timeCardInfo->employeeNumber" disabled>
     <br>
     
     Work Center #:<br>
     <input type="text" value="$timeCardInfo->wcNumber" disabled>
     <br>
     
     Job #:<br>
     <input type="text" value="$timeCardInfo->jobNumber" disabled>
     <br>
     
     Setup time (hours):<br>
     <input type="number" min="0" max="10" value="$timeCardInfo->setupTimeHour" disabled>
     <input type="number" min="0" max="45" value="$timeCardInfo->setupTimeMinute" disabled>
     <br>
     
     Run time (hours):<br>
     <input type="number" min="1" max="10" value="$timeCardInfo->runTimeHour" disabled>
     <input type="number" min="0" max="45" value="$timeCardInfo->runTimeMinute" disabled>
     <br>
     
      Pan count:<br>
      <input type="number" id="panCount-input" min="1" max="30" value="$timeCardInfo->panCount" disabled>
      <br>
      
      Good part count:<br>
      <input type="number" min="1" max="10000" value="$timeCardInfo->partsCount" disabled>
      <br>
      
      Scrap part count:<br>
      <input type="number" min="1" max="10000" value="$timeCardInfo->scrapCount" disabled>
      <br>
      
      Comments:<br>
      <input type="text" name="comments" value="$timeCardInfo->comments">
      <br>
      
      <br>

      <button type="button" onclick="submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')">Cancel</button>
      <button type="button" onclick="submitForm('timeCardForm', 'timeCard.php', 'enter_part_count', 'update_time_card_info')">Back</button>
      <button type="button" onclick="submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'save_time_card')">Save</button>

   </form>
HEREDOC;
}

?>